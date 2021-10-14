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
        	'name' => 'Dr',
            'lastname' => 'Nick',
        	'email' => 'nick@med.com',
            'professional_id' => 'MED-002',
            'speciality' => 'General',
            'phone' => '6151457878',
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
        $user['code'] = strtoupper(substr($user['name'],0,3).substr($user['lastname'],0,2)."-".substr(hash('sha256',$user['id']),0,5));
        $user->assignRole('Patient');
        $user->save();
        $user = User::create([
        	'name' => 'Ruben',
            'lastname' => 'Sandoval',
        	'email' => 'ruben@mail.com',
            'phone' => '6121457970',
        	'password' => Hash::make('secret')
        ]);
        $user['code'] = strtoupper(substr($user['name'],0,3).substr($user['lastname'],0,2)."-".substr(hash('sha256',$user['id']),0,5));
        $user->assignRole('Patient');
        $user->save();
        $user = User::create([
        	'name' => 'Darien',
            'lastname' => 'Ramírez',
        	'email' => 'darien@mail.com',
            'phone' => '6121457940',
        	'password' => Hash::make('secret')
        ]);
        $user['code'] = strtoupper(substr($user['name'],0,3).substr($user['lastname'],0,2)."-".substr(hash('sha256',$user['id']),0,5));
        $user->assignRole('Patient');
        $user->save();
        $user = User::create([
        	'name' => 'Ignacio',
            'lastname' => 'Iglesias',
        	'email' => 'ignacio@mail.com',
            'phone' => '6121457440',
        	'password' => Hash::make('secret')
        ]);
        $user['code'] = strtoupper(substr($user['name'],0,3).substr($user['lastname'],0,2)."-".substr(hash('sha256',$user['id']),0,5));
        $user->assignRole('Patient');
        $user->save();
    }
}
