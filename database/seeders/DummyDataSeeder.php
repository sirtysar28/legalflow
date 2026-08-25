<?php

namespace Database\Seeders;

use App\Enums\ApplicationStatus;
use App\Enums\ApplicationType;
use App\Enums\AccessStatus;
use App\Models\Application;
use App\Models\ApplicationField;
use App\Models\ApplicationHistory;
use App\Models\ApplicationReview;
use App\Models\Department;
use App\Models\Document;
use App\Models\DocumentAccessRequest;
use App\Models\DocumentRequirement;
use App\Models\PermitType;
use App\Models\Role;
use App\Models\Supplier;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Str;

class DummyDataSeeder extends Seeder
{
    /**
     * Seed data dummy besar untuk testing search & pagination.
     * Jalankan: php artisan db:seed --class=DummyDataSeeder
     */
    public function run(): void
    {
        // ============================================================
        // 1. DIVISI (15 divisi)
        // ============================================================
        $deptData = [
            ['name' => 'Human Resources',       'code' => 'HRD'],
            ['name' => 'Finance & Accounting',  'code' => 'FNA'],
            ['name' => 'Marketing',             'code' => 'MKT'],
            ['name' => 'Research & Development', 'code' => 'RND'],
            ['name' => 'Production',            'code' => 'PRD'],
            ['name' => 'Quality Assurance',     'code' => 'QAS'],
            ['name' => 'Supply Chain',          'code' => 'SCH'],
            ['name' => 'Information Technology', 'code' => 'ITS'],
            ['name' => 'General Affairs',       'code' => 'GAF'],
            ['name' => 'Export Import',         'code' => 'EIM'],
            ['name' => 'Health Safety Env',     'code' => 'HSE'],
            ['name' => 'Corporate Planning',    'code' => 'CPL'],
            ['name' => 'Internal Audit',        'code' => 'IAD'],
            ['name' => 'Customer Service',      'code' => 'CSM'],
            ['name' => 'Legal & Compliance',     'code' => 'LGL'],
        ];
        $departments = collect();
        foreach ($deptData as $d) {
            $departments->push(Department::firstOrCreate(['code' => $d['code']], $d));
        }

        // ============================================================
        // 2. ROLES (pastikan ada)
        // ============================================================
        $roleUser  = Role::firstOrCreate(['name' => 'user'],  ['label' => 'User / Requester']);
        $roleLegal = Role::firstOrCreate(['name' => 'legal'], ['label' => 'Legal']);
        $roleAdmin = Role::firstOrCreate(['name' => 'admin'], ['label' => 'Admin']);

        // ============================================================
        // 3. USERS (30 user)
        // ============================================================
        $firstNames  = ['Ahmad','Budi','Citra','Dewi','Eko','Fajar','Gita','Hadi','Indah','Joko','Kartika','Lina','Mega','Nurul','Oscar','Prasetyo','Qori','Ratna','Sari','Tono','Umar','Vina','Wati','Xander','Yudi','Zahra','Andi','Bunga','Cahya','Dian'];
        $lastNames   = ['Pratama','Sari','Wijaya','Santoso','Kusuma','Hidayat','Putri','Rahayu','Permana','Suryadi','Lestari','Nugroho','Anggraeni','Setiawan','Utami','Ramadhani','Susanto','Wibowo','Handayani','Prabowo'];
        $domains     = ['legalflow.test','isuzu.co.id','trijayasolution.com','gmail.com'];

        $users = collect();
        for ($i = 0; $i < 30; $i++) {
            $fn = $firstNames[$i];
            $ln = $lastNames[$i % 20];
            $domain = $domains[$i % 4];
            $slug = Str::lower(Str::slug("{$fn} {$ln}"));
            $email = ($i < 3) ? "dummy{$i}@{$domain}" : "{$slug}@{$domain}";

            $role = match (true) {
                $i < 2  => $roleAdmin,
                $i < 5  => $roleLegal,
                default => $roleUser,
            };

            $dept = $departments[$i % $departments->count()];

            $users->push(User::firstOrCreate(
                ['email' => $email],
                [
                    'name'         => "{$fn} {$ln}",
                    'password'     => Hash::make('password'),
                    'role_id'      => $role->id,
                    'department_id' => $dept->id,
                    'status'       => $i % 7 === 0 ? 'inactive' : 'active',
                    'email_verified_at' => now(),
                ]
            ));
        }

        // ============================================================
        // 4. JENIS IZIN (10 tambahan)
        // ============================================================
        $permitData = [
            ['name' => 'SIUP (Surat Izin Usaha Perdagangan)',     'category' => 'USAHA',      'description' => 'Izin usaha perdagangan umum untuk kegiatan komersial.'],
            ['name' => 'IMB Lama (Izin Mendirikan Bangunan)',     'category' => 'BANGUNAN',   'description' => 'Izin mendirikan bangunan (regulasi lama).'],
            ['name' => 'Sertifikasi ISO 9001',                    'category' => 'OPERASIONAL', 'description' => 'Sertifikasi sistem manajemen mutu internasional.'],
            ['name' => 'AMDAL (Analisis Dampak Lingkungan)',      'category' => 'LINGKUNGAN', 'description' => 'Kajian lingkungan untuk usaha berdampak besar.'],
            ['name' => 'Izin Operasional Pabrik',                 'category' => 'OPERASIONAL', 'description' => 'Izin operasional kegiatan produksi pabrik.'],
            ['name' => 'Sertifikasi BPOM',                        'category' => 'PRODUK',     'description' => 'Izin edar produk makanan dan minuman dari BPOM.'],
            ['name' => 'Izin Gangguan (HO)',                      'category' => 'LINGKUNGAN', 'description' => 'Izin usaha yang dapat menimbulkan gangguan lingkungan.'],
            ['name' => 'SNI (Standar Nasional Indonesia)',        'category' => 'PRODUK',     'description' => 'Sertifikasi produk sesuai standar nasional.'],
            ['name' => 'Izin Lokasi',                             'category' => 'USAHA',      'description' => 'Izin penggunaan lokasi untuk kegiatan usaha.'],
            ['name' => 'Sertifikat TKDN (Tingkat Komponen Dalam Negeri)', 'category' => 'OPERASIONAL', 'description' => 'Sertifikasi kandungan komponen lokal dalam produk.'],
        ];
        $permitTypes = collect();
        foreach ($permitData as $p) {
            $permitTypes->push(PermitType::firstOrCreate(['name' => $p['name']], $p + ['is_active' => true]));
        }

        // ============================================================
        // 5. PERSYARATAN DOKUMEN (15 tambahan)
        // ============================================================
        $reqData = [
            ['application_type' => 'PERMIT', 'permit_type_id' => null, 'document_name' => 'Akta Perusahaan',           'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => null, 'document_name' => 'Surat Keterangan Domisili',  'is_required' => false],
            ['application_type' => 'PERMIT', 'permit_type_id' => null, 'document_name' => 'NPWP Perusahaan',            'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => null, 'document_name' => 'Foto Lokasi',                'is_required' => false],
            ['application_type' => 'PERMIT', 'permit_type_id' => null, 'document_name' => 'Rencana Anggaran Biaya',    'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => null, 'document_name' => 'Izin Lingkungan Terdahulu',  'is_required' => false],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'NDA (Non-Disclosure Agreement)', 'is_required' => false],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'MoU (Memorandum of Understanding)', 'is_required' => false],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'Rincian Harga (BoQ)',   'is_required' => true],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'Jadwal Pelaksanaan',    'is_required' => true],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'Bukti Asuransi',         'is_required' => false],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'Laporan Performa Vendor', 'is_required' => false],
            ['application_type' => 'PERMIT', 'permit_type_id' => null, 'document_name' => 'Surat Pernyataan',          'is_required' => true],
            ['application_type' => 'PERMIT', 'permit_type_id' => null, 'document_name' => 'Sertifikat Tanah',           'is_required' => true],
            ['application_type' => 'AGREEMENT', 'permit_type_id' => null, 'document_name' => 'Terms & Conditions',      'is_required' => true],
        ];
        foreach ($reqData as $req) {
            DocumentRequirement::firstOrCreate(
                ['application_type' => $req['application_type'], 'permit_type_id' => $req['permit_type_id'], 'document_name' => $req['document_name']],
                ['is_required' => $req['is_required'], 'is_active' => true]
            );
        }

        // ============================================================
        // 6. SUPPLIER (20 supplier)
        // ============================================================
        $supplierNames = [
            'PT Mitra Sejahtera Abadi', 'CV Bangun Karya Mandiri', 'PT Indotama Persada',
            'PT Cahaya Logistik Nusantara', 'CV Prima Teknik Utama', 'PT Sentosa Jaya Abadi',
            'PT Global Energi Mandiri', 'CV Harapan Bersama', 'PT Nusantara Makmur Sejahtera',
            'PT Karya Digitama', 'CV Sinar Terang Abadi', 'PT Bumi Resources Indonesia',
            'PT Aspira Jaya Logistik', 'CV Cipta Karya Konstruksi', 'PT Elang Perkasa Engineering',
            'PT Fajar Mutiara Indah', 'CV Graha Buana Cakrawala', 'PT Harmoni Dunia Teknologi',
            'PT Inti Buana Perkasa', 'CV Jaya Selaras Mandiri',
        ];
        $contactNames = ['Siti Aminah','Bambang Setiadi','Rina Wulandari','Hendra Gunawan','Yuliana Dewi','Agus Prasetyo','Lilis Suryani','Dedi Kurniawan','Nita Puspita','Wahyu Hidayat','Tati Sumiati','Rudi Hartono','Mega Silvia','Doni Firmansyah','Ani Rahmawati','Bayu Pratama','Cici Fitriani','Eko Saputro','Fitri Handayani','Gilang Ramadhan'];
        $riskLevels = ['LOW','MEDIUM','HIGH'];

        $suppliers = collect();
        foreach ($supplierNames as $i => $name) {
            $suppliers->push(Supplier::firstOrCreate(
                ['name' => $name],
                [
                    'npwp'                 => sprintf('%02d.%03d.%07d.0%s1.000', $i+10, $i*12+1, $i*777+100, $i),
                    'address'              => "Jl. Contoh No. {$i}50, Kota {$supplierNames[$i]}",
                    'phone'                => sprintf('021-%d%d%d%d', $i+1, $i*3, $i*7, $i+10),
                    'email'                => Str::slug($name).'@supplier.co.id',
                    'contact_person'       => $contactNames[$i],
                    'is_registered'        => (bool) ($i % 3 !== 1),
                    'assessment_available' => (bool) ($i % 4 !== 2),
                    'assessment_score'     => ($i % 3 !== 1) ? rand(55, 98) : null,
                    'assessment_date'      => ($i % 3 !== 1) ? now()->subMonths(rand(1, 12)) : null,
                    'risk_level'           => $riskLevels[$i % 3],
                    'data_complete'        => (bool) ($i % 2 === 0),
                    'documents_complete'   => (bool) ($i % 3 === 0),
                    'notes'                => $i % 5 === 0 ? 'Supplier prioritas tinggi untuk pengadaan Q4.' : null,
                ]
            ));
        }

        // ============================================================
        // 7. APPLICATIONS (50 pengajuan)
        // ============================================================
        $permitTitles = [
            'Permohonan NIB Kantor Cabang', 'Pengajuan PBG Gudang Baru', 'Permohonan SLF Gedung Kantor',
            'UKL-UPL Pabrik Produksi', 'Sertifikasi Halal Produk Makanan', 'TDG Gudang Penyimpanan',
            'Pengajuan SIUP Perdagangan Umum', 'IMB Renovasi Lobby Utama', 'Sertifikasi ISO 9001 Pabrik',
            'AMDAL Kawasan Industri', 'Izin Operasional Pabrik Cikarang', 'Sertifikasi BPOM Produk Minuman',
            'Izin Gangguan Usaha', 'Sertifikasi SNI Produk', 'Izin Lokasi Kantor Baru',
            'Sertifikasi TKDN Produk Lokal', 'NIB Cabang Surabaya', 'PBG Gudang Distribusi',
            'SLF Tower Perkantoran', 'UKL-UPL Area Pengolahan Limbah',
        ];
        $agreementTitles = [
            'Agreement Pengadaan Bahan Baku', 'Kontrak Jasa Maintenance', 'Agreement Sewa Kendaraan Operasional',
            'Kontrak Pengadaan IT Equipment', 'Agreement Jasa Kebersihan', 'Kontrak Pengadaan ATK Tahunan',
            'Agreement Sewa Gudang Logistik', 'Kontrak Jasa Security', 'Agreement Pengadaan Sparepart',
            'Kontrak Jasa Konsultan Hukum', 'Agreement Pengadaan Furniture', 'Kontrak Jasa Catering',
            'Agreement Sewa Alat Berat', 'Kontrak Pengadaan Uniform', 'Agreement Jasa Pengiriman',
            'Kontrak IT Support', 'Agreement Pengadaan Bahan Kimia', 'Kontrak Jasa Desain Grafis',
            'Agreement Rental Printer', 'Kontrak Pengadaan Listrik Tenaga Surya',
        ];

        $statuses = [
            ApplicationStatus::DRAFT,
            ApplicationStatus::SUBMITTED,
            ApplicationStatus::UNDER_REVIEW,
            ApplicationStatus::REVISION_REQUESTED,
            ApplicationStatus::RESUBMITTED,
            ApplicationStatus::APPROVED,
            ApplicationStatus::REJECTED,
        ];

        $allPermitTypes = PermitType::all();
        $applications = collect();

        for ($i = 0; $i < 50; $i++) {
            $isPermit = $i < 30;
            $type = $isPermit ? ApplicationType::PERMIT : ApplicationType::AGREEMENT;
            $titleList = $isPermit ? $permitTitles : $agreementTitles;
            $baseTitle = $titleList[$i % count($titleList)];
            $title = "{$baseTitle} #" . ($i + 1);

            $statusIdx = $i % count($statuses);
            $status = $statuses[$statusIdx];

            $user = $users[($i + 5) % $users->count()];
            $dept = $departments[($i + 2) % $departments->count()];

            $permitTypeId = $isPermit ? $allPermitTypes->random()->id : null;
            $supplierId = ! $isPermit ? $suppliers->random()->id : null;

            $daysAgo = rand(1, 60);
            $submittedAt = $status === ApplicationStatus::DRAFT ? null : now()->subDays($daysAgo);
            $approvedAt = in_array($status, [ApplicationStatus::APPROVED]) ? now()->subDays(max(1, $daysAgo - 2)) : null;
            $rejectedAt = $status === ApplicationStatus::REJECTED ? now()->subDays(max(1, $daysAgo - 1)) : null;
            $validUntil = ($status === ApplicationStatus::APPROVED && rand(0, 1)) ? now()->addMonths(rand(1, 24)) : null;

            $app = Application::create([
                'application_number' => 'TEMP',
                'application_type'   => $type->value,
                'user_id'           => $user->id,
                'department_id'     => $dept->id,
                'permit_type_id'    => $permitTypeId,
                'supplier_id'       => $supplierId,
                'title'             => $title,
                'description'       => "Deskripsi pengajuan {$title}. Ini adalah data dummy untuk testing search dan pagination.",
                'status'            => $status->value,
                'submitted_at'      => $submittedAt,
                'approved_at'       => $approvedAt,
                'rejected_at'       => $rejectedAt,
                'valid_until'       => $validUntil,
            ]);

            $app->update([
                'application_number' => Application::generateNumber($app->id, $type),
            ]);

            // Fields
            if ($isPermit) {
                ApplicationField::create(['application_id' => $app->id, 'field_name' => 'nama_pemohon', 'field_value' => $user->name]);
                ApplicationField::create(['application_id' => $app->id, 'field_name' => 'kontak', 'field_value' => '0812-'.sprintf('%04d-%04d', rand(1000,9999), rand(1000,9999))]);
                ApplicationField::create(['application_id' => $app->id, 'field_name' => 'lokasi', 'field_value' => "Jl. Contoh No. {$i}, Jakarta" ]);
            } else {
                ApplicationField::create(['application_id' => $app->id, 'field_name' => 'jenis_agreement', 'field_value' => 'Pengadaan']);
                ApplicationField::create(['application_id' => $app->id, 'field_name' => 'nilai_kontrak', 'field_value' => 'Rp '.number_format(rand(50, 5000) * 1000000, 0, ',', '.')]);
                ApplicationField::create(['application_id' => $app->id, 'field_name' => 'tanggal_mulai', 'field_value' => now()->format('Y-m-d')]);
            }

            // Histories
            ApplicationHistory::create([
                'application_id' => $app->id,
                'user_id'        => $user->id,
                'action'         => 'Membuat draft pengajuan',
                'new_status'     => ApplicationStatus::DRAFT->value,
                'created_at'     => now()->subDays($daysAgo + 1),
            ]);

            if ($status !== ApplicationStatus::DRAFT) {
                ApplicationHistory::create([
                    'application_id' => $app->id,
                    'user_id'        => $user->id,
                    'action'         => 'Pengajuan diajukan ke Legal',
                    'old_status'     => ApplicationStatus::DRAFT->value,
                    'new_status'     => ApplicationStatus::SUBMITTED->value,
                    'created_at'     => $submittedAt,
                ]);
            }

            if (in_array($status, [ApplicationStatus::UNDER_REVIEW, ApplicationStatus::REVISION_REQUESTED, ApplicationStatus::APPROVED, ApplicationStatus::REJECTED])) {
                $reviewer = $users[rand(0, 4)]; // admin/legal
                ApplicationHistory::create([
                    'application_id' => $app->id,
                    'user_id'        => $reviewer->id,
                    'action'         => 'Legal/Admin mulai review',
                    'old_status'     => ApplicationStatus::SUBMITTED->value,
                    'new_status'     => ApplicationStatus::UNDER_REVIEW->value,
                    'created_at'     => now()->subDays($daysAgo - 1),
                ]);
            }

            if ($status === ApplicationStatus::REVISION_REQUESTED) {
                $reviewer = $users[rand(0, 4)];
                ApplicationHistory::create([
                    'application_id' => $app->id,
                    'user_id'        => $reviewer->id,
                    'action'         => 'Legal/Admin meminta revisi',
                    'old_status'     => ApplicationStatus::UNDER_REVIEW->value,
                    'new_status'     => ApplicationStatus::REVISION_REQUESTED->value,
                    'notes'          => 'Dokumen kelengkapan belum memenuhi persyaratan. Mohon dilengkapi.',
                    'created_at'     => now()->subDays($daysAgo - 1)->subHours(3),
                ]);
            }

            if ($status === ApplicationStatus::APPROVED) {
                $reviewer = $users[rand(0, 4)];
                ApplicationHistory::create([
                    'application_id' => $app->id,
                    'user_id'        => $reviewer->id,
                    'action'         => 'Pengajuan disetujui',
                    'old_status'     => ApplicationStatus::UNDER_REVIEW->value,
                    'new_status'     => ApplicationStatus::APPROVED->value,
                    'notes'          => 'Semua dokumen lengkap. Izin diterbitkan.',
                    'created_at'     => $approvedAt,
                ]);
            }

            if ($status === ApplicationStatus::REJECTED) {
                $reviewer = $users[rand(0, 4)];
                ApplicationHistory::create([
                    'application_id' => $app->id,
                    'user_id'        => $reviewer->id,
                    'action'         => 'Pengajuan ditolak',
                    'old_status'     => ApplicationStatus::UNDER_REVIEW->value,
                    'new_status'     => ApplicationStatus::REJECTED->value,
                    'notes'          => 'Dokumen tidak memenuhi syarat. Pengajuan ditolak.',
                    'created_at'     => $rejectedAt,
                ]);
            }

            // Dokumen dummy (hanya 1 per application)
            Document::create([
                'application_id' => $app->id,
                'document_type'  => $isPermit ? 'Surat Permohonan' : 'Draft Agreement',
                'file_name'     => ($isPermit ? 'surat_permohonan' : 'draft_agreement') . "_v1.pdf",
                'file_path'     => "documents/{$app->id}/dummy_{$i}.pdf",
                'mime_type'     => 'application/pdf',
                'size'          => rand(100000, 5000000),
                'version'       => 1,
                'status'        => $status === ApplicationStatus::APPROVED ? 'ISSUED' : 'UPLOADED',
                'uploaded_by'   => $user->id,
            ]);

            $applications->push($app);
        }

        // ============================================================
        // 8. ACCESS REQUESTS (25 permintaan akses)
        // ============================================================
        $approvedApps = $applications->filter(fn ($a) => $a->status === ApplicationStatus::APPROVED->value);
        $accessReasons = [
            'Diperlukan untuk review kontrak dengan supplier yang sama.',
            'Dokumen dibutuhkan sebagai referensi untuk pengajuan baru.',
            'Audit internal membutuhkan akses ke dokumen ini.',
            'Keperluan koordinasi antar divisi terkait proyek.',
            'Dokumen pendukung untuk laporan manajemen.',
            'Untuk verifikasi kelengkapan legalitas perizinan.',
            'Referensi cross-check data keuangan dan perjanjian.',
        ];
        $accessStatuses = [
            AccessStatus::REQUESTED, AccessStatus::APPROVED, AccessStatus::REJECTED, AccessStatus::EXPIRED,
        ];

        for ($i = 0; $i < 25; $i++) {
            $app = $approvedApps->get($i % max(1, $approvedApps->count()));
            if (!$app) continue;

            // requester harus beda dengan pemilik application
            $requester = $users->filter(fn ($u) => $u->id !== $app->user_id)->random();

            $accStatus = $accessStatuses[$i % 4];
            $reviewer = $users->random();

            $data = [
                'application_id' => $app->id,
                'requested_by'   => $requester->id,
                'reason'        => $accessReasons[$i % count($accessReasons)],
                'status'        => $accStatus->value,
                'access_type'   => ($accStatus === AccessStatus::APPROVED) ? (rand(0,1) ? 'VIEW' : 'DOWNLOAD') : null,
                'reviewed_by'   => in_array($accStatus, [AccessStatus::APPROVED, AccessStatus::REJECTED]) ? $reviewer->id : null,
                'review_notes'  => in_array($accStatus, [AccessStatus::APPROVED, AccessStatus::REJECTED]) ? 'Telah direview oleh admin.' : null,
                'approved_at'   => in_array($accStatus, [AccessStatus::APPROVED, AccessStatus::REJECTED]) ? now()->subDays(rand(1, 15)) : null,
                'expired_at'    => $accStatus === AccessStatus::APPROVED ? now()->addMonths(rand(1, 6)) : ($accStatus === AccessStatus::EXPIRED ? now()->subDays(5) : null),
                'created_at'    => now()->subDays(rand(1, 30)),
            ];

            DocumentAccessRequest::create($data);
        }

        $this->command?->info('✅ Dummy data selesai di-seed!');
        $this->command?->newLine();
        $this->command?->info("  Divisi:              {$departments->count()}");
        $this->command?->info("  Users:               {$users->count()}");
        $this->command?->info("  Jenis Izin:          {$permitTypes->count()} (tambahan)");
        $this->command?->info("  Persyaratan:         15 (tambahan)");
        $this->command?->info("  Supplier:            {$suppliers->count()}");
        $this->command?->info("  Pengajuan:           {$applications->count()}");
        $this->command?->info("  Access Requests:     25");
        $this->command?->newLine();
        $this->command?->info('  Semua user password: password');
        $this->command?->info('  Login admin: dummy0@legalflow.test');
    }
}
