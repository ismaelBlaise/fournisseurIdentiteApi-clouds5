<?php

namespace App\Console\Commands;

use App\Models\Cryptocurrency;
use Illuminate\Console\Command;

class UpdateCryptoPrices extends Command
{
    protected $signature = 'crypto:update-prices';
    protected $description = 'Update cryptocurrency prices with random variations';

    public function handle()
    {
        $cryptocurrencies = Cryptocurrency::all();

        foreach ($cryptocurrencies as $crypto) {
            // Generate a random price variation between -5% and +5%
            $variation = rand(-500, 500) / 10000;
            $newPrice = $crypto->current_price * (1 + $variation);
            
            $crypto->update(['current_price' => $newPrice]);
        }

        $this->info('Cryptocurrency prices updated successfully.');
    }
}