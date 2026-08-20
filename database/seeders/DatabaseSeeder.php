<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Models\Application;
use App\Models\ApplicationField;
use App\Models\ApplicationHistory;
use App\Models\ApplicationReview;
use App\Models\Department;
use App\Models\DocumentAccessRequest;
use App\Models\DocumentFolder;
use App\Models\DocumentRequirement;
use App\Models\PermitType;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;

/**
 * Seeder IDEMPOTENT — aman dijalankan berulang kali di database yang sudah
 * terisi (tidak melempar duplicate entry). Master data di-updateOrCreate,
 * data demo pengajuan hanya dibuat bila tabel applications masih kosong.
 */
class DatabaseSeeder extends Seeder
{
    public function run(): void
    {
        // ------------------------------------------------------------------
        // Roles & Divisi (idempotent via kolom unik)
        // ------------------------------------------------------------------
        $roleUser = Role::updateOrCreate(['name' => 'user'], ['label' => 'User / Requester']);
        $roleLegal = Role::updateOrCreate(['name' => 'legal'], ['label' => 'Legal']);
        $roleAdmin = Role::updateOrCreate(['name' => 'admin'], ['label' => 'Admin']);
        $roleSuperAdmin = Role::updateOrCreate(['name' => 'super_admin'], ['label' => 'Super Admin']);

        $divisiA = Department::updateOrCreate(['code' => 'DA'], ['name' => 'Divisi A']);
        $divisiB = Department::updateOrCreate(['code' => 'DB'], ['name' => 'Divisi B']);
        Department::updateOrCreate(['code' => 'DC'], ['name' => 'Divisi C']);
        $purchasing = Department::updateOrCreate(['code' => 'PRC'], ['name' => 'Purchasing']);
        Department::updateOrCreate(['code' => 'LGL'], ['name' => 'Legal & Compliance']);

        // ------------------------------------------------------------------
        // Users (password semua: "password")
        // ------------------------------------------------------------------
        $admin = User::updateOrCreate(['email' => 'admin@legalflow.test'], [
            'name' => 'Admin LegalFlow',
            'password' => 'password',
            'role_id' => $roleSuperAdmin->id,
            'department_id' => $divisiA->id,
            'status' => 'active',
        ]);

        $legal = User::updateOrCreate(['email' => 'legal@legalflow.test'], [
            'name' => 'Bunga Legal',
            'password' => 'password',
            'role_id' => $roleLegal->id,
            'department_id' => $divisiA->id,
            'status' => 'active',
        ]);

        $requester = User::updateOrCreate(['email' => 'user@legalflow.test'], [
            'name' => 'Andi Requester',
            'password' => 'password',
            'role_id' => $roleUser->id,
            'department_id' => $divisiA->id,
            'status' => 'active',
        ]);

        $purchaser = User::updateOrCreate(['email' => 'budi@legalflow.test'], [
            'name' => 'Budi Purchasing',
            'password' => 'password',
            'role_id' => $roleUser->id,
            'department_id' => $purchasing->id,
            'status' => 'active',
        ]);

        $otherUser = User::updateOrCreate(['email' => 'citra@legalflow.test'], [
            'name' => 'Citra Divisi B',
            'password' => 'password',
            'role_id' => $roleUser->id,
            'department_id' => $divisiB->id,
            'status' => 'active',
        ]);

        // ------------------------------------------------------------------
        // Jenis Izin & Persyaratan Dokumen (per kategori sesuai workflow)
        // ------------------------------------------------------------------
        $izinNIB = PermitType::updateOrCreate(['name' => 'NIB (Nomor Induk Berusaha)'], [
            'category' => 'USAHA',
            'description' => 'Identitas tunggal pelaku usaha via OSS-RBA.',
        ]);
        $izinPBG = PermitType::updateOrCreate(['name' => 'PBG (Persetujuan Bangunan Gedung)'], [
            'category' => 'BANGUNAN',
            'description' => 'Persetujuan bangunan gedung baru/renovasi.',
        ]);
        $izinSLF = PermitType::updateOrCreate(['name' => 'SLF (Sertifikat Laik Fungsi)'], [
            'category' => 'BANGUNAN',
            'description' => 'Sertifikat kelaikan fungsi bangunan gedung.',
        ]);
        $izinUklUpl = PermitType::updateOrCreate(['name' => 'UKL-UPL (Upaya Pengelolaan Lingkungan)'], [
            'category' => 'LINGKUNGAN',
            'description' => 'Dokumen lingkungan untuk usaha berdampak menengah.',
        ]);
        $izinHalal = PermitType::updateOrCreate(['name' => 'Sertifikasi Halal'], [
            'category' => 'PRODUK',
            'description' => 'Sertifikasi halal produk makanan/minuman/obat.',
        ]);
        $izinTDG = PermitType::updateOrCreate(['name' => 'TDG (Tanda Daftar Gudang)'], [
            'category' => 'OPERASIONAL',
            'description' => 'Pendaftaran gudang penyimpanan barang.',
        ]);

        $requirements = [
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinNIB->id, 'document_name' => 'Surat Permohonan', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinNIB->id, 'document_name' => 'KTP', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinNIB->id, 'document_name' => 'Dokumen Pendukung', 'is_required' => false],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinPBG->id, 'document_name' => 'Surat Permohonan', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinPBG->id, 'document_name' => 'Agreement', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinPBG->id, 'document_name' => 'Dokumen Legal', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinSLF->id, 'document_name' => 'Surat Permohonan', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinSLF->id, 'document_name' => 'As-built Drawing', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinUklUpl->id, 'document_name' => 'Formulir UKL-UPL', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinUklUpl->id, 'document_name' => 'Dokumen Lingkungan', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinHalal->id, 'document_name' => 'Surat Permohonan', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinHalal->id, 'document_name' => 'Sertifikat Halal Bahan', 'is_required' => false],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinTDG->id, 'document_name' => 'Surat Permohonan', 'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => $izinTDG->id, 'document_name' => 'Bukti Kepemilikan Gudang', 'is_required' => true],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'Draft Agreement', 'is_required' => true],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'Proposal Komersial', 'is_required' => true],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'Supplier Assessment Report', 'is_required' => true],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'Dokumen Legal Supplier', 'is_required' => false],
        ];

        foreach ($requirements as $req) {
            DocumentRequirement::updateOrCreate(
                [
                    'application_type' => $req['application_type'],
                    'permit_type_id' => $req['permit_type_id'],
                    'document_name' => $req['document_name'],
                ],
                ['is_required' => $req['is_required'], 'is_active' => true]
            );
        }

        // ------------------------------------------------------------------
        // Suppliers (dengan status assessment berbeda-beda)
        // ------------------------------------------------------------------
        $supplierLolos = Supplier::updateOrCreate(['npwp' => '01.234.567.8-901.000'], [
            'name' => 'PT Sumber Makmur Jaya',
            'address' => 'Jl. Industri Raya No. 10, Jakarta',
            'phone' => '021-5551234',
            'email' => 'procurement@sumbermakmur.co.id',
            'contact_person' => 'Dewi Santika',
            'is_registered' => true,
            'assessment_available' => true,
            'assessment_score' => 87.50,
            'assessment_date' => '2026-05-12',
            'risk_level' => 'LOW',
            'data_complete' => true,
            'documents_complete' => true,
        ]);

        Supplier::updateOrCreate(['npwp' => '02.345.678.9-012.000'], [
            'name' => 'CV Karya Abadi',
            'address' => 'Jl. Merdeka No. 5, Bandung',
            'phone' => '022-4447890',
            'email' => 'info@karyaabadi.co.id',
            'contact_person' => 'Rangga Pratama',
            'is_registered' => true,
            'assessment_available' => false,
            'assessment_score' => 54.00,
            'assessment_date' => '2026-02-20',
            'risk_level' => 'HIGH',
            'data_complete' => true,
            'documents_complete' => false,
            'notes' => 'Assessment supplier belum tersedia & dokumen belum lengkap.',
        ]);

        // ------------------------------------------------------------------
        // Folder manual demo (idempotent)
        // ------------------------------------------------------------------
        $folderLegal = DocumentFolder::updateOrCreate(
            ['name' => 'Legal 2026', 'parent_id' => null],
            ['created_by' => $admin->id]
        );
        DocumentFolder::updateOrCreate(
            ['name' => 'Kontrak Vendor', 'parent_id' => $folderLegal->id],
            ['created_by' => $admin->id]
        );
        DocumentFolder::updateOrCreate(
            ['name' => 'Perizinan', 'parent_id' => $folderLegal->id],
            ['created_by' => $admin->id]
        );

        // ------------------------------------------------------------------
        // Data demo pengajuan — HANYA jika belum ada pengajuan sama sekali,
        // agar seeder tidak menduplikasi demo saat dijalankan ulang.
        // ------------------------------------------------------------------
        if (Application::exists()) {
            $this->command?->info('Data pengajuan sudah ada — demo pengajuan dilewati (seeder idempotent).');
            $this->command?->info('Master data (user/divisi/izin/supplier/folder) sudah dipastikan lengkap.');

            return;
        }

        $this->seedApplication(
            requester: $requester,
            departmentId: $divisiA->id,
            type: ApplicationType::PERMIT,
            permitTypeId: $izinNIB->id,
            title: 'Permohonan NIB Kantor Cabang Bandung',
            status: ApplicationStatus::SUBMITTED,
            fields: [
                'nama_pemohon' => 'Andi Requester', 'kontak' => '0812-3456-7890',
                'tujuan' => 'Pembukaan kantor cabang di Bandung',
                'lokasi' => 'Jl. Asia Afrika No. 20, Bandung',
                'target_selesai' => now()->addMonth()->format('Y-m-d'),
                'catatan_legal' => 'Mohon diproses sebelum akhir bulan.',
            ],
        );

        $this->seedApplication(
            requester: $requester,
            departmentId: $divisiA->id,
            type: ApplicationType::PERMIT,
            permitTypeId: $izinUklUpl->id,
            title: 'Permohonan UKL-UPL Pabrik Bekasi',
            status: ApplicationStatus::REVISION_REQUESTED,
            fields: [
                'nama_pemohon' => 'Andi Requester', 'kontak' => '0812-3456-7890',
                'tujuan' => 'Kepatuhan lingkungan pabrik baru',
                'lokasi' => 'Kawasan Industri MM2100 Bekasi',
            ],
            reviewer: $legal,
            revisionNotes: 'Formulir UKL-UPL belum mencantumkan batas emisi. Mohon lengkapi dokumen lingkungan.',
        );

        $approved = $this->seedApplication(
            requester: $purchaser,
            departmentId: $purchasing->id,
            type: ApplicationType::AGREEMENT,
            title: 'Agreement Pengadaan Bahan Baku 2026',
            status: ApplicationStatus::APPROVED,
            fields: [
                'jenis_agreement' => 'Pengadaan', 'jenis_pihak' => 'Vendor',
                'nama_counterparty' => 'PT Sumber Makmur Jaya',
                'nilai_kontrak' => '2.500.000.000', 'mata_uang' => 'IDR',
                'tanggal_mulai' => '2026-01-01', 'tanggal_selesai' => now()->addWeeks(3)->format('Y-m-d'),
                'pihak_pertama' => 'PT Trijaya Solution',
                'ruang_lingkup' => 'Pengadaan bahan baku produksi tahunan.',
            ],
            supplierId: $supplierLolos->id,
            reviewer: $legal,
        );

        $this->seedApplication(
            requester: $purchaser,
            departmentId: $purchasing->id,
            type: ApplicationType::AGREEMENT,
            title: 'Agreement Jasa Maintenance AC',
            status: ApplicationStatus::DRAFT,
            fields: [
                'jenis_agreement' => 'Pengadaan', 'jenis_pihak' => 'Vendor',
                'nama_counterparty' => 'PT Sumber Makmur Jaya',
                'nilai_kontrak' => '150.000.000', 'mata_uang' => 'IDR',
                'tanggal_mulai' => '2026-09-01', 'tanggal_selesai' => '2027-08-31',
                'ruang_lingkup' => 'Maintenance rutin AC seluruh kantor pusat.',
            ],
            supplierId: $supplierLolos->id,
        );

        // Izin terbit yang sudah KADALUARSA (demo fitur auto-expired)
        $this->seedApplication(
            requester: $requester,
            departmentId: $divisiA->id,
            type: ApplicationType::PERMIT,
            permitTypeId: $izinTDG->id,
            title: 'TDG Gudang Cikarang ( Lama)',
            status: ApplicationStatus::APPROVED,
            fields: [
                'nama_pemohon' => 'Andi Requester', 'kontak' => '0812-3456-7890',
                'tujuan' => 'Penyimpanan barang distribusi area Timur',
                'lokasi' => 'Cikarang, Bekasi',
            ],
            reviewer: $legal,
            validUntil: now()->subMonth()->format('Y-m-d'),
        );

        // ------------------------------------------------------------------
        // Contoh permintaan akses dokumen
        // ------------------------------------------------------------------
        if ($approved) {
            DocumentAccessRequest::create([
                'application_id' => $approved->id,
                'requested_by' => $otherUser->id,
                'reason' => 'Dokumen diperlukan untuk proses review kontrak dengan supplier yang sama.',
                'status' => 'ACCESS_REQUESTED',
            ]);
        }

        $this->command?->info('Seed LegalFlow selesai. Login: admin@legalflow.test / legal@legalflow.test / user@legalflow.test (password: password)');
    }

    private function seedApplication(
        User $requester,
        int $departmentId,
        ApplicationType $type,
        string $title,
        ApplicationStatus $status,
        array $fields,
        ?int $permitTypeId = null,
        ?int $supplierId = null,
        ?User $reviewer = null,
        ?string $revisionNotes = null,
        ?string $validUntil = null,
    ): Application {
        $application = Application::create([
            'application_number' => 'TEMP',
            'application_type' => $type->value,
            'user_id' => $requester->id,
            'department_id' => $departmentId,
            'permit_type_id' => $permitTypeId,
            'supplier_id' => $supplierId,
            'title' => $title,
            'description' => 'Contoh pengajuan hasil seed data.',
            'status' => $status->value,
            'submitted_at' => $status === ApplicationStatus::DRAFT ? null : now()->subDays(2),
            'approved_at' => $status === ApplicationStatus::APPROVED ? now()->subDay() : null,
            'rejected_at' => $status === ApplicationStatus::REJECTED ? now()->subDay() : null,
            'valid_until' => $validUntil ?? ($type === ApplicationType::AGREEMENT && $status === ApplicationStatus::APPROVED
                ? ($fields['tanggal_selesai'] ?? null)
                : null),
        ]);

        $application->update([
            'application_number' => Application::generateNumber($application->id, $type),
        ]);

        foreach ($fields as $name => $value) {
            ApplicationField::create([
                'application_id' => $application->id,
                'field_name' => $name,
                'field_value' => $value,
            ]);
        }

        ApplicationHistory::create([
            'application_id' => $application->id,
            'user_id' => $requester->id,
            'action' => 'Membuat draft pengajuan',
            'new_status' => ApplicationStatus::DRAFT->value,
            'created_at' => now()->subDays(3),
        ]);

        if ($status !== ApplicationStatus::DRAFT) {
            ApplicationHistory::create([
                'application_id' => $application->id,
                'user_id' => $requester->id,
                'action' => 'Pengajuan diajukan ke Legal',
                'old_status' => ApplicationStatus::DRAFT->value,
                'new_status' => ApplicationStatus::SUBMITTED->value,
                'created_at' => now()->subDays(2),
            ]);
        }

        if ($status === ApplicationStatus::REVISION_REQUESTED && $reviewer) {
            ApplicationHistory::create([
                'application_id' => $application->id,
                'user_id' => $reviewer->id,
                'action' => 'Legal/Admin mulai review',
                'old_status' => ApplicationStatus::SUBMITTED->value,
                'new_status' => ApplicationStatus::UNDER_REVIEW->value,
                'created_at' => now()->subDays(1),
            ]);
            ApplicationHistory::create([
                'application_id' => $application->id,
                'user_id' => $reviewer->id,
                'action' => 'Legal/Admin meminta revisi',
                'old_status' => ApplicationStatus::UNDER_REVIEW->value,
                'new_status' => ApplicationStatus::REVISION_REQUESTED->value,
                'notes' => $revisionNotes,
                'created_at' => now()->subDays(1)->subHours(2),
            ]);
            ApplicationReview::create([
                'application_id' => $application->id,
                'reviewer_id' => $reviewer->id,
                'action' => ApplicationReview::ACTION_REVISION,
                'status' => ApplicationStatus::REVISION_REQUESTED->value,
                'notes' => $revisionNotes,
                'reviewed_at' => now()->subDays(1)->subHours(2),
            ]);
        }

        if ($status === ApplicationStatus::APPROVED && $reviewer) {
            ApplicationHistory::create([
                'application_id' => $application->id,
                'user_id' => $reviewer->id,
                'action' => 'Legal/Admin mulai review',
                'old_status' => ApplicationStatus::SUBMITTED->value,
                'new_status' => ApplicationStatus::UNDER_REVIEW->value,
                'created_at' => now()->subDays(1)->subHours(6),
            ]);
            ApplicationHistory::create([
                'application_id' => $application->id,
                'user_id' => $reviewer->id,
                'action' => 'Pengajuan disetujui',
                'old_status' => ApplicationStatus::UNDER_REVIEW->value,
                'new_status' => ApplicationStatus::APPROVED->value,
                'notes' => 'Semua kelengkapan terpenuhi. Dokumen diterbitkan.',
                'created_at' => now()->subDay(),
            ]);
            ApplicationReview::create([
                'application_id' => $application->id,
                'reviewer_id' => $reviewer->id,
                'action' => ApplicationReview::ACTION_APPROVE,
                'status' => ApplicationStatus::APPROVED->value,
                'notes' => 'Semua kelengkapan terpenuhi. Dokumen diterbitkan.',
                'reviewed_at' => now()->subDay(),
            ]);
        }

        return $application;
    }
}
