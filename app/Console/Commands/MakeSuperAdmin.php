<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeSuperAdmin extends Command
{
    protected $signature = 'make:super-admin {email}';
    protected $description = 'Make a user super admin by email';

    public function handle()
    {
        $email = $this->argument('email');
        $user = User::where('email', $email)->first();

        if (!$user) {
            $this->error("User with email {$email} not found!");
            return 1;
        }

        $user->role = 'super_admin';
        $user->save();

        $this->info("User {$email} is now a super admin!");
        return 0;
    }
}
