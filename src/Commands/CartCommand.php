<?php

namespace Ganyicz\Cart\Commands;

use Illuminate\Console\Command;

class CartCommand extends Command
{
    public $signature = 'eloquent-cart';

    public $description = 'My command';

    public function handle()
    {
        $this->comment('All done');
    }
}
