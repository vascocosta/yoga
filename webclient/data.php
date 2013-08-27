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
$data = $controller->get_data($_GET["groomer"], $_GET["samples"]);
?>
        <table class="menu">
            <tr><th class="menu"><b>Section:</b> Data</th><th class="menu"><b>Username:</b> <?php echo $_SESSION["username"]; ?></th><th class="menu"><b>Navigation:</b> <a href="summary.php">Summary</a> - <a href="">Help</a> - <a href="index.php">Logout</a></th></tr>
        </table>
        <br>
        <h3>Groomer: <?php echo $_GET["groomer"]; ?></h3>
        <br>
        <table class="view">
            <tr><th class="view">Time</th><th class="view">Groomer Snapshot</th></tr>
            <?php for($row = 0; $row != sizeof($data); $row++) { ?>
                <tr>
                    <td class="view">
                        <?php echo $data[$row]["time"]; ?>
                    </td>
                    <!-- START OF HTML STYLED GROOMER -->
                    <td class="view">
                        <table width="450" height="196" border="0" align="center" cellpadding="0" cellspacing="0" background="images/bg.jpg">
                        <tr>
                            <td height="196">
                                <table width="440" height="180" border="0" align="center" cellpadding="0" cellspacing="0">
                                    <tr>
                                        <td width="80" background="images/bg-cc.jpg">
                                            <table width="91%" border="0" align="center" cellpadding="0" cellspacing="2">
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["clock"] != "" ? "images/lgreen.jpg" : "images/lgrey.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">CLK</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["clock"] == "internal" ? "images/lyellow.jpg" : "images/lgrey.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">INT.CLK</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo substr($data[$row]["clock"], 0, 4) == "ext_" ? "images/lyellow.jpg" : "images/lgrey.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">EXT.CLK</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["clock"] == "looptimed" ? "images/lyellow.jpg" : "images/lgrey.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font size="1" face="Arial, Helvetica, sans-serif">LOOP.CLK</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["clock"] == "ext_2mhz" ? "images/lyellow.jpg" : "images/lgrey.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">EXT.CLK2048 </font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["clock"] == "ext_1.5mhz" ? "images/lyellow.jpg" : "images/lgrey.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">EXT.CLK1544</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="images/lgrey.jpg" width="7" height="15" /></td>
                                                    <td valign="middle"><font size="1" face="Arial, Helvetica, sans-serif">ALARM</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="images/lgrey.jpg" width="7" height="15" /></td>
                                                    <td valign="middle"><font size="1" face="Arial, Helvetica, sans-serif">SHELF TEST </font></td>
                                                </tr>
                                            </table>
                                        </td>
                                        <td width="45" background="<?php echo $data[$row]["configs"][0] != "UNQP" ? "images/bg-e1.jpg" : "images/bg-ue.jpg"; ?>">
                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="2">
                                                <?php if($data[$row]["configs"][0] != "UNQP") { ?>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][0][0] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port1</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][0][1] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port2 </font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][0][2] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port3</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][0][3] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port4</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][0][4] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port5</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][0][5] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port6</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][0][6] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port7</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][0][7] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port8</font></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </td>
                                        <td width="45" background="<?php echo $data[$row]["configs"][1] != "UNQP" ? "images/bg-e1.jpg" : "images/bg-ue.jpg"; ?>">
                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="2">
                                                <?php if($data[$row]["configs"][1] != "UNQP") { ?>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][1][0] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port1</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][1][1] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port2 </font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][1][2] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port3</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][1][3] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port4</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][1][4] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port5</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][1][5] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port6</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][1][6] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port7</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][1][7] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port8</font></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </td>
                                        <td width="45" background="<?php echo $data[$row]["configs"][2] != "UNQP" ? "images/bg-e1.jpg" : "images/bg-ue.jpg"; ?>">
                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="2">
                                                <?php if($data[$row]["configs"][2] != "UNQP") { ?>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][2][0] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port1</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][2][1] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port2 </font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][2][2] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port3</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][2][3] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port4</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][2][4] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port5</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][2][5] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port6</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][2][6] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port7</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][2][7] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port8</font></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </td>
                                        <td width="45" background="<?php echo $data[$row]["configs"][3] != "UNQP" ? "images/bg-e1.jpg" : "images/bg-ue.jpg"; ?>">
                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="2">
                                                <?php if($data[$row]["configs"][3] != "UNQP") { ?>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][3][0] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port1</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][3][1] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port2 </font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][3][2] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port3</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][3][3] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port4</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][3][4] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port5</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][3][5] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port6</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][3][6] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port7</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][3][7] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port8</font></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </td>
                                        <td width="45" background="<?php echo $data[$row]["configs"][4] != "UNQP" ? "images/bg-e1.jpg" : "images/bg-ue.jpg"; ?>">
                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="2">
                                                <?php if($data[$row]["configs"][4] != "UNQP") { ?>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][4][0] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port1</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][4][1] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port2 </font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][4][2] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port3</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][4][3] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port4</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][4][4] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port5</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][4][5] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port6</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][4][6] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port7</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][4][7] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port8</font></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </td>
                                        <td width="45" background="<?php echo $data[$row]["configs"][5] != "UNQP" ? "images/bg-e1.jpg" : "images/bg-ue.jpg"; ?>">
                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="2">
                                                <?php if($data[$row]["configs"][5] != "UNQP") { ?>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][5][0] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port1</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][5][1] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port2 </font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][5][2] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port3</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][5][3] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port4</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][5][4] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port5</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][5][5] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port6</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][5][6] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port7</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][5][7] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port8</font></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </td>
                                        <td width="45" background="<?php echo $data[$row]["configs"][6] != "UNQP" ? "images/bg-e1.jpg" : "images/bg-ue.jpg"; ?>">
                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="2">
                                                <?php if($data[$row]["configs"][6] != "UNQP") { ?>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][6][0] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port1</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][6][1] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port2 </font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][6][2] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port3</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][6][3] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port4</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][6][4] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port5</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][6][5] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port6</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][6][6] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port7</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][6][7] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port8</font></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </td>
                                        <td width="45" background="<?php echo $data[$row]["configs"][7] != "UNQP" ? "images/bg-e1.jpg" : "images/bg-ue.jpg"; ?>">
                                            <table width="80%" border="0" align="center" cellpadding="0" cellspacing="2">
                                                <?php if($data[$row]["configs"][7] != "UNQP") { ?>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][7][0] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port1</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][7][1] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port2 </font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][7][2] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port3</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][7][3] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port4</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][7][4] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port5</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][7][5] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port6</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][7][6] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port7</font></td>
                                                </tr>
                                                <tr>
                                                    <td valign="middle"><img src="<?php echo $data[$row]["alarms"][7][7] != "NO_SYNC" ? "images/lgreen.jpg" : "images/lred.jpg"; ?>" width="7" height="15" /></td>
                                                    <td valign="middle"><font face="Arial, Helvetica, sans-serif" size="1">Port8</font></td>
                                                </tr>
                                                <?php } ?>
                                            </table>
                                        </td>
                                    </tr>
                                </table>
                            </td>
                        </tr>
                        </table>
                    </td>
                    <!-- END OF HTML STYLED GROOMER -->
                </tr>
            <?php } ?>
        </table>
<?php require("include/footer.php"); ?>
<?php } else {
header("HTTP/1.1 301 Moved Permanently");
header("Location: error.php?message=Access%20Denied");
exit(0);
} ?>