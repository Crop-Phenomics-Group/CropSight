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

if (!isset($_SESSION['project_selection'])) {
    $projects = $sql->query("SELECT * FROM " . $sqldatabase . ".projectgroup WHERE active=1;");
    if ($result = $projects->fetch_assoc()) {
        $_SESSION['project_selection'] = $result['groupid'];
    } //$result = $projects->fetch_assoc()
    else {
        $_SESSION['project_selection'] = '';
    }
} //!isset($_SESSION['project_selection'])




$nodeselection = "none";
$class         = "navButtonGrey";


if (!isset($_SESSION['view'])) {
    $_SESSION['view'] = 'list';
} //!isset($_SESSION['view'])

if (isset($_GET['view'])) {
    if ($_GET['view'] == 'grid') {
        $_SESSION['view'] = 'grid';
    } //$_GET['view'] == 'grid'
    else {
        $_SESSION['view'] = 'list';
    }
} //isset($_GET['view'])

if ($authorised == true) {
?>

<div id="navbar">

<?php
    if ($admin) {
        if (isset($_GET['selection'])) {
            $_SESSION['project_selection'] = $sql->real_escape_string($_GET['selection']);
        } //isset($_GET['selection'])
        elseif (isset($_GET['page']) && $_GET['page'] == 'admin') {
            $class = "navButton";
        } //isset($_GET['page']) && $_GET['page'] == 'admin'
?>

<div style="float: right">

<?php
        include('loginstate.php');
?>


<a href="index.php?page=admin" class="<?php
        echo $class;
?>">Admin</a>


</div>


<?php


        $projects = $sql->query("SELECT * FROM " . $sqldatabase . ".projectgroup WHERE active=1");



        while ($project = $projects->fetch_assoc()) {

            if ($_SESSION['project_selection'] == $project['groupid']) {
                $class = "navButton";
            } //$_SESSION['project_selection'] == $project['groupid']
            else {
                $class = "navButtonGrey";
            }

?>
<a href="index.php?selection=<?php
            echo $project['groupid'];
?>" class="<?php
            echo $class;
?>"><?php
            echo $project['name'];
?></a>

<?php
        } //$project = $projects->fetch_assoc()
?>

<div class="dropdown">
  <button onclick="historicaldatabutton()" class="dropbtn">Historical Data</button>
  <div id="historicaldata" class="dropdown-content">


    <?php
        $projects = $sql->query("SELECT * FROM " . $sqldatabase . ".projectgroup WHERE active=0");
        while ($project = $projects->fetch_assoc()) {
?>

    <a href="index.php?selection=<?php
            echo $project['groupid'];
?>"><?php
            echo $project['name'];
?></a>


    <?php

        } //$project = $projects->fetch_assoc()
?>

  </div>
</div>

<?php
    } //$admin

} //$authorised == true
else {

?>

    <div style="float: right">

<?php
    include('loginstate.php');
?>

</div>




      <?php

    if (isset($_GET['selection'])) {
        $_SESSION['project_selection'] = $sql->real_escape_string($_GET['selection']);


        $permissiontest  = $sql->query("SELECT COUNT(email) as total FROM " . $sqldatabase . ".permissions WHERE email='" . $_SESSION['email'] . "' AND projectid='" . $_SESSION['project_selection'] . "';");
        $permissioncount = $permissiontest->fetch_assoc();
        if ($permissioncount['total'] == 0) {
            $projectselection = "none";
        } //$permissioncount['total'] == 0
    } //isset($_GET['selection'])

    if (isset($_SESSION['email'])) {
        $permissions = $sql->query("SELECT * FROM " . $sqldatabase . ".permissions WHERE email='" . $_SESSION['email'] . "';");

        while ($project = $permissions->fetch_assoc()) {

            if ($_SESSION['project_selection'] == "none") {
                $_SESSION['project_selection'] = $project['projectid'];
            } //$_SESSION['project_selection'] == "none"

            if ($_SESSION['project_selection'] == $project['projectid']) {
                $class = "navButton";
            } //$_SESSION['project_selection'] == $project['projectid']
            else {
                $class = "navButtonGrey";
            }


        } //$project = $permissions->fetch_assoc()
        $details = $sql->query("SELECT * FROM " . $sqldatabase . ".projects WHERE projectid='" . $project['projectid'] . "';");
        $res     = $details->fetch_assoc();
?>
<a href="index.php?selection=<?php
        echo $res['projectid'];
?>" class="<?php
        echo $class;
?>"><?php
        echo $res['description'];
?></a>
<?php

    } //isset($_SESSION['email'])

}
?>

    </div>

    <?php

if (isset($_GET['node'])) {
    $nodeproject = $sql->query("SELECT projectid from " . $sqldatabase . ".pistatus WHERE macaddress = '" . $sql->real_escape_string($_GET['node']) . "';");
    if ($projectid = $nodeproject->fetch_assoc()) {
        if ($admin) {
            $nodeselection = $sql->real_escape_string($_GET['node']);
        } //$admin
        else {
            $usercount = $sql->query("SELECT COUNT(projectid) AS total FROM " . $sqldatabase . ".permissions WHERE projectid='" . $projectid['projectid'] . "' AND email='" . $_SESSION['email'] . "';");
            $countres  = $usercount->fetch_assoc();
            if ($countres['total'] > 0) {
                $nodeselection = $sql->real_escape_string($_GET['node']);
            } //$countres['total'] > 0
        }
    } //$projectid = $nodeproject->fetch_assoc()
} //isset($_GET['node'])



?>

<script>
/* When the user clicks on the button,
toggle between hiding and showing the dropdown content */
function historicaldatabutton() {
    document.getElementById("historicaldata").classList.toggle("show");
}

// Close the dropdown menu if the user clicks outside of it
window.onclick = function(event) {
  if (!event.target.matches('.dropbtn')) {

    var dropdowns = document.getElementsByClassName("dropdown-content");
    var i;
    for (i = 0; i < dropdowns.length; i++) {
      var openDropdown = dropdowns[i];
      if (openDropdown.classList.contains('show')) {
        openDropdown.classList.remove('show');
      }
    }
  }
}
</script>