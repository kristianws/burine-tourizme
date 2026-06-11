<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TourAppSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        // 1. Matikan Foreign Key Check (Mendukung PostgreSQL Supabase & MySQL)
        $driver = DB::getDriverName();
        if ($driver === 'pgsql') {
            DB::statement("SET session_replication_role = 'replica';");
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=0;');
        }
        
        // 2. Kosongkan Data Lama dengan Urutan yang Aman
        DB::table('destinations')->truncate();
        DB::table('bisnis_owners')->truncate();
        DB::table('users')->truncate();
        DB::table('categories')->truncate();

        // 3. SEED DATA: CATEGORIES
        $categories = [
            ['id' => 1, 'name' => 'Budaya & Sejarah'],
            ['id' => 2, 'name' => 'Alam & Taman'],
            ['id' => 3, 'name' => 'Wisata Air'],
            ['id' => 4, 'name' => 'Modern & Edukasi'],
        ];

        foreach ($categories as $category) {
            DB::table('categories')->insert([
                'id' => $category['id'],
                'name' => $category['name'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 4. SEED DATA: USERS (Password default adalah: 'password')
        $passwordHash = '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi';
        $users = [
            ['id' => 1, 'fullname' => 'Super Admin Wisata', 'username' => 'admin_wisata', 'email' => 'admin@soloraya.go.id', 'role' => 'admin'],
            ['id' => 2, 'fullname' => 'Budi Santoso', 'username' => 'budi_bo', 'email' => 'budi@owner.com', 'role' => 'bisnis_owner'],
            ['id' => 3, 'fullname' => 'Siti Rahma', 'username' => 'siti_bo', 'email' => 'siti@owner.com', 'role' => 'bisnis_owner'],
            ['id' => 4, 'fullname' => 'Joko Widodo', 'username' => 'joko_bo', 'email' => 'joko@owner.com', 'role' => 'bisnis_owner'],
            ['id' => 5, 'fullname' => 'Rini Handayani', 'username' => 'rini_bo', 'email' => 'rini@owner.com', 'role' => 'bisnis_owner'],
            ['id' => 6, 'fullname' => 'Agus Wijaya', 'username' => 'agus_bo', 'email' => 'agus@owner.com', 'role' => 'bisnis_owner'],
            ['id' => 7, 'fullname' => 'Michael Smith', 'username' => 'bule_petualang', 'email' => 'michael@tourist.com', 'role' => 'tourist'],
            ['id' => 8, 'fullname' => 'Andi Pratama', 'username' => 'andi_jalan', 'email' => 'andi@tourist.com', 'role' => 'tourist'],
        ];

        foreach ($users as $user) {
            DB::table('users')->insert([
                'id' => $user['id'],
                'fullname' => $user['fullname'],
                'username' => $user['username'],
                'email' => $user['email'],
                'password' => $passwordHash,
                'role' => $user['role'],
                'status' => 'active',
                'email_verified_at' => now(),
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 5. SEED DATA: BISNIS_OWNERS (Relasi Berdasarkan user_id 2 s/d 6)
        $bisnisOwners = [
            ['id' => 1, 'user_id' => 2, 'nik' => '3372011212890001', 'ktp' => 'ktp_budi.jpg', 'nib' => '9120001234561'],
            ['id' => 2, 'user_id' => 3, 'nik' => '3372022305900002', 'ktp' => 'ktp_siti.jpg', 'nib' => '9120001234562'],
            ['id' => 3, 'user_id' => 4, 'nik' => '3372031111850003', 'ktp' => 'ktp_joko.jpg', 'nib' => '9120001234563'],
            ['id' => 4, 'user_id' => 5, 'nik' => '3306124408930004', 'ktp' => 'ktp_rini.jpg', 'nib' => '9120001234564'],
            ['id' => 5, 'user_id' => 6, 'nik' => '3310151207910005', 'ktp' => 'ktp_agus.jpg', 'nib' => '9120001234565'],
        ];

        foreach ($bisnisOwners as $bo) {
            DB::table('bisnis_owners')->insert([
                'id' => $bo['id'],
                'user_id' => $bo['user_id'],
                'nik' => $bo['nik'],
                'ktp_photo' => $bo['ktp'],
                'status' => 'approved',
                'verification_status' => true,
                'verification_at' => now(),
                'verification_notes' => 'Dokumen Terverifikasi Otomatis',
                'nib' => $bo['nib'],
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 6. SEED DATA: DESTINATIONS (Relasi Berdasarkan bisnis_owner_id 1 s/d 5)
        $destinations = [
            [
                'id' => 1, 'bisnis_owner_id' => 1, 'category_id' => 1, 'name' => 'Kraton Surakarta Hadiningrat',
                'gmaps' => 'https://maps.app.goo.gl/kratonsolo', 'location' => 'Surakarta, Solo Raya', 'price' => 15000.00,
                'desc' => 'Istana resmi Kesunanan Surakarta yang kaya akan sejarah dan budaya Jawa.',
                'open' => '09:00:00', 'close' => '14:00:00', 'thumb' => 'kraton.jpg'
            ],
            [
                'id' => 2, 'bisnis_owner_id' => 2, 'category_id' => 1, 'name' => 'Pura Mangkunegaran',
                'gmaps' => 'https://maps.app.goo.gl/mangkunegaran', 'location' => 'Surakarta, Solo Raya', 'price' => 20000.00,
                'desc' => 'Istana kadipaten di Solo yang memiliki arsitektur megah dan koleksi pusaka bersejarah.',
                'open' => '08:30:00', 'close' => '15:00:00', 'thumb' => 'mangkunegaran.jpg'
            ],
            [
                'id' => 3, 'bisnis_owner_id' => 1, 'category_id' => 4, 'name' => 'Solo Safari',
                'gmaps' => 'https://maps.app.goo.gl/solosafari', 'location' => 'Surakarta, Solo Raya', 'price' => 45000.00,
                'desc' => 'Kawasan wisata edukasi satwa modern yang interaktif untuk keluarga.',
                'open' => '08:30:00', 'close' => '16:30:00', 'thumb' => 'solosafari.jpg'
            ],
            [
                'id' => 4, 'bisnis_owner_id' => 3, 'category_id' => 2, 'name' => 'Taman Balekambang',
                'gmaps' => 'https://maps.app.goo.gl/balekambang', 'location' => 'Surakarta, Solo Raya', 'price' => 0.00,
                'desc' => 'Taman kota bersejarah dengan suasana asri dan rusa yang dilepasliarkan.',
                'open' => '07:00:00', 'close' => '17:00:00', 'thumb' => 'balekambang.jpg'
            ],
            [
                'id' => 5, 'bisnis_owner_id' => 4, 'category_id' => 1, 'name' => 'Candi Cetho',
                'gmaps' => 'https://maps.app.goo.gl/candicetho', 'location' => 'Karanganyar, Solo Raya', 'price' => 15000.00,
                'desc' => 'Candi Hindu di lereng Gunung Lawu dengan pemandangan kabut eksotis.',
                'open' => '07:00:00', 'close' => '17:00:00', 'thumb' => 'cetho.jpg'
            ],
            [
                'id' => 6, 'bisnis_owner_id' => 4, 'category_id' => 2, 'name' => 'Grojogan Sewu Tawangmangu',
                'gmaps' => 'https://maps.app.goo.gl/grojogan', 'location' => 'Karanganyar, Solo Raya', 'price' => 22000.00,
                'desc' => 'Air terjun legendaris yang dikelilingi hutan lindung dan kera liar.',
                'open' => '08:00:00', 'close' => '16:00:00', 'thumb' => 'grojogan.jpg'
            ],
            [
                'id' => 7, 'bisnis_owner_id' => 2, 'category_id' => 4, 'name' => 'De Tjolomadoe',
                'gmaps' => 'https://maps.app.goo.gl/tjolomadoe', 'location' => 'Karanganyar, Solo Raya', 'price' => 35000.00,
                'desc' => 'Bekas pabrik gula kolonial yang direvitalisasi menjadi museum modern.',
                'open' => '10:00:00', 'close' => '21:00:00', 'thumb' => 'detjolomadoe.jpg'
            ],
            [
                'id' => 8, 'bisnis_owner_id' => 5, 'category_id' => 3, 'name' => 'Umbul Ponggok',
                'gmaps' => 'https://maps.app.goo.gl/ponggok', 'location' => 'Klaten, Solo Raya', 'price' => 15000.00,
                'desc' => 'Wisata air unik dengan konsep foto bawah air menggunakan properti menarik.',
                'open' => '08:00:00', 'close' => '16:00:00', 'thumb' => 'ponggok.jpg'
            ],
            [
                'id' => 9, 'bisnis_owner_id' => 5, 'category_id' => 3, 'name' => 'Umbul Pelem',
                'gmaps' => 'https://maps.app.goo.gl/pelem', 'location' => 'Klaten, Solo Raya', 'price' => 10000.00,
                'desc' => 'Pemandian mata air alami yang jernih dengan suasana pedesaan rindang.',
                'open' => '07:00:00', 'close' => '17:00:00', 'thumb' => 'pelem.jpg'
            ],
            [
                'id' => 10, 'bisnis_owner_id' => 5, 'category_id' => 1, 'name' => 'Candi Plaosan',
                'gmaps' => 'https://maps.app.goo.gl/plaosan', 'location' => 'Klaten, Solo Raya', 'price' => 10000.00,
                'desc' => 'Candi kembar Buddha yang indah peninggalan Kerajaan Mataram Kuno.',
                'open' => '08:00:00', 'close' => '17:00:00', 'thumb' => 'plaosan.jpg'
            ],
            [
                'id' => 11, 'bisnis_owner_id' => 5, 'category_id' => 2, 'name' => 'New Selo',
                'gmaps' => 'https://maps.app.goo.gl/newselo', 'location' => 'Boyolali, Solo Raya', 'price' => 10000.00,
                'desc' => 'Gardu pandang di lereng Gunung Merapi dengan lanskap pemandangan megah.',
                'open' => '08:00:00', 'close' => '18:00:00', 'thumb' => 'newselo.jpg'
            ],
            [
                'id' => 12, 'bisnis_owner_id' => 5, 'category_id' => 4, 'name' => 'Cepogo Cheese Park',
                'gmaps' => 'https://maps.app.goo.gl/cepogo', 'location' => 'Boyolali, Solo Raya', 'price' => 25000.00,
                'desc' => 'Wisata edukasi keluarga berkonsep peternakan dari Cimory Group.',
                'open' => '08:00:00', 'close' => '17:30:00', 'thumb' => 'cepogo.jpg'
            ],
            [
                'id' => 13, 'bisnis_owner_id' => 3, 'category_id' => 4, 'name' => 'The Heritage Palace',
                'gmaps' => 'https://maps.app.goo.gl/heritage', 'location' => 'Sukoharjo, Solo Raya', 'price' => 30000.00,
                'desc' => 'Wahana wisata bergaya kastil Eropa klasik Eropa dan museum mobil antik.',
                'open' => '09:00:00', 'close' => '16:30:00', 'thumb' => 'heritage.jpg'
            ],
            [
                'id' => 14, 'bisnis_owner_id' => 4, 'category_id' => 1, 'name' => 'Museum Purba Sangiran',
                'gmaps' => 'https://maps.app.goo.gl/sangiran', 'location' => 'Sragen, Solo Raya', 'price' => 15000.00,
                'desc' => 'Situs warisan dunia UNESCO yang menyimpan fosil manusia purba terlengkap.',
                'open' => '08:00:00', 'close' => '16:00:00', 'thumb' => 'sangiran.jpg'
            ],
            [
                'id' => 15, 'bisnis_owner_id' => 4, 'category_id' => 2, 'name' => 'Waduk Gajah Mungkur',
                'gmaps' => 'https://maps.app.goo.gl/gajahmungkur', 'location' => 'Wonogiri, Solo Raya', 'price' => 12000.00,
                'desc' => 'Waduk raksasa dengan fasilitas perahu wisata dan taman bermain.',
                'open' => '08:00:00', 'close' => '17:00:00', 'thumb' => 'gajahmungkur.jpg'
            ],
        ];

        foreach ($destinations as $dest) {
            DB::table('destinations')->insert([
                'id' => $dest['id'],
                'bisnis_owner_id' => $dest['bisnis_owner_id'],
                'category_id' => $dest['category_id'],
                'name' => $dest['name'],
                'gmaps' => $dest['gmaps'],
                'location' => $dest['location'],
                'price' => $dest['price'],
                'description' => $dest['desc'],
                'open_time' => $dest['open'],
                'close_time' => $dest['close'],
                'thumbnail' => $dest['thumb'],
                'status' => 'approved',
                'notes' => 'Data awal aplikasi resmi',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }

        // 7. Nyalakan Kembali Foreign Key Check
        if ($driver === 'pgsql') {
            DB::statement("SET session_replication_role = 'origin';");
        } else {
            DB::statement('SET FOREIGN_KEY_CHECKS=1;');
        }

        $this->command->info('Database Terisi Sempurna Secara Hardcoded Array!');
    }
}