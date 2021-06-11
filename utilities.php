<?php

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

function trackProgress($file)
{
    $currentFile = 1;
    $totalFile = 1;
    $percent = 0;
    $error = false;

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
                    if(preg_match('/(\d+(\.\d+)?)% of \d/', $line, $matches))
                    {
                        $percent = ((strpos($matches[1], '.') !== false) ? floatval($matches[1]) : intval($matches[1]));
                    }
                }
            }
        }

        fclose($handle);
    } else 
        $error = "file could not be opened";

    return ["percent" => $percent, "current" => $currentFile, "total" => $totalFile, "error" => $error]; 
}

function getProgressBis($file)
{
    $fileArray = trackProgress($file);
    $error = true;
    $percent = -1;

    if ($fileArray["error"] == false)
    {
        $part = $fileArray["percent"] / $fileArray["total"];
        $files = ($fileArray["current"]-1 / $fileArray["total"] ) * 100;
        $percent = $part + $files;

        if ($percent > 100)
            $percent = 100;

        $error = false;

    } else $error = $fileArray["error"];

    return [ "progress" => $percent, "error" => $error ];
}

?>