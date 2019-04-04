<?php
/*
Copyright 2018 The Zhou Laboratory, Earlham Institute, Norwich, UK

Permission is hereby granted, free of charge, to any person obtaining a copy of this software and associated
documentation files (the "Software"), to deal in the Software without restriction, including without limitation the
rights to use, copy, modify, merge, publish, distribute, sublicense, and/or sell copies of the Software, and to permit
persons to whom the Software is furnished to do so, subject to the following conditions:

The above copyright notice and this permission notice shall be included in all copies or substantial portions of the
Software.

THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR IMPLIED, INCLUDING BUT NOT LIMITED TO THE
WARRANTIES OF MERCHANTABILITY, FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE AUTHORS OR
COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR
OTHERWISE, ARISING FROM, OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE SOFTWARE.
*/
?>


<?php
if (isset($_GET['useredit'])) {

    $emailval = "";
    $nameval  = "";
    $adminval = "";

    if ($_GET['useredit'] != "add") {
        $userlookup = $sql->query("SELECT * FROM " . $sqldatabase . ".users WHERE email='" . $sql->real_escape_string($_GET['useredit']) . "';");
        if ($userdetails = $userlookup->fetch_assoc()) {
            $emailval = $userdetails['email'];
            $nameval  = $userdetails['name'];
            $adminval = $userdetails['admin'];
        } //$userdetails = $userlookup->fetch_assoc()
    } //$_GET['useredit'] != "add"

?>


<h2>Add/Edit User</h2>

<div id="loginform">
<form method="post" action="index.php?page=admin">
<table id="formtable">
<input type="hidden" name="useredit" value="edit" />
<tr><td>Name:</td><td><input type="text" name="username" value="<?php
    echo $nameval;
?>" /></td></tr>
<tr><td>Email:</td><td><?php
    if ($emailval == "") {
?><input type="text" name="useremail" value="<?php
        echo $emailval;
?>" /><?php
    } //$emailval == ""
    else {
        echo $emailval;
?> <input type="hidden" name="useremail" value="<?php
        echo $emailval;
?>" /> <?php
    }
?></td></tr>
<tr><td>Admin:</td><td><input type="checkbox" name="useradmin" value="admin" <?php
    if ($adminval) {
        echo 'checked="checked"';
    } //$adminval
?> /></td></tr>
<tr><td colspan="2"><input type="submit" value="Submit" /></td></tr>
</table>
</form>
</div>


<?php

} //isset($_GET['useredit'])


if (isset($_GET['projectedit'])) {
    $projectid   = "";
    $description = "";

    if ($_GET['projectedit'] != "add") {
        $projectlookup = $sql->query("SELECT * FROM " . $sqldatabase . ".projects WHERE projectid='" . $sql->real_escape_string($_GET['projectedit']) . "';");
        if ($projectdetails = $projectlookup->fetch_assoc()) {
            $projectid   = $projectdetails['projectid'];
            $description = $projectdetails['description'];
        } //$projectdetails = $projectlookup->fetch_assoc()
    } //$_GET['projectedit'] != "add"
?>

<h2>Add/Edit Project</h2>

<div id="loginform">
<form method="post" action="index.php?page=admin">
<table id="formtable">
<input type="hidden" name="projectedit" value="edit" />
<tr><td>Project ID:</td><td><?php
    if ($projectid == "") {
?><input type="text" name="projectid" value="<?php
        echo $projectid;
?>" /><?php
    } //$projectid == ""
    else {
        echo $projectid;
?> <input type="hidden" name="projectid" value="<?php
        echo $projectid;
?>" /> <?php
    }
?></td></tr>
<tr><td>Description:</td><td><input type="text" name="projectdescription" value="<?php
    echo $description;
?>" /></td></tr>

<tr><td>Users:</td><td>
<?php
    $userlist = $sql->query("SELECT * FROM " . $sqldatabase . ".users");
    while ($userdetails = $userlist->fetch_assoc()) {

        $userauth = false;

        $res      = $sql->query("SELECT COUNT(*) AS total from " . $sqldatabase . ".permissions WHERE email='" . $userdetails['email'] . "' AND projectid='" . $projectid . "'");
        $rescount = $res->fetch_assoc();

        if ($rescount['total'] > 0) {
            $userauth = true;
        } //$rescount['total'] > 0

?>
       <input type="checkbox" name="privilige<?php
        echo $userdetails['email'];
?>"  <?php
        if ($userauth) {
            echo 'checked="checked"';
        } //$userauth
?> /> <?php
        echo $userdetails['email'];
?><br />
        <?php
    } //$userdetails = $userlist->fetch_assoc()
?>
</td></tr>

<tr><td colspan="2"><input type="submit" value="Submit" /></td></tr>
</table>
</form>
</div>

<?php

} //isset($_GET['projectedit'])


?>