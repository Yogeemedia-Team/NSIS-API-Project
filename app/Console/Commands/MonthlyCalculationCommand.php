<?php

namespace App\Console\Commands;

use App\Http\Controllers\FeesAutomateController;
use App\Repositories\FeesCalculationRepository;
use Illuminate\Console\Command;

class MonthlyCalculationCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'fees:monthly-calculation';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Generate Student Fees';


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
        $feesController->monthly_fee();

        $this->info('Monthly fees generated successfully.');
    }
}
