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
if($_GET['action'] == "delete") {
    $controller->delete_user($_GET["id"]);
}
else if($_GET['action'] == "edit") {
    $user = $controller->get_user($_GET["id"]);
    $edit_id = $user[0]["id"];
    $edit_privilege = $user[0]["privilege"];
    $edit_username = $user[0]["username"];
    $edit_password = $user[0]["password"];
    $edit_email = $user[0]["email"];
    $edit_phone = $user[0]["phone"];
}
else if($_SERVER["REQUEST_METHOD"] == "POST") {
    $data["id"] = $_POST["id"];
    $data["privilege"] = $_POST["privilege"];
    $data["username"] = $_POST["username"];
    $data["password"] = $_POST["password"];
    $data["email"] = $_POST["email"];
    $data["phone"] = $_POST["phone"];
    if($_POST["id"]) {
        $controller->update_user($data);
    }
    else {
        $controller->insert_user($data);
    }
}
else {
}
$users = $controller->get_users();
?>
        <table class="menu">
            <tr><th class="menu"><b>Section:</b> Users</th><th class="menu"><b>Username:</b> <?php echo $_SESSION["username"]; ?></th><th class="menu"><b>Navigation:</b> <a href="summary.php">Summary</a> - <a href="">Help</a> - <a href="index.php">Logout</a></th></tr>
        </table>
        <br>
        <form method="post" action="users.php">
            <input name="id" type="hidden" value="<?php echo $edit_id; ?>" />
            <table class="login">
                <tr>
                    <td class="login">
                        Privilege
                    </td>
                    <td class="login">
                        <select name="privilege">
                            <option value="user" <?php if($edit_privilege == "user") { echo "selected=\"selected\""; } ?>>user</option>
                            <option value="admin" <?php if($edit_privilege == "admin") { echo "selected=\"selected\""; } ?>>admin</option>
                        </select>
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Username
                    </td>
                    <td class="login">
                        <input name="username" type="text" value="<?php echo $edit_username; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Password
                    </td>
                    <td class="login">
                        <input name="password" type="password" value="<?php echo $edit_password; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Email
                    </td>
                    <td class="login">
                        <input name="email" type="text" value="<?php echo $edit_email; ?>">
                    </td>
                </tr>
                <tr>
                    <td class="login">
                        Phone
                    </td>
                    <td class="login">
                        <input name="phone" type="text" value="<?php echo $edit_phone; ?>">
                    </td>
                </tr>
                <tr>
                    <td colspan="2" class="login">
                        <input type="submit" value="Insert" />
                    </td>
                </tr>
            </table>
        </form>
        <br>
        <table class="view">
            <tr><th class="view">Privilege</th><th class="view">Username</th><th class="view">Email</th><th class="view">Phone</th><th class="view">Operations</th></tr>
            <?php for($row = 0; $row != sizeof($users); $row++) { ?>
                <tr>
                    <td class="view">
                        <?php echo $users[$row]["privilege"]; ?>
                    </td>
                    <td class="view">
                        <?php echo $users[$row]["username"]; ?>
                    </td>
                    <td class="view">
                        <?php echo $users[$row]["email"]; ?>
                    </td>
                    <td class="view">
                        <?php echo $users[$row]["phone"]; ?>
                    </td>
                    <td class="view">
                        <a href="users.php?action=delete&id=<?php echo $users[$row]["id"]; ?>">delete</a>
                        <a href="users.php?action=edit&id=<?php echo $users[$row]["id"]; ?>">edit</a>
                    </td>
                </tr>
            <?php } ?>
        </table>
<?php require("include/footer.php"); ?>
<?php } else {
header("HTTP/1.1 301 Moved Permanently");
header("Location: error.php?message=Access%20Denied");
exit(0);
} ?>