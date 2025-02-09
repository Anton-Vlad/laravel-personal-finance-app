<?php

namespace App\Console\Commands;

use App\Models\RonExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;

class DailyExchangeRates extends Command
{
    protected $signature = 'exchange:daily-rates';
    protected $description = 'Fetch latest RON exchange rates from BNR API';

    private $allowed_currencies = ['EUR', 'USD', 'GBP'];

    public function handle(): void
    {
        $url = "https://www.bnr.ro/nbrfxrates.xml";

        // Fetch exchange rate from BNR API
        $response = Http::get($url);

        if ($response->failed()) {
            $this->error('Failed to fetch exchange rates from BNR.');
            logger()->error($response->json());
            return;
        }

        // Parse XML response
        $xml = new SimpleXMLElement($response->body());
        $publishDate = (string)$xml->Header->PublishingDate;

        $this->info('Exchange rates from ' . $publishDate);

        try {
            foreach ($xml->Body->Cube as $day) {
                $date = (string)$day['date'];

                foreach ($day->Rate as $rate) {
                    $currency = (string)$rate['currency'];
                    if (in_array($currency, $this->allowed_currencies)) {
                        $this->storeRate($date, $currency, (float)$rate);
                    }
                }
            }

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }

        return;
    }

    private function storeRate($date, $currency, $rate)
    {
        RonExchangeRate::updateOrCreate([
            'date' => $date,
            'currency' => $currency,
        ], [
            'rate' => $rate
        ]);
    }
}
