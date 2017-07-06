<?php

// Elasticsearch PHP
//    require 'vendor/autoload.php';
//    use Elasticsearch\ClientBuilder;
//    $client = Elasticsearch\ClientBuilder::create()->build();

// Define Data
    $name = 'kibana.gif';
    $fp = fopen($name, 'rb');
    header("Content-Type: image/gif");
    header("Content-Length: " . filesize($name));
    fpassthru($fp);

    date_default_timezone_set("Asia/Bangkok");
    $Date = date("c");
    $Time = date("H:i:s");
    $user_agent = $_SERVER['HTTP_USER_AGENT'];

// GET IP
    $IP = getenv('HTTP_CLIENT_IP')?:
        getenv('HTTP_X_FORWARDED_FOR')?:
        getenv('HTTP_X_FORWARDED')?:
        getenv('HTTP_FORWARDED_FOR')?:
        getenv('HTTP_FORWARDED')?:
        getenv('REMOTE_ADDR');

// GET OS
    function getOS() {
	global $user_agent;
	$os_platform = "Unknown OS Platform";
	$os_array = array(
			'/windows nt 10/i'      => 'Windows 10',
			'/windows nt 6.3/i'     => 'Windows 8.1',
			'/windows nt 6.2/i'     => 'Windows 8',
			'/windows nt 6.1/i'     => 'Windows 7',
			'/windows nt 6.0/i'     => 'Windows Vista',
			'/windows nt 5.2/i'     => 'Windows Server 2003/XP x64',
			'/windows nt 5.1/i'     => 'Windows XP',
			'/windows xp/i'         => 'Windows XP',
			'/windows nt 5.0/i'     => 'Windows 2000',
			'/windows me/i'         => 'Windows ME',
			'/win98/i'              => 'Windows 98',
			'/win95/i'              => 'Windows 95',
			'/win16/i'              => 'Windows 3.11',
			'/macintosh|mac os x/i' => 'Mac OS X',
			'/mac_powerpc/i'        => 'Mac OS 9',
			'/linux/i'              => 'Linux',
			'/ubuntu/i'             => 'Ubuntu',
			'/iphone/i'             => 'iPhone',
			'/ipod/i'               => 'iPod',
			'/ipad/i'               => 'iPad',
			'/android/i'            => 'Android',
			'/blackberry/i'         => 'BlackBerry',
			'/webos/i'              => 'Mobile'
	);
	foreach ($os_array as $regex => $value) {
		if (preg_match($regex, $user_agent)) {
			$os_platform = $value;
		}
	}
	return $os_platform;
}

//GET BROWSER
    function getBrowser() {
	global $user_agent;
	$browser = "Unknown Browser";
	$browser_array = array(
			'/msie/i'      => 'Internet Explorer',
			'/firefox/i'   => 'Firefox',
			'/safari/i'    => 'Safari',
			'/chrome/i'    => 'Chrome',
			'/opera/i'     => 'Opera',
			'/netscape/i'  => 'Netscape',
			'/maxthon/i'   => 'Maxthon',
			'/konqueror/i' => 'Konqueror',
			'/mobile/i'    => 'Handheld Browser'
	);
	foreach ($browser_array as $regex => $value) {
		if (preg_match($regex, $user_agent)) {
			$browser = $value;
		}
	}
	return $browser;
}

// PHP CURL
    $host = 'localhost';
    $port = '9200';
    $username = "root";
    $password = "rootpassword";
    $index = 'webstat';
    $type = 'nstdaweb';

    session_start();

    $type = isset($_GET["s"]) ? $_GET["s"] : "nstdaweb";
    $System = isset($_GET["s"]) ? $_GET["s"] : "nstdaweb";
    $OS = getOS();
    $Color = isset($_GET["c"]) ? $_GET["c"] : "";
    $Browser = getBrowser();
    $Res = $_GET["w"] ."x". $_GET["h"];
    // $Query = $_SERVER['QUERY_STRING'];
    $Path = isset($_SERVER['HTTP_REFERER']) ? $_SERVER['HTTP_REFERER'] : $_GET["n"];
    $User = isset($_GET["u"]) ? $_GET["u"] : "";
    $sIP = $IP.".".session_id();
    $Deskuser = gethostbyaddr($_SERVER['REMOTE_ADDR']);

    $json_doc = array(
    			"Date" => $Date,
    			"Time" => $Time,
	    		"IP" => $IP,
    			"sIP" => $sIP,
	    		"OS" => $OS,
	    		"Browser" => $Browser,
	    		"Path" => $Path,
	    		"System" => $System,
    			"Color" => $Color,
    			"Res" => $Res,
    			"Deskuser" => $Deskuser,
    			"User" => $User
            );
    $json_doc = json_encode($json_doc);

    $baseUri = 'http://'.$host.':'.$port.'/'.$index.'/'.$type.'/';

    $ci = curl_init();
    curl_setopt($ci, CURLOPT_URL, $baseUri);
    curl_setopt($ci, CURLOPT_PORT, $port);
    curl_setopt($ci, CURLOPT_USERPWD, "$username:$password");
    curl_setopt($ci, CURLOPT_TIMEOUT, 200);
    curl_setopt($ci, CURLOPT_RETURNTRANSFER, 1);
    curl_setopt($ci, CURLOPT_FORBID_REUSE, 0);
    curl_setopt($ci, CURLOPT_CUSTOMREQUEST, 'POST');
    curl_setopt($ci, CURLOPT_POSTFIELDS, $json_doc);
    $response = curl_exec($ci);
    curl_close($ci);


?>