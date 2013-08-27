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

require("include/configuration.php"); 
require("include/database_access.class.php");

class Controller {
    var $port;
    var $timeout;
    var $retries;
    var $mysql_host;
    var $mysql_user;
    var $mysql_password;
    var $mysql_database;
    var $mysql_table_prefix;
    
    public function __construct($port, $timeout, $retries, $mysql_host, $mysql_user, $mysql_password, $mysql_database, $mysql_table_prefix) {
        $this->port = $port;
        $this->timeout = $timeout;
        $this->retries = $retries;
        $this->mysql_host = $mysql_host;
        $this->mysql_user = $mysql_user;
        $this->mysql_password = $mysql_password;
        $this->mysql_database = $mysql_database;
        $this->mysql_table_prefix = $mysql_table_prefix;
    }
    
    private function connect($address, $port, $timeout, $retries) {
        $socket = socket_create(AF_INET, SOCK_STREAM, SOL_TCP);
        if($socket === false) {
            $this->log_errors("socket_create(): " . socket_strerror(socket_last_error($socket)) . "\n");
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
            $this->log_errors("socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n");
            echo "socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n";
        }
        socket_write($socket, chr(255).chr(253).chr(1).chr(255).chr(253).chr(3), 6);
        if($result === false) {
            $this->log_errors("socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n");
            echo "socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n";
        }
        return $socket;
    }

    private function disconnect($socket) {
        socket_close($socket);
    }
    
    private function do_command($socket, $command) {
        foreach(str_split($command) as $character) {
            $result = socket_write($socket, $character, 1);
            if($result === false) {
                $this->log_errors("socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n");
                echo "socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n";
            }
            $result = socket_read($socket, 1);
            if($result === false) {
                $this->log_errors("socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n");
                echo "socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n";
            }
        }
        $result = socket_write($socket, "\r\n", 1);
        if($result === false) {
            $this->log_errors("socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n");
            echo "socket_write(): " . socket_strerror(socket_last_error($socket)) . "\n";
        }
        $result = socket_read($socket, 1);
        if($result === false) {
            $this->log_errors("socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n");
            echo "socket_read(): " . socket_strerror(socket_last_error($socket)) . "\n";
        }
    }

    private function get_response($socket) {
        /*
            Ugly workaround using socket_set_option to timeout the socket
            wich otherwise will oddly keep blocking.
        */
        $result = socket_set_option($socket, SOL_SOCKET, SO_RCVTIMEO, array("sec"=>0, "usec"=>500000 ));
        if($result === false) {
            $this->log_errors("socket_set_option(): " . socket_strerror(socket_last_error($socket)) . "\n");
            echo "socket_set_option(): " . socket_strerror(socket_last_error($socket)) . "\n";
        }
        $response = "";
        while ($out = socket_read($socket, 2048)) {
            $response .= $out;
        }
        return $response;
    }
    
    private function log_errors($error) {
        $handle = fopen("errors.log", "a");
        if($handle === false) {
            echo "fopen(): Failed opening file\n";
        }
        $result = fwrite($handle, date("d-m-Y H:i:s", time() - 3600) . " - " . $error);
        if($result === false) {
            echo "fwrite(): Failed writing file\n";
        }
    }
    
    private function parse_data($data) {
        for($row = 0; $row != sizeof($data); $row++) {
            $parsed_data[$row]["time"] = $data[$row]["time"];
            $parsed_data[$row]["clock"] = $data[$row]["clock"];
            $parsed_data[$row]["configs"] = explode(",", $data[$row]["configs"]);
            $slots = explode(",", $data[$row]["alarms"]);
            for($slot = 0; $slot != sizeof($slots); $slot++) {
                $parsed_data[$row]["alarms"][$slot] = explode(":", $slots[$slot]);
            }
        }
        return $parsed_data;
    }
    
    public function set_mapping($map, $groomer) {
        $socket = $this->connect($groomer, $this->port, $this->timeout, $this->retries);
        $this->do_command($socket, "#sel=config");
        $response = $this->get_response($socket);
        $this->do_command($socket, "map/" . $map["mode"] . "/" . $map["source_slot"] . "/" . $map["source_port"] . "/" . $map["source_timeslot"] . "=" . $map["destination_slot"] . "/" . $map["destination_port"] . "/" . $map["destination_timeslot"]);
        $response = $this->get_response($socket);
        $this->disconnect($socket);
        return str_replace("VCL-MC-CC-CONFIG>", "", trim($response));
    }    
    
    public function save_mapping($groomer) {
        $socket = $this->connect($groomer, $this->port, $this->timeout, $this->retries);
        $slots = array();
        for($slot = 1; $slot <= 8; $slot++) {
            $ports = array();
            for($port = 1; $port <= 8; $port++) {
                $this->do_command($socket, "mapping/tx/" . $slot . "/" . $port . "?");
                $response = $this->get_response($socket);
                if(!strstr($response, "ERROR")) {
                    $map .= $response;
                }
            }
        }
        $this->disconnect($socket);
        return $map;
    }
    
    public function get_data($groomer, $samples = null) {
        $database_access = new DatabaseAccess($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        $data = $database_access->get_data($this->mysql_table_prefix . str_replace(".", "_", $groomer), $samples);
        return $this->parse_data($data);
    }
    
    public function get_groomers() {
        $database_access = new DatabaseAccess($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        $groomers = $database_access->get_tables($this->mysql_table_prefix);
        return $groomers;
    }
    
    public function get_settings() {
        $database_access = new DatabaseAccess($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        $settings = $database_access->get_data("settings");
        return $settings;
    }
    
    public function update_settings($data) {
        $database_access = new DatabaseAccess($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        $database_access->update_data("settings", $data);
    }
    
    public function get_users() {
        $database_access = new DatabaseAccess($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        $users = $database_access->get_data("users");
        return $users;
    }
    
    public function get_user($id) {
        $database_access = new DatabaseAccess($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        $user = $database_access->get_data("users", null, "id = $id");
        return $user;
    }
    
    public function delete_user($id) {
        $database_access = new DatabaseAccess($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        $database_access->delete_data("users", $id);
    }
    
    public function insert_user($data) {
        $database_access = new DatabaseAccess($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        $database_access->insert_data("users", $data);
    }
    
    public function update_user($data) {
        $database_access = new DatabaseAccess($this->mysql_host, $this->mysql_user, $this->mysql_password, $this->mysql_database);
        $database_access->update_data("users", $data);
    }
    
    public function check_login($username, $password) {
        $login["valid"] = false;
        $login["admin"] = false;
        $users = $this->get_users();
        for($row = 0; $row != sizeof($users); $row++) {
            if($username == $users[$row]["username"] && $password == $users[$row]["password"]) {
                $login["valid"] = true;
                if($users[$row]["privilege"] == "admin") {
                    $login["admin"] = true;
                }
            }
        }
        return $login;
    }
    
    function run_in_background($command, $priority = 0) {
        system("($command >/dev/null) >/dev/null &");
    }
}
?>