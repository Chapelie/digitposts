<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeUserAdmin extends Command
{
    protected $signature = 'user:make-admin {email : L\'email de l\'utilisateur à promouvoir}';
    protected $description = 'Promouvoir un utilisateur en administrateur';

    public function handle()
    {
        $email = $this->argument('email');
        
        $user = User::where('email', $email)->first();
        
        if (!$user) {
            $this->error("Aucun utilisateur trouvé avec l'email: {$email}");
            return Command::FAILURE;
        }
        
        if ($user->role === 'admin') {
            $this->info("L'utilisateur {$user->firstname} {$user->lastname} est déjà administrateur.");
            return Command::SUCCESS;
        }
        
        $user->update(['role' => 'admin', 'is_admin' => true]);
        
        $this->info("L'utilisateur {$user->firstname} {$user->lastname} ({$email}) est maintenant administrateur.");
        
        return Command::SUCCESS;
    }
}
