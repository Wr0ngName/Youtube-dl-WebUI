<!DOCTYPE html>
<?php
    require_once("config.php"); 
    require_once("sessions.php");
    require_once("utilities.php");

    if(isset($_GET['logout']) && $_GET['logout'] == 1) endSession();
?>
<html>
    <head>
        <meta charset="utf-8">
        <title>Youtube-dl WebUI - List of videos</title>
        <link rel="stylesheet" href="css/bootstrap.css" media="screen">
        <link rel="stylesheet" href="css/bootswatch.min.css">
    </head>
    <body >
        <div class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo $mainPage; ?>">Youtube-dl WebUI</a>
            </div>
            <div class="navbar-collapse  collapse navbar-responsive-collapse">
                <ul class="nav navbar-nav">
                    <li><a href="<?php echo $mainPage; ?>">Download</a></li>
                    <li class="active"><a href="<?php echo $listPage; ?>">Browse</a></li>
                </ul>
            </div>
        </div>
        <div class="container">
<?php
if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1)
{
    if(isset($_GET['fileToDel']))
    {
        $rawFile = htmlspecialchars($_GET['fileToDel']);
        $tmpFile = pathinfo($rawFile);
        $fileToDel  = $tmpFile['basename'];

        $outputType = 'danger';

        if(file_exists($folder.$fileToDel) && substr($fileToDel, 0, 1) != '/' && substr($fileToDel, 0, 1) != '.')
        {
            if(unlink($folder.$fileToDel))
            {
                $outputType = 'success';
                $outputMsg = 'File '.$fileToDel.' has been deleted!';
                echo '<div class="panel-heading"><h3 class="panel-title">File to delete : '.$fileToDel.'</h3></div>';
                echo '<div class="panel-body"></div>';
                echo '</div>';
                echo '<p><a href="'.$listPage.'">Go back</a></p>';
            }
            else
            {
                $outputMsg = 'File '.$fileToDel.' could not be deleted!';
            }
        }
        else
        {
                $outputMsg = 'File '.$fileToDel.' could not be found!';
        }
        
        echo '<div class="row">';
        echo '<div class="col-lg-12">';

        echo '<div class="panel panel-'.$outputType.'">';
        echo '  <div class="panel-heading"><h3 class="panel-title">File to delete : '.$fileToDel.'</h3></div>';
        echo '  <div class="panel-body">'.$outputMsg.'</div>';
        echo '</div>';
        echo '<p><a href="'.$listPage.'">Go back</a></p>';

        echo '</div>';
        echo '</div>';
    }
    elseif(!file_exists($folder))
    {
            echo '<div class="alert alert-danger">
                    <strong>Error : </strong> Destination folder doesn\'t exist or is not found here.
                </div>';
    }
    else { ?>
        <h2>List of available videos :</h2>
            <table class="table table-striped table-hover ">
                <thead>
                    <tr>
                        <th style="min-width:800px; height:35px">Title</th>
                        <th style="min-width:80px">Size</th>
                        <th style="min-width:110px">Remove link</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
<?php
            foreach(glob($folder."*") as $file)
            {
                $filename = str_replace($folder, "", $file); // Need to fix accent problem with something like this : utf8_encode
                echo "<tr>"; //New line
                echo "<td height=\"30px\"><a target=\"_blank\" href=\"".$getPage."?fileToGet=$filename\">$filename</a></td>"; //1st col
                echo "<td>".human_filesize(filesize($folder.$filename))."</td>"; //2nd col
                echo "<td><a href=\"".$listPage."?fileToDel=$filename\" class=\"text-danger\">Delete</a></td>"; //3rd col
                echo "</tr>"; //End line
            }
        }
} 
else {
    echo '<div class="alert alert-danger"><strong>Access denied:</strong> You must log in!</div>';
} ?>
                    </tr>
                </tbody>
            </table>
            <br/>
            <?php if(!isset($_GET['fileToDel'])) echo "<a href=".$mainPage.">Back to download page</a>"; ?>
        </div><!-- End container -->
        <br>
        <footer>
            <div class="well text-center">
                <p><a href="https://github.com/p1rox/Youtube-dl-WebUI" target="_blank">Forked from Github</a></p>
                <p>Adapted by <a href="https://twitter.com/_wr0ngname_" target="_blank">@_wr0ngname_</a> - Website : <a href="https://wr0ng.name" target="_blank">wr0ng.name</a></p>
            </div>
        </footer>
    </body>
</html>