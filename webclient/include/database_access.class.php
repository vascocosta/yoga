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

class DatabaseAccess {
    var $mysql_host;
    var $mysql_user;
    var $mysql_password;
    var $mysql_database;
    var $link;
    var $result;
    var $query;
    var $data;
    
    public function __construct($mysql_host, $mysql_user, $mysql_password, $mysql_database) {
        $this->mysql_host = $mysql_host;
        $this->mysql_user = $mysql_user;
        $this->mysql_password = $mysql_password;
        $this->mysql_database = $mysql_database;
    }
    
    private function connect() {
        $this->link = mysql_connect($this->mysql_host, $this->mysql_user, $this->mysql_password);
        if($this->link === false) {
            die("mysql_connect(): " . mysql_error($this->link) . "\n");
        }
        $this->result = mysql_select_db($this->mysql_database, $this->link);
        if($this->result === false) {
            die("mysql_select_db(): " . mysql_error($this->link) . "\n");
        }
    }
    
    private function disconnect() {
        mysql_close($this->link);
    }
    
    public function get_data($mysql_table, $rows = null, $where = null) {
        $this->connect();
        if($rows) {
            $this->query = "SELECT COUNT(*) FROM $mysql_table";
            $this->result = mysql_query($this->query, $this->link);
            if($this->result === false) {
                die("mysql_query(): " . mysql_error($this->link) . "\n");
            }
            $row = mysql_fetch_row($this->result);
            $total_rows = $row[0];
            if($total_rows > $rows) {
                $limit_string = "LIMIT " . ($total_rows - $rows) . "," . $total_rows;
            }
            else {
                $limit_string = null;
            }
        }
        else {
            $limit_string = null;
        }
        if($where) {
            $where_string = "WHERE " . $where;
        }
        else {
            $where_string = null;
        }
        $this->query = "SELECT * FROM $mysql_table $where_string $limit_string";
        $this->result = mysql_query($this->query, $this->link);
        if($this->result === false) {
            die("mysql_query(): " . mysql_error($this->link) . "\n");
        }
        while($row = mysql_fetch_assoc($this->result)) {
            $this->data[] = $row;
        }
        $this->disconnect();
        return $this->data;
    }
    
    public function delete_data($mysql_table, $id) {
        $this->connect();
        $this->query = "DELETE FROM $mysql_table WHERE id = $id";
        $this->result = mysql_query($this->query, $this->link);
        if($this->result === false) {
            die("mysql_query(): " . mysql_error($this->link) . "\n");
        }
        $this->disconnect();
    }
    
    public function insert_data($mysql_table, $data) {
        $this->connect();
        foreach($data as $key => $value) {
            $fields_string .= "$key , ";
            if($value) {
                $values_string .= "'$value' , ";
            }
            else {
                $values_string .= "NULL , ";
            }
        }
        $fields_string = substr($fields_string, 0, -3);
        $values_string = substr($values_string, 0, -3);
        $this->query = "INSERT INTO `$mysql_table` (
            $fields_string )
            VALUES ( $values_string
        )";
        $this->result = mysql_query($this->query, $this->link);
        if($this->result === false) {
            die("mysql_query(): " . mysql_error($this->link) . "\n");
        }
        $this->disconnect();
    }
    
    public function update_data($mysql_table, $data) {
        $this->connect();
        $id = $data["id"];
        foreach($data as $key => $value) {
            if($key != "id") {
                $fields_values_string .= "$key = '$value' , ";
            }
        }
        $fields_values_string = substr($fields_values_string, 0, -3);
        $this->query = "UPDATE $mysql_table
            SET $fields_values_string
            WHERE id = $id
        ";
        $this->result = mysql_query($this->query, $this->link);
        if($this->result === false) {
            die("mysql_query(): " . mysql_error($this->link) . "\n");
        }
        $this->disconnect();
    }
    
    public function get_tables($pattern) {
        $this->connect();
        $this->query = "SHOW TABLES LIKE '%" . $pattern ."%'";
        $this->result = mysql_query($this->query, $this->link);
        if($this->result === false) {
            die("mysql_query(): " . mysql_error($this->link) . "\n");
        }
        while($row = mysql_fetch_array($this->result)) {
            $this->data[] = $row;
        }
        $this->disconnect();
        return $this->data;
    }
}
?>