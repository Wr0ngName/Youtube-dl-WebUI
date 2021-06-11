<?php
    require_once("config.php"); 
    require_once("sessions.php");
    require_once("utilities.php");

    if (!isset($_POST['url']) && !isset($_GET['url']))
        $input = json_decode(file_get_contents('php://input'), true);
    elseif (isset($_POST['url']))
        $input = $_POST;
    elseif (isset($_GET['url']))
        $input = $_GET;

    $return = [ "result" => "waiting", "message" => "" ];

    if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1 && !empty($input['url']) && !empty($input['downloadFileType']))
    {
        $url = escapeshellarg($input['url']);
        $namingScheme = '%(uploader)s - %(title)s (key: %(id)s).%(ext)s';
        $temp = tempnam($folder, ".ytprogress_");

        $_SESSION['task'] = $temp;
        $cmd = 'youtube-dl --newline -f \'bestvideo[height<=1080]+bestaudio/best[height<=1080]\' -o ' . escapeshellarg($folder.$namingScheme) . ' ' . $url;

        if ($input['downloadFileType'] == 'audio')
            $cmd .= ' -x --audio-format mp3';

        exec('nohup sh -c "' . $cmd . ' > ' . $temp . ' ; rm ' . $temp . '" > /dev/null 2>/dev/null &', $output, $ret);

        if($ret == 0)
        {
            $return = [ "result" => "success", "message" => "" ];
        }
        else
        {

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