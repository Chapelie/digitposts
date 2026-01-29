<?php

namespace App\Models;

// use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Concerns\HasUuids;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;

class User extends Authenticatable
{
    /** @use HasFactory<\Database\Factories\UserFactory> */
    use HasFactory, Notifiable, HasUuids;


    /**
     * The attributes that are mass assignable.
     *
     * @var list<string>
     */
    protected $fillable = [
        'firstname',
        'lastname',
        'email',
        'password',
        'phone',
        'organization',
        'bio',
        'website',
        'location',
        'google_id',
        'google_avatar',
        'is_admin',
        'role',
    ];

    /**
     * The attributes that should be hidden for serialization.
     *
     * @var list<string>
     */
    protected $hidden = [
        'password',
        'remember_token',
    ];

    /**
     * Get the attributes that should be cast.
     *
     * @return array<string, string>
     */
    protected function casts(): array
    {
        return [
            'email_verified_at' => 'datetime',
            'password' => 'hashed',
            'is_admin' => 'boolean',
        ];
    }

    /**
     * Vérifie si l'utilisateur est administrateur (rôle admin)
     */
    public function isAdmin(): bool
    {
        return $this->role === 'admin';
    }

    /**
     * Vérifier si l'utilisateur a un abonnement actif pour un type de plan
     */
    public function hasActiveSubscriptionFor(string $planType): bool
    {
        return Subscription::hasActiveSubscription($this->id, $planType);
    }

    /**
     * Relation avec les feeds (campagnes créées)
     */
    public function feeds()
    {
        return $this->hasMany(Feed::class);
    }

    /**
     * Relation avec les inscriptions
     */
    public function registrations()
    {
        return $this->hasMany(Registration::class);
    }

    /**
     * Relation avec les favoris
     */
    public function favorites()
    {
        return $this->hasMany(Favorite::class);
    }

    /**
     * Relation avec les abonnements
     */
    public function subscriptions()
    {
        return $this->hasMany(Subscription::class);
    }

    /**
     * Obtenir l'abonnement actif de l'utilisateur (optionnellement pour un type)
     */
    public function activeSubscription(?string $planType = null)
    {
        $q = $this->hasOne(Subscription::class)
            ->where('status', 'active')
            ->where('payment_status', 'paid')
            ->where('end_date', '>', now());
        if ($planType !== null) {
            $q->where('plan_type', $planType);
        }
        return $q->latest('end_date');
    }
}
