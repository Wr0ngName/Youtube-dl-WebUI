<?php
    require_once("config.php"); 
    require_once("sessions.php");
    require_once("utilities.php");

    $return = [ "result" => "progress", "message" => "", "progress" => 0 ];

    if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1 && isset($_SESSION['task']))
    {
        if (file_exists($_SESSION['task']))
        {
            $taskFile = $_SESSION['task'];
            $progress = getProgressBis($taskFile);
        }
        else
        {
            $progress = 100;
        }
        $return['progress'] = $progress;
    }
    else
    {
        $return = [ "result" => "error", "message" => "Wrong input data provided." ];
    }

    echo json_encode($return);

?>