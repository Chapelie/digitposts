<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Password;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Str;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\DB;

class PasswordResetController extends Controller
{
    /**
     * Afficher le formulaire de demande de réinitialisation
     */
    public function showRequestForm()
    {
        return view('auth.forgot-password');
    }

    /**
     * Envoyer le lien de réinitialisation par email ou SMS
     */
    public function sendResetLink(Request $request)
    {
        $request->validate([
            'identifier' => 'required|string',
        ], [
            'identifier.required' => 'Veuillez entrer votre email ou numéro de téléphone.',
        ]);

        $identifier = $request->identifier;

        // Déterminer si c'est un email ou un téléphone
        $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
        
        if ($isEmail) {
            // Recherche par email
            $user = User::where('email', $identifier)->first();
        } else {
            // Recherche par téléphone
            $user = User::where('phone', $identifier)->first();
        }

        if (!$user) {
            return back()->withErrors([
                'identifier' => 'Aucun compte trouvé avec cet email ou numéro de téléphone.'
            ])->withInput();
        }

        // Générer un token de réinitialisation
        $token = Str::random(64);
        
        // Supprimer les anciens tokens
        DB::table('password_reset_tokens')
            ->where('email', $user->email)
            ->delete();

        // Créer un nouveau token
        DB::table('password_reset_tokens')->insert([
            'email' => $user->email,
            'token' => Hash::make($token),
            'created_at' => now()
        ]);

        // Envoyer l'email de réinitialisation
        try {
            Mail::send('emails.password-reset', [
                'user' => $user,
                'token' => $token,
                'resetUrl' => route('password.reset', ['token' => $token, 'email' => $user->email])
            ], function ($message) use ($user) {
                $message->to($user->email)
                        ->subject('Réinitialisation de votre mot de passe - DigitPosts');
            });

            return back()->with('success', 'Un lien de réinitialisation a été envoyé à votre adresse email.');
        } catch (\Exception $e) {
            Log::error('Erreur envoi email réinitialisation: ' . $e->getMessage());
            return back()->withErrors([
                'identifier' => 'Erreur lors de l\'envoi de l\'email. Veuillez réessayer.'
            ])->withInput();
        }
    }

    /**
     * Afficher le formulaire de réinitialisation
     */
    public function showResetForm(Request $request, $token)
    {
        $email = $request->email;
        
        // Vérifier que le token est valide
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $email)
            ->first();

        if (!$passwordReset || !Hash::check($token, $passwordReset->token)) {
            return redirect()->route('password.request')
                ->with('error', 'Ce lien de réinitialisation est invalide ou a expiré.');
        }

        // Vérifier que le token n'a pas expiré (60 minutes)
        if (now()->diffInMinutes($passwordReset->created_at) > 60) {
            DB::table('password_reset_tokens')
                ->where('email', $email)
                ->delete();
            
            return redirect()->route('password.request')
                ->with('error', 'Ce lien de réinitialisation a expiré. Veuillez en demander un nouveau.');
        }

        return view('auth.reset-password', [
            'token' => $token,
            'email' => $email
        ]);
    }

    /**
     * Réinitialiser le mot de passe
     */
    public function reset(Request $request)
    {
        $request->validate([
            'token' => 'required',
            'email' => 'required|email',
            'password' => 'required|min:8|confirmed',
        ], [
            'password.required' => 'Le mot de passe est requis.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        // Vérifier le token
        $passwordReset = DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->first();

        if (!$passwordReset || !Hash::check($request->token, $passwordReset->token)) {
            return back()->withErrors([
                'email' => 'Ce lien de réinitialisation est invalide ou a expiré.'
            ])->withInput();
        }

        // Vérifier l'expiration
        if (now()->diffInMinutes($passwordReset->created_at) > 60) {
            DB::table('password_reset_tokens')
                ->where('email', $request->email)
                ->delete();
            
            return back()->withErrors([
                'email' => 'Ce lien de réinitialisation a expiré. Veuillez en demander un nouveau.'
            ])->withInput();
        }

        // Mettre à jour le mot de passe
        $user = User::where('email', $request->email)->first();
        
        if (!$user) {
            return back()->withErrors([
                'email' => 'Utilisateur non trouvé.'
            ])->withInput();
        }

        $user->password = Hash::make($request->password);
        $user->save();

        // Supprimer le token utilisé
        DB::table('password_reset_tokens')
            ->where('email', $request->email)
            ->delete();

        return redirect()->route('login')
            ->with('success', 'Votre mot de passe a été réinitialisé avec succès. Vous pouvez maintenant vous connecter.');
    }
}
