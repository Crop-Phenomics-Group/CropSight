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