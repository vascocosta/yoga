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

$title = "VCL-MegaConnect Network Panel";
$style = "css/style.css";
$refresh_timeout = "10";
$history_samples = "10";
$summary_columns = "4";
/*
    Do not edit below!
*/
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
$settings = $data;
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
if($settings[0]["title"]) {
    $title = $settings[0]["title"];
}
if($settings[0]["style"]) {
    $style = $settings[0]["style"];
}
if($settings[0]["refresh_timeout"]) {
    $refresh_timeout = $settings[0]["refresh_timeout"];
}
if($settings[0]["history_samples"]) {
    $history_samples = $settings[0]["history_samples"];
}
if($settings[0]["summary_columns"]) {
    $summary_columns = $settings[0]["summary_columns"];
}
?>