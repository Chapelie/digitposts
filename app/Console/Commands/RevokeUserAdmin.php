<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class RevokeUserAdmin extends Command
{
    protected $signature = 'user:revoke-admin {email : L\'email de l\'utilisateur}';
    protected $description = 'Révoquer les droits administrateur d\'un utilisateur';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Aucun utilisateur trouvé avec l'email: {$email}");
            return Command::FAILURE;
        }
        
        if ($user->role !== 'admin') {
            $this->info("L'utilisateur {$user->firstname} {$user->lastname} n'est pas administrateur.");
            return Command::SUCCESS;
        }
        
        $user->update(['role' => 'user', 'is_admin' => false]);
        
        $this->info("Les droits administrateur de {$user->firstname} {$user->lastname} ({$email}) ont été révoqués.");
        
        return Command::SUCCESS;
    }
}
