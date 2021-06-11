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
    if(isset($_SESSION['logged']) && $_SESSION['logged'] == 1)
    { ?>
            <form class="form-horizontal" id="dlForm" action="<?php echo $ajaxPage; ?>" method="POST">
                <fieldset id="ajax-form">
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

                <div id="ajax-wait" style="display:none;">
                    <div class="progress">
                      <div class="progress-bar progress-bar-striped progress-bar-animated" role="progressbar" aria-valuemin="0" aria-valuemax="100" style="width: 1%" id="ajax-wait-progress"></div>
                    </div>
                </div>
                <div id="ajax-output"></div>
            </form>

            <div id="ajax-notif" role="alert" aria-live="assertive" aria-atomic="true" class="toast" data-autohide="false" style="display:none;">
              <div class="toast-header">
                <strong class="mr-auto">Progress no moving?</strong>
              </div>
              <div class="toast-body">
                Dont panic! Your file is being converted to the right format.<br />Please be patient.
              </div>
            </div>

            <script type="text/javascript">
            const contactForm = document.getElementById("dlForm");
            var progressInterval = 0;

            function showNotif() {
                document.getElementById("ajax-notif").style.display = "block";
            }

            function hideNotif() {
                document.getElementById("ajax-notif").style.display = "none";
            }

            function showProgress() {
                var targetProgress = "<?php echo $progressPage; ?>";
                var requestProgress = new XMLHttpRequest();
                requestProgress.open("GET", targetProgress);
                requestProgress.onload = function () {
                    var output = document.getElementById("ajax-output");
                    if (requestProgress.readyState === 4 && requestProgress.status === 200) {
                        var jsonDataProgress = JSON.parse(requestProgress.response);
                        document.getElementById("ajax-wait-progress").style.width = jsonDataProgress.progress + "%";

                        var progressLast = parseInt(jsonDataProgress.progress);

                        if(jsonDataProgress.result == "converting") {
                            showNotif();
                        } else if(progressLast == 100) {
                            stopProgress();
                            output.innerHTML = '<div class="alert alert-success"><strong>Download succeed!</strong> <a href="<?php echo $listPage; ?>" class="alert-link">Go to the file</a>.</div>';
                            document.getElementById("ajax-form").style.display = 'block';
                            document.getElementById("ajax-wait").style.display = 'none';
                            hideNotif();
                        } else if(jsonDataProgress.message != "") {
                            stopProgress();
                            output.innerHTML = '<div class="alert alert-danger"><strong>Download error!</strong> General error happened. Contact the administrator if this happens again.</div>';
                            document.getElementById("ajax-form").style.display = 'block';
                            document.getElementById("ajax-wait").style.display = 'none';
                            hideNotif();
                        }
                    } else {
                        output.innerHTML = '<div class="alert alert-danger"><strong>Download error!</strong> General error happened. Contact the administrator if this happens again.</div>';
                        document.getElementById("ajax-form").style.display = 'block';
                        document.getElementById("ajax-wait").style.display = 'none';
                        hideNotif();
                    }
                }
                requestProgress.send();
            }

            function stopProgress() {
                console.log('timeout unset');
                clearInterval(progressInterval);
            }

            function reportProgress() {
                showProgress()
                progressInterval = setInterval(showProgress, 1000);
                console.log('timeout set');
            }

            contactForm.addEventListener("submit", function(event) {

                event.preventDefault();

                var request = new XMLHttpRequest();
                var downloadFileType =  "video";
                var ele = document.getElementsByName('downloadFileType');
                for(i = 0; i < ele.length; i++) {
                    if(ele[i].checked)
                        downloadFileType = ele[i].value;
                }

                var url = document.getElementById("url").value;

                var data = encodeURIComponent("downloadFileType") + '=' + encodeURIComponent(downloadFileType) + '&' + encodeURIComponent("url") + '=' + encodeURIComponent(url);

                var target = "<?php echo $ajaxPage; ?>?" + data;
                request.open("GET", target, true);
                request.setRequestHeader("Content-Type", "x-www-form-urlencoded");

                request.onload = function () {
                    var output = document.getElementById("ajax-output");

                    if (request.readyState === 4 && request.status === 200) {
                        var jsonData = JSON.parse(request.response);

                        if (jsonData.result == "success") {
                            reportProgress();
                        }
                        else
                        {
                            output.innerHTML = '<div class="alert alert-danger"><strong>Download error!</strong> More Details:<pre>' + jsonData.message + '</pre></div>';
                        }
                    } else {
                        output.innerHTML = '<div class="alert alert-danger"><strong>Download error!</strong> General error happened. Contact the administrator if this happens again.</div>';
                    }
                };

                document.getElementById("ajax-form").style.display = 'none';
                document.getElementById("ajax-wait").style.display = 'block';

                request.send();

            });

            <?php
                if (isset($_SESSION['task']) && file_exists($_SESSION['task']))
                {
                    echo "  reportProgress();
                            document.getElementById(\"ajax-form\").style.display = 'none';
                            document.getElementById(\"ajax-wait\").style.display = 'block';";
                }
            ?>
            </script>

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
