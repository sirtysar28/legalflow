<?php

namespace App\Http\Controllers;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Models\Application;
use App\Models\ApplicationField;
use App\Models\ApplicationHistory;
use App\Models\Department;
use App\Models\DocumentRequirement;
use App\Models\PermitType;
use App\Models\Supplier;
use App\Services\AccessService;
use App\Services\ApplicationWorkflowService;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;

class ApplicationController extends Controller
{
    public function __construct(private ApplicationWorkflowService $workflow)
    {
    }

    /**
     * Daftar pengajuan: user melihat miliknya, legal/admin melihat semua.
     * Berfungsi sekaligus sebagai Dashboard Perizinan / Dashboard Purchasing
     * (stats ringkasan + grafik distribusi status) dan List/Monitoring.
     */
    public function index(Request $request)
    {
        Application::syncExpiry();

        $type = ApplicationType::tryFrom((string) $request->query('type'));
        $status = ApplicationStatus::tryFrom((string) $request->query('status'));
        $user = auth()->user();

        $query = Application::with(['user', 'department', 'permitType', 'supplier'])
            ->with('fields')
            ->latest();

        if (! $user->canReview()) {
            $query->mine($user);
        }

        if ($type) {
            $query->ofType($type);
        }

        if ($status) {
            $query->status($status);
        }

        // Filter monitoring: jenis izin (Perizinan) / divisi (Agreement, legal-admin).
        if ($request->filled('permit_type')) {
            $query->where('permit_type_id', $request->query('permit_type'));
        }
        if ($request->filled('department') && $user->canReview()) {
            $query->where('department_id', $request->query('department'));
        }

        // Pencarian nomor / judul.
        if ($request->filled('q')) {
            $q = (string) $request->query('q');
            $query->where(fn ($w) => $w->where('title', 'like', "%{$q}%")
                ->orWhere('application_number', 'like', "%{$q}%"));
        }

        $applications = $query->paginate(10)->withQueryString();

        // Muat dokumen untuk progress kelengkapan tiap baris.
        $applications->loadMissing('documents');

        // ---------- Ringkasan dashboard per modul ----------
        $stats = $this->moduleStats($type, $user);
        $chart = $this->statusDistribution($type, $user);
        $trend = $this->monthlyTrend($type, $user);
        $expiringSoon = null;

        if ($type === ApplicationType::PERMIT || $type === ApplicationType::AGREEMENT) {
            $expiringQuery = Application::with(['user', 'department', 'permitType', 'supplier'])
                ->expiringSoon((int) config('legalflow.expiring_soon_days', 30));
            if (! $user->canReview()) {
                $expiringQuery->mine($user);
            }
            if ($type) {
                $expiringQuery->ofType($type);
            }
            $expiringSoon = $expiringQuery->orderBy('valid_until')->take(5)->get();
        }

        return view('applications.index', [
            'applications'  => $applications,
            'type'          => $type,
            'status'        => $status,
            'statuses'      => ApplicationStatus::options(),
            'stats'         => $stats,
            'chart'         => $chart,
            'trend'         => $trend,
            'expiringSoon'  => $expiringSoon,
            'permitTypes'   => PermitType::where('is_active', true)->orderBy('name')->get(),
            'departments'   => Department::orderBy('name')->get(),
        ]);
    }

