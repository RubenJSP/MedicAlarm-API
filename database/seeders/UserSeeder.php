<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;

class UserSeeder extends Seeder
{
    /**                               
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $user = User::create([
        	'name' => 'Apu',
            'lastname' => 'Nahasapeemapetilon',
        	'email' => 'admin@med.com',
            'professional_id' => 'MED-001',
            'speciality' => 'General',
            'phone' => '6121457878',
        	'password' => Hash::make('secret')
        ]);
        $user->assignRole('Medic');
        $user = User::create([
        	'name' => 'Homero',
            'lastname' => 'Simpson',
        	'email' => 'homer@mail.com',
            'phone' => '6121457979',
        	'password' => Hash::make('secret')
        ]);
        $user['code'] = strtoupper(substr(hash('sha256',$user['id']),0,5));
        $user->assignRole('Patient');
        $user->save();
    }
}
