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
if (isset($_GET[$developer_key])) {
    if ($_GET[$developer_key] == $developer_key_value) {
        include("database.php");
        if (isset($_GET['long'])) {
            $long = $sql->real_escape_string($_GET['long']);
        } //isset($_GET['long'])
        else {
            die("Longitude not set");
        }
        if (isset($_GET['lat'])) {
            $lat = $sql->real_escape_string($_GET['lat']);
        } //isset($_GET['lat'])
        else {
            die("Latitude not set");
        }
        $sql_statement = "UPDATE cropmonitor.projects SET longitude='" . $long . "', latitude='" . $lat . "' WHERE local=1;";
        $sql->query($sql_statement);
        echo $sql_statement;
    } //$_GET[$developer_key] == $developer_key_value
    else {
        die("Wrong Key Value");
    }
} //isset($_GET[$developer_key])
else {
    die("Wrong Key");
}
?>