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

function endswith($string, $test)
{
    $strlen  = strlen($string);
    $testlen = strlen($test);
    if ($testlen > $strlen)
        return false;
    return substr_compare($string, $test, $strlen - $testlen, $testlen) === 0;
}

if (isset($_GET['RywyD7WHf2cSp7REqzkHyNEy'])) {
    if ($_GET['RywyD7WHf2cSp7REqzkHyNEy'] == 'MQV3HKs5VkBTDFCpHEc7aYxP') {

        include('database.php');

        $files = scandir('csv/unprocessed');
        foreach ($files as $file) {
            if (endswith($file, '.csv')) {
                $parts = explode('.', $file);

                if (count($parts) == 3) {
                    $macaddress = $parts[0];
                    $timestamp  = $parts[1];

                    $result = $sql->query("INSERT INTO " . $sqldatabase . ".pidata VALUES (macaddress='" . $sql->real_escape_string($macaddress) . "',timestamp='" . $sql->real_escape_string($timestamp) . "',datafile='" . $sql->real_escape_string($file) . "');");

                    if ($result) {
                        rename('csv/unprocessed/' . $file, 'csv/' . $file);
                    } //$result
                } //count($parts) == 3
            } //endswith($file, '.csv')
        } //$files as $file
    } //$_GET['RywyD7WHf2cSp7REqzkHyNEy'] == 'MQV3HKs5VkBTDFCpHEc7aYxP'
} //isset($_GET['RywyD7WHf2cSp7REqzkHyNEy'])
?>