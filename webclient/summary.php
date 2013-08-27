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
if($_SESSION["permission"] == "yes") {
?>
<?php require("include/configuration.php"); ?>
<?php require("include/controller.class.php"); ?>
<?php require("include/header.php"); ?>
<?php
$controller = new Controller($port, $timeout, $retries, $mysql_host, $mysql_user, $mysql_password, $mysql_database, $mysql_table_prefix);
$groomers = $controller->get_groomers();
$groomers_count = sizeof($groomers);
?>
        <table class="menu">
            <tr><th class="menu"><b>Section:</b> Summary</th><th class="menu"><b>Username:</b> <?php echo $_SESSION["username"]; ?></th><th class="menu"><b>Navigation:</b> <?php if($_SESSION["admin"] == "yes") { ?><a href="settings.php">Settings</a> - <?php } ?><?php if($_SESSION["admin"] == "yes") { ?><a href="users.php">Users</a> - <?php } ?><a href="">Help</a> - <a href="index.php?permission=no">Logout</a></th></tr>
        </table>
        <br>
        <br>
        <table class="summary">
            <tr><th class="summary">Total Groomers</th><th class="summary">Groomers Online</th><th class="summary">Groomers Offline</th></tr>
            <tr><td class="summary"><?php echo $groomers_count; ?></td><td class="summary">N/A</td><td class="summary">N/A</td></tr>
        </table>
        <br>
        <br>
        <table cellspacing="10">
            <?php for($row = 0; $row != ceil(($groomers_count / $summary_columns)); $row++) { ?>
            <tr>
                <?php for($column = $row * $summary_columns; $column != min($row * $summary_columns + $summary_columns, $groomers_count); $column++) { ?>
                <td>
                    <table class="summary">
                        <tr><td colspan="3" class="summary"><?php echo str_replace("_", ".", str_replace($mysql_table_prefix, "", $groomers[$column][0])); ?><br><img src="images/vcl-mc-64.jpg"/></td></tr>
                        <tr><td class="summary"><a href="data.php?groomer=<?php echo str_replace("_", ".", str_replace($mysql_table_prefix, "", $groomers[$column][0])); ?>&samples=1&refresh=yes">Monitor</a></td><td class="summary"><a href="data.php?groomer=<?php echo str_replace("_", ".", str_replace($mysql_table_prefix, "", $groomers[$column][0])); ?>&samples=<?php echo $history_samples; ?>">History</a></td><td class="summary"><?php if($_SESSION["admin"] == "yes") { ?><a href="mapping.php?groomer=<?php echo str_replace("_", ".", str_replace($mysql_table_prefix, "", $groomers[$column][0])); ?>">Mapping</a><?php } ?></td></tr>
                    </table>
                </td>
                <?php } ?>
            </tr>
            <?php } ?>
        </table>
<?php require("include/footer.php"); ?>
<?php } else {
header("HTTP/1.1 301 Moved Permanently");
header("Location: error.php?message=Access%20Denied");
exit(0);
} ?>