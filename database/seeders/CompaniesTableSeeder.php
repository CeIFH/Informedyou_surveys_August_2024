<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;

class CompaniesTableSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        Company::create(['name' => 'Company A']);
        Company::create(['name' => 'Company B']);
        Company::create(['name' => 'Company C']);
    }
}
