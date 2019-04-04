<?php

class project
{
    public function __construct($projectid, $description, $projectgroup, $active, $longitude, $latitude) {
        $this->projectid = $projectid;
        $this->description = $description;
        $this->projectgroup = $projectgroup;
        $this->active = $active;
        $this->longitude = $longitude;
        $this->latitude = $latitude;
    }

}

class project_list{
    public function __construct($list) {
        $this->projects = $list;
    }
}

$list = array();

$SQL_QUERY = "SELECT * FROM " . $GLOBALS['sqldatabase'] . ".projects";

if(isset($_GET['project_group']))
{
    $SQL_QUERY = "SELECT * FROM " . $GLOBALS['sqldatabase'] . ".projects WHERE projectgroup='" . $_GET['project_group'] . "'";
}

$project_list = $GLOBALS['sql']->query($SQL_QUERY) or die($GLOBALS['sql']->error);;
while ($project = $project_list->fetch_assoc()) {

    $project_obj=new project($project['projectid'], $project['description'], $project['projectgroup'], $project['active'], $project['longitude'], $project['latitude']);

    array_push($list, $project_obj);

}

$list_obj = new project_list($list);

$json = json_encode($list_obj);

echo $json;













?>