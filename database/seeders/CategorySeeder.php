<?php

namespace Database\Seeders;

use App\Models\Category;
use Illuminate\Database\Seeder;

class CategorySeeder extends Seeder
{
    public function run(): void
    {
        $categories = [
            // Catégories pour les formations
            ['name' => 'Développement Web', 'type' => 'training'],
            ['name' => 'Marketing Digital', 'type' => 'training'],
            ['name' => 'Design Graphique', 'type' => 'training'],
            ['name' => 'Gestion de Projet', 'type' => 'training'],
            ['name' => 'Intelligence Artificielle', 'type' => 'training'],
            
            // Catégories pour les événements
            ['name' => 'Conférence', 'type' => 'event'],
            ['name' => 'Workshop', 'type' => 'event'],
            ['name' => 'Meetup', 'type' => 'event'],
            ['name' => 'Hackathon', 'type' => 'event'],
            ['name' => 'Séminaire', 'type' => 'event'],
        ];

        foreach ($categories as $category) {
            Category::create([
                'id' => \Illuminate\Support\Str::uuid(),
                'name' => $category['name'],
                'type' => $category['type'],
            ]);
        }
    }
}
