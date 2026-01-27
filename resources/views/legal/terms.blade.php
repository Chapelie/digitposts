@extends('layouts.app')

@section('title', 'Conditions d\'utilisation')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-8 md:p-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Conditions d'utilisation</h1>
        <p class="text-sm text-gray-500 mb-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>

        <div class="prose prose-lg max-w-none">
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">1. Acceptation des conditions</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    En accédant et en utilisant la plateforme DigitPosts, vous acceptez d'être lié par les présentes conditions d'utilisation. 
                    Si vous n'acceptez pas ces conditions, veuillez ne pas utiliser notre plateforme.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">2. Description du service</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    DigitPosts est une plateforme en ligne qui permet aux utilisateurs de :
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Découvrir et s'inscrire à des formations et événements professionnels au Burkina Faso</li>
                    <li>Créer et publier des activités (formations et événements)</li>
                    <li>Gérer leurs inscriptions et favoris</li>
                    <li>Effectuer des paiements sécurisés pour les activités payantes</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">3. Inscription et compte utilisateur</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Pour utiliser certaines fonctionnalités de la plateforme, vous devez créer un compte. Vous vous engagez à :
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Fournir des informations exactes, complètes et à jour</li>
                    <li>Maintenir la sécurité de votre mot de passe</li>
                    <li>Être responsable de toutes les activités sous votre compte</li>
                    <li>Notifier immédiatement DigitPosts de toute utilisation non autorisée</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">4. Utilisation de la plateforme</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Vous vous engagez à utiliser la plateforme uniquement à des fins légales et de manière conforme aux présentes conditions. 
                    Il est strictement interdit de :
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Publier du contenu illégal, offensant, diffamatoire ou frauduleux</li>
                    <li>Usurper l'identité d'une autre personne ou entité</li>
                    <li>Transmettre des virus, vers ou autres codes malveillants</li>
                    <li>Tenter d'accéder de manière non autorisée à la plateforme</li>
                    <li>Utiliser la plateforme pour des activités commerciales non autorisées</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">5. Contenu utilisateur</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    En publiant du contenu sur DigitPosts, vous accordez à la plateforme une licence non exclusive, 
                    mondiale et gratuite pour utiliser, reproduire et afficher ce contenu dans le cadre de la fourniture du service.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Vous garantissez que vous détenez tous les droits nécessaires sur le contenu que vous publiez et 
                    que ce contenu ne viole aucun droit de propriété intellectuelle ou autre droit d'un tiers.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">6. Paiements et remboursements</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Les paiements pour les activités payantes sont traités via notre partenaire de paiement sécurisé. 
                    Les conditions de remboursement sont déterminées par l'organisateur de chaque activité et peuvent varier.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    DigitPosts se réserve le droit de modifier les frais de service à tout moment, avec un préavis raisonnable.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">7. Propriété intellectuelle</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Tous les droits de propriété intellectuelle sur la plateforme DigitPosts, y compris mais sans s'y limiter, 
                    le design, le code source, les logos et les marques, sont la propriété exclusive de DigitPosts ou de ses concédants de licence.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">8. Limitation de responsabilité</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    DigitPosts agit en tant que plateforme intermédiaire et n'est pas responsable du contenu publié par les utilisateurs, 
                    de la qualité des activités proposées, ou des transactions entre utilisateurs et organisateurs.
                </p>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Dans la mesure permise par la loi, DigitPosts décline toute responsabilité pour les dommages directs, 
                    indirects, accessoires ou consécutifs résultant de l'utilisation de la plateforme.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">9. Modification des conditions</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    DigitPosts se réserve le droit de modifier ces conditions d'utilisation à tout moment. 
                    Les modifications entreront en vigueur dès leur publication sur la plateforme. 
                    Il est de votre responsabilité de consulter régulièrement ces conditions.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">10. Résiliation</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    DigitPosts se réserve le droit de suspendre ou de résilier votre compte à tout moment, 
                    sans préavis, en cas de violation de ces conditions d'utilisation.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">11. Droit applicable</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Les présentes conditions d'utilisation sont régies par les lois du Burkina Faso. 
                    Tout litige sera soumis à la juridiction exclusive des tribunaux compétents du Burkina Faso.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">12. Contact</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Pour toute question concernant ces conditions d'utilisation, vous pouvez nous contacter à l'adresse suivante : 
                    <a href="mailto:contact@digitposts.com" class="text-blue-600 hover:underline">contact@digitposts.com</a>
                </p>
            </section>
        </div>

        <div class="mt-8 pt-8 border-t">
            <a href="{{ route('home') }}" class="inline-flex items-center text-blue-600 hover:text-blue-700">
                <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                    <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M10 19l-7-7m0 0l7-7m-7 7h18"/>
                </svg>
                Retour à l'accueil
            </a>
        </div>
    </div>
</div>
@endsection
