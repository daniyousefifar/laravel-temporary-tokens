<?php

namespace MyDaniel\TemporaryTokens\Commands;

use Illuminate\Console\Command;
use MyDaniel\TemporaryTokens\Models\TemporaryToken;

/**
 * Artisan command to prune expired temporary tokens from the database.
 */
class PruneExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temporary-tokens:prune-expired {--hours= : The number of hours to retain expired temporary tokens}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune tokens expired for more than specified number of hours';

    /**
     * Execute the console command.
     *
     * @return int The command exit code.
     */
    public function handle(): int
    {
        $model = TemporaryToken::class;

        $hours = $this->option('hours') ?? config('temporary-tokens.prune_expired_after_hours', 24);;

        $this->components->task(
            'Pruning tokens with expired expires_at timestamps',
            fn() => $model::where('expires_at', '<', now()->subHours($hours))->delete()
        );

        $this->info("Tokens expired for more than [$hours hours] pruned successfully.");

        return Command::SUCCESS;
    }
}
