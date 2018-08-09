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

include('database.php');

$macaddress = '';

if (isset($_GET['macaddress'])) {
    $macaddress = $sql->real_escape_string($_GET['macaddress']);
} //isset($_GET['macaddress'])


$latest = TRUE;

if (isset($_GET['date'])) {
    $latest = FALSE;
} //isset($_GET['date'])

if ($latest) {
    $result = $sql->query("SELECT date FROM " . $sqldatabase . ".images WHERE macaddress='" . $macaddress . "' ORDER BY date DESC");
    if ($row = $result->fetch_assoc()) {
        $imageresult = $sql->query("SELECT * FROM " . $sqldatabase . ".images WHERE macaddress='" . $macaddress . "' AND date='" . $row['date'] . "'");

        if ($imagerow = $imageresult->fetch_assoc()) {
            header("Content-type: image/jpeg");
            echo $imagerow['image'];
        } //$imagerow = $imageresult->fetch_assoc()
        else {
            die("Failed to fetch image");
        }
    } //$row = $result->fetch_assoc()
    else {
        die("Failed to fetch latest image from device");
    }
} //$latest
else {
    $imageresult = $sql->query("SELECT * FROM " . $sqldatabase . ".images WHERE macaddress='" . $macaddress . "' AND date='" . $sql->real_escape_string($_GET['date']) . "'");

    if ($imagerow = $imageresult->fetch_assoc()) {
        header("Content-type: image/jpeg");
        echo $imagerow['image'];
    } //$imagerow = $imageresult->fetch_assoc()
    else {
        die("Failed to fetch image");
    }
}
?>