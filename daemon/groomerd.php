#!/usr/bin/php5
<?php
/*
    Copyright (C) 2007 Vasco Costa

    This file is part of yoga.

    yoga is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License as published by
    the Free Software Foundation; either version 3 of the License, or
    (at your option) any later version.

    yoga is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program.  If not, see <http://www.gnu.org/licenses/>.
*/

/*
    Configuration:
    The first block comprises default values for MySQL varibales.
    The second block comprises default values for varibles which
    can be overriden through the database or command line.
*/
$mysql_host = "localhost";
$mysql_user = "mysql_user";
$mysql_password = "mysql_password";
$mysql_database = "mysql_database";
$mysql_table_prefix = "mysql_table_prefix";

$addresses = array("192.168.0.1");
$port = 10001;
$delay = 60;
$timeout = 10;
$retries = 6;
$verbose = false;
$debug = false;

function get_settings($mysql_host, $mysql_database, $mysql_user, $mysql_password) {
    $link = mysql_connect($mysql_host, $mysql_user, $mysql_password);
    if($link === false) {
        log_errors("mysql_connect(): " . mysql_error($link) . "\n");
        die("mysql_connect(): " . mysql_error($link) . "\n");
    }
    $result = mysql_select_db($mysql_database, $link);
    if($result === false) {
        log_errors("mysql_select_db(): " . mysql_error($link) . "\n");
        die("mysql_select_db(): " . mysql_error($link) . "\n");
    }
    $query = "SELECT * FROM settings";
    $result = mysql_query($query, $link);
    if($result === false) {
        log_errors("mysql_query(): " . mysql_error($link) . "\n");
        die("mysql_query(): " . mysql_error($link) . "\n");
    }
    while($row = mysql_fetch_assoc($result)) {
        $data[] = $row;
    }
    mysql_close($link);
    return $data;
}

function connect($address, $port, $timeout, $retries) {
    $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
    if($socket === false) {
        log_errors("socket_create(): " . socket_strerror(socket_last_error($socket)) . "\n");
        die("socket_create(): " . socket_strerror(socket_last_error($socket)) . "\n");
    }
    while(($result = socket_connect($socket, $address, $port)) === false && $retries > 0) {
        $retries--;
        sleep($timeout);
    }
    /*
        Ugly adhoc one shot telnet options negotiation:
        server: IAC WILL ECHO (ff fb 01), IAC WILL SUPRESS GOAHEAD (ff fb 03)
        client: IAC DO ECHO (ff fd 01), IAC DO SUPRESS GOAHEAD (ff fd 03)
    */
    socket_read($socket, 6);
    if($result === false) {
        log_errors("socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n");
        echo "socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n";
    }
    socket_write($socket, chr(255).chr(253).chr(1).chr(255).chr(253).chr(3), 6);
    if($result === false) {
        log_errors("socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n");
        echo "socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n";
    }
    return $socket;
}

function disconnect($socket) {
    socket_close($socket);
}

function do_command($socket, $command) {
    foreach(str_split($command) as $character) {
        $result = socket_write($socket, $character, 1);
        if($result === false) {
            log_errors("socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n");
            echo "socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n";
        }
        $result = socket_read($socket, 1);
        if($result === false) {
            log_errors("socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n");
            echo "socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n";
        }
    }
    $result = socket_write($socket, "\r\n", 1);
    if($result === false) {
        log_errors("socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n");
        echo "socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n";
    }
    $result = socket_read($socket, 1);
    if($result === false) {
        log_errors("socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n");
        echo "socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n";
    }
}

function get_response($socket) {
    /*
        Ugly workaround using socket_set_option to timeout the socket
        wich otherwise will oddly keep blocking.
    */
    $result = socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>0, "usec"=>500000 ));
    if($result === false) {
        log_errors("socket_set_option(): " . socket_strerror(socket_last_error($socket)) . "\n");
        echo "socket_set_option(): " . socket_strerror(socket_last_error($socket)) . "\n";
    }
    $response = "";
    while ($out = socket_read($socket, 2048)) {
        $response .= $out;
    }
    return $response;
}

function get_time($socket) {
    do_command($socket, "time?");
    $response = get_response($socket);
    $lines = explode("\n", $response);
    $chunks = explode("=", $lines[2]);
    return trim($chunks[1]);
}

function get_date($socket) {
    do_command($socket, "date?");
    $response = get_response($socket);
    $lines = explode("\n", $response);
    $chunks = explode("=", $lines[2]);
    return trim($chunks[1]);
}

