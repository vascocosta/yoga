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
if($_SESSION["permission"] == "yes" && $_SESSION["admin"] == "yes") {
?>
<?php require("include/configuration.php"); ?>
<?php require("include/controller.class.php"); ?>
<?php require("include/header.php"); ?>
<?php
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $controller = new Controller($port, $timeout, $retries, $mysql_host, $mysql_user, $mysql_password, $mysql_database, $mysql_table_prefix);
    $map["mode"] = $_POST["mode"];
    $map["source_slot"] = $_POST["source_slot"];
    $map["source_port"] = $_POST["source_port"];
    $map["source_timeslot"] = $_POST["source_timeslot"];
    $map["destination_slot"] = $_POST["destination_slot"];
    $map["destination_port"] = $_POST["destination_port"];
    $map["destination_timeslot"] = $_POST["destination_timeslot"];
    $groomer = $_POST["groomer"];
    $response = $controller->set_mapping($map, $groomer);
}
else if($_GET["save"] == "yes") {
    $controller = new Controller($port, $timeout, $retries, $mysql_host, $mysql_user, $mysql_password, $mysql_database, $mysql_table_prefix);
    $groomer = $_GET["groomer"];
    $map = $controller->save_mapping($groomer);
    header('Content-type: application/txt');
    header('Content-Disposition: attachment; filename="map.txt"');
    echo $map;
}

?>
        <table class="menu">
            <tr><th class="menu"><b>Section:</b> Mapping</th><th class="menu"><b>Username:</b> <?php echo $_SESSION["username"]; ?></th><th class="menu"><b>Navigation:</b> <a href="summary.php">Summary</a> - <a href="">Help</a> - <a href="index.php">Logout</a></th></tr>
        </table>
        <br>
        <h3>Groomer: <?php echo $_REQUEST["groomer"]; ?></h3>
        <br>
        <form method="post" action="mapping.php">
            <table class="login">
                <input name="groomer" type="hidden" value="<?php echo $_REQUEST["groomer"]; ?>" />
                <tr>
                    <td class="login">Source</td>
                    <td colspan="2" class="login">
                        <select name="mode">
                            <option value="tx">tx</option>
                            <option value="rx">rx</option>
                            <option value="rx-tx">txrx</option>
                        </select>
                    </td>
                    <td class="login">Destination</td>
                </tr>
                <tr>
                    <td class="login">Timeslot</td>
                    <td class="login">
                        <select name="source_timeslot">
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                            <option value="26">26</option>
                            <option value="27">27</option>
                            <option value="28">28</option>
                            <option value="29">29</option>
                            <option value="30">30</option>
                            <option value="31">31</option>
                        </select>
                    </td>
                    <td class="login">
                        <select name="destination_timeslot">
                            <option value="01">01</option>
                            <option value="02">02</option>
                            <option value="03">03</option>
                            <option value="04">04</option>
                            <option value="05">05</option>
                            <option value="06">06</option>
                            <option value="07">07</option>
                            <option value="08">08</option>
                            <option value="09">09</option>
                            <option value="10">10</option>
                            <option value="11">11</option>
                            <option value="12">12</option>
                            <option value="13">13</option>
                            <option value="14">14</option>
                            <option value="15">15</option>
                            <option value="16">16</option>
                            <option value="17">17</option>
                            <option value="18">18</option>
                            <option value="19">19</option>
                            <option value="20">20</option>
                            <option value="21">21</option>
                            <option value="22">22</option>
                            <option value="23">23</option>
                            <option value="24">24</option>
                            <option value="25">25</option>
                            <option value="26">26</option>
                            <option value="27">27</option>
                            <option value="28">28</option>
                            <option value="29">29</option>
                            <option value="30">30</option>
                            <option value="31">31</option>
                        </select>
                    </td>
                    <td class="login">Timeslot</td>
                </tr>
                <tr>
                    <td class="login">Port</td>
                    <td class="login">
                        <select name="source_port">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                    </td>
                    <td class="login">
                        <select name="destination_port">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                    </td>
                    <td class="login">Port</td>
                </tr>
                <tr>
                    <td class="login">Slot</td>
                    <td class="login">
                        <select name="source_slot">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                    </td>
                    <td class="login">
                        <select name="destination_slot">
                            <option value="1">1</option>
                            <option value="2">2</option>
                            <option value="3">3</option>
                            <option value="4">4</option>
                            <option value="5">5</option>
                            <option value="6">6</option>
                            <option value="7">7</option>
                            <option value="8">8</option>
                        </select>
                    </td>
                    <td class="login">Slot</td>
                </tr>
                <tr>
                    <td class="login"><a href="mapping.php?groomer=<?php echo $_REQUEST["groomer"]; ?>&save=yes">Save</a></td>
                    <td class="login"><a href="mapping.php?groomer=<?php echo $_REQUEST["groomer"]; ?>&restore=yes">Restore</a></td>
                    <td class="login"><a href="mapping.php?groomer=<?php echo $_REQUEST["groomer"]; ?>&backup=yes">Backup</a></td>
                    <td class="login"><input type="submit" value="Map" /></td>
                </tr>
            </table>
        </form>
        <br>
        <?php echo $response; ?>
        <br>
        <br>
        <?php if(substr($response, 0, 2) == "OK") { ?>
        <?php echo "Mode: " . $_POST["mode"]; ?>
        <br>
        <?php echo "Slot: " . $_POST["source_slot"] . " -> " . $_POST["destination_slot"]; ?>
        <br>
        <?php echo "Port: " . $_POST["source_port"] . " -> " . $_POST["destination_port"]; ?>
        <br>
        <?php echo "Timeslot: " . $_POST["source_timeslot"] . " -> " . $_POST["destination_timeslot"]; ?>
        <?php } ?>
<?php require("include/footer.php"); ?>
<?php } else {
header("HTTP/1.1 301 Moved Permanently");
header("Location: error.php?message=Access%20Denied");
exit(0);
} ?>