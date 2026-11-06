<?php

namespace App\Console\Commands;

use App\Models\Campus;
use Illuminate\Console\Command;

class CreateCampus extends Command
{
    protected $signature = 'campus:create {name} {slug}';

    protected $description = 'Create a separate Lost & Found portal for a campus';

    public function handle(): int
    {
        $name = trim((string) $this->argument('name'));
        $slug = strtolower(trim((string) $this->argument('slug')));

        if ($name === '') {
            $this->components->error('Campus name cannot be empty.');

            return self::FAILURE;
        }

        if (! preg_match('/^[a-z0-9]+(?:-[a-z0-9]+)*$/', $slug)) {
            $this->components->error('Slug must contain lowercase letters, numbers, and single hyphens only.');

            return self::FAILURE;
        }

        if (Campus::query()->where('slug', $slug)->exists()) {
            $this->components->error("Campus slug '{$slug}' already exists.");

            return self::FAILURE;
        }

        Campus::create([
            'name' => $name,
            'slug' => $slug,
            'is_active' => true,
        ]);

        $this->components->info("Campus portal created: /kampus/{$slug}");

        return self::SUCCESS;
    }
}
