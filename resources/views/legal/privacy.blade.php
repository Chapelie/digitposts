@extends('layouts.app')

@section('title', 'Politique de confidentialité')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-8 md:p-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Politique de confidentialité</h1>
        <p class="text-sm text-gray-500 mb-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>

        <div class="prose prose-lg max-w-none">
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">1. Introduction</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    DigitPosts ("nous", "notre", "nos") s'engage à protéger votre vie privée. Cette politique de confidentialité 
                    explique comment nous collectons, utilisons, stockons et protégeons vos informations personnelles lorsque vous 
                    utilisez notre plateforme.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">2. Informations que nous collectons</h2>
                <h3 class="text-xl font-semibold text-gray-700 mb-3 mt-4">2.1 Informations que vous nous fournissez</h3>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Informations de compte :</strong> nom, prénom, adresse e-mail, numéro de téléphone</li>
                    <li><strong>Informations de profil :</strong> organisation, bio, site web, localisation</li>
                    <li><strong>Informations de paiement :</strong> traitées par notre partenaire de paiement sécurisé</li>
                    <li><strong>Contenu :</strong> formations et événements que vous créez, commentaires, messages</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-700 mb-3 mt-4">2.2 Informations collectées automatiquement</h3>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Données de navigation :</strong> adresse IP, type de navigateur, pages visitées</li>
                    <li><strong>Cookies et technologies similaires :</strong> voir notre <a href="{{ route('legal.cookies') }}" class="text-blue-600 hover:underline">politique des cookies</a></li>
                    <li><strong>Données d'utilisation :</strong> interactions avec la plateforme, préférences</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">3. Utilisation de vos informations</h2>
                <p class="text-gray-700 leading-relaxed mb-4">Nous utilisons vos informations pour :</p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Fournir, maintenir et améliorer nos services</li>
                    <li>Traiter vos inscriptions et paiements</li>
                    <li>Vous envoyer des notifications importantes concernant votre compte</li>
                    <li>Personnaliser votre expérience sur la plateforme</li>
                    <li>Détecter et prévenir la fraude et les abus</li>
                    <li>Respecter nos obligations légales</li>
                    <li>Analyser l'utilisation de la plateforme pour améliorer nos services</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">4. Partage de vos informations</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Nous ne vendons jamais vos informations personnelles. Nous pouvons partager vos informations uniquement dans les cas suivants :
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Avec les organisateurs :</strong> lorsque vous vous inscrivez à une activité, l'organisateur reçoit vos informations de contact nécessaires</li>
                    <li><strong>Prestataires de services :</strong> nous pouvons partager des informations avec nos prestataires de confiance (hébergement, paiement, analyse)</li>
                    <li><strong>Obligations légales :</strong> si la loi l'exige ou pour protéger nos droits</li>
                    <li><strong>Avec votre consentement :</strong> dans d'autres cas, uniquement avec votre consentement explicite</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">5. Sécurité de vos données</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Nous mettons en œuvre des mesures de sécurité techniques et organisationnelles appropriées pour protéger vos informations 
                    personnelles contre l'accès non autorisé, la perte, la destruction ou l'altération. Cela inclut :
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Chiffrement des données sensibles</li>
                    <li>Accès restreint aux informations personnelles</li>
                    <li>Surveillance régulière de nos systèmes</li>
                    <li>Formation de notre personnel sur la protection des données</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">6. Conservation des données</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Nous conservons vos informations personnelles aussi longtemps que nécessaire pour fournir nos services et respecter 
                    nos obligations légales. Lorsque vous supprimez votre compte, nous supprimons ou anonymisons vos données personnelles, 
                    sauf si la loi nous oblige à les conserver.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">7. Vos droits</h2>
                <p class="text-gray-700 leading-relaxed mb-4">Conformément à la réglementation applicable, vous avez le droit de :</p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Accès :</strong> demander une copie de vos informations personnelles</li>
                    <li><strong>Rectification :</strong> corriger vos informations inexactes ou incomplètes</li>
                    <li><strong>Suppression :</strong> demander la suppression de vos données personnelles</li>
                    <li><strong>Opposition :</strong> vous opposer au traitement de vos données</li>
                    <li><strong>Portabilité :</strong> recevoir vos données dans un format structuré</li>
                    <li><strong>Limitation :</strong> demander la limitation du traitement de vos données</li>
                </ul>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Pour exercer ces droits, contactez-nous à <a href="mailto:privacy@digitposts.com" class="text-blue-600 hover:underline">privacy@digitposts.com</a>
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">8. Cookies</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Nous utilisons des cookies et technologies similaires pour améliorer votre expérience. 
                    Pour plus d'informations, consultez notre <a href="{{ route('legal.cookies') }}" class="text-blue-600 hover:underline">politique des cookies</a>.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">9. Modifications de cette politique</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Nous pouvons modifier cette politique de confidentialité de temps à autre. Nous vous informerons de tout changement 
                    significatif en publiant la nouvelle politique sur cette page et en mettant à jour la date de "dernière mise à jour".
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">10. Contact</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Pour toute question concernant cette politique de confidentialité ou pour exercer vos droits, contactez-nous :
                </p>
                <div class="bg-gray-50 p-4 rounded-lg">
                    <p class="text-gray-700 mb-2"><strong>Email :</strong> <a href="mailto:privacy@digitposts.com" class="text-blue-600 hover:underline">privacy@digitposts.com</a></p>
                    <p class="text-gray-700"><strong>Adresse :</strong> Burkina Faso</p>
                </div>
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
