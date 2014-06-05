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
  |	Route::get('/', 'HomeController@showWelcome');
  |
 */

class HomeController extends BaseController {
    public function __construct() {
        // Closure as callback
        $serverfile = fopen($filename = base_path() . '/app/storage/servers.txt', 'c') or die("Can't create file. Make sure app/storage is set to 777");
        $badpeeps = fopen($filename = base_path() . '/app/storage/badpeeps.txt' , 'c') or die("Can't create file, Make sure app/storage is set to 777");
        fclose($serverfile);
        fclose($badpeeps);
    }
    public function showWelcome() {
        return View::make('home');
    }

    public function showDashboard() {
        //Get servers for drop downlists
        $servers = DB::table('servers')->select('url')->get();
        $servers = json_encode($servers);
        //get most recent report date
        $date = DB::table('highuser')
                ->select('reportran_at')
                ->orderBy('reportran_at', 'desc')
                ->first();
        $date = $this->objectToArray($date);
        $date = date("Y-m-d", strtotime($date["reportran_at"]));
        //get users from last report and eventually return the worst user
        $users = DB::table('highuser')
                ->select('server', 'username', 'reportran_at', 'diskspace')
                ->orderBy('diskspace', 'desc')
                ->where('reportran_at', 'LIKE', "%" . $date . "%")
                ->get();
        $users = $this->objectToArray($users);
        //sort servers to find the worst offender
        $sortserver = array();
        for ($i = 0; $i < count($users); $i++) {
            $sortserver[$i] = $users[$i]["server"];
        }
        sort($sortserver);
        $c = array_count_values($sortserver);
        $val = array_search(max($c), $c);
        //get server disk space
        $reportdates = DB::select(DB::raw('SELECT DISTINCT DATE_FORMAT(reportran_at, "%Y-%m-%d") AS reportran_at FROM highuser'));
        $reportdates = $this->objectToArray($reportdates);
        $reportdates = json_encode($reportdates);
        //share variables and return view
        $return = array('servers' => $servers, 'users' => $users, 'date' => $date, 'badserver' => $val, 'acctbadserver' => max($c), 'reportdates' => $reportdates);
        return View::make('dashboard', array('return' => $return));
    }

    public function showSecret() {
        return View::make('secret');
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

}
