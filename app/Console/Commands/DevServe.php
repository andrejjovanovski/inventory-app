<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Process\Process;

class DevServe extends Command
{
    protected $signature = "dev:serve";
    protected $description = "Run php artisan serve and queue:work with timestamps and graceful shutdown (no auto-restart)";

    private bool $stopRequested = false;
    /**
     * @return int
     */
    public function handle(): int
    {
        pcntl_async_signals(true);

        // Handle CTRL + C
        pcntl_signal(SIGINT, function () {
            $this->stopRequested = true;
            $this->logSystem("Stop requested! Shutting down processes...");
        });

        $this->info("🚀 Starting Laravel dev environment...");
        $this->info("SERVER + QUEUE running. Press CTRL+C to stop.\n");

        $this->runProcesses();

        $this->logSystem("All processes stopped. Goodbye!");
        return 0;
    }
    /**
     * @return void
     */
    private function runProcesses(): void
    {
        $server = new Process(["php", "artisan", "serve"]);
        $queue = new Process(["php", "artisan", "queue:work"]);

        $server->setTimeout(null);
        $queue->setTimeout(null);

        $server->start();
        $queue->start();

        $this->logSystem("Server and Queue started.");

        // Keep reading output while both are running
        while (
            !$this->stopRequested &&
            $server->isRunning() &&
            $queue->isRunning()
        ) {
            foreach (
                ["SERVER" => $server, "QUEUE" => $queue]
                as $label => $process
            ) {
                $buffer =
                    $process->getIncrementalOutput() .
                    $process->getIncrementalErrorOutput();

                if (!empty($buffer)) {
                    $this->log($label, $buffer);
                }
            }
            usleep(50000);
        }

        // Stop processes if either exits or CTRL+C is pressed
        if ($server->isRunning()) {
            $server->stop(1);
            $this->logSystem("SERVER stopped.");
        }

        if ($queue->isRunning()) {
            $queue->stop(1);
            $this->logSystem("QUEUE stopped.");
        }
    }
    /**
     * @return void
     */
    private function log(string $label, string $message): void
    {
        $lines = explode("\n", $message);
        foreach ($lines as $line) {
            if (!empty(trim($line))) {
                $time = date("H:i:s");
                echo "[$time][$label] $line\n";
            }
        }
    }
    /**
     * @return void
     */
    private function logSystem(string $message): void
    {
        $time = date("H:i:s");
        echo "[$time][SYSTEM] $message\n";
    }
}
