<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Models\Setting;

class ToggleAutoMarkAbsent extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'attendance:toggle-auto-mark {action : enable or disable}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Enable or disable automatic marking of absent students';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $action = $this->argument('action');
        
        if (!in_array($action, ['enable', 'disable'])) {
            $this->error('Action must be either "enable" or "disable"');
            return 1;
        }
        
        $enabled = $action === 'enable';
        
        Setting::set(
            'system.auto_mark_absent',
            $enabled,
            'boolean',
            'system',
            'Auto Mark Siswa Alpha',
            'Otomatis menandai siswa yang tidak hadir sebagai alpha',
            false
        );
        
        $status = $enabled ? 'enabled' : 'disabled';
        $this->info("Auto mark absent has been {$status}.");
        
        if ($enabled) {
            $this->line('Students will be automatically marked as alpha if they don\'t attend.');
        } else {
            $this->line('Students will NOT be automatically marked as alpha. Use --force to override.');
        }
        
        return 0;
    }
}
