<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;

class MakeAdmin extends Command
{
    protected $signature = 'make:admin {email : Email пользователя}';

    protected $description = 'Назначить пользователя администратором (is_admin = true)';

    public function handle(): int
    {
        $email = $this->argument('email');

        $user = User::where('email', $email)->first();

        if (! $user) {
            $this->error("Пользователь с email [{$email}] не найден.");
            return self::FAILURE;
        }

        if ($user->is_admin) {
            $this->info("Пользователь [{$email}] уже является администратором.");
            return self::SUCCESS;
        }

        $user->update(['is_admin' => true]);
        $this->info("Пользователь [{$email}] назначен администратором.");

        return self::SUCCESS;
    }
}
