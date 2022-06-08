<?php
    require_once("config.php"); 
    require_once("sessions.php");
    require_once("utilities.php");

    if(isset($_GET['logout']) && $_GET['logout'] == 1) endSession();
?>
<!DOCTYPE html>
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
    ?>
            <h2>List of available videos :</h2>
            <table class="table table-striped table-hover ">
                <thead>
                    <tr>
                        <th style="min-width:800px; height:35px">Title</th>
                        <th style="min-width:80px">Size</th>
                    </tr>
                </thead>
                <tbody>
                    <tr>
<?php
            $filesList = glob($folder."*");
            usort($filesList, function($a, $b) => filemtime($a) - filemtime($b));
	     
            foreach($filesList as $file)
            {
                if(!strpos($file, '.ytdl') && !strpos($file, '.temp'))
                {
                    $filename = str_replace($folder, "", $file); // Need to fix accent problem with something like this : utf8_encode

		    $link = $getPage."?fileToGet=".base64_encode($filename);
                    $style = "";
		    if(strpos($file, '.part'))
		    {
			$link = "#";
                        $style = "background-color: #d5e8ee !important;";
		    }
                    echo "<tr>"; //New line
                    echo "<td height=\"30px\" style=\"".$style."\"><a target=\"_blank\" href=\"".$link."\">$filename</a></td>"; //1st col
                    echo "<td>".human_filesize(filesize($folder.$filename))."</td>"; //2nd col
                    echo "</tr>"; //End line
                }
            }
} 
else {
    echo '<div class="alert alert-danger"><strong>Access denied!</strong></div>';
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
                <p><a href="https://github.com/Wr0ngName/Youtube-dl-WebUI" target="_blank">Forked from Github</a></p>
                <p>Adapted by <a href="https://twitter.com/_wr0ngname_" target="_blank">@_wr0ngname_</a> - Website : <a href="https://wr0ng.name" target="_blank">wr0ng.name</a></p>
            </div>
        </footer>
    </body>
</html>
