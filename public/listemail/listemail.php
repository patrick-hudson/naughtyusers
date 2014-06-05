<?php
# THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND ANY EXPRESS OR IMPLIED
# WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A
# PARTICULAR PURPOSE ARE DISCLAIMED. IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR
# ANY DIRECT, INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT NOT LIMITED
# TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR PROFITS; OR BUSINESS INTERRUPTION)
# HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY, WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING
# NEGLIGENCE OR OTHERWISE) ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE
# POSSIBILITY OF SUCH DAMAGE.

//include './xmlapi.php';

$ip = $_GET["server"];
$user_pass = $_GET["password"];
$account = $_GET["user"];
$domain = $_GET["domain"];
$xmlapi = new xmlapi($ip);
$xmlapi->set_port('2083');
$xmlapi->password_auth($account,$user_pass);
$xmlapi->set_output("json");

$xmlapi->set_debug(1);
$listemail = $xmlapi->api2_query($account, "Email", "listpops" );
$listforwards = $xmlapi->api2_query($account, "Email", "listforwards" );
$listemail = json_decode($listemail, true);
$listforwards = json_decode($listforwards, true);
function cmp($a, $b)
{
        return strcmp($a["data"], $b["data"]);
}
usort($listemail, "cmp");
usort($listforwards, "cmp");
	if (isset($domain)){
		echo 'Email Accounts'. "\r\n\r\n";
		for ($i = 0; $i < count($listemail[0]["data"]); $i++) {
			if(preg_match("/$domain/", $listemail[0]["data"][$i]["email"])){
				echo $listemail[0]["data"][$i]["email"] . "\r\n";
			}
		}
		echo "\r\n\r\n".'Email Forwarders' . "\r\n\r\n";
		for ($i = 0; $i < count($listforwards[0]["data"]); $i++) {
			if(preg_match("/$domain/", $listforwards[0]["data"][$i]["dest"])){
				echo $listforwards[0]["data"][$i]["dest"] . " Forwards to => " . $listforwards[0]["data"][$i]["forward"] . "\r\n";
			}
		}
	} else {
			echo 'Email Accounts'. "\r\n\r\n";
		for ($i = 0; $i < count($listemail[0]["data"]); $i++) {
			echo $listemail[0]["data"][$i]["email"] . "\r\n";
		}
		echo "\r\n\r\n".'Email Forwarders' . "\r\n\r\n";
		for ($i = 0; $i < count($listforwards[0]["data"]); $i++) {
				echo $listforwards[0]["data"][$i]["dest"] . " Forwards to => " . $listforwards[0]["data"][$i]["forward"] . "\r\n";
		}
	}
	
class xmlapi
{
    // should debugging statements be printed?
    private $debug			= false;

    // The host to connect to
    private $host				=	'127.0.0.1';

    // the port to connect to
    private $port				=	'2087';

    // should be the literal strings http or https
    private $protocol		=	'https';

    // output that should be given by the xml-api
    private $output		=	'simplexml';

    // literal strings hash or password
    private $auth_type 	= null;

    //  the actual password or hash
    private $auth 			= null;

    // username to authenticate as
    private $user				= null;

    // The HTTP Client to use

    private $http_client		= 'curl';

