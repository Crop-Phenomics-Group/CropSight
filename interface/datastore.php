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