    /**
     * Statistik ringkasan modul (kartu di atas list).
     */
    private function moduleStats(?ApplicationType $type, $user): array
    {
        $base = Application::query();
        if (! $user->canReview()) {
            $base->mine($user);
        }
        if ($type) {
            $base->ofType($type);
        }

        $count = fn ($modifier) => (clone $base)->when(true, $modifier)->count();

        if ($type === ApplicationType::PERMIT) {
            // Dashboard Perizinan: terdaftar, diproses, akan kadaluarsa, izin terbit
            return [
                ['label' => 'Total Pengajuan Izin', 'value' => $count(fn ($q) => $q), 'class' => 'stat-purple', 'icon' => 'bi-journal-text'],
                ['label' => 'Sedang Diproses', 'value' => $count(fn ($q) => $q->whereIn('status', [
                    ApplicationStatus::SUBMITTED->value, ApplicationStatus::UNDER_REVIEW->value,
                    ApplicationStatus::REVISION_REQUESTED->value, ApplicationStatus::RESUBMITTED->value,
                ])), 'class' => 'stat-blue', 'icon' => 'bi-arrow-repeat'],
                ['label' => 'Izin Terbit', 'value' => $count(fn ($q) => $q->status(ApplicationStatus::APPROVED)), 'class' => 'stat-green', 'icon' => 'bi-patch-check'],
                ['label' => 'Akan Kadaluarsa', 'value' => $count(fn ($q) => $q->expiringSoon((int) config('legalflow.expiring_soon_days', 30))), 'class' => 'stat-amber', 'icon' => 'bi-hourglass-split'],
            ];
        }

        if ($type === ApplicationType::AGREEMENT) {
            // Dashboard Purchasing: total, kontrak aktif, menunggu persetujuan, kadaluarsa
            return [
                ['label' => 'Total Agreement', 'value' => $count(fn ($q) => $q), 'class' => 'stat-purple', 'icon' => 'bi-files'],
                ['label' => 'Kontrak Aktif', 'value' => $count(fn ($q) => $q->active()), 'class' => 'stat-green', 'icon' => 'bi-file-earmark-check'],
                ['label' => 'Menunggu Persetujuan', 'value' => $count(fn ($q) => $q->whereIn('status', [
                    ApplicationStatus::SUBMITTED->value, ApplicationStatus::UNDER_REVIEW->value,
                    ApplicationStatus::RESUBMITTED->value,
                ])), 'class' => 'stat-blue', 'icon' => 'bi-hourglass-top'],
                ['label' => 'Akan Kadaluarsa', 'value' => $count(fn ($q) => $q->expiringSoon((int) config('legalflow.expiring_soon_days', 30))), 'class' => 'stat-amber', 'icon' => 'bi-hourglass-split'],
            ];
        }

        // Semua pengajuan (tanpa modul)
        return [
            ['label' => 'Total Pengajuan', 'value' => $count(fn ($q) => $q), 'class' => 'stat-purple', 'icon' => 'bi-stack'],
            ['label' => 'Menunggu Review', 'value' => $count(fn ($q) => $q->whereIn('status', [
                ApplicationStatus::SUBMITTED->value, ApplicationStatus::RESUBMITTED->value,
            ])), 'class' => 'stat-blue', 'icon' => 'bi-inbox'],
            ['label' => 'Disetujui', 'value' => $count(fn ($q) => $q->status(ApplicationStatus::APPROVED)), 'class' => 'stat-green', 'icon' => 'bi-check2-circle'],
            ['label' => 'Ditolak', 'value' => $count(fn ($q) => $q->status(ApplicationStatus::REJECTED)), 'class' => 'stat-red', 'icon' => 'bi-x-circle'],
        ];
    }

    /**
     * Tren pengajuan 6 bulan terakhir (diajukan vs disetujui) untuk bar chart.
     */
    private function monthlyTrend(?ApplicationType $type, $user): array
    {
        $base = Application::query();
        if (! $user->canReview()) {
            $base->mine($user);
        }
        if ($type) {
            $base->ofType($type);
        }

        $months = collect(range(5, 0))->map(fn ($i) => now()->subMonths($i)->startOfMonth());
        $start = $months->first();
        $end = now()->endOfMonth();

        $submitted = (clone $base)
            ->whereNotNull('submitted_at')->whereBetween('submitted_at', [$start, $end])
            ->get(['submitted_at'])
            ->groupBy(fn ($a) => $a->submitted_at->format('Y-m'));

        $approved = (clone $base)
            ->whereNotNull('approved_at')->whereBetween('approved_at', [$start, $end])
            ->get(['approved_at'])
            ->groupBy(fn ($a) => $a->approved_at->format('Y-m'));

        $bulan = [1 => 'Jan', 2 => 'Feb', 3 => 'Mar', 4 => 'Apr', 5 => 'Mei', 6 => 'Jun',
                  7 => 'Jul', 8 => 'Agu', 9 => 'Sep', 10 => 'Okt', 11 => 'Nov', 12 => 'Des'];

        $labels = $dataSubmitted = $dataApproved = [];
        foreach ($months as $month) {
            $key = $month->format('Y-m');
            $labels[]        = $bulan[(int) $month->format('n')] . ' ' . $month->format('y');
            $dataSubmitted[] = $submitted->get($key)?->count() ?? 0;
            $dataApproved[]  = $approved->get($key)?->count() ?? 0;
        }

        return ['labels' => $labels, 'submitted' => $dataSubmitted, 'approved' => $dataApproved];
    }

    /**
     * Distribusi status untuk grafik (Chart.js).
     */
    private function statusDistribution(?ApplicationType $type, $user): array
    {
        $base = Application::query();
        if (! $user->canReview()) {
            $base->mine($user);
        }
        if ($type) {
            $base->ofType($type);
        }

        $counts = (clone $base)
            ->selectRaw('status, COUNT(*) as total')
            ->groupBy('status')
            ->pluck('total', 'status');

        $labels = $data = $colors = [];
        foreach (ApplicationStatus::cases() as $case) {
            $total = (int) ($counts[$case->value] ?? 0);
            if ($total === 0) {
                continue;
            }
            $labels[] = $case->label();
            $data[]   = $total;
            $colors[] = match ($case->color()) {
                'secondary' => '#6c757d', 'info' => '#0dcaf0', 'primary' => '#2d5da8',
                'warning'   => '#ffc107', 'success' => '#22a04d', 'danger' => '#dc2626',
                'dark'      => '#212529', default => '#8d99ae',
            };
        }

        return ['labels' => $labels, 'data' => $data, 'colors' => $colors];
    }

