<?php

namespace App\Console\Commands;

use App\Models\OrderPaymentHistory;
use Illuminate\Console\Command;

class GenerateReceiptNumbers extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'receipts:generate-numbers';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate receipt numbers for existing payment records';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {
        $payments = OrderPaymentHistory::whereNull('receipt_number')->orderBy('id')->get();
        $count = $payments->count();

        if ($count === 0) {
            $this->info('No payments without receipt numbers found.');
            return 0;
        }

        $this->info("Found {$count} payments without receipt numbers.");

        $bar = $this->output->createProgressBar($count);
        $bar->start();

        // Keep track of the current date code and sequence number
        $currentDateCode = '';
        $currentSequence = 0;

        foreach ($payments as $payment) {
            // Get the date from the payment date
            $paymentDate = $payment->date_of_payment ?? date('Y-m-d');
            $dateCode = date('Ym', strtotime($paymentDate));

            // If we're in a new month, reset the sequence
            if ($dateCode !== $currentDateCode) {
                $currentDateCode = $dateCode;
                $currentSequence = 1;
            } else {
                $currentSequence++;
            }

            // Generate and save the receipt number
            $payment->receipt_number = 'PR-' . $currentDateCode . '-' . str_pad($currentSequence, 4, '0', STR_PAD_LEFT);
            $payment->save();

            $bar->advance();
        }

        $bar->finish();
        $this->newLine();
        $this->info("Successfully generated receipt numbers for {$count} payments.");

        return 0;
    }
}
