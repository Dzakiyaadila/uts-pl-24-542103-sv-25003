<?php

namespace App\Console\Commands;

use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\Hash;
use Illuminate\Support\Facades\Validator;

class CreateDevUser extends Command
{
    protected $signature = 'orderly:create-dev-user';

    protected $description = 'Create a local development user';

    public function handle(): int
    {
        if (! app()->environment('local')) {
            $this->error('This command is only available locally.');

            return self::FAILURE;
        }

        $name = $this->ask('Name');
        $email = $this->ask('Email');
        $password = $this->secret('Password');

        $validator = Validator::make(
            compact('name', 'email', 'password'),
            [
                'name' => ['required', 'string', 'max:255'],
                'email' => ['required', 'email', 'unique:users,email'],
                'password' => ['required', 'string', 'min:8'],
            ],
        );

        if ($validator->fails()) {
            $this->error($validator->errors()->first());

            return self::FAILURE;
        }

        User::create([
            'name' => $name,
            'email' => $email,
            'password' => Hash::make($password),
        ]);

        $this->info('Development user created.');

        return self::SUCCESS;
    }
}
