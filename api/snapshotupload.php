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
if (isset($_GET['ping'])) {
    if ($_GET['ping'] == 'test') {
        echo '100';
        exit();
    } //$_GET['ping'] == 'test'
} //isset($_GET['ping'])
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
if (isset($_GET[$developer_key])) {
    if ($_GET[$developer_key] == $developer_key_value) {

        include('database.php');
        if (!isset($_FILES['file']['type'])) {
            die('No image submitted');
        } //!isset($_FILES['file']['type'])
        if (!($handle = fopen($_FILES['file']['tmp_name'], "r"))) {
            die('Error opening temp file');
        } //!($handle = fopen($_FILES['file']['tmp_name'], "r"))
        else if (!($image = fread($handle, filesize($_FILES['file']['tmp_name'])))) {
            die('Error reading temp file');
        } //!($image = fread($handle, filesize($_FILES['file']['tmp_name'])))
        else {
            fclose($handle);
            $image      = $sql->real_escape_string($image);
            // Commit image to the database
            $name       = '';
            $macaddress = '';
            $dateoffset = '-1';
            if (isset($_GET['dateoffset'])) {
                $dateoffset = '-' . $sql->real_escape_string($_GET['dateoffset']);
            } //isset($_GET['dateoffset'])
            if (isset($_FILES['file']['name'])) {
                $name = $sql->real_escape_string($_FILES['file']['name']);
            } //isset($_FILES['file']['name'])
            if (isset($_GET['macaddress'])) {
                $macaddress = $sql->real_escape_string($_GET['macaddress']);
            } //isset($_GET['macaddress'])
            else {
                die('Macaddress not set');
            }
            $sql->query("DELETE FROM " . $sqldatabase . ".images WHERE macaddress='" . $macaddress . "';");
            echo "DELETE FROM " . $sqldatabase . ".images WHERE macaddress='" . $macaddress . "';";
            $query = 'INSERT INTO ' . $sqldatabase . '.images (date,name,image,macaddress) VALUES (TIMESTAMPADD(DAY,' . $dateoffset . ',CURRENT_TIMESTAMP()),"' . $name . '","' . $image . '","' . $macaddress . '")';
            if (!($sql->query($query))) {
                die('Error writing image to database: ' . $sql->error);
            } //!($sql->query($query))
            else {
                die('Image successfully copied to database');
            }
        }
    } //$_GET[$developer_key] == $developer_key_value
} //isset($_GET[$developer_key])
?>