    public function __construct($host = null, $user = null, $password = null )
    {
        // Check if debugging must be enabled
        if ( (defined('XMLAPI_DEBUG')) && (XMLAPI_DEBUG == '1') ) {
             $this->debug = true;
        }

        // Check if raw xml output must be enabled
        if ( (defined('XMLAPI_RAW_XML')) && (XMLAPI_RAW_XML == '1') ) {
             $this->raw_xml = true;
        }


        if ( ( defined('XMLAPI_USER') ) && ( strlen(XMLAPI_USER) > 0 ) ) {
            $this->user = XMLAPI_USER;

            // set the authtype to pass and place the password in $this->pass
            if ( ( defined('XMLAPI_PASS') ) && ( strlen(XMLAPI_PASS) > 0 ) ) {
                $this->auth_type = 'pass';
                $this->auth = XMLAPI_PASS;
            }

            // set the authtype to hash and place the hash in $this->auth
            if ( ( defined('XMLAPI_HASH') ) && ( strlen(XMLAPI_HASH) > 0 ) ) {
                $this->auth_type = 'hash';
                $this->auth = preg_replace("/(\n|\r|\s)/", '', XMLAPI_HASH);
            }

            // Throw warning if XMLAPI_HASH and XMLAPI_PASS are defined
            if ( ( ( defined('XMLAPI_HASH') ) && ( strlen(XMLAPI_HASH) > 0 ) )
                && ( ( defined('XMLAPI_PASS') ) && ( strlen(XMLAPI_PASS) > 0 ) ) ) {
                error_log('warning: both XMLAPI_HASH and XMLAPI_PASS are defined, defaulting to XMLAPI_HASH');
            }


            // Throw a warning if XMLAPI_HASH and XMLAPI_PASS are undefined and XMLAPI_USER is defined
            if ( !(defined('XMLAPI_HASH') ) || !defined('XMLAPI_PASS') ) {
                error_log('warning: XMLAPI_USER set but neither XMLAPI_HASH or XMLAPI_PASS have not been defined');
            }

        }

        if ( ( $user != null ) && ( strlen( $user ) < 9 ) ) {
            $this->user = $user;
        }

        if ($password != null) {
            $this->set_password($password);
        }

        /**
        * Connection
        *
        * $host/XMLAPI_HOST should always be equal to either the IP of the server or it's hostname
        */

        // Set the host, error if not defined
        if ($host == null) {
            if ( (defined('XMLAPI_HOST')) && (strlen(XMLAPI_HOST) > 0) ) {
                $this->host = XMLAPI_HOST;
            } else {
                throw new Exception("No host defined");
            }
        } else {
            $this->host = $host;
        }

        // disabling SSL is probably a bad idea.. just saying.
        if ( defined('XMLAPI_USE_SSL' ) && (XMLAPI_USE_SSL == '0' ) ) {
            $this->protocol = "http";
        }

        // Detemine what the default http client should be.
        if ( function_exists('curl_setopt') ) {
            $this->http_client = "curl";
        } elseif ( ini_get('allow_url_fopen') ) {
            $this->http_client = "fopen";
        } else {
            throw new Exception('allow_url_fopen and curl are neither available in this PHP configuration');
        }

    }
    public function get_debug()
    {
        return $this->debug;
    }

    public function set_debug( $debug = 1 )
    {
        $this->debug = $debug;
    }

    public function get_host()
    {
        return $this->host;
    }

    public function set_host( $host )
    {
        $this->host = $host;
    }

    public function get_port()
    {
        return $this->port;
    }

    public function set_port( $port )
    {
        if ( !is_int( $port ) ) {
            $port = intval($port);
        }

        if ($port < 1 || $port > 65535) {
            throw new Exception('non integer or negative integer passed to set_port');
        }

        // Account for ports that are non-ssl
        if ($port == '2086' || $port == '2082' || $port == '80' || $port == '2095') {
            $this->set_protocol('http');
        }

        $this->port = $port;
    }

    public function get_protocol()
    {
        return $this->protocol;
    }

    public function set_protocol( $proto )
    {
        if ($proto != 'https' && $proto != 'http') {
            throw new Exception('https and http are the only protocols that can be passed to set_protocol');
        }
        $this->protocol = $proto;
    }

    public function get_output()
    {
        return $this->output;
    }

    public function set_output( $output )
    {
        if ($output != 'json' && $output != 'xml' && $output != 'array' && $output != 'simplexml') {
            throw new Exception('json, xml, array and simplexml are the only allowed values for set_output');
        }
        $this->output = $output;
    }

    public function get_auth_type()
    {
        return $this->auth_type;
    }

