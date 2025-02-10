<?php

namespace App\Console\Commands;

use App\Models\Statement;
use App\Models\Transaction;
use App\Models\User;
use Illuminate\Console\Command;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Symfony\Component\Process\Process;
use Symfony\Component\Process\Exception\ProcessFailedException;

class ParseStatement extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'parse:statement {user}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Command description';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        $user = User::findOrFail($this->argument('user'));

        $statements = Statement::where('user_id', $user->id)->where('processed', false)->get();


        try {
            DB::beginTransaction();

            foreach ($statements as $statement) {
                $fullFilePath = Storage::disk('private')->path($statement->file_path);

                $output = $this->parseStatement($fullFilePath);

                $this->info("Statement for user: " . $user->name . "\n" . $fullFilePath . "\n");
                if ($output) {
                    foreach ($output['transactions'] as $entry) {
                        try {
                            $details = [...$entry['details']];
                            if (!empty($entry['location'])) {
                                array_unshift($details, $entry['location']);
                            }
                            Transaction::create([
                                'user_id' => $user->id,
                                'statement_id' => $statement->id,
                                'currency' => $entry['currency'],
                                'date' => $entry['date'],
                                'recurring' => false,
                                'details' => $details,
                                'name' => $entry['name'],
                                'amount' => ($entry['type'] == 'expense' ? (-1) * $entry['amount'] : $entry['amount']),
                            ]);

                            $this->info("Node.js OK" ); // . print_r($output, true

                        } catch (\Exception $e) {
                            throw $e;
                            $this->error("Failed to process user ID {$user->id}: " . $e->getMessage());
                        }
                    }

                    Statement::where('id', $statement->id)->update(['processed' => true]);
                }
            }

            DB::commit();
            $this->info("All user statements completed successfully.");
        } catch (\Exception $e) {
            // Rollback transaction in case of failure
            DB::rollBack();
//            Log::error("Transaction failed: " . $e->getMessage());
            $this->error("An error occurred. Transaction rolled back.");
        }

    }

    private function parseStatement($filePath)
    {
        // Define the Node.js script path
        $nodeScriptPath = base_path('node_scripts/parseStatement.js');

        // Set working directory to node_scripts to resolve relative paths
        $process = new Process(['node', $nodeScriptPath, $filePath]);
        $process->setWorkingDirectory(base_path('node_scripts'));

        $process->run();

        if (!$process->isSuccessful()) {
            throw new ProcessFailedException($process);
        }

        $output = $process->getOutput();
        $decoded = json_decode($output, true);

        if (json_last_error() !== JSON_ERROR_NONE) {
            $this->error("Invalid JSON output: " . json_last_error_msg());
            return null;
        }

        return $decoded;
    }
}
