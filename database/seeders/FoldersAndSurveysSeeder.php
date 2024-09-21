<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\Company;
use App\Models\Folder;
use App\Models\Survey;
use App\Models\User;

class FoldersAndSurveysSeeder extends Seeder
{
    public function run()
    {
        // Get all users
        $users = User::all();

        // Get existing companies
        $companies = Company::all();

        // Assign all users to all companies
        foreach ($users as $user) {
            $user->companies()->syncWithoutDetaching($companies->pluck('id'));
        }

        // Create folders and surveys for each company
        foreach ($companies as $company) {
            // Create folders
            $folders = $this->createFoldersForCompany($company, ['Folder 1', 'Folder 2']);

            // Create surveys in folders
            $this->createSurveysForFolders($folders, 2);

            // Create surveys without folders
            $this->createSurveysForCompany($company, 2);
        }

        // Log the created data
        \Log::info('Seeded Companies:', Company::all()->toArray());
        \Log::info('Seeded Folders:', Folder::all()->toArray());
        \Log::info('Seeded Surveys:', Survey::all()->toArray());
        \Log::info('User-Company Associations:', \DB::table('company_user')->get()->toArray());
    }

    private function createFoldersForCompany($company, $folderNames)
    {
        return collect($folderNames)->map(function ($name) use ($company) {
            return Folder::create([
                'name' => $name,
                'company_id' => $company->id,
            ]);
        });
    }

    private function createSurveysForFolders($folders, $count)
    {
        $folders->each(function ($folder) use ($count) {
            for ($i = 1; $i <= $count; $i++) {
                Survey::create([
                    'title' => "Survey {$i} in {$folder->name}",
                    'content' => json_encode(['question' => 'Sample Question']),
                    'company_id' => $folder->company_id,
                    'folder_id' => $folder->id,
                ]);
            }
        });
    }

    private function createSurveysForCompany($company, $count)
    {
        for ($i = 1; $i <= $count; $i++) {
            Survey::create([
                'title' => "Survey {$i} without folder in {$company->name}",
                'content' => json_encode(['question' => 'Sample Question']),
                'company_id' => $company->id,
            ]);
        }
    }
}
