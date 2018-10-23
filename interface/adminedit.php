<?php
/*
Copyright 2018 The Zhou Laboratory, Earlham Institute, Norwich, UK

Redistribution and use in source and binary forms, with or without modification,
are permitted provided that the following conditions are met:

1. Redistributions of source code must retain the above copyright notice, this
   list of conditions and the following disclaimer.

2. Redistributions in binary form must reproduce the above copyright notice, this
   list of conditions and the following disclaimer in the documentation and/or
   other materials provided with the distribution.

3. Neither the name of the copyright holder nor the names of its contributors may
   be used to endorse or promote products derived from this software without
   specific prior written permission.

THIS SOFTWARE IS PROVIDED BY THE COPYRIGHT HOLDERS AND CONTRIBUTORS "AS IS" AND
ANY EXPRESS OR IMPLIED WARRANTIES, INCLUDING, BUT NOT LIMITED TO, THE IMPLIED
WARRANTIES OF MERCHANTABILITY AND FITNESS FOR A PARTICULAR PURPOSE ARE DISCLAIMED.
IN NO EVENT SHALL THE COPYRIGHT HOLDER OR CONTRIBUTORS BE LIABLE FOR ANY DIRECT,
INDIRECT, INCIDENTAL, SPECIAL, EXEMPLARY, OR CONSEQUENTIAL DAMAGES (INCLUDING, BUT
NOT LIMITED TO, PROCUREMENT OF SUBSTITUTE GOODS OR SERVICES; LOSS OF USE, DATA, OR
PROFITS; OR BUSINESS INTERRUPTION) HOWEVER CAUSED AND ON ANY THEORY OF LIABILITY,
WHETHER IN CONTRACT, STRICT LIABILITY, OR TORT (INCLUDING NEGLIGENCE OR OTHERWISE)
ARISING IN ANY WAY OUT OF THE USE OF THIS SOFTWARE, EVEN IF ADVISED OF THE POSSIBILITY
OF SUCH DAMAGE.
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