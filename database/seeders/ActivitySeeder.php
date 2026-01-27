<?php

namespace Database\Seeders;

use App\Models\Event;
use App\Models\Training;
use App\Models\Feed;
use App\Models\User;
use App\Models\Category;
use Illuminate\Database\Seeder;
use Carbon\Carbon;

class ActivitySeeder extends Seeder
{
    public function run(): void
    {
        $users = User::all();
        $trainingCategories = Category::where('type', 'training')->get();
        $eventCategories = Category::where('type', 'event')->get();

        // Créer des formations
        $trainings = [
            [
                'title' => 'Formation Laravel Avancé',
                'description' => 'Maîtrisez les fonctionnalités avancées de Laravel : queues, jobs, events, caching, et plus encore.',
                'start_date' => Carbon::now()->addDays(30),
                'end_date' => Carbon::now()->addDays(32),
                'location' => 'Paris',
                'place' => 50, // nombre de places
                'amount' => 299,
                'canPaid' => true,
                'link' => 'https://example.com/laravel-formation',
            ],
            [
                'title' => 'Marketing Digital pour Débutants',
                'description' => 'Apprenez les bases du marketing digital : SEO, réseaux sociaux, email marketing.',
                'start_date' => Carbon::now()->addDays(45),
                'end_date' => null, // Coming soon
                'location' => 'Lyon',
                'place' => 30, // nombre de places
                'amount' => 0,
                'canPaid' => false,
                'link' => null,
            ],
            [
                'title' => 'Design UI/UX Moderne',
                'description' => 'Créez des interfaces utilisateur modernes et intuitives avec les meilleures pratiques UX.',
                'start_date' => Carbon::now()->addDays(60),
                'end_date' => Carbon::now()->addDays(62),
                'location' => 'Marseille',
                'place' => 25, // nombre de places
                'amount' => 199,
                'canPaid' => true,
                'link' => 'https://example.com/design-formation',
            ],
        ];

        foreach ($trainings as $trainingData) {
            $training = Training::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'title' => $trainingData['title'],
                'description' => $trainingData['description'],
                'start_date' => $trainingData['start_date'],
                'end_date' => $trainingData['end_date'],
                'location' => $trainingData['location'],
                'place' => $trainingData['place'],
                'amount' => $trainingData['amount'],
                'canPaid' => $trainingData['canPaid'],
                'link' => $trainingData['link'],
            ]);

            // Créer le feed associé via la relation polymorphique
            $feed = new Feed([
                'id' => \Illuminate\Support\Str::uuid(),
                'isPrivate' => false,
                'status' => 'publiée',
                'user_id' => $users->random()->id,
            ]);

            $training->feed()->save($feed);

            // Attacher des catégories
            $selectedCategories = $trainingCategories->random(rand(1, 2));
            foreach ($selectedCategories as $category) {
                $training->categories()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'name' => $category->name,
                    'type' => $category->type,
                ]);
            }
        }

        // Créer des événements
        $events = [
            [
                'title' => 'Conférence Tech 2024',
                'description' => 'La plus grande conférence tech de l\'année avec des experts internationaux.',
                'start_date' => Carbon::now()->addDays(15)->setTime(9, 0),
                'amount' => 50,
            ],
            [
                'title' => 'Workshop React Native',
                'description' => 'Apprenez à créer des applications mobiles avec React Native en une journée.',
                'start_date' => Carbon::now()->addDays(25)->setTime(14, 0),
                'amount' => 0,
            ],
            [
                'title' => 'Meetup Développeurs',
                'description' => 'Rencontrez d\'autres développeurs et partagez vos expériences.',
                'start_date' => Carbon::now()->addDays(10)->setTime(19, 0),
                'amount' => 0,
            ],
        ];

        foreach ($events as $eventData) {
            $event = Event::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'title' => $eventData['title'],
                'description' => $eventData['description'],
                'start_date' => $eventData['start_date'],
                'amount' => $eventData['amount'],
            ]);

            // Créer le feed associé via la relation polymorphique
            $feed = new Feed([
                'id' => \Illuminate\Support\Str::uuid(),
                'isPrivate' => false,
                'status' => 'publiée',
                'user_id' => $users->random()->id,
            ]);

            $event->feed()->save($feed);

            // Attacher des catégories
            $selectedCategories = $eventCategories->random(rand(1, 2));
            foreach ($selectedCategories as $category) {
                $event->categories()->create([
                    'id' => \Illuminate\Support\Str::uuid(),
                    'name' => $category->name,
                    'type' => $category->type,
                ]);
            }
        }
    }
}
