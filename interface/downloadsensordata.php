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