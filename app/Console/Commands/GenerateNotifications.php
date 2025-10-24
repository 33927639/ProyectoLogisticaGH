<?php

namespace App\Console\Commands;

use App\Services\NotificationService;
use Illuminate\Console\Command;

class GenerateNotifications extends Command
{
    /**
     * The name and signature of the console command.
     */
    protected $signature = 'notifications:generate';

    /**
     * The console command description.
     */
    protected $description = 'Generate automatic notifications based on system state';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $this->info('Generating automatic notifications...');

        try {
            NotificationService::generateAutomaticNotifications();
            
            $this->info('✅ Automatic notifications generated successfully!');
            
            return Command::SUCCESS;
        } catch (\Exception $e) {
            $this->error('❌ Error generating notifications: ' . $e->getMessage());
            
            return Command::FAILURE;
        }
    }
}
