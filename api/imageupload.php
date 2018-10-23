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
if (isset($_GET['ping'])) {
    if ($_GET['ping'] == 'test') {
        echo '100';
        exit();
    }
}
if (!isset($_GET[$developer_key])) {
    http_response_code(400);
    die("unauthorised");
}
if ($_GET[$developer_key] != $developer_key_value) {
    http_response_code(400);
    die("unauthorised");
}
include("database.php");
function create_zip($files = array(), $destination = '', $overwrite = false)
{
    if (file_exists($destination) && !$overwrite) {
        return false;
    }
    $valid_files = array();
    if (is_array($files)) {
        foreach ($files as $file) {
            if (file_exists($file)) {
                $valid_files[] = $file;
            }
        }
    }
    if (count($valid_files)) {
        $zip = new ZipArchive();
        if ($zip->open($destination, $overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
            return false;
        }
        foreach ($valid_files as $file) {
            $zip->addFile($file, $file);
        }
        $zip->close();
        return file_exists($destination);
    } else {
        return false;
    }
}
if (isset($_GET['package'])) {
    $day       = '';
    $year      = '';
    $month     = '';
    $dateparts = explode("_", $_GET['package']);
    if (count($dateparts) < 3) {
        http_response_code(400);
        die("Not enough parts in date");
    }
    $year       = $dateparts[0];
    $month      = $dateparts[1];
    $day        = $dateparts[2];
    $macaddress = str_replace(":", "-", $_GET['macaddress']);
    $dirname    = "/mnt/usb/" . $macaddress . "/" . $year . "/" . $month . "/" . $day;
    if (!file_exists($dirname)) {
        http_response_code(400);
        die("Directory does not exist (" . $dirname . ")");
    }
    $filestozip = array();
    $dirfiles   = scandir($dirname);
    foreach ($dirfiles as $f) {
        if ($f != '.' and $f != '..') {
            array_push($filestozip, $dirname . "/" . $f);
        }
    }
    $zip = $dirname . "/" . $day . "-" . $month . "-" . $year . ".zip";
    create_zip($filestozip, $zip, true);
    exit();
}
if (!isset($_FILES['file']['type'])) {
    http_response_code(400);
    die('No image submitted');
}
$nameparts = explode("Date-", $_FILES["file"]["name"]);
$year      = '';
$month     = '';
$day       = '';
$dirname   = '';
//$imagename = '';
if (count($nameparts) > 1) {
    $date      = $nameparts[1];
    $dateparts = explode("_", $date);
    if (count($dateparts) > 1) {
        $date      = $dateparts[0];
        $dateparts = explode("-", $date);
        if (count($dateparts) > 2) {
            $year  = $dateparts[2];
            $month = str_pad($dateparts[1], 2, '0', STR_PAD_LEFT);
            $day   = str_pad($dateparts[0], 2, '0', STR_PAD_LEFT);
        } else {
            http_response_code(400);
            die("Not enough parts in date");
        }
    } else {
        http_response_code(400);
        die("Not enough parts in Date");
    }
} else {
    http_response_code(400);
    die("Not enough parts in filename");
}
$nodedetails  = $sql->query("SELECT * FROM " . $sqldatabase . ".pistatus WHERE macaddress='" . $_GET['macaddress'] . "'");
$projectid    = '';
$projectgroup = '';
if ($node_det = $nodedetails->fetch_assoc()) {
    $projectid = $node_det['projectid'];
} else {
    die("no project details");
}
$projectdetails = $sql->query("SELECT * FROM " . $sqldatabase . ".projects WHERE projectid='" . $projectid . "'");
if ($proj_det = $nodedetails->fetch_assoc()) {
    $projectgroup = $proj_det['projectgroup'];
} else {
    die("no project group");
}
$macaddress = str_replace(":", "-", $_GET['macaddress']);
$target_dir = "/mnt/data/" . $projectgroup . "/" . $projectid . "/" . $macaddress . "/" . $year . "/" . $month . "/" . $day;
if (!file_exists($target_dir)) {
    mkdir($target_dir, 0777, TRUE);
}
$target_file   = $target_dir . "/" . $_FILES["file"]["name"];
$imageFileType = pathinfo($target_file, PATHINFO_EXTENSION);
// Check if file already exists
if (file_exists($target_file)) {
    http_response_code(200);
    die("Sorry, file already exists.");
}
//$return_val = move_uploaded_file($_FILES["file"]["tmp_name"], $target_file);
if (copy($_FILES['file']['tmp_name'], $target_file)) {
    http_response_code(200);
    echo "The file " . basename($_FILES["file"]["name"]) . " has been uploaded.";
} else {
    http_response_code(400);
    echo "Sorry, there was an error uploading your file." . $_FILES['file']['tmp_name'];
}
?>