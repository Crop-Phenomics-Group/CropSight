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
$macaddress   = $_GET['macaddress'];
$projectgroup = '';
$project      = '';

function human_filesize($bytes, $decimals = 2)
{
    $sz     = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

$pj_query = $sql->query("SELECT * FROM " . $sqldatabase . ".pistatus WHERE macaddress='" . $macaddress . "';");

if ($pj = $pj_query->fetch_assoc()) {
    $project = $pj['projectid'];
} //$pj = $pj_query->fetch_assoc()
else {
    die("No project");
}

$pg_query = $sql->query("SELECT * FROM " . $sqldatabase . ".projects WHERE projectid='" . $project . "';");

if ($pg = $pg_query->fetch_assoc()) {
    $projectgroup = $pg['projectgroup'];
} //$pg = $pg_query->fetch_assoc()
else {
    die("No project group");
}

$device_folder = '/mnt/data/' . $projectgroup . '/' . $project . '/' . str_replace(':', '-', $macaddress);

$years = preg_grep('/^([^.])/', scandir($device_folder));

?>
<h2>Monthly Image Series Archives</h2>
<table id="filedownload" class="display compact" cellspacing="0">
<thead>
<tr style="background-color: #ddeedd;">
<th>Download</th><th>Filesize</th><th>Number of Images</th>
</tr>
</thead>

<tbody>

<?php


foreach ($years as $year) {



    $year_folder = $device_folder . '/' . $year;



    if (is_dir($year_folder)) {




        $months = scandir($year_folder);

        foreach ($months as $month) {

            if ((strpos($month, '.zip') !== false)) {

                $month_folder = $year_folder . '/' . $month;



                $path       = $year_folder . '/' . $month;
                $imagecount = 0;



                $zip = new ZipArchive();
                $zip->open($path);
                $imagecount = $zip->numFiles;
                $zip->close();


?>

                <tr>
                <td><a href="<?php
                echo $path;
?>" target="blank"><?php
                echo $month;
?></a></td>
                <td><?php
                echo human_filesize(filesize($path));
?></td>
                <td><?php
                echo $imagecount;
?></td>
                </tr>

                <?php


            } //(strpos($month, '.zip') !== false)

        } //$months as $month
    } //is_dir($year_folder)
} //$years as $year


?>

</tbody>
</table>
<script>
    $(document).ready(function() {
     var table = $('#filedownload').DataTable( {
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