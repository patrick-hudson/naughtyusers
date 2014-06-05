<?php

class CpanelController extends Controller {

    public function __construct() {
        // Closure as callback
        $serverfile = fopen($filename = base_path() . '/app/storage/servers.txt', 'c') or die("Can't create file");
        $badpeeps = fopen($filename = base_path() . '/app/storage/badpeeps.txt' , 'c') or die("Can't create file");
        fclose($serverfile);
        fclose($badpeeps);
    }

    public $finalreturn = "";

    /*
     * YOU MUST SET YOUR ROOT USERNAME AND PASSWORD HERE!
     */

    public function DoLogin($host) {
        //PasswordController() place root password in this file so you don't accidentally git commit it (whoops)
        $rootpassword = new PasswordController();
        Cpanel::set_user("root");
        Cpanel::set_password($rootpassword->RootPassword());
        Cpanel::set_host($host);
    }

    /*
     * Set your servers here. Keep the array format the same. Server type => Start server => End Server
     * 
     * NOTE - THIS WILL REQUIRE MODIFICATION SHOULD YOU DECIDE TO RUN THIS ON A NON-A2HOSTING server.
     */

    public function ServerList() {
        $prefix = $this->Prefix();
        $serveroptions = array(
            "shared" => array('start' => 'a2s26.a2hosting.com', 'end' => 'a2s85.a2hosting.com'),
            "icelandshared" => array('start' => 'ths1.a2hosting.com', 'end' => 'ths1.a2hosting.com'),
            "sharedssd" => array('start' => 'a2ss1.a2hosting.com', 'end' => 'a2ss9.a2hosting.com'),
            "icelandsharedssd" => array('start' => 'thss1.a2hosting.com', 'end' => 'thss2.a2hosting.com'),
            "reseller" => array('start' => 'sr1.supercp.com', 'end' => 'sr12.supercp.com'),
            "icelandreseller" => array('start' => 'thsr1.supercp.com', 'end' => 'thsr1.supercp.com'),
            "resellerssd" => array('start' => 'ssr1.supercp.com', 'end' => 'ssr6.supercp.com')
        );
        return $serveroptions;
    }

    public function SharedDomain() {
        return ".a2hosting.com";
    }

    public function ResellerDomain() {
        return ".supercp.com";
    }

    public function Prefix() {
        $serverprefixes = array(
            "shared" => "a2s",
            "icelandshared" => "ths",
            "sharedssd" => "a2ss",
            "icelandsharedssd" => "thss",
            "reseller" => "sr",
            "icelandreseller" => "thsr",
            "resellerssd" => "ssr"
        );
        return $serverprefixes;
    }

    public function SetServer($start, $end, $type, $method) {
        //Start server, end server, type is set via an artisan command (hint: look in commands/reseller or shared.)
        try {
            //laravel doesn't have a query builder for regex. So I made my own.
            $server = DB::table('servers')->whereRaw("name regexp '$type'")->get();
            //get servers from database
            //convert object to array
            $server = $this->object_to_array($server);
            //find start and stop servers to search for and get their array indexes
            $start = $this->searchForId($start, $server);
            $end = $this->searchForId($end, $server);
            //are we listing the naughty users, or getting statistics for our resellers?
            if ($method == "listaccounts") {
                return $this->GetListAccounts($server, $start, $end);
            }
            if ($method == "listreseller") {
                return $this->GetResellerStatistics($server, $start, $end);
            }
        } catch (Exception $e) {
            return 'Exception: ' . $e->getTraceAsString();
        }
    }

    public function searchForId($id, $array) {
        foreach ($array as $key => $val) {
            if ($val['url'] == $id) {
                return $key;
            }
        }
        return null;
    }

    //laravel returns all database arrays in the Object form. I hate objects. Lets convert them to arrays instead.
    public function object_to_array($obj) {
        $_arr = is_object($obj) ? get_object_vars($obj) : $obj;
        foreach ($_arr as $key => $val) {
            $val = (is_array($val) || is_object($val)) ? $this->object_to_array($val) : $val;
            $arr[$key] = $val;
        }
        return $arr;
    }

