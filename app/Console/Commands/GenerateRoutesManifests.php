<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;

class GenerateRoutesManifests extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'app:routes';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate routes manifests';

    /**
     * Execute the console command.
     */
    public function handle(): void
    {
        $this->call('ziggy:generate', [
            'path' => './resources/js/ziggy/routes.js',
        ]);

        $this->call('ziggy:generate', [
            'path' => './resources/js/ziggy/all.js',
            '--group' => 'all',
        ]);
    }
}
