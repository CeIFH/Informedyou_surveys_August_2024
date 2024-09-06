<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use App\Models\SurveyViewsAnalytics;
use App\Models\Survey;
use App\Models\Folder;
use App\Models\User;
use Carbon\Carbon;
use Faker\Factory as Faker;

class SurveyViewsAnalyticsSeeder extends Seeder
{
    public function run()
    {
        $faker = Faker::create();

        // Get all survey, folder, and user IDs
        $surveyIds = Survey::pluck('id')->toArray();
        $folderIds = Folder::pluck('id')->toArray();
        $userIds = User::pluck('id')->toArray();

        // Generate data for the last 90 days
        $startDate = Carbon::now()->subDays(90);
        $endDate = Carbon::now();

        while ($startDate <= $endDate) {
            foreach ($surveyIds as $surveyId) {
                $viewsCount = $faker->numberBetween(0, 100);

                for ($i = 0; $i < $viewsCount; $i++) {
                    $viewedAt = $startDate->copy()->addMinutes($faker->numberBetween(0, 1439));
                    $responseStartedAt = $faker->boolean(80) ? $viewedAt->copy()->addMinutes($faker->numberBetween(0, 30)) : null;
                    $responseCompletedAt = $responseStartedAt ? $responseStartedAt->copy()->addMinutes($faker->numberBetween(1, 60)) : null;
                    $responseDuration = $responseStartedAt && $responseCompletedAt ? $responseCompletedAt->diffInSeconds($responseStartedAt) : null;

                    SurveyViewsAnalytics::create([
                        'survey_id' => $surveyId,
                        'folder_id' => $faker->randomElement($folderIds),
                        'user_id' => $faker->optional(0.3)->randomElement($userIds),
                        'session_id' => $faker->uuid,
                        'ip_address' => $faker->ipv4,
                        'geolocation' => json_encode([
                            'country' => $faker->country,
                            'city' => $faker->city,
                            'latitude' => $faker->latitude,
                            'longitude' => $faker->longitude,
                        ]),
                        'device_type' => $faker->randomElement(['desktop', 'mobile', 'tablet']),
                        'browser_type' => $faker->randomElement(['Chrome', 'Firefox', 'Safari', 'Edge']),
                        'device_os' => $faker->randomElement(['Windows', 'macOS', 'iOS', 'Android', 'Linux']),
                        'referral_source' => $faker->optional()->url,
                        'viewed_at' => $viewedAt,
                        'response_started_at' => $responseStartedAt,
                        'response_completed_at' => $responseCompletedAt,
                        'response_duration' => $responseDuration,
                        'is_completed' => $responseCompletedAt ? true : false,
                        'is_returning_user' => $faker->boolean(30),
                        'survey_version' => $faker->numberBetween(1, 100),
                        'survey_score' => $faker->optional()->randomFloat(2, 1, 5),
                    ]);
                }
            }

            $startDate->addDay();
        }
    }
}