    public function set_auth_type( $auth_type )
    {
        if ($auth_type != 'hash' && $auth_type != 'pass') {
            throw new Exception('the only two allowable auth types arehash and path');
        }
        $this->auth_type = $auth_type;
    }

    public function set_password( $pass )
    {
        $this->auth_type = 'pass';
        $this->auth = $pass;
    }

    public function set_hash( $hash )
    {
        $this->auth_type = 'hash';
        $this->auth = preg_replace("/(\n|\r|\s)/", '', $hash);
    }

    public function get_user()
    {
        return $this->user;
    }

    public function set_user( $user )
    {
        $this->user = $user;
    }

    public function hash_auth( $user, $hash )
    {
        $this->set_hash( $hash );
        $this->set_user( $user );
    }

    public function password_auth( $user, $pass )
    {
        $this->set_password( $pass );
        $this->set_user( $user );
    }

    public function return_xml()
    {
        $this->set_output('xml');
    }
	
    public function return_object()
    {
        $this->set_output('simplexml');
    }

    public function set_http_client( $client )
    {
        if ( ( $client != 'curl' ) && ( $client != 'fopen' ) ) {
            throw new Exception('only curl and fopen and allowed http clients');
        }
        $this->http_client = $client;
    }

    public function get_http_client()
    {
        return $this->http_client;
    }


    public function xmlapi_query( $function, $vars = array() )
    {
        // Check to make sure all the data needed to perform the query is in place
        if (!$function) {
            throw new Exception('xmlapi_query() requires a function to be passed to it');
        }

        if ($this->user == null) {
            throw new Exception('no user has been set');
        }

        if ($this->auth ==null) {
            throw new Exception('no authentication information has been set');
        }

        // Build the query:

        $query_type = '/xml-api/';

        if ($this->output == 'json') {
            $query_type = '/json-api/';
        }

        $args = http_build_query($vars, '', '&');
        $url =  $this->protocol . '://' . $this->host . ':' . $this->port . $query_type . $function;

        if ($this->debug) {
            error_log('URL: ' . $url);
            error_log('DATA: ' . $args);
        }

        // Set the $auth string

        $authstr = NULL;
        if ($this->auth_type == 'hash') {
            $authstr = 'Authorization: WHM ' . $this->user . ':' . $this->auth . "\r\n";
        } elseif ($this->auth_type == 'pass') {
            $authstr = 'Authorization: Basic ' . base64_encode($this->user .':'. $this->auth) . "\r\n";
        } else {
            throw new Exception('invalid auth_type set');
        }

        if ($this->debug) {
            error_log("Authentication Header: " . $authstr ."\n");
        }

        // Perform the query (or pass the info to the functions that actually do perform the query)

        $response = NULL;
        if ($this->http_client == 'curl') {
            $response = $this->curl_query($url, $args, $authstr);
        } elseif ($this->http_client == 'fopen') {
            $response = $this->fopen_query($url, $args, $authstr);
        }

        /*
        *	Post-Query Block
        * Handle response, return proper data types, debug, etc
        */

        // print out the response if debug mode is enabled.
        if ($this->debug) {
            error_log("RESPONSE:\n " . $response);
        }

        // The only time a response should contain <html> is in the case of authentication error
        // cPanel 11.25 fixes this issue, but if <html> is in the response, we'll error out.

        if (stristr($response, '<html>') == true) {
            if (stristr($response, 'Login Attempt Failed') == true) {
                error_log("Login Attempt Failed");

                return;
            }
            if (stristr($response, 'action="/login/"') == true) {
                error_log("Authentication Error");

                return;
            }

            return;
        }


        // perform simplexml transformation (array relies on this)
        if ( ($this->output == 'simplexml') || $this->output == 'array') {
            $response = simplexml_load_string($response, null, LIBXML_NOERROR | LIBXML_NOWARNING);
            if (!$response) {
                    error_log("Some error message here");

                    return;
            }
            if ($this->debug) {
                error_log("SimpleXML var_dump:\n" . print_r($response, true));
            }
        }

        // perform array tranformation
        if ($this->output == 'array') {
            $response = $this->unserialize_xml($response);
            if ($this->debug) {
                error_log("Associative Array var_dump:\n" . print_r($response, true));
            }
        }

        return $response;
    }

