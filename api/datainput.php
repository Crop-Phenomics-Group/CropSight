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
        include("database.php");
        $data              = json_decode(file_get_contents('php://input'), true);
        $macaddress        = '';
        $ipaddress         = $_SERVER['REMOTE_ADDR'];
        $internalipaddress = '';
        $hostname          = '';
        $uptime            = '';
        $duration          = 0;
        $latestimage       = '';
        $position          = '0,0';
        if (isset($data['macaddress'])) {
            $macaddress = $sql->real_escape_string($data['macaddress']);
        } //isset($data['macaddress'])
        if ($data['macaddress'] == '') {
            echo $data;
            die("macaddress not set");
        } //$data['macaddress'] == ''
        if (isset($data['ipaddress'])) {
            $internalipaddress = $sql->real_escape_string($data['ipaddress']);
        } //isset($data['ipaddress'])
        if (isset($data['hostname'])) {
            $hostname = $sql->real_escape_string($data['hostname']);
        } //isset($data['hostname'])
        if (isset($data['duration'])) {
            $duration = $sql->real_escape_string($data['duration']);
        } //isset($data['duration'])
        if (isset($data['uptime'])) {
            $uptime = $sql->real_escape_string($data['uptime']);
        } //isset($data['uptime'])
        if (isset($data['latestimage'])) {
            $latestimage = $sql->real_escape_string($data['latestimage']);
        } //isset($data['latestimage'])
        if (isset($data['position'])) {
            $position = $sql->real_escape_string($data['position']);
        } //isset($data['position'])
        $existing = $sql->query("SELECT COUNT(macaddress) AS total from " . $sqldatabase . ".pistatus WHERE macaddress='" . $macaddress . "';");
        $count    = $existing->fetch_assoc();
        if ($count['total'] == 0) {
            echo 'CQ not found';
            $sql->query("INSERT INTO " . $sqldatabase . ".pistatus (macaddress, ipaddress, internalipaddress, hostname, uptime, duration, lastimage, position) VALUES " . "('" . $macaddress . "','" . $ipaddress . "','" . $internalipaddress . "','" . $hostname . "','" . $uptime . "'," . $duration . ",'"  . $latestimage . "', " . $position . "');");
            $errorstring = $sql->error;
            echo $errorstring;
            $sql->query("DELETE * FROM " . $sqldatabase . ".pistorage WHERE macaddress='" . $macaddress . "';");
            foreach ($data['storage'] as $store) {
                $mountpoint = '';
                $total      = '';
                $available  = '';
                if (isset($store['mountpoint'])) {
                    $mountpoint = $sql->real_escape_string($store['mountpoint']);
                } //isset($store['mountpoint'])
                if (isset($store['total'])) {
                    $total = $sql->real_escape_string($store['total']);
                } //isset($store['total'])
                if (isset($store['available'])) {
                    $available = $sql->real_escape_string($store['available']);
                } //isset($store['available'])
                $sql->query("INSERT INTO " . $sqldatabase . ".pistorage (macaddress, mountpoint, total, free, online) VALUES('" . $macaddress . "', '" . $mountpoint . "', " . $total . ", " . $available . ", 1);");
            } //$data['storage'] as $store
        } //$count['total'] == 0
        else {
            echo "CQ found";
            $sql_query = "UPDATE " . $sqldatabase . ".pistatus SET ipaddress='" . $ipaddress . "',position='" . $position . "',internalipaddress='" . $internalipaddress . "',hostname='" . $hostname . "',uptime='" . $uptime . "',duration=" . $duration . "," . "lastupdate = '" . time() . "',lastimage='" . $latestimage ." WHERE macaddress='" . $macaddress . "';";
            echo $sql_query;
            $sql->query($sql_query);
            $errorstring = $sql->error;
            echo $errorstring;
            $sql->query("UPDATE " . $sqldatabase . ".pistorage SET online=0 WHERE macaddress='" . $macaddress . "';");
            foreach ($data['storage'] as $store) {
                $mountpoint = '';
                $total      = '';
                $available  = '';
                if (isset($store['mountpoint'])) {
                    $mountpoint = $sql->real_escape_string($store['mountpoint']);
                } //isset($store['mountpoint'])
                if (isset($store['total'])) {
                    $total = $sql->real_escape_string($store['total']);
                } //isset($store['total'])
                if (isset($store['available'])) {
                    $available = $sql->real_escape_string($store['available']);
                } //isset($store['available'])
                if ($store['mountpoint'] != 'null') {
                    $storagepresent = $sql->query("SELECT COUNT(macaddress) AS total from " . $sqldatabase . ".pistorage WHERE macaddress='" . $macaddress . "' AND mountpoint='" . $mountpoint . "';");
                    $storagecount   = $storagepresent->fetch_assoc();
                    if ($storagecount['total'] == 0) {
                        $sql->query("INSERT INTO " . $sqldatabase . ".pistorage (macaddress, mountpoint, total, free, online) VALUES('" . $macaddress . "', '" . $mountpoint . "', " . $total . ", " . $available . ", 1);");
                    } //$storagecount['total'] == 0
                    else {
                        $sql->query("UPDATE " . $sqldatabase . ".pistorage SET total=" . $total . ",free=" . $available . ",online=1 WHERE macaddress='" . $macaddress . "' AND mountpoint='" . $mountpoint . "';");
                    }
                } //$store['mountpoint'] != 'null'
            } //$data['storage'] as $store
        }
        $sensors  = array(
            'temp',
            'humid',
            'soilhumid',
            'soiltemp',
            'light'
        );
        $minrange = array(
            -20,
            0,
            0,
            -20,
            0
        );
        $maxrange = array(
            100,
            100,
            100,
            100,
            100
        );
        for ($i = 0; $i < 5; $i++) {
            if (isset($data[$sensors[$i]])) {
                if (is_numeric($data[$sensors[$i]])) {
                    if (floatval($data[$sensors[$i]]) >= $minrange[$i] && floatval($data[$sensors[$i]]) <= $maxrange[$i]) {
                        if (strpos($data[$sensors[$i]], 'e') == false) {
                            $sql->query("INSERT INTO " . $sqldatabase . ".sensorreading (macaddress, sensor, reading) VALUES ('" . $macaddress . "', '" . $sensors[$i] . "', '" . $sql->real_escape_string($data[$sensors[$i]]) . "')");
                            echo 'inputted ' . $sensors[$i];
                        } //strpos($data[$sensors[$i]], 'e') == false
                    } //floatval($data[$sensors[$i]]) >= $minrange[$i] && floatval($data[$sensors[$i]]) <= $maxrange[$i]
                } //is_numeric($data[$sensors[$i]])
            } //isset($data[$sensors[$i]])
        } //$i = 0; $i < 5; $i++
    } //$_GET[$developer_key] == $developer_key_value
} //isset($_GET[$developer_key])
?>