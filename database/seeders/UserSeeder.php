<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class UserSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        for ($i = 0; $i < 1000_000; $i++) {
            array_push($data, [
                'name' => 'Test User',
                'email' => 'test'.$i.'@example.com',
                'password' => 'pass',
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo '.';
        }
        echo "\n inserting";

        collect($data)->chunk(10_000)->each(function ($chunk) {
            DB::table('users')->insert($chunk->toArray());
            echo "\n inserted chunk";
        });

        echo "\n inserted";
    }
}
