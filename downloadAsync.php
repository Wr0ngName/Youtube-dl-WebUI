<?php
    require_once("config.php"); 
    require_once("sessions.php");
    require_once("utilities.php");

    if (!isset($_POST['url']))
        $input = json_decode(file_get_contents('php://input'), true);

    $return = [ "result" => "waiting", "message" => "" ];

    if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1 && !empty($input['url']) && !empty($input['downloadFileType']))
    {
        $url = escapeshellarg($input['url']);
        $namingScheme = '%(uploader)s - %(title)s (key: %(id)s).%(ext)s';

        if ($input['downloadFileType'] == 'audio')
            $cmd = 'youtube-dl -x --audio-format mp3 -f \'bestvideo[height<=1080]+bestaudio/best[height<=1080]\' -o ' . escapeshellarg($folder.$namingScheme) . ' ' . $url . ' 2>&1';
        else
            $cmd = 'youtube-dl -f \'bestvideo[height<=1080]+bestaudio/best[height<=1080]\' -o ' . escapeshellarg($folder.$namingScheme) . ' ' . $url . ' 2>&1';

        exec($cmd, $output, $ret);

        if($ret == 0)
        {
            $return = [ "result" => "success", "message" => "" ];
        }
        else{

            $msg = "";
            foreach($output as $out)
            {
                $msg .= $out . "\n"; 
            }

            $return = [ "result" => "error", "message" => $msg ];
        }
    }
    else
    {
        $return = [ "result" => "error", "message" => "Wrong input data provided." ];
    }

    echo json_encode($return);

?>