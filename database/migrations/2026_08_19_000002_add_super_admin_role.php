<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

return new class extends Migration
{
    /**
     * Tambah role super_admin + promosi akun admin utama
     * (admin@legalflow.test) menjadi Super Admin.
     */
    public function up(): void
    {
        if (DB::table('roles')->where('name', 'super_admin')->doesntExist()) {
            DB::table('roles')->insert([
                'name' => 'super_admin',
                'label' => 'Super Admin',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        $superAdmin = DB::table('roles')->where('name', 'super_admin')->first();

        // Promosi akun admin utama agar tetap bisa masuk area admin.
        DB::table('users')
            ->where('email', 'admin@legalflow.test')
            ->update(['role_id' => $superAdmin->id]);
    }

    public function down(): void
    {
        // Kembalikan akun admin utama ke role admin bila role dihapus.
        $admin = DB::table('roles')->where('name', 'admin')->first();

        if ($admin) {
            DB::table('users')
                ->where('role_id', DB::table('roles')->where('name', 'super_admin')->value('id'))
                ->update(['role_id' => $admin->id]);
        }

        DB::table('roles')->where('name', 'super_admin')->delete();
    }
};