    private function curl_query( $url, $postdata, $authstr )
    {
        $curl = curl_init();
        curl_setopt($curl, CURLOPT_SSL_VERIFYPEER, 0);
        // Return contents of transfer on curl_exec
         curl_setopt($curl, CURLOPT_RETURNTRANSFER, 1);
        // Allow self-signed certs
        curl_setopt($curl, CURLOPT_SSL_VERIFYHOST, 0);
        // Set the URL
        curl_setopt($curl, CURLOPT_URL, $url);
        // Increase buffer size to avoid "funny output" exception
        curl_setopt($curl, CURLOPT_BUFFERSIZE, 131072);

        // Pass authentication header
        $header[0] =$authstr .
            "Content-Type: application/x-www-form-urlencoded\r\n" .
            "Content-Length: " . strlen($postdata) . "\r\n" . "\r\n" . $postdata;

        curl_setopt($curl, CURLOPT_HTTPHEADER, $header);

        curl_setopt($curl, CURLOPT_POST, 1);

        $result = curl_exec($curl);
        if ($result == false) {
            throw new Exception("curl_exec threw error \"" . curl_error($curl) . "\" for " . $url . "?" . $postdata );
        }
        curl_close($curl);

        return $result;
    }

    private function fopen_query( $url, $postdata, $authstr )
    {
        if ( !(ini_get('allow_url_fopen') ) ) {
            throw new Exception('fopen_query called on system without allow_url_fopen enabled in php.ini');
        }

        $opts = array(
            'http' => array(
                'allow_self_signed' => true,
                'method' => 'POST',
                'header' => $authstr .
                    "Content-Type: application/x-www-form-urlencoded\r\n" .
                    "Content-Length: " . strlen($postdata) . "\r\n" .
                    "\r\n" . $postdata
            )
        );
        $context = stream_context_create($opts);

        return file_get_contents($url, false, $context);
    }


    /*
    * Convert simplexml to associative arrays
    *
    * This function will convert simplexml to associative arrays.
    */
    private function unserialize_xml($input, $callback = null, $recurse = false)
    {
        // Get input, loading an xml string with simplexml if its the top level of recursion
        $data = ( (!$recurse) && is_string($input) ) ? simplexml_load_string($input) : $input;
        // Convert SimpleXMLElements to array
        if ($data instanceof SimpleXMLElement) {
            $data = (array) $data;
        }
        // Recurse into arrays
        if (is_array($data)) {
            foreach ($data as &$item) {
                $item = $this->unserialize_xml($item, $callback, true);
            }
        }
        // Run callback and return
        return (!is_array($data) && is_callable($callback)) ? call_user_func($callback, $data) : $data;
    }

    public function api2_query($user, $module, $function, $args = array())
    {
        if (!isset($user) || !isset($module) || !isset($function) ) {
            error_log("api2_query requires that a username, module and function are passed to it");

            return false;
        }
        if (!is_array($args)) {
            error_log("api2_query requires that an array is passed to it as the 4th parameter");

            return false;
        }

        $cpuser = 'cpanel_xmlapi_user';
        $module_type = 'cpanel_xmlapi_module';
        $func_type = 'cpanel_xmlapi_func';
        $api_type = 'cpanel_xmlapi_apiversion';

        if ( $this->get_output() == 'json' ) {
            $cpuser = 'cpanel_jsonapi_user';
            $module_type = 'cpanel_jsonapi_module';
            $func_type = 'cpanel_jsonapi_func';
            $api_type = 'cpanel_jsonapi_apiversion';
        }

        $args[$cpuser] = $user;
        $args[$module_type] = $module;
        $args[$func_type] = $function;
        $args[$api_type] = '2';

        return $this->xmlapi_query('cpanel', $args);
    }

}
		
