<?php
    require_once("config.php"); 
    require_once("sessions.php");
    require_once("utilities.php");

    if(isset($_GET['logout']) && $_GET['logout'] == 1) endSession();

    if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1)
    {
        if(isset($_GET['fileToGet']))
        {
            $rawFile = htmlspecialchars($_GET['fileToGet']);
            $tmpFile = pathinfo($rawFile);
            $fileToGet  = $tmpFile['basename'];

            if(file_exists($folder.$fileToGet) && substr($fileToGet, 0, 1) != '/' && substr($fileToGet, 0, 1) != '.')
            {
                //Get file type and set it as Content Type
                $finfo = finfo_open(FILEINFO_MIME_TYPE);
                header('Content-Type: ' . finfo_file($finfo, $folder.$fileToGet));
                finfo_close($finfo);

                //Use Content-Disposition: attachment to specify the filename
                header('Content-Disposition: attachment; filename='.basename($folder.$fileToGet));

                //No cache
                header('Expires: 0');
                header('Cache-Control: must-revalidate');
                header('Pragma: public');

                //Define file size
                header('Content-Length: ' . filesize($folder.$fileToGet));

                ob_clean();
                flush();
                readfile($folder.$fileToGet);
                exit;

            }
            else
            {
                header("Location: ".$listPage);
            }
        }
        else
        {
            header("Location: ".$listPage);
        }
    }
    else
    {
        header("Location: ".$mainPage);
    }

?>