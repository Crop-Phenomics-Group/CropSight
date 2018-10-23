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

include("nodegridcell.php");

function check_device_status($id)
{
    $devicelist = $GLOBALS['sql']->query("SELECT * FROM " . $GLOBALS['sqldatabase'] . ".pistatus WHERE macaddress = '" . $id . "';");
    if ($device = $devicelist->fetch_assoc()) {

        if ($device['projectid'] != '') {
            $projectlist = $GLOBALS['sql']->query("SELECT * FROM " . $GLOBALS['sqldatabase'] . ".projects WHERE projectid = '" . $device['projectid'] . "';");
            if ($project = $projectlist->fetch_assoc()) {
                if ($project['active'] == 0) {
                    return 'inactive';
                } //$project['active'] == 0
            } //$project = $projectlist->fetch_assoc()
        } //$device['projectid'] != ''

        $time    = $device['lastupdate'];
        $curtime = time();

        $status = 'ok';

        if (($curtime - $time) > $device['duration'] * 66) {
            $status = 'offline';
        } //($curtime - $time) > $device['duration'] * 66
        else {
            $storeCount = $GLOBALS['sql']->query("SELECT online FROM " . $GLOBALS['sqldatabase'] . ".pistorage WHERE macaddress='" . $device['macaddress'] . "';");
            while ($row = $storeCount->fetch_assoc()) {
                if ($row['online'] == 0) {
                    $status = 'warning';
                } //$row['online'] == 0
            } //$row = $storeCount->fetch_assoc()
        }

        return $status;

    } //$device = $devicelist->fetch_assoc()
    else {
        return 'offline';
    }
}

function get_uptime($uptime, $lastupdate)
{
    $upduration = (int) (time() - $uptime);
    return secondsToTime($upduration);

}

function overall_runtime($sql, $sqldatabase, $macaddress)
{
    $timeQuery = $sql->query("SELECT timestamp FROM " . $sqldatabase . ".sensorreading WHERE macaddress='" . $macaddress . "' ORDER BY timestamp ASC;");

    if ($timerow = $timeQuery->fetch_assoc()) {
        return secondsToTime((int) (time() - strtotime($timerow['timestamp'])));

    } //$timerow = $timeQuery->fetch_assoc()
    else {
        $lastupdatequery = $sql->query("SELECT uptime, lastupdate FROM " . $sqldatabase . ".pistatus WHERE macaddress='" . $macaddress . "';");
        if ($row = $lastupdatequery->fetch_assoc()) {
            return get_uptime($row['uptime'], $row['lastupdate']);
        } //$row = $lastupdatequery->fetch_assoc()
    }

    return 0;
}



$gridwidth = 6;

function secondsToTime($seconds)
{
    $dtF = new \DateTime('@0');
    $dtT = new \DateTime("@$seconds");
    return $dtF->diff($dtT)->format('%a days, %h hours and %i minutes');
}

?>

<div id="pagetools">

<table cellspacing="0">

<tr>
<td id="pagetoolslabel" class="pagetoolscell">PAGE<br />TOOLS</td>
<td class="pagetoolscell"><div class="pagetoolsbutton" ><img class="pagetoolsicon" src="images/icon_printer.png" onclick="window.print();return false;" /></div></td>
<td class="pagetoolscell"><a class="pagetoolsbutton" href="index.php" ><img class="pagetoolsicon" src="images/icon_refresh.png" /></a></td>
<td class="pagetoolscell"><a class="pagetoolsbutton" href="index.php?view=list"><img class="pagetoolsicon" src="images/icon_list.png" /></a></td>
<td class="pagetoolscell"><a class="pagetoolsbutton" href="index.php?view=grid"><img class="pagetoolsicon" src="images/icon_grid.png" /></a></td>

</tr>

</table>

</div>

<div class="print">

<?php
$nodes;

if ($_SESSION['view'] == 'list') {
    include('nodetable.php');
    if ($nodeselection != "none") {
        include('graphdata.php');
    } //$nodeselection != "none"
} //$_SESSION['view'] == 'list'
else {
    if ($nodeselection != "none") {
        $nodenames = $sql->query("SELECT hostname FROM " . $sqldatabase . ".pistatus WHERE macaddress = '" . $nodeselection . "' ORDER BY hostname ASC;");
        if ($name = $nodenames->fetch_assoc()) {
            echo "<h2>" . $name['hostname'] . "</h2>";
        } //$name = $nodenames->fetch_assoc()

        $nodes = $sql->query("SELECT * FROM " . $sqldatabase . ".pistatus WHERE macaddress = '" . $nodeselection . "' ORDER BY hostname ASC;");
        include('nodetable.php');
        if ($nodeselection != "none") {
            include('graphdata.php');
        } //$nodeselection != "none"

    } //$nodeselection != "none"




    ////////////////////////////////////MAP

    include('map.php');


    /////////////////////////////////////


    $projects = $sql->query("SELECT projectid, description FROM " . $sqldatabase . ".projects WHERE projectgroup='" . $_SESSION['project_selection'] . "' AND active=1 ORDER BY projectid DESC;");

    while ($project = $projects->fetch_assoc()) {

        echo '<div class="projectcontainer">';

        echo "<h2>" . $project['description'] . "</h2>";

        renderMap($sql, $sqldatabase, $project['projectid']);

        $grid = $sql->query("SELECT * FROM " . $sqldatabase . ".pistatus WHERE projectid = '" . $project['projectid'] . "' ORDER BY hostname ASC;");


?>

<?php

        $columnindex = 0;

        while ($gridnode = $grid->fetch_assoc()) {

            $columnindex = node_grid_cell($gridnode, $gridwidth, $sql, $sqldatabase, $columnindex);

        } //$gridnode = $grid->fetch_assoc()

?>

<div style="clear:both;"></div>

</div>



<?php
    } //$project = $projects->fetch_assoc()
?>

 <?php
    $projects = $sql->query("SELECT projectid, description FROM " . $sqldatabase . ".projects WHERE projectgroup='" . $_SESSION['project_selection'] . "' AND active=0 ORDER BY projectid DESC;");

    while ($project = $projects->fetch_assoc()) {

        echo '<div class="projectcontainer">';

        echo "<h2>" . $project['description'] . "</h2>";


        renderMap($sql, $sqldatabase, $project['projectid']);

        echo "</div>";

    } //$project = $projects->fetch_assoc()
?>

</div>

<?php
}
?>