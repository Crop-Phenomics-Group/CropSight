<?php
include("developer_key.php");
include("database.php");

if(!isset($_GET['func'])){

die("No function specified");

}

if(!isset($_GET['developer_key']))
{
 die("No developer key specified");
}

if ($_GET['developer_key'] != $developer_key){
    die("Unauthorised developer key");
}

switch($_GET['func']){
    case "get_project_groups":
        include("api/get_project_groups.php");
        break;
    case "get_projects":
        include("api/get_projects.php");
        break;
    case "get_devices":
        include("api/get_devices.php");
        break;
    case "get_datasets":
        include("api/get_datasets.php");
        break;
    case "get_data":
        include("api/get_data.php");
        break;
    default:
        die("Unsupported function specified");

}


?>