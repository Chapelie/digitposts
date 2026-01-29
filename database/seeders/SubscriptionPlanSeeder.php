<?php

namespace Database\Seeders;

use App\Models\SubscriptionPlan;
use Illuminate\Database\Seeder;

class SubscriptionPlanSeeder extends Seeder
{
    public function run(): void
    {
        $plans = [
            [
                'type' => SubscriptionPlan::TYPE_FREE_EVENTS,
                'name' => 'Accès événements gratuits',
                'description' => 'Accès aux événements et formations gratuites pendant la durée de l\'abonnement.',
                'amount' => 2000,
                'duration_weeks' => 3,
                'is_active' => true,
            ],
            [
                'type' => SubscriptionPlan::TYPE_CREATE_ACTIVITIES,
                'name' => 'Création d\'activités',
                'description' => 'Droit de créer et publier des formations et événements sur la plateforme.',
                'amount' => 5000,
                'duration_weeks' => 4,
                'is_active' => true,
            ],
        ];

        foreach ($plans as $data) {
            SubscriptionPlan::updateOrCreate(
                ['type' => $data['type']],
                $data
            );
        }
    }
}
