<?php

/*
  |--------------------------------------------------------------------------
  | Default Home Controller
  |--------------------------------------------------------------------------
  |
  | You may wish to use controllers instead of, or in addition to, Closure
  | based routes. That's great! Here is an example controller method to
  | get you started. To route to this controller, just add the route:
  |
  |	Route::get('/', 'ResultsController@showWelcome');
  |
 */

class ReportController extends BaseController {

    public $finalreturn = "";

    public function ShowFunctions() {
        $servers = DB::table('servers')->select('url')->get();
        $servers = json_encode($servers);
        return View::make('site/user/functions')->with('servers', $servers);
    }

    public function ShowResults($results, $type) {
        $results = $this->objectToArray($results);
        View::share('type', $type);
        return View::make('site/results')->with('users', $results);
    }

    public function objectToArray($d) {
        if (is_object($d)) {
            // Gets the properties of the given object
            // with get_object_vars function
            $d = get_object_vars($d);
        }

        if (is_array($d)) {
            /*
             * Return array converted to object
             * Using __FUNCTION__ (Magic constant)
             * for recursive call
             */
            return array_map(array($this, 'objectToArray'), $d);
            //$this->d = get_object_vars($d);
        } else {
            // Return array
            return $d;
        }
    }

    public function ResetKeys() {

        file_put_contents(base_path().'/app/storage/servers.txt', "");
        file_put_contents(base_path().'/app/storage/badpeeps.txt', "");
        return Redirect::action('ReportController@ShowFunctions');
    }
    //searching is quite poor, there's most likely a better way to do this.
    public function SimpleSearch() {
        if (!is_null(Input::get('serverbox'))) {
            $input = Input::get('serverbox');
            $sqlqry = "SELECT * FROM `highuser` WHERE";
            foreach ($input as $srv) {
                $sqlqry .= " `server`='$srv' OR ";
            }
            $sqlqry = substr_replace($sqlqry, "", -3);
            $results = DB::select(DB::raw("$sqlqry"));
            return $this->ShowResults($results, "disk");
        }
        if (!is_null(Input::get('username'))) {
            $input = Input::get('username');
            $sqlqry = "SELECT * FROM `highuser` WHERE `username` = '$input'";
            $results = DB::select(DB::raw("$sqlqry"));
            return $this->ShowResults($results, "disk");
        }
        if (!is_null(Input::get('diskspace'))) {
            $input = Input::get('diskspace');
            $sqlqry = "SELECT * FROM `highuser` WHERE `diskspace` > '$input'";
            $results = DB::select(DB::raw("$sqlqry"));
            return $this->ShowResults($results, "disk");
        }
        if (!is_null(Input::get('servers'))) {
            $input = Input::get('servers');
            $sqlqry = "SELECT * FROM `highuser` WHERE `server` = '$input'";
            $results = DB::select(DB::raw("$sqlqry"));
            return $this->ShowResults($results, "disk");
        }
        if (!is_null(Input::get('reseller'))) {
            $input = Input::get('reseller');
            $date = DB::select(DB::raw("SELECT * FROM `resellerstats` WHERE `server` = '$input'ORDER BY reportran_at DESC limit 1"));
            $date = $this->objectToArray($date);
            $date = $date[0]['reportran_at'];
            $sqlqry = "SELECT * FROM `resellerstats` WHERE `server` = '$input' AND `reportran_at` = '$date'";
            $results = DB::select(DB::raw("$sqlqry"));
            return $this->ShowResults($results, "reseller");
        }
        if (!is_null(Input::get('date'))) {
            $input = Input::get('date');
            $sqlqry = "SELECT * FROM `highuser` WHERE `reportran_at` LIKE '%$input%'";
            $results = DB::select(DB::raw("$sqlqry"));
            return $this->ShowResults($results, "disk");
        }
        if (!is_null(Input::get('datepick'))) {
            $input = Input::get('datepick');
            $sqlqry = "SELECT * FROM `highuser` WHERE `reportran_at` > '$input'";
            $results = DB::select(DB::raw("$sqlqry"));
            return $this->ShowResults($results, "disk");
        }
        if (!is_null(Input::get('multiserver')) || !is_null(Input::get('multidate')) || !is_null(Input::get('multidisk'))) {
            $sqlqry = "SELECT * FROM `highuser` WHERE `server` = ". "'".Input::get('multiserver')."' "
                    . "AND `reportran_at` LIKE "."'%".Input::get('multidate')."%' "
                    . "AND `diskspace` > "."'".Input::get('multidisk')."'";
            $results = DB::select(DB::raw("$sqlqry"));
            return $this->ShowResults($results, "disk");
        }
    }

