<?php

class device
{
    public function __construct($macaddress, $ipaddress, $hostname, $uptime, $lastupdate, $projectid, $lastimage, $position) {
        $this->deviceid = $macaddress;
        $this->ipaddress = $ipaddress;
        $this->hostname = $hostname;
        $this->uptime = $uptime;
        $this->lastupdate = $lastupdate;
        $this->projectid = $projectid;
        $this->lastimage = $lastimage;
        $this->position = $position;
    }

}

class device_list{
    public function __construct($list) {
        $this->devices = $list;
    }
}

$list = array();

$SQL_QUERY = "SELECT * FROM " . $GLOBALS['sqldatabase'] . ".pistatus";

if(isset($_GET['project']))
{
    $SQL_QUERY = "SELECT * FROM " . $GLOBALS['sqldatabase'] . ".pistatus WHERE projectid='" . $_GET['project'] . "'";
}

$device_list = $GLOBALS['sql']->query($SQL_QUERY) or die($GLOBALS['sql']->error);;
while ($device = $device_list->fetch_assoc()) {

    $device_obj=new device($device['macaddress'], $device['ipaddress'], $device['hostname'], $device['uptime'], $device['lastupdate'], $device['projectid'], $device['lastimage'], $device['position']);

    array_push($list, $device_obj);

}

$list_obj = new device_list($list);

$json = json_encode($list_obj);

echo $json;













?>