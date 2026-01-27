<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Models\User;
use Illuminate\Support\Facades\Hash;
use Illuminate\Validation\Rules;
use Illuminate\Support\Facades\Log;
use Exception;
use Laravel\Socialite\Facades\Socialite;

class AuthController extends Controller
{
    public function loginForm()
    {
        try {
            $seoData = [
                'seoTitle' => 'Connexion - DigitPosts',
                'seoDescription' => 'Connectez-vous à votre compte DigitPosts pour accéder à vos formations, événements et favoris.',
                'seoKeywords' => 'connexion, login, compte, DigitPosts',
                'seoUrl' => route('login'),
                'seoType' => 'website',
            ];
            return view('auth.login', $seoData);
        } catch (Exception $e) {
            Log::error('Login form error: ' . $e->getMessage());
            return redirect()->back()->with('error', 'Une erreur est survenue lors du chargement du formulaire.');
        }
    }

    public function login(Request $request)
    {
        try {
            $request->validate([
                'identifier' => ['required', 'string'],
                'password' => ['required'],
            ], [
                'identifier.required' => 'Veuillez entrer votre email ou numéro de téléphone.',
                'password.required' => 'Le mot de passe est requis.',
            ]);

            $identifier = $request->identifier;
            $password = $request->password;

            // Déterminer si c'est un email ou un téléphone
            $isEmail = filter_var($identifier, FILTER_VALIDATE_EMAIL);
            
            // Chercher l'utilisateur par email ou téléphone
            if ($isEmail) {
                $user = User::where('email', $identifier)->first();
            } else {
                $user = User::where('phone', $identifier)->first();
            }

            // Vérifier le mot de passe et connecter
            if ($user && Hash::check($password, $user->password)) {
                Auth::login($user, true);
                $request->session()->regenerate();
                return redirect()->intended('/')->with('success', 'Connexion réussie !');
            }

            return back()
                ->withInput($request->only('identifier'))
                ->withErrors([
                    'identifier' => 'Identifiants incorrects ou compte inexistant.',
                ]);

        } catch (Exception $e) {
            Log::error('Login error for identifier ' . $request->identifier . ': ' . $e->getMessage());
            return back()->with('error', 'Une erreur technique est survenue. Veuillez réessayer.');
        }
    }

    public function registerForm()
    {
        try {
            $seoData = [
                'seoTitle' => 'Inscription - DigitPosts',
                'seoDescription' => 'Créez votre compte DigitPosts pour accéder à des formations et événements professionnels au Burkina Faso. Inscription gratuite et rapide.',
                'seoKeywords' => 'inscription, créer compte, s\'inscrire, DigitPosts, Burkina Faso',
                'seoUrl' => route('register'),
                'seoType' => 'website',
            ];
            return view('auth.register', $seoData);
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
            'email' => ['required', 'string', 'email', 'max:255', 'unique:users,email'],
            'phone' => ['nullable', 'string', 'max:20', 'unique:users,phone'],
            'password' => ['required', 'min:8', 'confirmed'],
        ], [
            'email.unique' => 'Cet email est déjà utilisé.',
            'phone.unique' => 'Ce numéro de téléphone est déjà utilisé.',
            'password.min' => 'Le mot de passe doit contenir au moins 8 caractères.',
            'password.confirmed' => 'Les mots de passe ne correspondent pas.',
        ]);

        $user = User::create([
            'firstname' => $validated['first_name'],
            'lastname' => $validated['last_name'],
            'email' => $validated['email'],
            'phone' => $validated['phone'] ?? null,
            'password' => Hash::make($validated['password']),
        ]);
        
        // Connexion automatique avec "Remember Me" activé
        Auth::login($user, true);
        
        return redirect()->intended('/')->with('success', 'Inscription réussie ! Bienvenue.');
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
     * Rediriger vers le provider OAuth (Google)
     */
    public function redirectToProvider($provider)
    {
        if ($provider !== 'google') {
            return redirect()->route('login')
                ->with('error', 'Provider d\'authentification non supporté.');
        }

        // Vérifier si Google OAuth est configuré
        if (empty(config('services.google.client_id')) || empty(config('services.google.client_secret'))) {
            Log::warning('Google OAuth not configured');
            return redirect()->route('login')
                ->with('error', 'L\'authentification Google n\'est pas configurée. Veuillez utiliser le formulaire de connexion.');
        }

        try {
            return Socialite::driver($provider)->redirect();
        } catch (Exception $e) {
            Log::error('OAuth redirect error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Erreur lors de la redirection vers Google. Veuillez réessayer.');
        }
    }

    /**
     * Gérer le callback OAuth (Google)
     */
    public function handleProviderCallback($provider)
    {
        if ($provider !== 'google') {
            return redirect()->route('login')
                ->with('error', 'Provider d\'authentification non supporté.');
        }

        try {
            $googleUser = Socialite::driver($provider)->user();

            // Chercher ou créer l'utilisateur
            $user = User::where('email', $googleUser->getEmail())
                ->orWhere('google_id', $googleUser->getId())
                ->first();

            if ($user) {
                // Mettre à jour les informations Google si nécessaire
                if (!$user->google_id) {
                    $user->update([
                        'google_id' => $googleUser->getId(),
                        'google_avatar' => $googleUser->getAvatar(),
                    ]);
                }
            } else {
                // Créer un nouvel utilisateur
                $nameParts = explode(' ', $googleUser->getName(), 2);
                $firstname = $nameParts[0] ?? '';
                $lastname = $nameParts[1] ?? $firstname;

                $user = User::create([
                    'firstname' => $firstname,
                    'lastname' => $lastname,
                    'email' => $googleUser->getEmail(),
                    'google_id' => $googleUser->getId(),
                    'google_avatar' => $googleUser->getAvatar(),
                    'password' => Hash::make(uniqid()), // Mot de passe aléatoire (non utilisé)
                    'email_verified_at' => now(),
                ]);
            }

            // Connecter l'utilisateur
            Auth::login($user, true);

            return redirect()->intended('/')->with('success', 'Connexion réussie avec Google !');

        } catch (Exception $e) {
            Log::error('OAuth callback error: ' . $e->getMessage());
            return redirect()->route('login')
                ->with('error', 'Erreur lors de l\'authentification Google. Veuillez réessayer.');
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
