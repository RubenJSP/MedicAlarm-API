<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Carbon\Carbon;
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
        	'email' => 'apu@medicalarm.com',
            'professional_id' => 'MED-001',
            'speciality' => 'General',
            'phone' => '6121457878',
        	'password' => Hash::make('secret'),
            'email_verified_at' => Carbon::now()
        ]);
        $user->assignRole('Medic');
        $user = User::create([
        	'name' => 'Dr',
            'lastname' => 'Nick',
        	'email' => 'nick@medicalarm.com',
            'professional_id' => 'MED-002',
            'speciality' => 'General',
            'phone' => '6151457878',
        	'password' => Hash::make('secret'),
            'email_verified_at' => Carbon::now()
        ]);
        $user->assignRole('Medic');
        $user = User::create([
        	'name' => 'Homero',
            'lastname' => 'Simpson',
        	'email' => 'homer@medicalarm.com',
            'phone' => '6121457979',
        	'password' => Hash::make('secret'),
            'email_verified_at' => Carbon::now()
        ]);
        $user['code'] = strtoupper(substr($user['name'],0,3).substr($user['lastname'],0,2)."-".substr(hash('sha256',$user['id']),0,5));
        $user->assignRole('Patient');
        $user->save();
        $user = User::create([
        	'name' => 'Ruben',
            'lastname' => 'Sandoval',
        	'email' => 'ruben@medicalarm.com',
            'phone' => '6121457970',
        	'password' => Hash::make('secret'),
            'email_verified_at' => Carbon::now()
        ]);
        $user['code'] = strtoupper(substr($user['name'],0,3).substr($user['lastname'],0,2)."-".substr(hash('sha256',$user['id']),0,5));
        $user->assignRole('Patient');
        $user->save();
    }
}
