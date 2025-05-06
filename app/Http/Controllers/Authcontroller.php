<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use Exception;

class AuthController extends Controller
{
    public function loginForm()
    {
        try {
            return view('auth.login');
        } catch (Exception $e) {
            Log::error('Login form error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors du chargement du formulaire.');
        }
    }

    public function login(Request $request)
    {
        try {
            $credentials = $request->validate([
                'email' => ['required', 'email'],
                'password' => ['required'],
            ]);

            if (Auth::attempt($credentials, $request->remember)) {
                $request->session()->regenerate();
                return redirect()->intended('dashboard')->with('success', 'Connexion réussie !');
            }

            return back()
                ->withInput($request->only('email', 'remember'))
                ->withErrors([
                    'email' => 'Identifiants incorrects ou compte inexistant.',
                ]);

        } catch (Exception $e) {
            Log::error('Login error for email ' . $request->email . ': ' . $e->getMessage());
            return back()->with('error', 'Une erreur technique est survenue. Veuillez réessayer.');
        }
    }

    public function registerForm()
    {
        try {
            return view('auth.register');
        } catch (Exception $e) {
            Log::error('Register form error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Impossible de charger le formulaire d\'inscription.');
        }
    }

    // Traite l'inscription
    public function register(Request $request)
    {
            $validated = $request->validate([
                'first_name' => ['required', 'string', 'max:255'],
                'last_name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'string', 'email', 'max:255', 'unique:users'],
                'password' => ['required', 'confirmed'],
            ]);
            $user = new User();
            $user->firstname = $validated['first_name'];
            $user->lastname = $validated['last_name'];
            $user->email = $validated['email'];
            $user->password = Hash::make($validated['password']);
            $user->save();
            Auth::login($user);
            return redirect('/')->with('success', 'Inscription réussie ! Bienvenue.');


    }

    // Déconnexion
    public function logout(Request $request)
    {
        try {
            Auth::logout();
            $request->session()->invalidate();
            $request->session()->regenerateToken();
            return redirect('/')->with('success', 'Déconnexion réussie.');
        } catch (Exception $e) {
            Log::error('Logout error: ' . $e->getMessage());
            return redirect('/')->with('error', 'Erreur lors de la déconnexion.');
        }
    }

    /**
     * Transforme les erreurs techniques en messages utilisateur
     */
    private function getUserFriendlyError(Exception $e): string
    {
        if (str_contains($e->getMessage(), 'unique:users')) {
            return 'Cet email est déjà utilisé.';
        }

        if (str_contains($e->getMessage(), 'password')) {
            return 'Le mot de passe ne respecte pas les exigences de sécurité.';
        }

        return 'Une erreur inattendue est survenue.';
    }
}
