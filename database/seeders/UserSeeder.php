<?php

namespace Database\Seeders;

use App\Models\User;
use App\Models\UserPermission;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $users = [
            ['id' => 1, 'nama' => 'Budi Santoso', 'username' => 'budi.santoso', 'email' => 'budi.santoso@gmail.com', 'jabatan' => 'administrator', 'status' => 'online'],
            ['id' => 2, 'nama' => 'Siti Rahayu', 'username' => 'siti.rahayu', 'email' => 'siti.rahayu@gmail.com', 'jabatan' => 'administrator', 'status' => 'offline'],
            ['id' => 3, 'nama' => 'Hendra Gunawan', 'username' => 'hendra.gunawan', 'email' => 'hendra.gunawan@gmail.com', 'jabatan' => 'administrator', 'status' => 'online'],
            ['id' => 4, 'nama' => 'Agus Wijaya', 'username' => 'agus.wijaya', 'email' => 'agus.wijaya@gmail.com', 'jabatan' => 'pekerja', 'status' => 'online'],
            ['id' => 5, 'nama' => 'Dewi Lestari', 'username' => 'dewi.lestari', 'email' => 'dewi.lestari@gmail.com', 'jabatan' => 'pekerja', 'status' => 'online'],
            ['id' => 6, 'nama' => 'Fajar Ramadhan', 'username' => 'fajar.ramadhan', 'email' => 'fajar.ramadhan@gmail.com', 'jabatan' => 'pekerja', 'status' => 'offline'],
            ['id' => 7, 'nama' => 'Rina Kartika', 'username' => 'rina.kartika', 'email' => 'rina.kartika@gmail.com', 'jabatan' => 'pekerja', 'status' => 'online'],
            ['id' => 8, 'nama' => 'Putri Ayu', 'username' => 'putri.ayu', 'email' => 'putri.ayu@gmail.com', 'jabatan' => 'pekerja', 'status' => 'offline'],
            ['id' => 9, 'nama' => 'Yusuf Hidayat', 'username' => 'yusuf.hidayat', 'email' => 'yusuf.hidayat@gmail.com', 'jabatan' => 'pekerja', 'status' => 'online'],
            ['id' => 10, 'nama' => 'Maya Anggraini', 'username' => 'maya.anggraini', 'email' => 'maya.anggraini@gmail.com', 'jabatan' => 'pekerja', 'status' => 'offline'],
        ];

        $hashedPassword = Hash::make('password');

        foreach ($users as $userData) {
            User::updateOrCreate(
                ['id' => $userData['id']],
                [
                    'nama' => $userData['nama'],
                    'username' => $userData['username'],
                    'email' => $userData['email'],
                    'password' => $hashedPassword,
                    'jabatan' => $userData['jabatan'],
                    'status' => $userData['status'],
                ]
            );
        }

        $permissions = [
            ['user_id' => 1, 'modul' => 'alat', 'diizinkan' => 1],
            ['user_id' => 1, 'modul' => 'lokasi', 'diizinkan' => 1],
            ['user_id' => 1, 'modul' => 'jadwal', 'diizinkan' => 1],
            ['user_id' => 2, 'modul' => 'alat', 'diizinkan' => 1],
            ['user_id' => 2, 'modul' => 'lokasi', 'diizinkan' => 1],
            ['user_id' => 2, 'modul' => 'jadwal', 'diizinkan' => 1],
            ['user_id' => 3, 'modul' => 'alat', 'diizinkan' => 1],
            ['user_id' => 3, 'modul' => 'lokasi', 'diizinkan' => 1],
            ['user_id' => 3, 'modul' => 'jadwal', 'diizinkan' => 1],
            ['user_id' => 4, 'modul' => 'alat', 'diizinkan' => 1],
            ['user_id' => 4, 'modul' => 'lokasi', 'diizinkan' => 0],
            ['user_id' => 4, 'modul' => 'jadwal', 'diizinkan' => 1],
            ['user_id' => 5, 'modul' => 'alat', 'diizinkan' => 0],
            ['user_id' => 5, 'modul' => 'lokasi', 'diizinkan' => 1],
            ['user_id' => 5, 'modul' => 'jadwal', 'diizinkan' => 1],
            ['user_id' => 6, 'modul' => 'alat', 'diizinkan' => 1],
            ['user_id' => 6, 'modul' => 'lokasi', 'diizinkan' => 0],
            ['user_id' => 6, 'modul' => 'jadwal', 'diizinkan' => 0],
            ['user_id' => 7, 'modul' => 'alat', 'diizinkan' => 0],
            ['user_id' => 7, 'modul' => 'lokasi', 'diizinkan' => 0],
            ['user_id' => 7, 'modul' => 'jadwal', 'diizinkan' => 1],
            ['user_id' => 8, 'modul' => 'alat', 'diizinkan' => 0],
            ['user_id' => 8, 'modul' => 'lokasi', 'diizinkan' => 1],
            ['user_id' => 8, 'modul' => 'jadwal', 'diizinkan' => 0],
            ['user_id' => 9, 'modul' => 'alat', 'diizinkan' => 1],
            ['user_id' => 9, 'modul' => 'lokasi', 'diizinkan' => 0],
            ['user_id' => 9, 'modul' => 'jadwal', 'diizinkan' => 1],
            ['user_id' => 10, 'modul' => 'alat', 'diizinkan' => 0],
            ['user_id' => 10, 'modul' => 'lokasi', 'diizinkan' => 0],
            ['user_id' => 10, 'modul' => 'jadwal', 'diizinkan' => 0],
        ];

        foreach ($permissions as $perm) {
            UserPermission::updateOrCreate(
                ['user_id' => $perm['user_id'], 'modul' => $perm['modul']],
                ['diizinkan' => $perm['diizinkan']]
            );
        }
    }
}
