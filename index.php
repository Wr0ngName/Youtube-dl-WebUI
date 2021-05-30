<?php
    require_once("config.php"); 
    require_once("sessions.php");
    require_once("utilities.php");

    if(isset($_POST['passwd']) && !empty($_POST['passwd'])) startSession($_POST['passwd']);
    if(isset($_GET['logout']) && $_GET['logout'] == 1) endSession();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Youtube-dl WebUI</title>
        <link rel="stylesheet" href="css/bootstrap.css" media="screen">
        <link rel="stylesheet" href="css/bootswatch.min.css">
    </head>
    <body>
        <div class="navbar navbar-default">
            <div class="navbar-header">
                <button type="button" class="navbar-toggle" data-toggle="collapse" data-target=".navbar-responsive-collapse">
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                    <span class="icon-bar"></span>
                </button>
                <a class="navbar-brand" href="<?php echo $mainPage; ?>">Youtube-dl WebUI</a>
            </div>
            <div class="navbar-collapse collapse navbar-responsive-collapse">
                <ul class="nav navbar-nav">
                    <li class="active"><a href="<?php echo $mainPage; ?>">Download</a></li>
                    <li><a href="<?php echo $listPage; ?>">Browse</a></li>
                </ul>
            </div>
        </div>
        <div class="container">
            <h1>Download</h1>
<?php
    if(isset($_GET['url']) && !empty($_GET['url']) && !empty($_GET['downloadFileType']) && $_SESSION['logged'] == 1)
    {
        $url = escapeshellarg($_GET['url']);
        $namingScheme = '%(uploader)s - %(title)s (key: %(id)s).%(ext)s';

        if ($_GET['downloadFileType'] == 'audio')
            $cmd = 'youtube-dl -x --audio-format mp3 -f \'bestvideo[height<=1080]+bestaudio/best[height<=1080]\' -o ' . escapeshellarg($folder.$namingScheme) . ' ' . $url . ' 2>&1';
        else
            $cmd = 'youtube-dl -f \'bestvideo[height<=1080]+bestaudio/best[height<=1080]\' -o ' . escapeshellarg($folder.$namingScheme) . ' ' . $url . ' 2>&1';

        exec($cmd, $output, $ret);
        if($ret == 0)
        {
            echo '<div class="alert alert-success">
                    <strong>Download succeed !</strong> <a href="'.$listPage.'" class="alert-link">Link to the file</a>.
                </div>';
        }
        else{
            echo '<div class="alert alert-dismissable alert-danger">
                    <strong>Oh snap!</strong> Something went wrong. Error code : <br>';
            foreach($output as $out)
            {
                echo $out . '<br>'; 
            }
            echo '</div>';
        }
    }
    elseif(isset($_SESSION['logged']) && $_SESSION['logged'] == 1)
    { ?>
            <form class="form-horizontal" action="<?php echo $mainPage; ?>">
                <fieldset>
                    <div class="form-group">
                        <div class="col-lg-10">
                            <input class="form-control" id="url" name="url" placeholder="Link to video or playlist" type="text">
                        </div>
                        <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary">Download</button>
                        </div>
                    </div>

                    <div class="form-group">
                        <div class="col-lg-12">
                            <label><input class="form-check-input" type="radio" name="downloadFileType" id="downloadFileType" value="video" checked="checked"> Video (MP4)</label>&nbsp;&nbsp;&nbsp;&nbsp;
                            <label><input class="form-check-input" type="radio" name="downloadFileType" id="downloadFileType" value="audio"> Audio (MP3)</label>
                        </div>
                    </div>
                    
                </fieldset>
            </form>
            <br>

            <?php destFolderExists($folder);?>
            <div class="row">
                <div class="col-lg-6">
                    <div class="panel panel-info">
                        <div class="panel-heading"><h3 class="panel-title">Info</h3></div>
                        <div class="panel-body">
                            <p>Free space : <?php if(file_exists($folder)){ echo human_filesize(disk_free_space($folder),1)."o";} else {echo "Folder not found";} ?></b></p>
                            <p>Download folder : <?php echo $folder ;?></p>
                        </div>
                    </div>
                </div>
                <div class="col-lg-6">
                    <div class="panel panel-info">
                        <div class="panel-heading"><h3 class="panel-title">Help</h3></div>
                        <div class="panel-body">
                            <p><b>How does it work ?</b></p>
                            <p>Simply paste your video link in the field and click "Download"</p>
                            <p><b>With which sites does it works ?</b></p>
                            <p><a href="http://rg3.github.io/youtube-dl/supportedsites.html">Here</a> is the list of the supported sites</p>
                            <p><b>How can I download the video on my computer ?</b></p>
                            <p>Go to "List of videos", choose one, right click on the link and do "Save target as ..." </p>
                        </div>
                    </div>
                </div>
            </div>
<?php
    }
    else{ ?>
        <form class="form-horizontal" action="<?php echo $mainPage; ?>" method="POST" >
            <fieldset>
                <legend>Login</legend>
                <div class="form-group">
                    <div class="col-lg-8">
                        <input class="form-control" id="passwd" name="passwd" placeholder="Password" type="password">
                    </div>
                    <div class="col-lg-2">
                        <button type="submit" class="btn btn-primary">Log in</button>
                    </div>
                </div>
            </fieldset>
        </form>
<?php
        }
    if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1) echo '<p><a href="index.php?logout=1">Logout</a></p>';
?>
        </div><!-- End container -->
        <footer>
            <div class="well text-center">
                <p><a href="https://github.com/p1rox/Youtube-dl-WebUI" target="_blank">Forked from Github</a></p>
                <p>Adapted by <a href="https://twitter.com/_wr0ngname_" target="_blank">@_wr0ngname_</a> - Website : <a href="https://wr0ng.name" target="_blank">wr0ng.name</a></p>
            </div>
        </footer>
    </body>
</html>
