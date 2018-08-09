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



    include('monitornodes.php');
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
        include('monitornodes.php');
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
            $columnindex = $columnindex + 1;
            if ($columnindex > $gridwidth) {

                $columnindex = 0;
            } //$columnindex > $gridwidth


            $status = check_device_status($gridnode['macaddress']);

?>
<div class="gridcell">

<h3><?php
            echo $gridnode['hostname'];
?></h3>

<div style="clear:both;"></div>

<a href="index.php?node=<?php
            echo $gridnode['macaddress'];
?>" style="text-decoration: none;">
<img src="fetchimage.php?macaddress=<?php
            echo $gridnode['macaddress'];
?>" alt="<?php
            echo $gridnode['hostname'];
?> Sample Image" class="thumbnailgrid" />
</a>
<br />



<?php

            $timeQuery      = $sql->query("SELECT timestamp FROM " . $sqldatabase . ".sensorreading WHERE macaddress='" . $gridnode['macaddress'] . "' ORDER BY timestamp ASC;");
            $overallRuntime = 0;

            if ($timerow = $timeQuery->fetch_assoc()) {
                $overallRuntime = (int) (time() - strtotime($timerow['timestamp']));

            } //$timerow = $timeQuery->fetch_assoc()

?>



<?php

            if ($status == 'ok') {
?>
               <a class="statusokgrid" href="index.php?node=<?php
                echo $gridnode['macaddress'];
?>"><?php


                echo overall_runtime($sql, $sqldatabase, $gridnode['macaddress']);


?></a>
            <?php
            } //$status == 'ok'
            elseif ($status == 'warning') {
?>
               <a class="statuswarninggrid" href="index.php?node=<?php
                echo $gridnode['macaddress'];
?>"><?php

                echo overall_runtime($sql, $sqldatabase, $gridnode['macaddress']);
?></a>
            <?php
            } //$status == 'warning'
                elseif ($status == 'inactive') {
?>
               <a class="statuscompletegrid" href="index.php?node=<?php
                echo $gridnode['macaddress'];
?>">Exp. Completed<br/><?php
                echo $gridnode['lastupdate'];
?></a>

            <?php
            } //$status == 'inactive'
            else {
?>

        <a class="statusofflinegrid" href="index.php?node=<?php
                echo $gridnode['macaddress'];
?>">OFFLINE<br/><?php
                echo $gridnode['lastupdate'];
?></a>

<?php
            }
?>

</div>



<?php

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