    //test our login speed. Yes we technically login twice, but this way we don't waste 30 minutes waiting for listaccounts() to return.
    public function DoLoginSpeed($host) {
        $rootpassword = new PasswordController();
        $ch = curl_init();
        curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
        curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0);
        $rootpassword = new PasswordController();
        curl_setopt($ch, CURLOPT_URL, 'https://' . $host . ':2087');
        curl_setopt($ch, CURLOPT_HEADER, 0);
        $authstr = 'Basic ' . base64_encode("root" . ':' . $rootpassword->RootPassword());
        $curlheaders[0] = '' . 'Authorization: ' . $authstr;
        curl_setopt($ch, CURLOPT_HTTPHEADER, $curlheaders);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_exec($ch);
        $info = curl_getinfo($ch);
        curl_close($ch);
        return $info['total_time'];
    }

    public function JsonResponse() {
        $filename = base_path() . '/app/storage/servers.txt';
        $contents = file($filename);
        foreach ($contents as $line) {
            echo $line . ',';
        }
    }

    public function JsonResponseSize() {
        $filename = base_path() . '/app/storage/badpeeps.txt';
        $contents = file($filename);
        foreach ($contents as $line) {
            echo $line . ',';
        }
    }

    public function getListAccounts($server, $start, $end) {
        try {
            $time = "";
            //loop through each server and return list accounts
            $timestamp = new DateTime();
            $timestamp = $timestamp->format('Y-m-d H:i:s');
            //get our prefix we set earlier
            $prefix = $this->Prefix();
            for ($j = $server[$start]['id']; $j <= $server[$end]['id']; $j++) {
                $count = 0;
                $json_array = "";
                //what cluster are we searching MORE REGEX!!!
                switch (true) {
                    case (preg_match('/^shared[0-9]+$/', $server[$start]['name'])):
                        $host = $prefix['shared'] . $j . $this->SharedDomain();
                        break;
                    case (preg_match('/^icelandshared[0-9]+$/', $server[$start]['name'])):
                        $host = $prefix['icelandshared'] . $j . $this->SharedDomain();
                        break;
                    case (preg_match('/^sharedssd[0-9]+$/', $server[$start]['name'])):
                        $host = $prefix['sharedssd'] . $j . $this->SharedDomain();
                        break;
                    case (preg_match('/^icelandsharedssd[0-9]+$/', $server[$start]['name'])):
                        $host = $prefix['icelandsharedssd'] . $j . $this->SharedDomain();
                        break;
                    case (preg_match('/^reseller[0-9]+$/', $server[$start]['name'])):
                        $host = $prefix['reseller'] . $j . $this->ResellerDomain();
                        break;
                    case (preg_match('/^icelandreseller[0-9]+$/', $server[$start]['name'])):
                        $host = $prefix['icelandreseller'] . $j . $this->ResellerDomain();
                        break;
                    case (preg_match('/^resellerssd[0-9]+$/', $server[$start]['name'])):
                        $host = $prefix['resellerssd'] . $j . $this->ResellerDomain();
                        break;
                    default:
                        break;
                }
                //find index of where we are searching
                $hostid = $this->searchForId($host, $server);
                //find array index for $host in array $server
                $speed = $this->DoLoginSpeed($server[$hostid]['url']);
                //does it take longer than 25 seconds to query a selected server? If yes, skip server, log the skip and move on.
                if ($speed > 25) {
                    $time .= $server[$hostid]['url'] . " took too long to respond " . $speed . "\r\n";
                    DB::table('failservers')->insert(
                            array(
                                'server' => $server[$hostid]['url'],
                                'timestamp' => $timestamp
                    ));
                    //log server server, time, and the number of naughty users on each server to a file so our jQuery Chart can read it.
                    $file = base_path() . '/app/storage/servers.txt';
                    $current = file_get_contents($file);
                    $current .= $server[$hostid]['url'] . " took too long to respond" . PHP_EOL;
                    file_put_contents($file, $current);

                    $file2 = base_path() . '/app/storage/badpeeps.txt';
                    $current2 = file_get_contents($file2);
                    $current2 .= 0 . PHP_EOL;
                    file_put_contents($file2, $current2);
                    continue;
                }
                $time .= $server[$hostid]['url'] . " took " . $speed . "\r\n";
                $this->DoLogin($server[$hostid]['url']);
                //login and run the list accounts on server.
                $json_array = json_decode(Cpanel::listaccts(), true);
                if (isset($json_array['acct'])) {
                    for ($i = 0; $i < count($json_array['acct']); $i++) {
                        //remove the letter M from results (math with letters is no bueno)
                        $json_array['acct'][$i]['diskused'] = str_replace("M", "", $json_array['acct'][$i]['diskused']);
                        //only log users with more than 20GB
                        if ($json_array['acct'][$i]['diskused'] > 20480) {
                            $count++;
                            $return[$i] = array(
                                'user' => $json_array['acct'][$i]['user'],
                                'diskused' => number_format((float) $json_array['acct'][$i]['diskused'] / 1024),
                                'created' => $json_array['acct'][$i]['unix_startdate'],
                                'owner' => $json_array['acct'][$i]['owner']
                            );
                            //some servers have resellers on them, lets go ahead and get the owner if not root, and get the owners disk quota and disk used.
                            $reseller_array = json_decode(Cpanel::resellerstats($return[$i]['owner']), true);
                            if ($return[$i]['owner'] != "root" && $reseller_array["result"]["statusmsg"] != "Reseller Does Not Exist") {
                                $reseller = array(
                                    'resellerdiskused' => number_format((float) $reseller_array["result"]["diskused"] / 1024),
                                    'resellerdiskquota' => number_format((float) $reseller_array["result"]["diskquota"] / 1024)
                                );
                            } else {
                                $reseller = array(
                                    'resellerdiskused' => 0,
                                    'resellerdiskquota' => 0
                                );
                            }
                            //get the account creation date. Really kinda useless, but you never know.
                            $created = date('Y-m-d H:i:s', $return[$i]['created']);
                            DB::table('highuser')->insert(
                                    array(
                                        'server' => $host,
                                        'username' => $return[$i]['user'],
                                        'diskspace' => $return[$i]['diskused'],
                                        'reportran_at' => $timestamp,
                                        'created_at' => $created,
                                        'owner_diskspace' => $reseller['resellerdiskused'],
                                        'owner_diskallowed' => $reseller['resellerdiskquota'],
                                        'owner' => $return[$i]['owner'])
                            );
                        }
                    }
                    //once we're done, lets go ahead and log the time each server took along with how many naughty users there were. Also time stamp it for fun.
                    if ($count != 0) {
                        DB::table('response')->insert(
                                array(
                                    'server' => $server[$hostid]['url'] . " took " . number_format((float) $speed, 2, '.', '') . " seconds to respond",
                                    'badusers' => $count,
                                    'timestamp' => $timestamp
                        ));
                        //log server response time to a file, that way we are able to pull it from a file using jQuery and json to give you a pretty results page.
                        $file = base_path() . '/app/storage/servers.txt';
                        $current = file_get_contents($file);
                        $current .= $server[$hostid]['url'] . " took " . number_format((float) $speed, 2, '.', '') . " seconds to respond" . PHP_EOL;
                        file_put_contents($file, $current);

                        $file2 = base_path() . '/app/storage/badpeeps.txt';
                        $current2 = file_get_contents($file2);
                        $current2 .= $count . PHP_EOL;
                        file_put_contents($file2, $current2);
                    } else {
                        //if the server took too long to respond? Lets log that in the file and the database
                        DB::table('response')->insert(
                                array(
                                    'server' => $server[$hostid]['url'] . " took " . number_format((float) $speed, 2, '.', '') . " seconds to respond",
                                    'badusers' => 0,
                                    'timestamp' => $timestamp
                        ));

                        $file = base_path() . '/app/storage/servers.txt';
                        $current = file_get_contents($file);
                        $current .= $server[$hostid]['url'] . " took " . number_format((float) $speed, 2, '.', '') . " seconds to respond" . PHP_EOL;
                        file_put_contents($file, $current);

                        $file2 = base_path() . '/app/storage/badpeeps.txt';
                        $current2 = file_get_contents($file2);
                        $current2 .= 0 . PHP_EOL;
                        file_put_contents($file2, $current2);
                    }
                }
            }
            //all good? Lets return the server reponse time
            return $time;
        } catch (Exception $e) {
            return 'Exception: ' . $e->getMessage();
        }
    }

    public function GetResellerStatistics($server, $start, $end) {
        //Reseller Stats, account owner, how many sub accounts are owned, and the total size of all the accounts. Useful for moving resellers
        //WARNING - This grabs ALL reseller servers on A2's network. This isn't resource intesive but it takes time.
        try {
            $time = "";
            //loop through each server and return list accounts
            $timestamp = new DateTime();
            $timestamp = $timestamp->format('Y-m-d H:i:s');
            for ($j = $server[$start]['id']; $j <= $server[$end]['id']; $j++) {
                $count = 0;
                $json_array = "";
                //what cluster are we searching - breaking out the lovely regex.
                switch (true) {
                    case (preg_match('/^icelandreseller[0-9]+$/', $server[$start]['name'])):
                        $host = 'thsr' . $j . '.supercp.com';
                        break;
                    case (preg_match_all('/^reseller[0-9]+$/', $server[$start]['name'])):
                        $host = 'sr' . $j . '.supercp.com';
                        break;
                    case (preg_match('/^resellerssd[0-9]+$/', $server[$start]['name'])):
                        $host = 'ssr' . $j . '.supercp.com';
                        break;
                    default:
                        break;
                }
                echo $host;
                echo PHP_EOL;
                //find index of where we are searching
                $hostid = $this->searchForId($host, $server);
                //find array index for $host in array $server
                $speed = $this->DoLoginSpeed($server[$hostid]['url']);
                //pretty much the same as ListAccounts. 
                if ($speed > 25) {
                    $time .= $server[$hostid]['url'] . " took too long to respond " . $speed . "\r\n";
                    DB::table('failservers')->insert(
                            array(
                                'server' => $server[$hostid]['url'],
                                'timestamp' => $timestamp
                    ));
                    $file = base_path() . '/app/storage/servers.txt';
                    $current = file_get_contents($file);
                    $current .= $server[$hostid]['url'] . " took too long to respond" . PHP_EOL;
                    file_put_contents($file, $current);

                    $file2 = base_path() . '/app/storage/badpeeps.txt';
                    $current2 = file_get_contents($file2);
                    $current2 .= 0 . PHP_EOL;
                    file_put_contents($file2, $current2);
                    continue;
                }
                $time .= $server[$hostid]['url'] . " took " . $speed . "\r\n";
                $this->DoLogin($server[$hostid]['url']);
                $json_array = json_decode(Cpanel::listresellers(), true);
                for ($i = 0; $i < count($json_array['reseller']); $i++) {
                    $reseller_array = json_decode(Cpanel::resellerstats($json_array['reseller'][$i]), true);
                    $reseller = $reseller_array["result"]["reseller"];
                    $acctcount = count($reseller_array["result"]["accts"]);
                    $diskusage = number_format((float) $reseller_array["result"]["diskused"] / 1024);
                    DB::table('resellerstats')->insert(
                            array(
                                'server' => $host,
                                'reseller' => $reseller,
                                'number_of_accounts' => $acctcount,
                                'diskspace_in_gb' => $diskusage,
                                'reportran_at' => $timestamp)
                    );
                }
                $file = base_path() . '/app/storage/servers.txt';
                $current = file_get_contents($file);
                $current .= $server[$hostid]['url'] . " took too long to respond" . PHP_EOL;
                file_put_contents($file, $current);

                $file2 = base_path() . '/app/storage/badpeeps.txt';
                $current2 = file_get_contents($file2);
                $current2 .= 0 . PHP_EOL;
                file_put_contents($file2, $current2);
            }
            return $time;
        } catch (Exception $e) {
            return 'Exception: ' . $e->getTraceAsString();
        }
    }

    public function AddServers() {
        try {
            //when adding new servers, we truncate old server table (No need to keep it)
            DB::table('servers')->delete();
            //grabs server list, prefix (ex a2s) and grabs domain (ex a2hosting.com)
            $servers = $this->ServerList();
            $prefix = $this->Prefix();
            $shareddomain = $this->SharedDomain();
            $resellerdomain = $this->ResellerDomain();
            //converts server start/end array into a usable index based array
            $serverarray = (array_keys($servers));
            //Initial loop to start, we have 7 catagories of servers to add, so it loops 7 times.
            for ($i = 0; $i < count($servers); $i++) {
                //a2hosting specific. Takes a2ss1.a2hosting.com and turns it into a usable server number. Returns 1 in this case.
                $dstart = explode('.', $servers[$serverarray[$i]]["start"]);
                preg_match("/(\d+)(?!.*\d)/", $dstart[0], $startarr);
                $dend = explode('.', $servers[$serverarray[$i]]["end"]);
                preg_match("/(\d+)(?!.*\d)/", $dend[0], $endarr);
                //standard switch to filter out each catagory and add them to the database for later use.
                for ($j = $startarr[0]; $j <= $endarr[0]; $j++) {
                    switch (true) {
                        case (preg_match('/^shared$/', array_keys($servers)[$i]));
                            $host = $prefix['shared'] . $j . $shareddomain;
                            $ip = gethostbyname($host);
                            echo $host . " " . $ip . " " . " " . array_keys($servers)[$i] . $j;
                            echo PHP_EOL;
                            DB::table('servers')->insert(
                                    array('id' => $j, 'name' => "shared" . $j, 'url' => $host, 'ip' => $ip)
                            );
                            break;
                        case (preg_match('/^icelandshared$/', array_keys($servers)[$i]));
                            $host = $prefix["icelandshared"] . $j . $shareddomain;
                            $ip = gethostbyname($host);
                            echo $host . " " . $ip . " " . " " . array_keys($servers)[$i] . $j;
                            echo PHP_EOL;
                            DB::table('servers')->insert(
                                    array('id' => $j, 'name' => "icelandshared" . $j, 'url' => $host, 'ip' => $ip)
                            );
                            break;
                        case (preg_match('/^sharedssd$/', array_keys($servers)[$i]));
                            $host = $prefix["sharedssd"] . $j . $shareddomain;
                            $ip = gethostbyname($host);
                            echo $host . " " . $ip . " " . " " . array_keys($servers)[$i] . $j;
                            echo PHP_EOL;
                            DB::table('servers')->insert(
                                    array('id' => $j, 'name' => "sharedssd" . $j, 'url' => $host, 'ip' => $ip)
                            );
                            break;
                        case (preg_match('/^icelandsharedssd$/', array_keys($servers)[$i]));
                            $host = $prefix["icelandsharedssd"] . $j . $shareddomain;
                            $ip = gethostbyname($host);
                            echo $host . " " . $ip . " " . " " . array_keys($servers)[$i] . $j;
                            echo PHP_EOL;
                            DB::table('servers')->insert(
                                    array('id' => $j, 'name' => "icelandsharedssd" . $j, 'url' => $host, 'ip' => $ip)
                            );
                            break;
                        case (preg_match('/^reseller$/', array_keys($servers)[$i]));
                            $host = $prefix["reseller"] . $j . $resellerdomain;
                            $ip = gethostbyname($host);
                            echo $host . " " . $ip . " " . " " . array_keys($servers)[$i] . $j;
                            echo PHP_EOL;
                            DB::table('servers')->insert(
                                    array('id' => $j, 'name' => "reseller" . $j, 'url' => $host, 'ip' => $ip)
                            );
                            break;
                        case (preg_match('/^icelandreseller$/', array_keys($servers)[$i]));
                            $host = $prefix["icelandreseller"] . $j . $resellerdomain;
                            $ip = gethostbyname($host);
                            echo $host . " " . $ip . " " . " " . array_keys($servers)[$i] . $j;
                            echo PHP_EOL;
                            DB::table('servers')->insert(
                                    array('id' => $j, 'name' => "icelandreseller" . $j, 'url' => $host, 'ip' => $ip)
                            );
                            break;
                        case (preg_match('/^resellerssd$/', array_keys($servers)[$i]));
                            $host = $prefix["resellerssd"] . $j . $resellerdomain;
                            $ip = gethostbyname($host);
                            echo $host . " " . $ip . " " . " " . array_keys($servers)[$i] . $j;
                            echo PHP_EOL;
                            DB::table('servers')->insert(
                                    array('id' => $j, 'name' => "resellerssd" . $j, 'url' => $host, 'ip' => $ip)
                            );
                            break;
                        default:
                            break;
                    }
                }
            }
        } catch (Exception $e) {
            return 'Exception: ' . $e->getMessage();
        }
    }

//That's the meat of it folks.
}


