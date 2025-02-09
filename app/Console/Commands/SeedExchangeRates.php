<?php

namespace App\Console\Commands;

use App\Models\RonExchangeRate;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Http;
use SimpleXMLElement;
use function Laravel\Prompts\progress;

class SeedExchangeRates extends Command
{
    protected $signature = 'exchange:seed-rates {date}';
    protected $description = 'Fetch RON exchange rates for the entire year from BNR API';

    private $allowed_currencies = ['EUR', 'USD', 'GBP'];

    public function handle(): void
    {
        $requestedDate = $this->argument('date'); // Expected format: YYYY-MM-DD

        // Validate date format
        if (!preg_match('/^\d{4}-\d{2}-\d{2}$/', $requestedDate)) {
            $this->error('Invalid date format. Use YYYY-MM-DD.');
            return;
        }

        $year = substr($requestedDate, 0, 4);
        $url = "https://www.bnr.ro/files/xml/years/nbrfxrates{$year}.xml";

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

        $this->info('Exchange rates from ' . $year);

        DB::beginTransaction();

        try {
            progress(
                label: 'Storing Exchange Rates for year ' . $year,
                steps: $xml->Body->Cube,
                callback: function ($day) {
                    $date = (string)$day['date'];

                    foreach ($day->Rate as $rate) {
                        $currency = (string)$rate['currency'];
                        if (in_array($currency, $this->allowed_currencies)) {
                            $this->storeRate($date, $currency, (float)$rate);
                        }
                    }
                }
            );

            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            $this->error($e->getMessage());
        }

        return;
    }

    private function storeRate($date, $currency, $rate)
    {
        RonExchangeRate::create([
            'date' => $date,
            'currency' => $currency,
            'rate' => $rate
        ]);
    }
}
