<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class reseller extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'reseller';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a naughty users report for the Reseller Servers';

    /**
     * Create a new command instance.
     *
     * @return void
     */
    public function __construct() {
        parent::__construct();
    }

    /**
     * Execute the console command.
     *
     * @return mixed
     */
    public function fire() {
        //create new CpanelController instance, and run the report that's handed off by the user via artisan.
        $cp = new CpanelController();
        $servers = $cp->ServerList();
        if ($this->argument('type') == 'normal') {
            $output = $cp->SetServer($servers["reseller"]['start'], $servers['reseller']['end'], '^reseller[0-9]+', 'listaccounts');
            $this->info($output);
        } else if ($this->argument('type') == 'ssd') {
            $output = $cp->SetServer($servers["resellerssd"]['start'], $servers['resellerssd']['end'], '^resellerssd[0-9]+', 'listaccounts');
            $this->info($output);
        } else if ($this->argument('type') == 'iceland') {
            $output = $cp->SetServer($servers["icelandreseller"]['start'], $servers['icelandreseller']['end'], '^icelandreseller[0-9]+', 'listaccounts');
            $this->info($output);
        } else if ($this->argument('type') == 'statistics') {
            $output = $cp->SetServer($servers["reseller"]['start'], $servers['reseller']['end'], '^reseller[0-9]+', 'listreseller');
            $output .= "\r\n" .  $cp->SetServer($servers["resellerssd"]['start'], $servers['resellerssd']['end'], '^resellerssd[0-9]+', 'listreseller');
            $output .= "\r\n" .  $cp->SetServer($servers["icelandreseller"]['start'], $servers['icelandreseller']['end'], 'icelandreseller[0-9]+', 'listreseller');
            $this->info($output);
        } else {
            $this->error("Please input a valid argument. example normal, ssd, iceland");
        }
    }

    protected function getArguments() {
        return array(
            array('type', InputArgument::REQUIRED, 'example php artisan shared ssd OR php artisan shared normal'),
        );
    }

}


