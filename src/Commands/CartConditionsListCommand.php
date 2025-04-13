<?php

namespace Soap\LaravelCartConditions\Commands;

use Illuminate\Console\Command;

class CartConditionsListCommand extends Command
{
    public $signature = 'cart-conditions:list';

    public $description = 'My command';

    public function handle(): int
    {
        $this->comment('All done');

        return self::SUCCESS;
    }
}
