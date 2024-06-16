<?php

namespace Zirsakht\TemporaryTokens\Console\Commands;

use Illuminate\Console\Command;
use Symfony\Component\Console\Attribute\AsCommand;
use Zirsakht\TemporaryTokens\Models\TemporaryToken;

#[AsCommand(name: 'temporary-tokens:prune-expired')]
class PruneExpired extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'temporary-tokens:prune-expired {--hours=24 : The number of hours to retain expired temporary tokens}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Prune tokens expired for more than specified number of hours';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle(): int
    {
        $model = TemporaryToken::class;

        $hours = $this->option('hours');

        $this->components->task(
            'Pruning tokens with expired expires_at timestamps',
            fn () => $model::where('expires_at', '<', now()->subHours($hours))->delete()
        );

        $this->components->info("Tokens expired for more than [$hours hours] pruned successfully.");

        return 0;
    }
}
