<?php

namespace App\Console\Commands;

use App\Models\Campus;
use App\Models\User;
use Illuminate\Console\Command;

class PromoteCampusAdmin extends Command
{
    protected $signature = 'campus:promote-admin {slug} {email}';

    protected $description = 'Grant campus administrator access to a registered user';

    public function handle(): int
    {
        $campus = Campus::query()->where('slug', $this->argument('slug'))->first();

        if ($campus === null) {
            $this->components->error('Campus not found.');

            return self::FAILURE;
        }

        $user = User::withoutGlobalScopes()
            ->where('campus_id', $campus->id)
            ->where('email', $this->argument('email'))
            ->first();

        if ($user === null) {
            $this->components->error('Register this user in the selected campus portal first.');

            return self::FAILURE;
        }

        $user->update(['role' => 'admin']);
        $this->components->info("{$user->email} is now an administrator for {$campus->name}.");

        return self::SUCCESS;
    }
}
