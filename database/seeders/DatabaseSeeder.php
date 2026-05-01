<?php

namespace Database\Seeders;

// use Illuminate\Database\Console\Seeds\WithoutModelEvents;

use App\Models\Kategori;
use App\Models\User;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        DB::table('user')->insert([
            [
                'nama' => 'Administrator',
                'email' => 'admin@gmail.com',
                'role' => '1',
                'status' => 1,
                'hp' => '0812345678901',
                'password' => bcrypt('P@55word'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
            [
                'nama' => 'Sopian Aji',
                'email' => 'sopian4ji@gmail.com',
                'role' => '0',
                'status' => 1,
                'hp' => '081234567892',
                'password' => bcrypt('P@55word'),
                'created_at' => now(),
                'updated_at' => now(),
            ],
        ]);
    
    #data kategori 
    Kategori::create([ 
    'nama_kategori' => 'Tshirt', 
    ]); 
    Kategori::create([ 
    'nama_kategori' => 'Hoodie', 
    ]); 
    Kategori::create([ 
    'nama_kategori' => 'Zipper', 
    ]); 
    Kategori::create([ 
    'nama_kategori' => 'Shoes', 
    ]); 
    Kategori::create([ 
    'nama_kategori' => 'Accesories', 
    ]);
    }
}
