<?php

namespace Database\Seeders;

use App\Models\Balance;
use App\Models\Budget;
use App\Models\Pot;
use App\Models\Transaction;
use App\Models\User;
// use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\File;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        // User::factory(10)->create();

        // User::factory()->create([
        //     'name' => 'Test User',
        //     'email' => 'test@example.com',
        // ]);

        $jsonFilePath = database_path('seeders/data.json');

        // Check if the file exists
        if (!File::exists($jsonFilePath)) {
            $this->command->error("JSON file not found at: {$jsonFilePath}");
            return;
        }

        // Read and decode the JSON file
        $jsonData = File::get($jsonFilePath);
        $data = json_decode($jsonData, true);

        if (is_null($data)) {
            $this->command->error('Error decoding JSON file.');
            return;
        }

        Balance::create($data['balance']);

        // Insert data into the database
        foreach ($data['transactions'] as $item) {
            Transaction::create($item);
            // $this->command->log('ITEM ' . $item['name']);
        }

        $this->command->info('Transactions seeded successfully from JSON file!');

        // Insert data into the database
        foreach ($data['budgets'] as $item) {
            Budget::create($item);
            // $this->command->log('ITEM ' . $item['name']);
        }

        $this->command->info('Budgets seeded successfully from JSON file!');

        // Insert data into the database
        foreach ($data['pots'] as $item) {
            Pot::create($item);
            // $this->command->log('ITEM ' . $item['name']);
        }

        $this->command->info('Pots seeded successfully from JSON file!');

        $this->command->info('Data seeded successfully from JSON file!');
    }
}
