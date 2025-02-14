<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SettingSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $data = [];
        for ($i = 0; $i < 1000_000; $i++) {
            array_push($data, [
                'key' => 'test'.$i,
                'value' => 'test'.$i,
                'created_at' => now(),
                'updated_at' => now(),
            ]);
            echo '.';
        }
        echo "\n inserting";
        collect($data)->chunk(10_000)->each(function ($chunk) {
            DB::table('settings')->insert($chunk->toArray());
            echo "\n inserted chunk";
        });
        echo "\n inserted";
    }
}