    public function create(Request $request)
    {
        $type = ApplicationType::tryFrom((string) $request->query('type'))
            ?? abort(404, 'Jenis pengajuan tidak valid.');

        $application = new Application([
            'application_type' => $type,
            'title'            => old('title'),
            'description'      => old('description'),
        ]);

        return $this->formView($application, true);
    }

    public function store(Request $request)
    {
        $type = ApplicationType::tryFrom((string) $request->input('application_type'))
            ?? abort(404, 'Jenis pengajuan tidak valid.');

        $data = $this->validatedBaseData($request, $type);

        $application = DB::transaction(function () use ($request, $type, $data) {
            $application = Application::create($data + [
                'application_number' => 'TEMP', // diganti langsung setelah insert (butuh ID)
                'application_type' => $type->value,
                'user_id'          => auth()->id(),
                'department_id'    => auth()->user()->department_id,
                'status'           => ApplicationStatus::DRAFT->value,
            ]);

            $application->update([
                'application_number' => Application::generateNumber($application->id, $type),
            ]);

            $this->syncFields($application, $request);

            ApplicationHistory::create([
                'application_id' => $application->id,
                'user_id'        => auth()->id(),
                'action'         => 'Membuat draft pengajuan',
                'new_status'     => ApplicationStatus::DRAFT->value,
            ]);

            return $application;
        });

        return redirect()
            ->route('applications.edit', $application)
            ->with('success', 'Draft pengajuan berhasil dibuat. Lengkapi form & upload dokumen.');
    }

    public function show(Application $application)
    {
        $this->authorizeView($application);

        $application->load([
            'user', 'department', 'permitType', 'supplier',
            'fields', 'documents.uploader',
            'reviews.reviewer', 'histories.user',
            'accessRequests.requester', 'accessRequests.reviewer',
        ]);

        $requirements = DocumentRequirement::active()
            ->when(
                $application->application_type === ApplicationType::PERMIT,
                fn ($q) => $q->where('application_type', 'PERMIT')
                    ->where(fn ($w) => $w->whereNull('permit_type_id')
                        ->orWhere('permit_type_id', $application->permit_type_id)),
                fn ($q) => $q->where('application_type', 'AGREEMENT')
            )->get();

        $myAccess = $application->accessRequests
            ->firstWhere('requested_by', auth()->id());
        $hasAccess = AccessService::hasGrantedAccess(auth()->user(), $application);

        return view('applications.show', compact('application', 'requirements', 'myAccess', 'hasAccess'));
    }

    public function edit(Application $application)
    {
        $this->authorizeOwner($application);

        abort_unless($application->isEditable(), 403, 'Pengajuan tidak dapat diedit pada status saat ini.');

        return $this->formView($application, false);
    }

    public function update(Request $request, Application $application)
    {
        $this->authorizeOwner($application);
        abort_unless($application->isEditable(), 403, 'Pengajuan tidak dapat diedit pada status saat ini.');

        $data = $this->validatedBaseData($request, $application->application_type);
        $application->update($data);
        $this->syncFields($application, $request);

        ApplicationHistory::create([
            'application_id' => $application->id,
            'user_id'        => auth()->id(),
            'action'         => 'Memperbaiki data pengajuan',
            'old_status'     => $application->status->value,
            'new_status'     => $application->status->value,
            'notes'          => 'Data form diperbarui oleh pemohon.',
        ]);

        return redirect()->route('applications.show', $application)
            ->with('success', 'Data pengajuan berhasil diperbarui.');
    }

