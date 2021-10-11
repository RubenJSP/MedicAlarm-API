<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Key;
class KeySeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        for ($i=0; $i < 5; $i++) { 
            Key::create([
                'hospital' => 'FakeHospital',
                'key' => strtoupper(substr(hash('sha256',uniqid()),0,15))
            ]);
        }  
    }
}
