<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Description plateforme
    |--------------------------------------------------------------------------
    */
    'description' => 'DigitPosts est une plateforme numérique spécialisée dans la diffusion d\'informations relatives aux formations et événements au Burkina Faso. Elle s\'adresse aux professionnels, étudiants et organisations désireux de valoriser ou d\'accéder à des opportunités de développement.',

    'description_short' => 'Plateforme de formations et d\'événements au Burkina Faso.',

    /*
    |--------------------------------------------------------------------------
    | Zones (villes) pour le filtre et le formulaire
    |--------------------------------------------------------------------------
    */
    'zones' => [
        ['id' => 'ouagadougou', 'name' => 'Ouagadougou', 'region' => 'Centre'],
        ['id' => 'bobo-dioulasso', 'name' => 'Bobo-Dioulasso', 'region' => 'Hauts-Bassins'],
        ['id' => 'koudougou', 'name' => 'Koudougou', 'region' => 'Centre-Ouest'],
        ['id' => 'ouahigouya', 'name' => 'Ouahigouya', 'region' => 'Nord'],
        ['id' => 'fada-ngourma', 'name' => 'Fada N\'Gourma', 'region' => 'Est'],
        ['id' => 'tenkodogo', 'name' => 'Tenkodogo', 'region' => 'Centre-Est'],
        ['id' => 'banfora', 'name' => 'Banfora', 'region' => 'Cascades'],
        ['id' => 'kaya', 'name' => 'Kaya', 'region' => 'Centre-Nord'],
        ['id' => 'dedougou', 'name' => 'Dédougou', 'region' => 'Boucle du Mouhoun'],
        ['id' => 'gaoua', 'name' => 'Gaoua', 'region' => 'Sud-Ouest'],
        ['id' => 'manga', 'name' => 'Manga', 'region' => 'Centre-Sud'],
        ['id' => 'ziniare', 'name' => 'Ziniaré', 'region' => 'Plateau-Central'],
        ['id' => 'kombissiri', 'name' => 'Kombissiri', 'region' => 'Centre-Sud'],
        ['id' => 'po', 'name' => 'Pô', 'region' => 'Sud'],
        ['id' => 'nouna', 'name' => 'Nouna', 'region' => 'Nord-Ouest'],
    ],

    /*
    |--------------------------------------------------------------------------
    | Tarifs de diffusion (visibilité sur la plateforme)
    | 1 semaine : 2 000 FCFA | 2 semaines : 3 500 FCFA | 1 mois : 5 000 FCFA
    |--------------------------------------------------------------------------
    */
    'tarifs_diffusion' => [
        ['label' => '1 semaine', 'weeks' => 1, 'amount' => 2000],
        ['label' => '2 semaines', 'weeks' => 2, 'amount' => 3500],
        ['label' => '1 mois', 'weeks' => 4, 'amount' => 5000],
    ],

    /*
    |--------------------------------------------------------------------------
    | Afficher le nombre d'utilisateurs (admin / public)
    |--------------------------------------------------------------------------
    */
    'show_users_count' => false,

];
