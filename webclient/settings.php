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
$controller = new Controller($port, $timeout, $retries, $mysql_host, $mysql_user, $mysql_password, $mysql_database, $mysql_table_prefix);
if($_SERVER["REQUEST_METHOD"] == "POST") {
    $data = Array();
    $data["id"] = $_POST["id"];
    $data["addresses"] = $_POST["addresses"];
    $data["port"] = $_POST["port"];
    $data["delay"] = $_POST["delay"];
    $data["timeout"] = $_POST["timeout"];
    $data["retries"] = $_POST["retries"];
    $data["verbose"] = $_POST["verbose"];
    $data["debug"] = $_POST["debug"];
    $data["title"] = $_POST["title"];
    $data["style"] = $_POST["style"];
    $data["refresh_timeout"] = $_POST["refresh_timeout"];
    $data["history_samples"] = $_POST["history_samples"];
    $data["summary_columns"] = $_POST["summary_columns"];
    $controller->update_settings($data);
    /*
        TODO: Kill currently running daemon and respawn.
    */
    //$controller->run_in_background("/srv/www/htdocs/yoga/daemon/groomerd.php");
}
$settings = $controller->get_settings();
$edit_id = $settings[0]["id"];
$edit_addresses = $settings[0]["addresses"];
$edit_port = $settings[0]["port"];
$edit_delay = $settings[0]["delay"];
$edit_timeout = $settings[0]["timeout"];
$edit_retries = $settings[0]["retries"];
$edit_verbose = $settings[0]["verbose"];
$edit_debug = $settings[0]["debug"];
$edit_title = $settings[0]["title"];
$edit_style = $settings[0]["style"];
$edit_refresh_timeout = $settings[0]["refresh_timeout"];
$edit_history_samples = $settings[0]["history_samples"];
$edit_summary_columns = $settings[0]["summary_columns"];
?>
        <table class="menu">
            <tr><th class="menu"><b>Section:</b> Settings</th><th class="menu"><b>Username:</b> <?php echo $_SESSION["username"]; ?></th><th class="menu"><b>Navigation:</b> <a href="summary.php">Summary</a> - <a href="">Help</a> - <a href="index.php">Logout</a></th></tr>
        </table>
        <br>
        </table>
        <br>
        <form method="post" action="settings.php">
            <input name="id" type="hidden" value="<?php echo $edit_id; ?>" />
            <table class="login">
                <tr>
                    <td class="login">
                        Addresses
                    </td>
                    <td class="login">
                        <input name="addresses" type="text" value="<?php echo $edit_addresses; ?>">
                    </td>
                    <td class="login">
                        (comma separeted groomer machine addresses)
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Port
                    </td>
                    <td class="login">
                        <input name="port" type="text" value="<?php echo $edit_port; ?>">
                    </td>
                    <td class="login">
                        (port to connect on the groomer machine)
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Delay
                    </td>
                    <td class="login">
                        <input name="delay" type="text" value="<?php echo $edit_delay; ?>">
                    </td>
                    <td class="login">
                        (samples elapsed between samples)
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Timeout
                    </td>
                    <td class="login">
                        <input name="timeout" type="text" value="<?php echo $edit_timeout; ?>">
                    </td>
                    <td class="login">
                        (seconds elapsed between connect retries)
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Retries
                    </td>
                    <td class="login">
                        <input name="retries" type="text" value="<?php echo $edit_retries; ?>">
                    </td>
                    <td class="login">
                        (number of connect retries)
                    </td>
                </tr>
                <tr>                
                    <td class="login">
                        Verbose
                    </td>
                    <td class="login">
                        <input name="verbose" type="text" value="<?php echo $edit_verbose; ?>">
                    </td>
                    <td class="login">
                        (verbosity)
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Debug
                    </td>
                    <td class="login">
                        <input name="debug" type="text" value="<?php echo $edit_debug; ?>">
                    </td>
                    <td class="login">
                        (output error messages)
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Title
                    </td>
                    <td class="login">
                        <input name="title" type="text" value="<?php echo $edit_title; ?>">
                    </td>
                    <td class="login">
                        (title to display on the administrator)
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Style
                    </td>
                    <td class="login">
                        <input name="style" type="text" value="<?php echo $edit_style; ?>">
                    </td>
                    <td class="login">
                        (theme to use on the administrator)
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Refresh Timeout
                    </td>
                    <td class="login">
                        <input name="refresh_timeout" type="text" value="<?php echo $edit_refresh_timeout; ?>">
                    </td>
                    <td class="login">
                        (seconds elapsed between refreshes)
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        History Samples
                    </td>
                    <td class="login">
                        <input name="history_samples" type="text" value="<?php echo $edit_history_samples; ?>">
                    </td>
                    <td class="login">
                        (number of history samples)
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Summary Columns
                    </td>
                    <td class="login">
                        <input name="summary_columns" type="text" value="<?php echo $edit_summary_columns; ?>">
                    </td>
                    <td class="login">
                        (number of summary columns per line)
                    </td>
                </tr>
                <tr>
                    <td colspan="3" class="login">
                        <input type="submit" value="Save" />
                    </td>
                </tr>
            </table>
        </form>
<?php require("include/footer.php"); ?>
<?php } else {
header("HTTP/1.1 301 Moved Permanently");
header("Location: error.php?message=Access%20Denied");
exit(0);
} ?>