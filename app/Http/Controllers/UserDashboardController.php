<?php

namespace App\Http\Controllers;

use App\Models\Feed;
use App\Models\Registration;
use App\Models\Favorite;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class UserDashboardController extends Controller
{

    /**
     * Dashboard principal de l'utilisateur
     */
    public function dashboard()
    {
        $user = Auth::user();
        
        // Statistiques de l'utilisateur
        $stats = $this->getUserStats($user->id);
        
        // Inscriptions récentes
        $recentRegistrations = Registration::where('user_id', $user->id)
            ->with(['feed.feedable'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();
        
        // Favoris récents
        $recentFavorites = Favorite::where('user_id', $user->id)
            ->with(['feed.feedable'])
            ->orderBy('created_at', 'desc')
            ->limit(5)
            ->get();

        return view('dashboard.user-dashboard', compact('stats', 'recentRegistrations', 'recentFavorites'));
    }

    /**
     * Page des inscriptions de l'utilisateur
     */
    public function myRegistrations()
    {
        $user = Auth::user();
        
        $registrations = Registration::where('user_id', $user->id)
            ->with(['feed.feedable'])
            ->orderBy('created_at', 'desc')
            ->paginate(10);

        return view('dashboard.my-registrations', compact('registrations'));
    }

    /**
     * Page des favoris de l'utilisateur
     */
    public function myFavorites()
    {
        $user = Auth::user();
        
        $favorites = Favorite::where('user_id', $user->id)
            ->with(['feed.feedable'])
            ->orderBy('created_at', 'desc')
            ->paginate(12);

        return view('dashboard.my-favorites', compact('favorites'));
    }

    /**
     * Ajouter/Retirer un favori
     */
    public function toggleFavorite(Request $request)
    {
        $request->validate([
            'feed_id' => 'required|exists:feeds,id'
        ]);

        $user = Auth::user();
        $feedId = $request->feed_id;

        $existingFavorite = Favorite::where('user_id', $user->id)
            ->where('feed_id', $feedId)
            ->first();

        if ($existingFavorite) {
            $existingFavorite->delete();
            $message = 'Retiré des favoris';
            $isFavorite = false;
        } else {
            Favorite::create([
                'user_id' => $user->id,
                'feed_id' => $feedId
            ]);
            $message = 'Ajouté aux favoris';
            $isFavorite = true;
        }

        if ($request->ajax()) {
            return response()->json([
                'success' => true,
                'message' => $message,
                'is_favorite' => $isFavorite
            ]);
        }

        return back()->with('success', $message);
    }

    /**
     * Vérifier si un feed est en favori
     */
    public function checkFavorite(Request $request)
    {
        $request->validate([
            'feed_id' => 'required|exists:feeds,id'
        ]);

        $user = Auth::user();
        $feedId = $request->feed_id;

        $isFavorite = Favorite::where('user_id', $user->id)
            ->where('feed_id', $feedId)
            ->exists();

        return response()->json([
            'is_favorite' => $isFavorite
        ]);
    }

    /**
     * Relancer un paiement échoué
     */
    public function retryPayment(Request $request)
    {
        $request->validate([
            'registration_id' => 'required|exists:registrations,id'
        ]);

        $registration = Registration::findOrFail($request->registration_id);
        
        // Vérifier que l'utilisateur connecté est bien celui de l'inscription
        if ($registration->user_id !== Auth::id()) {
            abort(403, 'Accès non autorisé');
        }

        // Vérifier que le paiement peut être relancé
        if (!in_array($registration->payment_status, ['failed', 'cancelled'])) {
            return back()->with('error', 'Ce paiement ne peut pas être relancé.');
        }

        // Rediriger vers la page de paiement Seamless
        return redirect()->route('payments.seamless-checkout', $registration->id)
            ->with('info', 'Relance du paiement...');
    }

    /**
     * Obtenir les statistiques de l'utilisateur (optimisé)
     */
    private function getUserStats($userId)
    {
        // Utiliser une seule requête avec des agrégations
        $stats = Registration::where('user_id', $userId)
            ->selectRaw('
                COUNT(*) as total,
                SUM(CASE WHEN status = "confirmed" THEN 1 ELSE 0 END) as confirmed,
                SUM(CASE WHEN status = "pending" THEN 1 ELSE 0 END) as pending,
                SUM(CASE WHEN payment_status = "paid" THEN amount_paid ELSE 0 END) as total_paid,
                SUM(CASE WHEN payment_status IN ("pending", "failed") THEN amount_paid ELSE 0 END) as pending_payments
            ')
            ->first();

        $totalFavorites = Favorite::where('user_id', $userId)->count();

        $totalRegistrations = $stats->total ?? 0;
        $confirmedRegistrations = $stats->confirmed ?? 0;

        return [
            'total_registrations' => $totalRegistrations,
            'confirmed_registrations' => $confirmedRegistrations,
            'pending_registrations' => $stats->pending ?? 0,
            'total_favorites' => $totalFavorites,
            'total_paid' => $stats->total_paid ?? 0,
            'pending_payments' => $stats->pending_payments ?? 0,
            'success_rate' => $totalRegistrations > 0 ? round(($confirmedRegistrations / $totalRegistrations) * 100, 2) : 0
        ];
    }

    /**
     * Exporter l'historique des inscriptions en PDF
     */
    public function exportRegistrations(Request $request)
    {
        $user = Auth::user();
        
        $registrations = Registration::where('user_id', $user->id)
            ->with(['feed.feedable'])
            ->orderBy('created_at', 'desc')
            ->get();

        // Calculer les statistiques
        $totalAmount = $registrations->where('payment_status', 'paid')->sum('amount_paid');
        $confirmedCount = $registrations->where('status', 'confirmed')->count();
        $paidCount = $registrations->where('payment_status', 'paid')->count();

        $data = [
            'user' => $user,
            'registrations' => $registrations,
            'totalAmount' => $totalAmount,
            'confirmedCount' => $confirmedCount,
            'paidCount' => $paidCount,
            'generatedAt' => now(),
        ];

        $pdf = \PDF::loadView('dashboard.exports.registrations-pdf', $data);
        
        $filename = 'mes_inscriptions_' . now()->format('Y-m-d') . '.pdf';
        
        return $pdf->download($filename);
    }
} 