function get_clock($socket) {
    do_command($socket, "system_clock?");
    $response = get_response($socket);
    $lines = explode("\n", $response);
    $chunks = explode("=", $lines[2]);
    return trim($chunks[1]);
}

function get_configs($socket) {
    do_command($socket, "config?");
    $response = get_response($socket);
    $lines = explode("\n", $response);
    $configs = "";
    foreach($lines as $line) {
        if(substr($line, 0, 4) == "SLOT") {
            $chunks = explode("->", $line);
            $configs .= trim($chunks[1]) . ",";
        }
    }
    return substr_replace($configs,"",-1);
}

function get_alarms($socket) {
    do_command($socket, chr(27));
    $response = get_response($socket);
    do_command($socket, "n");
    $response = get_response($socket);
    $alarms = "";
    for($i = 1; $i <= 8; $i++) {
        do_command($socket, "alarms/" . $i . "?");
        $response = get_response($socket);
        if(!strstr($response, "ERROR")) {
            $lines = explode("\n", $response);
            $ports = "";
            foreach($lines as $line) {
                if(substr($line, 3, 4) == "PORT") {
                    $chunks = explode("->", $line);
                    $smaller_chunks = explode(" ", $chunks[1]);
                    $ports .= trim(str_replace("*", "", $smaller_chunks[0])) . ":";
                }
            }
            $alarms .= substr_replace($ports,"",-1) . ",";
        }
        else {
            $alarms .= "NO_SYNC:NO_SYNC:NO_SYNC:NO_SYNC:NO_SYNC:NO_SYNC:NO_SYNC:NO_SYNC:,";
        }
    }
    return substr_replace($alarms,"",-1);
}

function get_mapping($socket) {
    /*
        ---BETA--- ---BETA--- ---BETA---
    */
    $slots = array();
    for($slot = 1; $slot <= 1; $slot++) {
        $ports = array();
        for($port = 1; $port <= 3; $port++) {
            do_command($socket, "mapping/tx/" . $slot . "/" . $port . "?");
            $response = get_response($socket);
            if(!strstr($response, "ERROR")) {
                $lines = explode("\n", $response);
                $coco ="";
                foreach($lines as $line) {
                    if(is_numeric(substr($line, 0, 1))) {
                        $coco .= "\n" . $line;
                    }
                }
            }
            array_push($ports, $coco);
        }
        array_push($slots, $ports);
    }
    return $slots;
}

function log_data($mysql_host, $mysql_database, $mysql_user, $mysql_password, $mysql_table_prefix, $address, $time, $clock, $configs, $alarms, $mapping = null) {
    $link = mysql_connect($mysql_host, $mysql_user, $mysql_password);
    if($link === false) {
        log_errors("mysql_connect(): " . mysql_error($link) . "\n");
        die("mysql_connect(): " . mysql_error($link) . "\n");
    }
    $result = mysql_select_db($mysql_database, $link);
    if($result === false) {
        log_errors("mysql_select_db(): " . mysql_error($link) . "\n");
        die("mysql_select_db(): " . mysql_error($link) . "\n");
    }
    $query = "INSERT INTO `$mysql_table_prefix" . strtr($address, ".", "_") ."` (
        `id` , `time` , `clock` , `configs` , `alarms` , `mapping` )
        VALUES ('NULL', '$time', '$clock', '$configs', '$alarms', '$mapping'
    )";
    $result = mysql_query($query, $link);
    if($result === false) {
        log_errors("mysql_query(): " . mysql_error($link) . "\n");
        die("mysql_query(): " . mysql_error($link) . "\n");
    }
    mysql_close($link);
}

function log_errors($error) {
    $handle = fopen("errors.log", "a");
    if($handle === false) {
        echo "fopen(): Failed opening file\n";
    }
    $result = fwrite($handle, date("d-m-Y H:i:s", time() - 3600) . " - " . $error);
    if($result === false) {
        echo "fwrite(): Failed writing file\n";
    }
}

