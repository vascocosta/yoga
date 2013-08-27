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

session_start();
if($_POST["username"] == "") {
?>
<?php require("include/configuration.php"); ?>
<?php require("include/header.php"); ?>
<?php
if($_GET["permission"] == "no") {
    $_SESSION = array();
    session_destroy();
    echo "logged out";
}
?>
        <br>
        <br>
        <form method="post" action="index.php">
            <table class="login">
                <tr><td class="login">Username:</td><td class="login"><input type="text" name="username" size="20" /></td></tr>
                <tr><td class="login">Password:</td><td class="login"><input type="password" name="password" size="20" /></td></tr>
                <tr><td colspan="2" class="login"><input type="submit" value="Login" /></td></tr>
            </table>
        </form>
<?php require("include/footer.php"); ?>
<?php } else {
$username=$_POST["username"];
$password=$_POST["password"];

require("include/configuration.php");
require("include/controller.class.php");

$controller = new Controller($port, $timeout, $retries, $mysql_host, $mysql_user, $mysql_password, $mysql_database, $mysql_table_prefix);
session_start();
$login = $controller->check_login($username, $password);
if($login["valid"]) {
    $_SESSION["username"] = $username;
    $_SESSION["permission"] = "yes";
}
if($login["admin"]) {
    $_SESSION["admin"] = "yes";
}
if($_SESSION["permission"] == "yes") {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: summary.php");
    exit(0);
}
else {
    header("HTTP/1.1 301 Moved Permanently");
    header("Location: error.php?message=Access%20Denied");
    exit(0);
}
} ?>