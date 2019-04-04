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