function display_data($address, $time, $clock, $configs, $alarms, $mapping = null) {
    echo "Time: $time\n\n";
    echo "Groomer:  $address\n";
    echo "\n ---------------------------------------------------------------\n";
    echo "|SLOT-01|SLOT-02|SLOT-03|SLOT-04|SLOT-05|SLOT-06|SLOT-07|SLOT-08|";
    echo "\n ---------------------------------------------------------------\n";
    echo "|";
    $slots = explode(",", $configs);
    foreach($slots as $slot) {
        if($slot == "E1-HiZ") {
            echo $slot . " |";
        }
        else if($slot == "UNQP") {
            echo $slot . "   |";
        }
    }
    echo "\n ---------------------------------------------------------------\n";
    $slots = explode(",", $alarms);
    $tmp_slots = array();
    foreach($slots as $slot) {
        $ports = explode(":", $slot);
        array_push($tmp_slots, $ports);
    }
    for($j = 0; $j != 8; $j++) {
        echo "|";
        for($i = 0; $i != 8; $i++) {
            if($tmp_slots[$i][$j]) {
                echo $tmp_slots[$i][$j] . "|";
            }
            else {
                echo "       |";
            }
        }
        echo "\n ---------------------------------------------------------------\n";
    }
    echo "|Clock: " . $clock . "                                                |";
    echo "\n ---------------------------------------------------------------\n\n";
}

function create_tables($mysql_host, $mysql_database, $mysql_user, $mysql_password, $mysql_table_prefix, $addresses) {
    $link = mysql_connect($mysql_host, $mysql_user, $mysql_password);
    if($link === false) {
        log_errors("mysql_connect(): " . mysql_error($link) . "\n");
        die("mysql_connect(): " . mysql_error($link) . "\n");
    }
    $result = mysql_select_db($mysql_database, $link);
    if($result === false) {
        log_errors("mysql_select_db(): " . mysql_error($link) . "\n");
        die("mysql_select_db(): " . mysql_error($link) . "\n");
    }
    foreach($addresses as $address) {
        $query = "CREATE TABLE `$mysql_table_prefix" . strtr($address, ".", "_") . "` (
            `id` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
            `time` VARCHAR( 50 ) NOT NULL ,
            `clock` VARCHAR( 50 ) NOT NULL ,
            `configs` VARCHAR( 500 ) NOT NULL ,
            `alarms` VARCHAR( 1000 ) NOT NULL ,
            `mapping` VARCHAR( 1000 ) NOT NULL
        )";
        $result = mysql_query($query, $link);
        if($result === false) {
            log_errors("mysql_query(): " . mysql_error($link) . "\n");
            die("mysql_query(): " . mysql_error($link) . "\n");
        }
    }
    mysql_close($link);
}

function drop_tables($mysql_host, $mysql_database, $mysql_user, $mysql_password, $mysql_table_prefix, $addresses) {
    $link = mysql_connect($mysql_host, $mysql_user, $mysql_password);
    if($link === false) {
        log_errors("mysql_connect(): " . mysql_error($link) . "\n");
        die("mysql_connect(): " . mysql_error($link) . "\n");
    }
    $result = mysql_select_db($mysql_database, $link);
    if($result === false) {
        log_errors("mysql_select_db(): " . mysql_error($link) . "\n");
        die("mysql_select_db(): " . mysql_error($link) . "\n");
    }
    foreach($addresses as $address) {
        $query = "DROP TABLE " . $mysql_table_prefix . strtr($address, ".", "_");
        $result = mysql_query($query, $link);
        if($result === false) {
            log_errors("mysql_query(): " . mysql_error($link) . "\n");
            die("mysql_query(): " . mysql_error($link) . "\n");
        }
    }
    mysql_close($link);
}

function truncate_tables($mysql_host, $mysql_database, $mysql_user, $mysql_password, $mysql_table_prefix, $addresses) {
    $link = mysql_connect($mysql_host, $mysql_user, $mysql_password);
    if($link === false) {
        log_errors("mysql_connect(): " . mysql_error($link) . "\n");
        die("mysql_connect(): " . mysql_error($link) . "\n");
    }
    $result = mysql_select_db($mysql_database, $link);
    if($result === false) {
        log_errors("mysql_select_db(): " . mysql_error($link) . "\n");
        die("mysql_select_db(): " . mysql_error($link) . "\n");
    }
    foreach($addresses as $address) {
        $query = "TRUNCATE TABLE " . $mysql_table_prefix . strtr($address, ".", "_");
        $result = mysql_query($query, $link);
        if($result === false) {
            log_errors("mysql_query(): " . mysql_error($link) . "\n");
            die("mysql_query(): " . mysql_error($link) . "\n");
        }
    }
    mysql_close($link);
}
    
