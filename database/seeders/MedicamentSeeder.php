<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Medicament;
class MedicamentSeeder extends Seeder
{
    /**
     * Run the database seeds.
     *
     * @return void
     */
    public function run()
    {
        $drugs = json_decode(file_get_contents("database/json/meds.json"),true);
        foreach ($drugs as $med) {
           Medicament::create([
               'name' => $med['name'],
               'via' => $med['via'],
           ]);
        }
    }
}
