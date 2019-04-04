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
if (isset($_POST['useredit'])) {
    $newemail = '';
    $newname  = '';
    $newadmin = 0;

    if (isset($_POST['useremail'])) {
        $newemail = $sql->real_escape_string($_POST['useremail']);
    } //isset($_POST['useremail'])

    if (isset($_POST['username'])) {
        $newname = $sql->real_escape_string($_POST['username']);
    } //isset($_POST['username'])

    if (isset($_POST['useradmin'])) {
        $newadmin = 1;
    } //isset($_POST['useradmin'])

    if ($newemail != '' and $newname != '') {
        $sql->query("DELETE FROM " . $sqldatabase . ".users WHERE email='" . $newemail . "'");
        $sql->query("INSERT INTO " . $sqldatabase . ".users ( email, name, admin ) VALUES ('" . $newemail . "','" . $newname . "'," . $newadmin . ")");
    } //$newemail != '' and $newname != ''
} //isset($_POST['useredit'])

if (isset($_GET['userdelete'])) {
    $sql->query("DELETE FROM " . $sqldatabase . ".users WHERE email='" . $sql->real_escape_string($_GET['userdelete']) . "'");
} //isset($_GET['userdelete'])


if (isset($_POST['projectedit'])) {
    $newid   = '';
    $newdecr = '';

    if (isset($_POST['projectid'])) {
        $newid = $sql->real_escape_string($_POST['projectid']);
    } //isset($_POST['projectid'])

    if (isset($_POST['projectdescription'])) {
        $newdecr = $sql->real_escape_string($_POST['projectdescription']);
    } //isset($_POST['projectdescription'])

    if ($newid != '' and $newdecr != '') {


        $sql->query("DELETE FROM " . $sqldatabase . ".projects WHERE projectid='" . $newid . "'");
        $sql->query("INSERT INTO " . $sqldatabase . ".projects ( projectid, description ) VALUES ('" . $newid . "','" . $newdecr . "')");

        $sql->query("DELETE FROM " . $sqldatabase . ".permissions WHERE projectid='" . $newid . "'");

        $userlist = $sql->query("SELECT email FROM " . $sqldatabase . ".users");
        while ($userdetail = $userlist->fetch_assoc()) {

            if (isset($_POST['privilige' . str_replace('.', '_', $userdetail['email'])])) {

                $sql->query("INSERT INTO " . $sqldatabase . ".permissions (email, projectid) VALUES ('" . $userdetail['email'] . "', '" . $newid . "')");
            } //isset($_POST['privilige' . str_replace('.', '_', $userdetail['email'])])
        } //$userdetail = $userlist->fetch_assoc()

    } //$newid != '' and $newdecr != ''


} //isset($_POST['projectedit'])


if (isset($_GET['projectdelete'])) {
    $sql->query("DELETE FROM " . $sqldatabase . ".projects WHERE projectid='" . $sql->real_escape_string($_GET['projectdelete']) . "'");
} //isset($_GET['projectdelete'])






?>
<a href="index.php?page=adminedit&useredit=add" style="float:right;width: 200px;" class="linkButton">Add User</a>
<h2>Users</h2>

<?php
$userlist = $sql->query("SELECT * from " . $sqldatabase . ".users");

?>


<table id="usertable" class="display compact" cellspacing="0" width="100%">
<thead>
<tr><th>Name</th><th>Email</th><th>Level</th><th></th></tr>
</thead><tbody>
<?php

while ($user = $userlist->fetch_assoc()) {
?>

<tr><td><?php
    echo $user['name'];
?></td><td><?php
    echo $user['email'];
?></td><td><?php
    if ($user['admin']) {
        echo 'Admin';
    } //$user['admin']
    else {
        echo 'User';
    }
?></td><td class="right-text"><a style="width:95px;" href="index.php?page=adminedit&useredit=<?php
    echo $user['email'];
?>"  class="linkButton">Edit</a> <a style="width:95px;" href="index.php?page=admin&userdelete=<?php
    echo $user['email'];
?>"  class="linkButton">Delete</a></td></tr>


<?php
} //$user = $userlist->fetch_assoc()

?>
</tbody>
</table>

<hr />
<a href="index.php?page=adminedit&projectedit=add" style="float:right;width: 200px;" class="linkButton">Add Project</a>
<h2>Projects</h2>


<?php
$projectlist = $sql->query("SELECT * from " . $sqldatabase . ".projects");

?>
<table id="projecttable" class="display compact" cellspacing="0">
<thead>
<tr><th>ID</th><th>Description</th><th>Users</th><th></th></tr>
</thead><tbody>
<?php

while ($project = $projectlist->fetch_assoc()) {
?>

<tr><td><?php
    echo $project['projectid'];
?></td><td><?php
    echo $project['description'];
?></td><td><?php

    $userstring = "";

    $userlist = $sql->query("SELECT email FROM " . $sqldatabase . ".permissions WHERE projectid='" . $project['projectid'] . "';");
    while ($row = $userlist->fetch_assoc()) {
        $namequery = $sql->query("SELECT name FROM " . $sqldatabase . ".users WHERE email='" . $row['email'] . "';");
        if ($newrow = $namequery->fetch_assoc()) {
            if ($userstring != "") {
                $userstring = $userstring . ", ";
            } //$userstring != ""



            $userstring = $userstring . "<b>" . $newrow['name'] . "</b> [" . $row['email'] . "]";
        } //$newrow = $namequery->fetch_assoc()
    } //$row = $userlist->fetch_assoc()

    echo $userstring;
?></td><td class="right-text"><a style="width:95px;" href="index.php?page=adminedit&projectedit=<?php
    echo $project['projectid'];
?>"  class="linkButton">Edit</a> <a style="width:95px;" href="index.php?page=admin&projectdelete=<?php
    echo $project['projectid'];
?>"  class="linkButton">Delete</a></td></tr>


<?php
} //$project = $projectlist->fetch_assoc()
?>
</tbody>
</table>



<script> $(document).ready(function() {
    $('#usertable').DataTable( {
        "ordering": false,


    });

        $('#projecttable').DataTable( {
        "ordering": false,
    } );

} );
</script>