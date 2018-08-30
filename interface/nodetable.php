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

include("nodetablerow.php");

function nodeTable($sql, $sqldatabase, $nodes, $title, $tablename, $active)
{

    echo '<div class="projectcontainer">';

    echo '<h2>' . $title . "</h2>";
?>

<div style="clear:both;"></div>

        <table id="<?php
    echo $tablename;
?>" class="display compact" cellspacing="0">
        <thead>

        <?php
    if ($active == TRUE) {
?>
           <tr style="background-color: #ddeedd;"><th >Workstation</th><th >Daily Image</th><th >Working Status</th><th >Experiments</th><th >Workstation ID</th><th>Data Management</th></tr>
        <?php
    } //$active == TRUE
    else {
?>
           <tr style="background-color: #ddeedd;"><th class="shrink">Workstation</th><th class="shrink">Last Image Captured</th><th class="shrink">Experiment Status</th><th class="expand">Data Management</th></tr>
        <?php
    }
?>

        </thead>
        <tbody>

<?php

    while ($node = $nodes->fetch_assoc()) {

        node_table_row($node, $active, $sql, $sqldatabase);

    } //$node = $nodes->fetch_assoc()
?>
   </tbody>
    </table>

<script>
    $(document).ready(function() {
     var table = $('#<?php
    echo $tablename;
?>').DataTable( {
     fixedHeader: {
            header: true,
            footer: false
        },
        "paging":   false,
        "ordering": false,
        "info":     false,
        "searching":false
    } );


} );
</script>
</div>
<?php
}
?>

<?php
if ($nodeselection != "none") {
    $nodes = $sql->query("SELECT * FROM " . $sqldatabase . ".pistatus WHERE macaddress = '" . $nodeselection . "' ORDER BY hostname ASC;");

    $inactive_project = $sql->query("SELECT * FROM " . $sqldatabase . ".pistatus WHERE macaddress = '" . $nodeselection . "';");
    $active           = FALSE;

    if ($projectquery = $inactive_project->fetch_assoc()) {

        $inactive_query = $sql->query("SELECT * FROM " . $sqldatabase . ".projects WHERE projectid='" . $projectquery['projectid'] . "';");


        if ($inactivestatus = $inactive_query->fetch_assoc()) {
            if ($inactivestatus['active'] == '1') {
                $active = TRUE;
            } //$inactivestatus['active'] == '1'
        } //$inactivestatus = $inactive_query->fetch_assoc()
    } //$projectquery = $inactive_project->fetch_assoc()

    nodeTable($sql, $sqldatabase, $nodes, '', 'nodeTable', $active);
} //$nodeselection != "none"
else {
    $projects = $sql->query("SELECT projectid, description FROM " . $sqldatabase . ".projects WHERE projectgroup='" . $_SESSION['project_selection'] . "' ORDER BY projectid DESC;");

    while ($project = $projects->fetch_assoc()) {
        $inactive_query = $sql->query("SELECT * FROM " . $sqldatabase . ".projects WHERE projectid='" . $project['projectid'] . "';");
        $active         = FALSE;

        if ($inactivestatus = $inactive_query->fetch_assoc()) {
            if ($inactivestatus['active'] == '1') {
                $active = TRUE;
            } //$inactivestatus['active'] == '1'
        } //$inactivestatus = $inactive_query->fetch_assoc()

        $nodes = $sql->query("SELECT * FROM " . $sqldatabase . ".pistatus WHERE projectid = '" . $project['projectid'] . "' ORDER BY hostname ASC;");

        nodeTable($sql, $sqldatabase, $nodes, $project['description'], 'table' . $project['projectid'], $active);
    } //$project = $projects->fetch_assoc()
}

?>