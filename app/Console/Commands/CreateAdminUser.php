<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;

class CreateAdminUser extends Command
{
    protected $signature = 'app:create-admin';

    protected $description = 'Create the initial administrator account from the server console';

    public function handle(): int
    {
        if (User::query()->exists()) {
            $this->error('An account already exists. Use Edit Account after logging in.');

            return self::FAILURE;
        }

        $name = $this->ask('Administrator name');
        $email = $this->ask('Administrator email');
        $password = $this->secret('Password (minimum 8 characters)');
        $passwordConfirmation = $this->secret('Confirm password');

        if (! $name || ! filter_var($email, FILTER_VALIDATE_EMAIL) || strlen((string) $password) < 8 || $password !== $passwordConfirmation) {
            $this->error('Enter a name, valid email, and matching passwords of at least 8 characters.');

            return self::FAILURE;
        }

        DB::transaction(function () use ($name, $email, $password): void {
            if (User::query()->lockForUpdate()->exists()) {
                throw new \RuntimeException('An account was created while this command was running.');
            }

            User::query()->create([
                'name' => $name,
                'email' => $email,
                'password' => $password,
            ]);
        });

        $this->info('Administrator account created. Sign in at /login.');

        return self::SUCCESS;
    }
}