function show_usage($executable) {
    echo
        "Usage: ". $executable . " [options]\n\n" .
        "-port {1-65535}\t\tport to connect on the groomer\n" .
        "-delay {1-86400}\tseconds elapsed between samples\n" .
        "-timeout {1-60}\t\tseconds elapsed between connect retries\n" .
        "-retries {1-10}\t\tnumber of connect retries\n" .
        "-verbose\t\tdisplay results on standard output\n" .
        "-debug\t\t\tdisplay all warnings on standard output\n" .
        "-create\t\t\tcreate database tables\n" .
        "-drop\t\t\tdrop database tables\n" .
        "-truncate\t\ttruncate database tables\n\n"
    ;
}

function parse_commandline($argc, $argv) {
    for($i = 1; $i != $argc; $i++) {
        if($i != $argc - 1 && strpos($argv[$i], "-") === 0 && strpos($argv[$i+1], "-") === false) {
            $options[trim($argv[$i], "-")] = $argv[$i+1];
            ++$i;
        }
        else if($i != $argc - 1 && strpos($argv[$i], "-") === 0 && strpos($argv[$i+1], "-") === 0) {
            $options[trim($argv[$i], "-")] = null;
        }
        else if(strpos($argv[$i], "-") === 0) {
            $options[trim($argv[$i], "-")] = null;
        }
    }
    return $options;
}

$settings = get_settings($mysql_host, $mysql_database, $mysql_user, $mysql_password);
if($settings[0]["addresses"]) {
    $addresses = explode(",",  $settings[0]["addresses"]);
}
if($settings[0]["port"]) {
    $port = $settings[0]["port"];
}
if($settings[0]["delay"]) {
    $delay = $settings[0]["delay"];
}
if($settings[0]["timeout"]) {
    $timeout = $settings[0]["timeout"];
}
if($settings[0]["retries"]) {
    $retries = $settings[0]["retries"];
}
if($settings[0]["verbose"]) {
    $verbose = $settings[0]["verbose"];
}
if($settings[0]["debug"]) {
    $debug = $settings[0]["debug"];
}
$options = parse_commandline($argc, $argv);
if($options) {
    foreach($options as $key => $value) {
        switch($key) {
            case("port"):
                if(is_numeric($value) && ($value > 0 && $value < 65536)) {
                    $port = $value;
                }
                else {
                    show_usage($argv[0]);
                    exit(0);
                }
                break;
            case("delay"):
                if(is_numeric($value) && ($value > 0 && $value < 864001)) {
                    $delay = $value;
                }
                else {
                    show_usage($argv[0]);
                    exit(0);
                }
                break;
            case("timeout"):
                if(is_numeric($value) && ($value > 1 && $value < 60)) {
                    $timeout = $value;
                }
                else {
                    show_usage($argv[0]);
                    exit(0);
                }
                break;
            case("retries"):
                if(is_numeric($value) && ($value > 1 && $value < 10)) {
                    $retries = $value;
                }
                else {
                    show_usage($argv[0]);
                    exit(0);
                }
                break;
            case("verbose"):
                $verbose = true;
                break;
            case("debug"):
                $debug = true;
                break;
            case("create"):
                create_tables($mysql_host, $mysql_database, $mysql_user, $mysql_password, $mysql_table_prefix, $addresses);
                exit(0);
            case("drop"):
                drop_tables($mysql_host, $mysql_database, $mysql_user, $mysql_password, $mysql_table_prefix, $addresses);
                exit(0);
            case("truncate"):
                truncate_tables($mysql_host, $mysql_database, $mysql_user, $mysql_password, $mysql_table_prefix, $addresses);
                exit(0);
            default:
                show_usage($argv[0]);
                exit(0);
        }
    }
}
else if($argc != 1) {
    show_usage($argv[0]);
    exit(0);
}
/*
    Debug enable/disable according to $debug boolean
*/
error_reporting($debug? E_ALL : 0);
/*
    Main loop:
    All work gets done within this infinite loop.
    Once an iteration reaches the end it sleeps.
*/
while(true) {
    foreach($addresses as $address) {
        $socket = connect($address, $port, $timeout, $retries);
        $time = date("d-m-Y H:i:s", time() - 3600);
        $clock = get_clock($socket);
        $configs = get_configs($socket);
        $alarms = get_alarms($socket);
        disconnect($socket);
        log_data($mysql_host, $mysql_database, $mysql_user, $mysql_password, $mysql_table_prefix, $address, $time, $clock, $configs, $alarms);
        if($verbose) {
            display_data($address, $time, $clock, $configs, $alarms);
        }
    }
    sleep($delay);
}
?>