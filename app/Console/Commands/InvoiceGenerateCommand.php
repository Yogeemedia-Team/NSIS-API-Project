<?php

namespace App\Console\Commands;

use App\Http\Controllers\FeesAutomateController;
use App\Repositories\FeesCalculationRepository;
use Illuminate\Console\Command;

class InvoiceGenerateCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
     protected $signature = 'fees:invoice-generate';
     protected $description = 'Generate invoices';

    /**
     * Execute the console command.
     */
    public function handle()
    {
        // Instantiate an instance of FeesCalculationRepository
        $feesCalculationRepository = app(FeesCalculationRepository::class);

        // Instantiate an instance of FeesAutomateController and pass the repository instance
        $feesController = new FeesAutomateController($feesCalculationRepository);

        // Call the invoice_generate method
        $feesController->invoice_generate();

        $this->info('Invoices generated successfully.');
    }
}
