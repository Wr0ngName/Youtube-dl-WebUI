<?php
    require_once("config.php"); 
    require_once("sessions.php");
    require_once("utilities.php");

    $input = $_POST;

    if(!empty($_FILES))
    {
        $input['url'] = $_FILES['url'];
    }

    $return = [ "result" => "waiting", "message" => "" ];
    $tmpfile = false;

    if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1 && !empty($input['url']) && !empty($input['downloadFileType']))
    {
        $namingScheme = '%(uploader)s - %(title)s (key: %(id)s).%(ext)s';
        $temp = tempnam($folder, ".ytprogress_");

        if(!empty($_FILES) && $_FILES['url']['error'] == UPLOAD_ERR_OK && is_uploaded_file($_FILES['url']['tmp_name']))
        {
            $handle = fopen($_FILES['url']['tmp_name'], "r");
            $tmpfilename = $temp . '_list';
            $tmpfile = fopen($tmpfilename, 'w');
            $content = "";

            if ($handle) {
                for ($line=0; $line < $maxUrls; $line++) {
                    if($linecontent = fgets($handle))
                        if (!filter_var(trim($linecontent), FILTER_VALIDATE_URL) === false)
                            $content .= $linecontent;
                    else
                        break;
                }
                fclose($handle);
            }
            fwrite($tmpfile, $content);
            fclose($tmpfile);

            $url = "-a " . escapeshellarg($tmpfilename);
        }
        else
        {
            $url = escapeshellarg($input['url']);
        }

        $_SESSION['task'] = $temp;

        $cmd = 'youtube-dl --newline --restrict-filenames -f \'bestvideo[height<=1080]+bestaudio/best[height<=1080]\' -o ' . escapeshellarg($folder.$namingScheme) . ' ' . $url;

        if ($input['downloadFileType'] == 'audio')
            $cmd .= ' -x --audio-format mp3';

        exec('nohup sh -c "' . $cmd . ' > ' . $temp . ' ; rm ' . $temp . ' ; rm ' . $temp . '_list" > /dev/null 2>/dev/null &', $output, $ret);

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