    /**
     * Ajukan / ajukan ulang pengajuan ke Legal.
     */
    public function submit(Request $request, Application $application)
    {
        $this->authorizeOwner($application);

        $from = $application->status;
        $expected = $from === ApplicationStatus::REVISION_REQUESTED
            ? ApplicationStatus::RESUBMITTED
            : ApplicationStatus::SUBMITTED;

        if (! $this->workflow->canTransition($application, $expected)) {
            abort(403, 'Pengajuan tidak dapat diajukan pada status saat ini.');
        }

        // Validasi kelengkapan form.
        foreach ($this->fieldConfig($application->application_type) as $field) {
            if ($field['required'] && blank($application->fieldValue($field['name']))) {
                return back()->withErrors([
                    'submit' => "Field \"{$field['label']}\" wajib diisi sebelum diajukan.",
                ]);
            }
        }

        // Validasi kelengkapan dokumen wajib.
        $requirements = DocumentRequirement::active()
            ->where('application_type', $application->application_type->value)
            ->when(
                $application->application_type === ApplicationType::PERMIT,
                fn ($q) => $q->where(fn ($w) => $w->whereNull('permit_type_id')
                    ->orWhere('permit_type_id', $application->permit_type_id))
            )
            ->where('is_required', true)
            ->pluck('document_name');

        $uploaded = $application->documents->pluck('document_type');
        $missing = $requirements->diff($uploaded);

        if ($missing->isNotEmpty()) {
            return back()->withErrors([
                'submit' => 'Dokumen wajib belum lengkap: ' . $missing->implode(', '),
            ]);
        }

        // Check Supplier Assessment untuk agreement.
        if ($application->application_type === ApplicationType::AGREEMENT) {
            if (! $application->supplier?->assessmentPassed()) {
                return back()->withErrors([
                    'submit' => 'Supplier belum lolos Supplier Assessment '
                        . '(terdaftar, assessment tersedia, data & dokumen lengkap).',
                ]);
            }
        }

        $this->workflow->{$from === ApplicationStatus::REVISION_REQUESTED ? 'resubmit' : 'submit'}(
            $application,
            auth()->user()
        );

        return redirect()->route('applications.show', $application)
            ->with('success', 'Pengajuan berhasil diajukan ke Legal.');
    }

    // ---------------------------------------------------------------------
    // Helpers
    // ---------------------------------------------------------------------

    private function validatedBaseData(Request $request, ApplicationType $type): array
    {
        $rules = [
            'title'       => ['required', 'string', 'max:255'],
            'description' => ['nullable', 'string'],
        ];

        if ($type === ApplicationType::PERMIT) {
            $rules['permit_type_id'] = ['required', 'exists:permit_types,id'];
        } else {
            $rules['supplier_id'] = ['required', 'exists:suppliers,id'];
        }

        $validated = $request->validate($rules, [
            'title.required' => 'Judul pengajuan wajib diisi.',
        ]);

        return [
            'title'          => $validated['title'],
            'description'    => $validated['description'] ?? null,
            'permit_type_id' => $type === ApplicationType::PERMIT ? ($validated['permit_type_id'] ?? null) : null,
            'supplier_id'    => $type === ApplicationType::AGREEMENT ? ($validated['supplier_id'] ?? null) : null,
        ];
    }

    private function syncFields(Application $application, Request $request): void
    {
        foreach ($this->fieldConfig($application->application_type) as $field) {
            $value = (string) $request->input("fields.{$field['name']}", '');

            ApplicationField::updateOrCreate(
                ['application_id' => $application->id, 'field_name' => $field['name']],
                ['field_value' => $value]
            );
        }
    }

    private function fieldConfig(ApplicationType $type): array
    {
        return $type === ApplicationType::PERMIT
            ? config('legalflow.permit_fields')
            : config('legalflow.agreement_fields');
    }

    private function formView(Application $application, bool $isCreate)
    {
        $application->status ??= ApplicationStatus::DRAFT;

        $fields = $this->fieldConfig($application->application_type);

        $requirements = DocumentRequirement::active()
            ->where('application_type', $application->application_type->value)
            ->when(
                $application->application_type === ApplicationType::PERMIT && $application->permit_type_id,
                fn ($q) => $q->where(fn ($w) => $w->whereNull('permit_type_id')
                    ->orWhere('permit_type_id', $application->permit_type_id))
            )
            ->orderBy('document_name')->get();

        return view('applications.form', [
            'application'  => $application,
            'isCreate'     => $isCreate,
            'fields'       => $fields,
            'requirements' => $requirements,
            'permitTypes'  => PermitType::where('is_active', true)->orderBy('name')->get(),
            'suppliers'    => Supplier::orderBy('name')->get(),
        ]);
    }

    private function authorizeOwner(Application $application): void
    {
        abort_unless($application->isOwnedBy(auth()->user()), 403, 'Anda bukan pemilik pengajuan ini.');
    }

    private function authorizeView(Application $application): void
    {
        $user = auth()->user();

        // Pemilik, Legal/Admin, atau user dengan akses yang diberikan.
        if ($application->isOwnedBy($user) || $user->canReview()
            || AccessService::hasGrantedAccess($user, $application)) {
            return;
        }

        // User lain hanya dapat melihat metadata pengajuan yang sudah APPROVED
        // (agar dapat meminta akses dokumen). Unduhan tetap diotorisasi.
        if ($application->status === ApplicationStatus::APPROVED) {
            return;
        }

        abort(403, 'Anda tidak memiliki akses ke pengajuan ini.');
    }
}
