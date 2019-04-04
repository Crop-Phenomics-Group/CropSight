<?php

class dataset{

    public function __construct($dataset_id, $dataset_description, $dataset_unit, $dataset_name, $dataset_count) {
        $this->dataset_id = $dataset_id;
        $this->dataset_description = $dataset_description;
        $this->dataset_unit = $dataset_unit;
        $this->dataset_count = $dataset_count;
        $this->dataset_name = $dataset_name;
    }

}

class dataset_list{
    public function __construct($list) {
        $this->datasets = $list;
    }
}

if(!isset($_GET['device_id']))
{
    die("No device_id set");
}

$list = array();

$dataset_list = $GLOBALS['sql']->query("SELECT * FROM " . $GLOBALS['sqldatabase'] . ".pisensor") or die($GLOBALS['sql']->error);

while($dataset = $dataset_list->fetch_assoc())
{
    $readings = $GLOBALS['sql']->query("SELECT * FROM " . $GLOBALS['sqldatabase'] . ".sensorreading WHERE sensor='" . $dataset['sensorid'] . "' AND macaddress='" . $_GET['device_id'] . "'") or die($GLOBALS['sql']->error);

    $dataset_obj = new dataset($dataset['sensorid'], $dataset['description'], $dataset['unit'], $dataset['name'], $readings->num_rows);

    array_push($list, $dataset_obj);
}

$list_obj = new dataset_list($list);

$json = json_encode($list_obj);

echo $json;

//http://127.0.0.1/api.php?func=get_datasets&developer_key=RywyD7WHf2cSp7REqzkHyNEy&device_id=bhdsjabhdsavkjadvhjcsa

?>