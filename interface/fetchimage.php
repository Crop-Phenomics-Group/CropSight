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