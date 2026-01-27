@extends('layouts.app')

@section('title', 'Politique des cookies')

@section('content')
<div class="container mx-auto px-4 py-12 max-w-4xl">
    <div class="bg-white rounded-lg shadow-lg p-8 md:p-12">
        <h1 class="text-3xl md:text-4xl font-bold text-gray-900 mb-6">Politique des cookies</h1>
        <p class="text-sm text-gray-500 mb-8">Dernière mise à jour : {{ date('d/m/Y') }}</p>

        <div class="prose prose-lg max-w-none">
            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">1. Qu'est-ce qu'un cookie ?</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Un cookie est un petit fichier texte stocké sur votre appareil (ordinateur, tablette ou smartphone) lorsque vous visitez un site web. 
                    Les cookies permettent au site web de reconnaître votre appareil et de mémoriser certaines informations sur vos préférences ou actions passées.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">2. Comment utilisons-nous les cookies ?</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    DigitPosts utilise des cookies pour améliorer votre expérience sur notre plateforme. Nous utilisons différents types de cookies :
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">3. Types de cookies utilisés</h2>
                
                <h3 class="text-xl font-semibold text-gray-700 mb-3 mt-4">3.1 Cookies essentiels</h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Ces cookies sont nécessaires au fonctionnement de la plateforme. Ils permettent des fonctionnalités de base comme la navigation 
                    sécurisée et l'accès aux zones protégées du site. Sans ces cookies, certains services ne peuvent pas être fournis.
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Cookies de session :</strong> maintiennent votre session active pendant votre visite</li>
                    <li><strong>Cookies de sécurité :</strong> protègent contre les attaques et maintiennent la sécurité</li>
                    <li><strong>Cookies CSRF :</strong> protègent contre les attaques de falsification de requêtes</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-700 mb-3 mt-4">3.2 Cookies de préférences</h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Ces cookies permettent à la plateforme de mémoriser vos choix (comme votre langue préférée) et de fournir des fonctionnalités 
                    améliorées et personnalisées.
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Préférences utilisateur :</strong> langue, région, paramètres d'affichage</li>
                    <li><strong>Filtres sauvegardés :</strong> vos préférences de recherche et filtres</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-700 mb-3 mt-4">3.3 Cookies analytiques</h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Ces cookies nous aident à comprendre comment les visiteurs utilisent notre plateforme en collectant des informations de manière anonyme. 
                    Cela nous permet d'améliorer le fonctionnement de la plateforme.
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Statistiques d'utilisation :</strong> pages visitées, temps passé, parcours de navigation</li>
                    <li><strong>Performance :</strong> temps de chargement, erreurs rencontrées</li>
                </ul>

                <h3 class="text-xl font-semibold text-gray-700 mb-3 mt-4">3.4 Cookies de fonctionnalité</h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Ces cookies permettent à la plateforme de fournir des fonctionnalités améliorées et personnalisées.
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Authentification :</strong> maintiennent votre session de connexion</li>
                    <li><strong>Panier et favoris :</strong> mémorisent vos sélections</li>
                    <li><strong>Notifications :</strong> préférences de notification</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">4. Cookies tiers</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Certains cookies sont placés par des services tiers qui apparaissent sur nos pages. Nous utilisons notamment :
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Service de paiement :</strong> pour le traitement sécurisé des paiements</li>
                    <li><strong>Google Analytics :</strong> pour l'analyse du trafic (si activé)</li>
                    <li><strong>Google OAuth :</strong> pour l'authentification via Google (si utilisée)</li>
                </ul>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Ces services tiers peuvent utiliser leurs propres cookies conformément à leurs propres politiques de confidentialité.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">5. Durée de conservation des cookies</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Les cookies que nous utilisons ont différentes durées de vie :
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><strong>Cookies de session :</strong> supprimés à la fermeture du navigateur</li>
                    <li><strong>Cookies persistants :</strong> restent sur votre appareil pendant une période déterminée (jusqu'à 1 an pour les préférences)</li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">6. Gestion des cookies</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Vous pouvez contrôler et gérer les cookies de plusieurs façons. Veuillez noter que la suppression ou le blocage des cookies 
                    peut affecter votre expérience utilisateur et certaines fonctionnalités peuvent ne plus être disponibles.
                </p>
                
                <h3 class="text-xl font-semibold text-gray-700 mb-3 mt-4">6.1 Paramètres du navigateur</h3>
                <p class="text-gray-700 leading-relaxed mb-4">
                    La plupart des navigateurs vous permettent de :
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li>Voir quels cookies sont stockés et les supprimer individuellement</li>
                    <li>Bloquer les cookies de tiers</li>
                    <li>Bloquer tous les cookies</li>
                    <li>Supprimer tous les cookies lorsque vous fermez le navigateur</li>
                </ul>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Pour plus d'informations sur la gestion des cookies dans votre navigateur, consultez l'aide de votre navigateur :
                </p>
                <ul class="list-disc list-inside text-gray-700 mb-4 space-y-2 ml-4">
                    <li><a href="https://support.google.com/chrome/answer/95647" target="_blank" class="text-blue-600 hover:underline">Google Chrome</a></li>
                    <li><a href="https://support.mozilla.org/fr/kb/activer-desactiver-cookies-preferences" target="_blank" class="text-blue-600 hover:underline">Mozilla Firefox</a></li>
                    <li><a href="https://support.apple.com/fr-fr/guide/safari/sfri11471/mac" target="_blank" class="text-blue-600 hover:underline">Safari</a></li>
                    <li><a href="https://support.microsoft.com/fr-fr/microsoft-edge/supprimer-les-cookies-dans-microsoft-edge-63947406-40ac-c3b8-57b9-2a946a29ae09" target="_blank" class="text-blue-600 hover:underline">Microsoft Edge</a></li>
                </ul>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">7. Cookies et appareils mobiles</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Sur les appareils mobiles, vous pouvez gérer les cookies via les paramètres de votre navigateur mobile. 
                    Les options peuvent varier selon le type d'appareil et le navigateur utilisé.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">8. Modifications de cette politique</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Nous pouvons mettre à jour cette politique des cookies de temps à autre pour refléter les changements dans les cookies que nous utilisons 
                    ou pour d'autres raisons opérationnelles, légales ou réglementaires. Veuillez consulter cette page régulièrement pour rester informé.
                </p>
            </section>

            <section class="mb-8">
                <h2 class="text-2xl font-semibold text-gray-800 mb-4">9. Contact</h2>
                <p class="text-gray-700 leading-relaxed mb-4">
                    Si vous avez des questions concernant notre utilisation des cookies, contactez-nous à :
                    <a href="mailto:privacy@digitposts.com" class="text-blue-600 hover:underline">privacy@digitposts.com</a>
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
