<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Services\MailAppendService;

class TestImapConnection extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'imap:test';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Test IMAP connection and list available folders';

    /**
     * Execute the console command.
     */
    public function handle(): int
    {
        $this->info('Testing IMAP connection...');
        $this->newLine();

        $service = new MailAppendService();
        $result = $service->testConnection();

        if ($result['success']) {
            $this->info('✓ ' . $result['message']);
            $this->newLine();
            
            $this->info('Available folders:');
            foreach ($result['folders'] as $folder) {
                $this->line('  - ' . $folder);
            }
            
            return Command::SUCCESS;
        }

        $this->error('✗ ' . $result['message']);
        return Command::FAILURE;
    }
}
