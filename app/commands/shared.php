<?php

use Illuminate\Console\Command;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Input\InputArgument;

class shared extends Command {

    /**
     * The console command name.
     *
     * @var string
     */
    protected $name = 'shared';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Run a naughty users report for the Shared Servers';

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
            $output = $cp->SetServer($servers["shared"]['start'], $servers['shared']['end'], '^shared[0-9]+$', 'listaccounts');
            $this->info($output);
        } else if ($this->argument('type') == 'ssd') {
            $output = $cp->SetServer($servers["sharedssd"]['start'], $servers['sharedssd']['end'], '^sharedssd[0-9]+', 'listaccounts');
            $this->info($output);
        } else if ($this->argument('type') == 'iceland') {
            $output = $cp->SetServer($servers["icelandshared"]['start'], $servers['icelandshared']['end'], '^icelandshared[0-9]+', 'listaccounts');
            $this->info($output);
        } else if ($this->argument('type') == 'icelandssd') {
            $output = $cp->SetServer($servers["icelandsharedssd"]['start'], $servers['icelandsharedssd']['end'], '^icelandsharedssd[0-9]+', 'listaccounts');
            $this->info($output);
        } else {
            $this->error("Please input a valid argument. example shared, ssd, iceland, icelandssd");
        }
    }

    protected function getArguments() {
        return array(
            array('type', InputArgument::REQUIRED, 'example php artisan shared ssd OR php artisan shared normal'),
        );
    }

}
