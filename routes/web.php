<?php

use App\Http\Controllers\AuthController;
use Illuminate\Support\Facades\Route;
use App\Http\Controllers\FeedController;
use App\Http\Controllers\CreatorController;
use App\Http\Controllers\InscriptionController;
use App\Http\Controllers\AdminController;
use App\Http\Controllers\PaymentController;
use App\Http\Controllers\ReceiptController;
use App\Http\Controllers\UserDashboardController;
use App\Http\Controllers\PasswordResetController;
use App\Http\Controllers\LegalController;
use App\Http\Controllers\SitemapController;
use App\Http\Controllers\SubscriptionController;
use App\Http\Controllers\AdminSubscriptionController;
use App\Http\Controllers\AdminSubscriptionPlanController;

Route::get('/', [FeedController::class, 'index'])->name('home');

// Sitemap
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Pages légales (accessibles à tous)
Route::get('/legal/terms', [LegalController::class, 'terms'])->name('legal.terms');
Route::get('/legal/privacy', [LegalController::class, 'privacy'])->name('legal.privacy');
Route::get('/legal/cookies', [LegalController::class, 'cookies'])->name('legal.cookies');

// Routes d'authentification (accessibles uniquement aux invités)
Route::middleware(['guest'])->group(function () {
    Route::get('/login', [AuthController::class, 'loginForm'])->name('login');
    Route::post('/login', [AuthController::class, 'login'])->middleware('throttle:5,1');
    Route::get('/register', [AuthController::class, 'registerForm'])->name('register');
    Route::post('/register', [AuthController::class, 'register'])->middleware('throttle:5,1');
    
    // OAuth (Google)
    Route::get('/auth/{provider}/redirect', [AuthController::class, 'redirectToProvider'])->name('login.provider');
    Route::get('/auth/{provider}/callback', [AuthController::class, 'handleProviderCallback'])->name('login.provider.callback');
    
    // Récupération de mot de passe
    Route::get('/password/reset', [PasswordResetController::class, 'showRequestForm'])->name('password.request');
    Route::post('/password/email', [PasswordResetController::class, 'sendResetLink'])->name('password.email')->middleware('throttle:3,1');
    Route::get('/password/reset/{token}', [PasswordResetController::class, 'showResetForm'])->name('password.reset');
    Route::post('/password/reset', [PasswordResetController::class, 'reset'])->name('password.update')->middleware('throttle:3,1');
});

// Route d'inscription accessible à tous
Route::get('/inscriptions/{uuid}/create', [InscriptionController::class, 'create'])->name('inscriptions.create');

