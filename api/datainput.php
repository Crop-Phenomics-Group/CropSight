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