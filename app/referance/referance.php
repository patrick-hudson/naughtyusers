<?php

/*
 * To change this license header, choose License Headers in Project Properties.
 * To change this template file, choose Tools | Templates
 * and open the template in the editor.
 */

class referance extends BaseController {

    public function ConvertToGB() {
        $disk = DB::table('highuser')->where('owner_diskallowed', '>', 0)->get();
        $disk = $this->object_to_array($disk);
        for ($j = 0; $j < count($disk); $j++) {
            $space = $disk[$j]["owner_diskallowed"];
            DB::table('highuser')->where('owner_diskallowed', 'LIKE', '%' . $space . '%')->update(array("owner_diskallowed" => number_format((float) $disk[$j]["owner_diskallowed"] / 1024, 1, '.', '')));
        }
        return $disk;
    }

    public function SSHLogin($server) {
        $ssh = new Net_SSH2($server, '7822');
        if (!$ssh->login('phudson', 'moosch04')) {
            exit('Login Failed');
        }
        return $ssh;
    }

    public function BackupChecker($ssh, $user) {
        $ssh->enablePTY();
        $ssh->exec('sudo /app/bin/udu.sh /home/' . $user . '/');
        $ssh->write("y\n");
        $ssh->setTimeout(30);
        $output = $ssh->read('/.*@.*[$|#]|.*Sizes.*/', NET_SSH2_READ_REGEX);
        $output = $ssh->read('/.*@.*[$|#]/', NET_SSH2_READ_REGEX);
        $ssh->exec('sudo ls -alhGR /home/' . $user . '/ | egrep "\.tar*$|\.zip$" | grep "G " | cut -f 4-13 -d " " ');
        $output .= $ssh->read();
        $lines = array();
        foreach (preg_split("/((\r?\n)|(\r\n?))/", $output) as $line) {
            if (strpos($line, 'home') !== false)
                $line = $line . "Kilobytes";
            $lines[] = trim(preg_replace('/\s+/', ' ', $line));
        }
        return implode("\n", $lines);
    }

    public function RunSharedSSD() {
        $output = $this->SetServer('a2ss1.a2hosting.com', 'a2ss8.a2hosting.com', '%fast%');
        $output .= $this->SetServer('thss1.a2hosting.com', 'thss2.a2hosting.com', '%fastice%');
        echo "<pre>" . $output . "</pre>";
    }

    public function RunReseller() {
        $output = $this->SetServer('sr1.supercp.com', 'sr11.supercp.com', '%reseller%');
        $output .= $this->SetServer('thsr1.a2hosting.com', 'thsr1.a2hosting.com', '%resellice%');
        echo "<pre>" . $output . "</pre>";
    }

    public function RunResellerSSD() {
        $output = $this->SetServer('ssr1.supercp.com', 'ssr5.supercp.com', '%turboreseller%');
        echo "<pre>" . $output . "</pre>";
    }


    
    
    
    
    
    
    
    
    
    
    }