Route::middleware(['auth'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    // Routes du créateur (pour les créateurs d'activités)
    Route::get('/creator/dashboard', [Creatorcontroller::class,'index'])->name('creator.dashboard');
    Route::get('/creator/campaigns/new', [CreatorController::class,'campaignCreate'])->name('campaigns.create');
    Route::get('/creator/campaigns', [CreatorController::class,'campaignIndex'])->name('campaigns.index');
    Route::post('/creator/campaigns/store', [CreatorController::class,'campaignStore'])->name('campaigns.store');
    Route::get('/creator/campaigns/{uuid}/registrations', [CreatorController::class,'campaignRegistrations'])->name('campaigns.registrations');
    Route::get('/creator/campaigns/{uuid}/registrations/export-pdf', [CreatorController::class,'exportCampaignRegistrationsPdf'])->name('campaigns.registrations.export-pdf');
    Route::get('/creator/settings', [CreatorController::class,'settings'])->name('settings');
    Route::post('/creator/settings', [CreatorController::class,'updateSettings'])->name('settings.update');
    
    // Routes de paiement (rate limit pour scalabilité)
    Route::get('/payments/seamless-checkout/{registrationId}', [PaymentController::class, 'seamlessCheckout'])->name('payments.seamless-checkout');
    Route::get('/payments/after-success', [PaymentController::class, 'afterSuccess'])->name('payments.after-success');
    Route::get('/payments/return/{registrationId}', [PaymentController::class, 'handleReturn'])->name('payments.return');
    Route::post('/payments/initiate', [PaymentController::class, 'initiatePayment'])->name('payments.initiate')->middleware('throttle:20,1');
    Route::post('/payments/check-status', [PaymentController::class, 'checkPaymentStatus'])->name('payments.check-status')->middleware('throttle:30,1');
    
    // Routes d'abonnement (rate limit pour scalabilité)
    Route::get('/subscriptions', [SubscriptionController::class, 'index'])->name('subscriptions.index');
    Route::get('/subscriptions/checkout', [SubscriptionController::class, 'checkout'])->name('subscriptions.checkout');
    Route::post('/subscriptions/initiate-payment', [SubscriptionController::class, 'initiatePayment'])->name('subscriptions.initiate-payment')->middleware('throttle:20,1');
    Route::get('/subscriptions/return/{subscriptionId}', [SubscriptionController::class, 'handleReturn'])->name('subscriptions.return');
    
    
    // Routes du dashboard utilisateur
    Route::get('/dashboard', [UserDashboardController::class, 'dashboard'])->name('user.dashboard');
    Route::get('/profile', [UserDashboardController::class, 'profile'])->name('user.profile');
    Route::get('/dashboard/my-registrations', [UserDashboardController::class, 'myRegistrations'])->name('user.registrations');
    Route::get('/dashboard/my-favorites', [UserDashboardController::class, 'myFavorites'])->name('user.favorites');
    Route::post('/dashboard/toggle-favorite', [UserDashboardController::class, 'toggleFavorite'])->name('user.toggle-favorite');
    Route::post('/dashboard/check-favorite', [UserDashboardController::class, 'checkFavorite'])->name('user.check-favorite');
    Route::post('/dashboard/retry-payment', [UserDashboardController::class, 'retryPayment'])->name('user.retry-payment');
    Route::get('/dashboard/export-registrations', [UserDashboardController::class, 'exportRegistrations'])->name('user.export-registrations');

    // Reçus de paiement (voir, télécharger, envoyer par mail)
    Route::get('/receipts/{registration}/view', [ReceiptController::class, 'show'])->name('receipts.show');
    Route::get('/receipts/{registration}/download', [ReceiptController::class, 'download'])->name('receipts.download');
    Route::post('/receipts/{registration}/send-email', [ReceiptController::class, 'sendEmail'])->name('receipts.send-email');
    
    // Routes du dashboard créateur (alias sous /dashboard/)
    Route::get('/dashboard/campaigns', [CreatorController::class, 'campaignIndex'])->name('dashboard.campaigns');
    Route::get('/dashboard/campaigns/new', [CreatorController::class, 'campaignCreate'])->name('dashboard.campaigns.create');
    Route::get('/dashboard/campaigns/{uuid}/registrations', [CreatorController::class, 'campaignRegistrations'])->name('dashboard.campaigns.registrations');
    Route::get('/dashboard/settings', [CreatorController::class, 'settings'])->name('dashboard.settings');
    Route::get('/dashboard/registrations', [CreatorController::class, 'campaignIndex'])->name('dashboard.registrations'); // Alias pour compatibilité
    
    // Routes d'inscription (rate limit pour scalabilité)
    Route::post('/inscriptions', [InscriptionController::class, 'store'])->name('inscriptions.store')->middleware('throttle:30,1');
    Route::get('/inscriptions', [InscriptionController::class, 'index'])->name('inscriptions.index');
    Route::get('/inscriptions/{registration}', [InscriptionController::class, 'show'])->name('inscriptions.show');
    
    // Routes Admin (protégées par middleware admin)
    Route::prefix('admin')->name('admin.')->middleware('admin')->group(function () {
        Route::get('/', [AdminController::class, 'index'])->name('dashboard');
        Route::get('/users', [AdminController::class, 'users'])->name('users');
        Route::get('/activities', [AdminController::class, 'activities'])->name('activities');
        Route::get('/registrations', [AdminController::class, 'registrations'])->name('registrations');

        // Plans d'abonnement (montant, durée modifiables par l'admin)
        Route::get('/plans', [AdminSubscriptionPlanController::class, 'index'])->name('plans.index');
        Route::get('/plans/{plan}/edit', [AdminSubscriptionPlanController::class, 'edit'])->name('plans.edit');
        Route::put('/plans/{plan}', [AdminSubscriptionPlanController::class, 'update'])->name('plans.update');

        // Abonnements (CRUD)
        Route::get('/subscriptions', [AdminSubscriptionController::class, 'index'])->name('subscriptions.index');
        Route::get('/subscriptions/create', [AdminSubscriptionController::class, 'create'])->name('subscriptions.create');
        Route::post('/subscriptions', [AdminSubscriptionController::class, 'store'])->name('subscriptions.store');
        Route::get('/subscriptions/{subscription}', [AdminSubscriptionController::class, 'show'])->name('subscriptions.show');
        Route::get('/subscriptions/{subscription}/edit', [AdminSubscriptionController::class, 'edit'])->name('subscriptions.edit');
        Route::put('/subscriptions/{subscription}', [AdminSubscriptionController::class, 'update'])->name('subscriptions.update');
        Route::delete('/subscriptions/{subscription}', [AdminSubscriptionController::class, 'destroy'])->name('subscriptions.destroy');
    });
});

// Webhook CinetPay (sans auth) : notifie quand un paiement est traité
Route::post('/payments/notify', [PaymentController::class, 'handleNotification'])->name('payments.notify');
Route::post('/subscriptions/notify', [SubscriptionController::class, 'handleNotification'])->name('subscriptions.notify');

Route::get('/campaigns/{uuid}', [FeedController::class,'show'])->name('campaigns.show');
