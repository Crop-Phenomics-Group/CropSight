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
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);

date_default_timezone_set('Europe/London');

include("database.php");
include("auth.php");

$macaddress = "";

if (isset($_GET['macaddress'])) {
    $macaddress = $_GET['macaddress'];
} //isset($_GET['macaddress'])
else {
    die("Mac address not set");
}

$hostname = "";

$cropquants = $sql->query("SELECT * FROM " . $sqldatabase . ".pistatus WHERE macaddress = '" . $macaddress . "';");
$cropquant  = "";

if ($cropquant = $cropquants->fetch_assoc()) {
    $hostname = $cropquant['hostname'];
} //$cropquant = $cropquants->fetch_assoc()
else {
    die("CQ not present in database");
}


header("Content-Type: text/csv");
header("Content-Disposition: attachment; filename=" . $hostname . ".csv");
header("Content-Transfer-Encoding: UTF-8");
header('Pragma: no-cache');
header("Expires: 0");

function outputCSV($data)
{
    $output = fopen("php://output", "w");
    foreach ($data as $row)
        fputcsv($output, $row); // here you can change delimiter/enclosure
    fclose($output);
}

$timestamps = $sql->query("SELECT DISTINCT timestamp FROM " . $sqldatabase . ".sensorreading WHERE macaddress = '" . $macaddress . "';");

$dataset = array();

$headings = array(
    "timestamp",
    "cputemp",
    "cpuuse",
    "memuse",
    "light",
    "temp",
    "humid",
    "soiltemp",
    "soilhumid"
);

array_push($dataset, $headings);

while ($timestamp = $timestamps->fetch_assoc()) {


    $data     = array(
        $timestamp['timestamp'],
        "",
        "",
        "",
        "",
        "",
        "",
        "",
        ""
    );
    $readings = $sql->query("SELECT * FROM " . $sqldatabase . ".sensorreading WHERE macaddress = '" . $macaddress . "' AND timestamp = '" . $timestamp['timestamp'] . "';");

    while ($reading = $readings->fetch_assoc()) {
        switch ($reading['sensor']) {
            case "cputemp":
                $data[1] = $reading['reading'];
                break;
            case "cpuuse":
                $data[2] = $reading['reading'];
                break;
            case "memuse":
                $data[3] = $reading['reading'];
                break;
            case "light":
                $data[4] = $reading['reading'];
                break;
            case "temp":
                $data[5] = $reading['reading'];
                break;
            case "humid":
                $data[6] = $reading['reading'];
                break;
            case "soiltemp":
                $data[7] = $reading['reading'];
                break;
            case "soilhumid":
                $data[8] = $reading['reading'];
                break;


        } //$reading['sensor']
    } //$reading = $readings->fetch_assoc()

    array_push($dataset, $data);

} //$timestamp = $timestamps->fetch_assoc()

outputCSV($dataset);

?>