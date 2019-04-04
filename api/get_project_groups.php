<?php

class group
{

    public function __construct($p_group_id, $p_name, $p_active) {
        $this->group_id = $p_group_id;
        $this->name = $p_name;
        $this->active = $p_active;
    }
}

class group_list
{
    public function __construct($p_list) {
        $this->project_groups = $p_list;
    }
}

$list = array();


$project_group_list = $GLOBALS['sql']->query("SELECT * FROM " . $GLOBALS['sqldatabase'] . ".projectgroup") or die($GLOBALS['sql']->error);
while ($project_group = $project_group_list->fetch_assoc()) {

    $group_obj=new group($project_group['groupid'], $project_group['name'], $project_group['active']);

    array_push($list, $group_obj);

}

$list_obj = new group_list($list);

$json = json_encode($list_obj);

echo $json;

?>