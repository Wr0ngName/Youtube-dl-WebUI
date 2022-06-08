<?php

function purgeOldDownloads($folder)
{
    $i = 0;
    $olds = listOldDownloads($folder);
    foreach ($olds as $old) {
        unlink($old);
        $i = $i + 1;
    }
    return $i;
}

function listOldDownloads($folder)
{
    $output = array();
    $files = glob($folder."*");
    $now   = time();
    foreach ($files as $file) {
        if (is_file($file)) {
            if ($now - filemtime($file) > 60 * 60 * 2) { // 2 hours
                array_push($output, $file);
            }
        }
    }
    return $output;
}

// Test if destination folder exists
function destFolderExists($destFolder)
{
    if(!file_exists($destFolder))
    {
        echo '<div class="alert alert-danger">
                <strong>Error : </strong> Destination folder doesn\'t exist or is not found here. 
            </div>';
    }
}

// Convert bytes to a more user-friendly value
function human_filesize($bytes, $decimals = 0)
{
    $sz = 'BKMGTP';
    $factor = floor((strlen($bytes) - 1) / 3);
    return sprintf("%.{$decimals}f", $bytes / pow(1024, $factor)) . @$sz[$factor];
}

// Parse output file from Youtube-dl to Array
function fileToArray($file)
{
    $line = '';

    $f = fopen($file, 'r');
    fseek($f, $cursor, SEEK_END);
    $cursor = -1;
    $char = fgetc($f);

    while ($char === "\n" || $char === "\r") {
        fseek($f, $cursor--, SEEK_END);
        $char = fgetc($f);
    }

    while ($char !== false && $char !== "\n" && $char !== "\r") {
        $line = $char . $line;
        fseek($f, $cursor--, SEEK_END);
        $char = fgetc($f);
    }

    $remove_whitespace = preg_replace('/\s+/', ' ', $line);

    return explode(" ", $remove_whitespace);
}

function getProgress($file)
{
    $fileArray = fileToArray($file);

    if (count($fileArray)>0)
        return str_replace('%', '', $fileArray[1]);
    else
        return -1;
}

function listSize($file)
{
    $totalFile = 0;

    $handle = fopen($file . '_list', "r");
    if ($handle) {
        while (fgets($handle) !== false) {
            $totalFile++;
        }

        fclose($handle);
    } else 
        $totalFile = 1;

    return $totalFile; 
}

function trackProgress($file)
{
    $currentFile = 1;
    $totalFile = 1;
    $percent = 0;
    $error = false;
    $listIndex = 0;

    $handle = fopen($file, "r");
    if ($handle) {
        while (($line = fgets($handle)) !== false) {
            if(preg_match("/\[download\]/", $line))
            {
                if(preg_match("/Downloading video/", $line))
                {
                    if(preg_match('/(\d+) of (\d+)/', $line, $matches))
                    {
                        $currentFile = intval($matches[1]);
                        $totalFile = intval($matches[2]);
                    }
                }
                elseif(preg_match("/iB\/s ETA/", $line))
                {
                    if(preg_match('/(\d+(\.\d+)?)% of [\~\d]/', $line, $matches))
                    {
                        $percent = ((strpos($matches[1], '.') !== false) ? floatval($matches[1]) : intval($matches[1]));
                    }
                }

                if(preg_match("/Destination:/", $line))
                {
                    $listIndex++;
                }
            }
        }

        fclose($handle);
    } else 
        $error = "file could not be opened";

    return ["percent" => $percent, "listIndex" => $listIndex, "current" => $currentFile, "total" => $totalFile, "error" => $error]; 
}

function getProgressBis($file)
{
    $fileArray = trackProgress($file);
    $error = true;
    $percent = -1;
    $listIndex = 0;

    if ($fileArray["error"] == false)
    {
        $part = $fileArray["percent"] / $fileArray["total"];                    //  9 / 21
        $files = (($fileArray["current"]-1) / $fileArray["total"] ) * 100;        //  1-1 / 21
        $percent = $part + $files;

        $listIndex = $fileArray["listIndex"];

        if ($percent > 100)
            $percent = 100;

        $error = false;

    } else $error = $fileArray["error"];

    return [ "progress" => $percent, "error" => $error, "listIndex" => $listIndex, "listSize" => listSize($file) ];
}

?>
