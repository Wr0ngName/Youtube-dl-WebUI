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
            $activity = getProgressBis($taskFile);

            if ($activity['error'] == false)
            {
                $progress = $activity['progress'];
                if($progress == 100)
                    $return['result'] = 'converting';
            }
            else
                $return['message'] = $activity['error'];
        }
        else
        {
            unset($_SESSION['task']);
            $progress = 100;
        }

        $return['progress'] = $progress;

        $return['index'] = $activity['listIndex'];
        $return['slist'] = $activity['listSize'];
    }
    else
    {
        $return = [ "result" => "error", "message" => "Wrong input data provided." ];
    }

    echo json_encode($return);

?>