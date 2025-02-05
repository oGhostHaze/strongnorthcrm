<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use App\Mail\ReorderLevelReportMail;
use Illuminate\Support\Facades\Mail;

class CheckLevels extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'check:levels';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Check inventory safety levels';

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
        Mail::to('joshua070915@gmail.com')->send(new ReorderLevelReportMail());
        Mail::to('ferdsragasa@yahoo.com')->send(new ReorderLevelReportMail());
        Mail::to('strongnorth9319@gmail.com')->send(new ReorderLevelReportMail());
        Mail::to('barbiemitz79@yahoo.com')->send(new ReorderLevelReportMail());
        info('Reorder Level Report sent!');
        return 0;
    }
}