    public function Graphs() {
        return View::make('graphs');
    }

    public function AddServers() {
        $cp = new CpanelController();
        $cp->AddServers();
        $cp->SetServer('sr1.supercp.com', 'sr11.supercp.com', '%reseller%', 'diskusage');
        $cp->SetServer('ssr1.supercp.com', 'ssr5.supercp.com', '%turboreseller%', 'diskusage');
        $cp->SetServer('thsr1.supercp.com', 'thsr1.supercp.com', '%resellice%', 'diskusage');
        $cp->SetServer('a2s26.a2hosting.com', 'a2s84.a2hosting.com', '%shared%', 'diskusage');
        $cp->SetServer('a2ss1.a2hosting.com', 'a2ss8.a2hosting.com', '%fast%', 'diskusage');
        $cp->SetServer('ths1.a2hosting.com', 'ths1.a2hosting.com', '%iceland%', 'diskusage');
        $cp->SetServer('thss1.a2hosting.com', 'thss2.a2hosting.com', '%fice%', 'diskusage');
    }

    public function RunUSShared() {
        $cp = new CpanelController();
        $output = $cp->SetServer('a2s26.a2hosting.com', 'a2s84.a2hosting.com', '%shared%', 'listaccounts');
        return View::make('site/results')->with('results', $output);
    }

    public function RunUSSSDShared() {
        $cp = new CpanelController();
        $output = $cp->SetServer('a2ss1.a2hosting.com', 'a2ss8.a2hosting.com', '%fast%', 'listaccounts');
        return View::make('site/results')->with('results', $output);
    }

    public function RunUSReseller() {
        $cp = new CpanelController();
        $output = $cp->SetServer('sr1.supercp.com', 'sr11.supercp.com', '%reseller%', 'listaccounts');
        return View::make('site/results')->with('results', $output);
    }

    public function RunUSSSDReseller() {
        $cp = new CpanelController();
        $output = $cp->SetServer('ssr1.supercp.com', 'ssr5.supercp.com', '%turboreseller%', 'listaccounts');
        return View::make('site/results')->with('results', $output);
    }

    public function RunIcelandShared() {
        $cp = new CpanelController();
        $output = $cp->SetServer('ths1.a2hosting.com', 'ths1.a2hosting.com', '%iceland%', 'listaccounts');
        return View::make('site/results')->with('results', $output);
    }

    public function RunIcelandSSDShared() {
        $cp = new CpanelController();
        $output = $cp->SetServer('thss1.a2hosting.com', 'thss2.a2hosting.com', '%fice%', 'listaccounts');
        return View::make('site/results')->with('results', $output);
    }

    public function RunIcelandReseller() {
        $cp = new CpanelController();
        $output = $cp->SetServer('thsr1.supercp.com', 'thsr1.supercp.com', '%resellice%', 'listaccounts');
        return View::make('site/results')->with('results', $output);
    }

    public function ResellerStats() {
        $cp = new CpanelController();
        $output = $cp->SetServer('sr1.supercp.com', 'sr11.supercp.com', '%reseller%', 'listreseller');
        $output .= "\r\n" . $output = $cp->SetServer('ssr1.supercp.com', 'ssr5.supercp.com', '%turboreseller%', 'listreseller');
        $output .= "\r\n" . $cp->SetServer('thsr1.supercp.com', 'thsr1.supercp.com', '%resellice%', 'listreseller');
        return View::make('site/results')->with('results', $output);
    }

}
