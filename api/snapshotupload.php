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