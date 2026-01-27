<?php

namespace App\Helpers;

use App\Models\Feed;
use App\Models\Event;
use App\Models\Training;

class SeoHelper
{
    /**
     * Générer les données structurées pour une organisation
     */
    public static function organizationSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'Organization',
            'name' => 'DigitPosts',
            'url' => config('app.url'),
            'logo' => config('app.url') . '/asset/image1_large.jpg',
            'description' => 'Plateforme dédiée à la diffusion d\'information sur les formations et évènements au Burkina Faso.',
            'address' => [
                '@type' => 'PostalAddress',
                'addressCountry' => 'BF',
                'addressLocality' => 'Ouagadougou'
            ],
            'sameAs' => [
                // Ajoutez vos réseaux sociaux ici
            ]
        ];
    }

    /**
     * Générer les données structurées pour un événement
     */
    public static function eventSchema(Feed $feed)
    {
        $feedable = $feed->feedable;
        $startDate = $feedable->start_date ?? now();
        $endDate = $feedable instanceof Training ? ($feedable->end_date ?? $startDate) : $startDate;
        
        $schema = [
            '@context' => 'https://schema.org',
            '@type' => $feedable instanceof Event ? 'Event' : 'Course',
            'name' => $feedable->title,
            'description' => strip_tags($feedable->description ?? ''),
            'url' => route('campaigns.show', $feed->id),
            'image' => $feedable->file ? asset('storage/' . $feedable->file) : null,
            'startDate' => $startDate,
            'organizer' => [
                '@type' => 'Organization',
                'name' => $feed->user->firstname . ' ' . $feed->user->lastname,
            ]
        ];

        if ($feedable instanceof Training && $feedable->end_date) {
            $schema['endDate'] = $feedable->end_date;
        }

        if ($feedable->location ?? $feedable->place ?? null) {
            $schema['location'] = [
                '@type' => 'Place',
                'name' => $feedable->location ?? $feedable->place,
            ];
        }

        if ($feedable->amount && $feedable->amount > 0) {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => $feedable->amount,
                'priceCurrency' => 'XOF',
                'availability' => 'https://schema.org/InStock',
                'url' => route('campaigns.show', $feed->id),
            ];
        } else {
            $schema['offers'] = [
                '@type' => 'Offer',
                'price' => '0',
                'priceCurrency' => 'XOF',
                'availability' => 'https://schema.org/InStock',
                'url' => route('campaigns.show', $feed->id),
            ];
        }

        return $schema;
    }

    /**
     * Générer les données structurées pour une page d'accueil
     */
    public static function websiteSchema()
    {
        return [
            '@context' => 'https://schema.org',
            '@type' => 'WebSite',
            'name' => 'DigitPosts',
            'url' => config('app.url'),
            'description' => 'Plateforme de formations et événements professionnels au Burkina Faso',
            'potentialAction' => [
                '@type' => 'SearchAction',
                'target' => [
                    '@type' => 'EntryPoint',
                    'urlTemplate' => config('app.url') . '/?search={search_term_string}'
                ],
                'query-input' => 'required name=search_term_string'
            ]
        ];
    }

    /**
     * Générer les données structurées pour un breadcrumb
     */
    public static function breadcrumbSchema($items)
    {
        $breadcrumbList = [
            '@context' => 'https://schema.org',
            '@type' => 'BreadcrumbList',
            'itemListElement' => []
        ];

        $position = 1;
        foreach ($items as $item) {
            $breadcrumbList['itemListElement'][] = [
                '@type' => 'ListItem',
                'position' => $position++,
                'name' => $item['name'],
                'item' => $item['url'] ?? null
            ];
        }

        return $breadcrumbList;
    }

    /**
     * Générer les données structurées pour une collection d'événements
     */
    public static function itemListSchema($feeds, $title = 'Formations et Événements')
    {
        $items = [];
        foreach ($feeds as $feed) {
            $items[] = [
                '@type' => $feed->feedable instanceof Event ? 'Event' : 'Course',
                'name' => $feed->feedable->title,
                'url' => route('campaigns.show', $feed->id),
                'image' => $feed->feedable->file ? asset('storage/' . $feed->feedable->file) : null,
            ];
        }

        return [
            '@context' => 'https://schema.org',
            '@type' => 'ItemList',
            'name' => $title,
            'itemListElement' => array_map(function ($item, $index) {
                return [
                    '@type' => 'ListItem',
                    'position' => $index + 1,
                    'item' => $item
                ];
            }, $items, array_keys($items))
        ];
    }
}
