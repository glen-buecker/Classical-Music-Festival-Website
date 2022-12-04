<?php
$armadillo = Slim::getInstance();
$rootUri = $armadillo->request()->getRootUri();
$resourceUri = $armadillo->request()->getResourceUri();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $this->data['pageTitle']; ?></title>
        <link href='//fonts.googleapis.com/css?family=Fira+Sans:300,500,300italic' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="<?php echo $rootUri; ?>/../core/css/adminStyles.css" />
        <?php if( isset($this->data['currentTab']) and ( $this->data['currentTab'] !== 'settings' or $this->data['currentTab'] !== 'dashboard' ) ): ?>
        <link rel="stylesheet" href="//cdn.datatables.net/plug-ins/1.10.7/integration/bootstrap/3/dataTables.bootstrap.css" />
        <link rel="stylesheet" href="https://cdn.datatables.net/responsive/1.0.6/css/dataTables.responsive.css" />
        <?php endif; ?>
        <?php if ( isset($this->data['currentTab']) and $this->data['currentTab'] == 'update' ): ?>
        <style type="text/css"> .updates { display: none; } </style>
        <?php endif; ?>
    </head>
    <body>
        <div id="topPanel" class="affix">
            <div class="backToSite pull-left"><a href="<?php echo $rootUri; ?>/../../../../../">&larr;&nbsp;<?php echo Armadillo_Language::msg('ARM_VISIT_SITE_LINK'); ?></a></div>
            <div class="loggedInUser pull-right"><?php
                if (isset($_SESSION['bootMe']) and $_SESSION['bootMe'] > time() and isset($_SESSION['loggedIn']) and $_SESSION['loggedIn'] === TRUE ) {
                    echo "<span class=\"text\">" . Armadillo_Language::msg('ARM_GREETING') . ", " . $_SESSION['username'] . "!&nbsp;&nbsp;&nbsp;<a href='" . $rootUri . "/users/edit/" . $_SESSION['userID'] . "/'>" . Armadillo_Language::msg('ARM_PROFILE_EDIT_LINK') . "</a>&nbsp;|&nbsp;</span><a href='" . $rootUri . "/logout/'>" . Armadillo_Language::msg('ARM_LOGOUT_LINK') . "</a>";
                } else { echo Armadillo_Language::msg('ARM_PLEASE_LOGIN_TEXT'); }
            ?></div>
        </div>
        <?php if ( (isset($_SESSION['bootMe']) and $_SESSION['bootMe'] > time() and isset($_SESSION['loggedIn']) and $_SESSION['loggedIn'] === TRUE) and $this->data['panelToDisplay'] != 'admin/deletePanel.php' ): ?>
        <div id="adminPanelTabs" class="text-center"><?php adminPanelTabs($this->data['currentTab'], $this->data['subPage']); ?></div>
        <?php endif; ?>
        <div class="adminPanelWrapper">
            <div id="adminPanelContainer" class="container">
                <?php
                    if ( isset($flash['notification']) ) { echo '<p class="notification bg-info">' . $flash['notification'] . '</p>'; }
                    if ( isset($flash['warning']) ) { echo '<p class="warning bg-danger">' . $flash['warning'] . '</p>'; }
                    if ( isset($this->data['notification']) ) {	echo '<p class="notification bg-info">' . $this->data['notification'] . '</p>'; }
                    if ( (isset($_SESSION['bootMe']) and $_SESSION['bootMe'] > time() and isset($_SESSION['loggedIn']) and $_SESSION['loggedIn'] === TRUE) or ($this->data['panelToDisplay'] == 'admin/login.php' and Armadillo_Data::adminUserAlreadyExists()) ) {
                        $armadillo->render($this->data['panelToDisplay']);
                    }
                    else if ( !Armadillo_Data::configFileExists() or !Armadillo_Data::adminUserAlreadyExists() ) { 
                        $armadillo->redirect(armadilloURL() . '/index.php/setup/'); 
                    }
                    else {
                        if ($resourceUri !== '/') {
                            Armadillo_User::logoutUser();
                            $armadillo->flash("notification", Armadillo_Language::msg('ARM_PLEASE_LOGIN_TEXT'));
                        }
                        $armadillo->flashKeep();
                        $armadillo->redirect(armadilloURL() . '/index.php/login/');
                    }
                ?>
            </div>
        </div>
        <div id="bottomPanel">
            <div class="armadillo_version pull-left">Version <?php echo $GLOBALS['armadilloVersion']; ?></div>
            <div class="armadillo_info pull-right">
                <a href="http://docs.nimblehost.com/armadillo/"><?php echo Armadillo_Language::msg('ARM_DOCUMENTATION_LINK'); ?></a></a>
            </div>
            <div class="clearer"></div>
        </div>
        <div class="clearer"></div>
        <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
        <?php if( isset($this->data['currentTab']) and ( $this->data['currentTab'] !== 'settings' or $this->data['currentTab'] !== 'dashboard' ) ): ?>
        <script src="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/js/bootstrap.min.js"></script>
        <script src="<?php echo $rootUri; ?>/../core/scripts/datatables.min.js"></script>   
        <script>
            $(document).ready(function() {
                var table = $('#summaryList').dataTable({ 
                    "pageLength": 25, 
                    "order": [<?php if ($this->data['currentTab'] == 'posts'): ?>[ 3, 'desc' ]<?php endif; ?>], 
                    "dom": "<'row'<'col-sm-3'l><'col-sm-4'f><'col-sm-5'p>><'row'<'col-sm-12'tr>><'row'<'col-sm-5'i><'col-sm-7'p>>"
                    <?php if ( isset($_SESSION['language']) and $_SESSION['language'] !== 'default' ): ?>, "language": { "url": "//cdn.datatables.net/plug-ins/1.10.7/i18n/<?php echo Armadillo_Language::langToLanguage($_SESSION['language']); ?>.json" } <?php endif; ?>
                });
                $('#dropboxAuthCodeSubmit').click(function(e){
                    if ($('#dropboxAuthCode').val() == '') {
                        $('#dropboxError').html('Please enter the authorization code provided by Dropbox').fadeTo(500,1).delay(3000).fadeTo(500,0);
                        e.preventDefault();
                        return false;
                    }
                });
            });
        </script>
        <?php endif; ?>
        <?php if( isset($this->data['currentTab']) and $this->data['currentTab'] === 'settings' ): ?>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
        <script src="<?php echo $rootUri; ?>/../core/scripts/jquery.ui.nestedSortable.js"></script>
        <script src="<?php echo $rootUri; ?>/../core/scripts/jscolor/jscolor.js"></script>
        <?php endif; ?>
        <script src="<?php echo $rootUri; ?>/../core/scripts/armadilloAdmin.js"></script>
        <?php  if( isset($this->data['currentTab']) and $this->data['currentTab'] === 'settings' ):  ?>
        <script src="<?php echo $rootUri; ?>/../core/scripts/jeditable.min.js"></script>
        <script>
            $('.collapse').collapse();
            var settingsMessage1 = "<?php echo Armadillo_Language::msg('ARM_SETTINGS_SAVED_TEXT'); ?>";
            var settingsMessage2 = "<?php echo Armadillo_Language::msg('ARM_SETTINGS_SAVED_RSS_UPDATE_FAILED'); ?>";
            var settingsError1 = "<?php echo Armadillo_Language::msg('ARM_SETTINGS_SAVE_ERROR_RESPONSE_UNKNOWN'); ?>";
            var settingsError2 = "<?php echo Armadillo_Language::msg('ARM_SETTINGS_SAVE_ERROR_AJAX_FAILURE'); ?>";
            var settingsError3 = "<?php echo Armadillo_Language::msg('ARM_SETTINGS_SAVE_ERROR_AJAX_NAV_FAILURE'); ?>";
            var settingsError4 = "<?php echo Armadillo_Language::msg('ARM_SETTINGS_SAVE_ERROR_RSS_ENABLED_FEEDFILE_NOT_SPECIFIED'); ?>";
            ArmadilloAdmin.makeMenuSortable();
            ArmadilloAdmin.displayRelevantSettings();
            ArmadilloAdmin.saveAllSettings(settingsMessage1, settingsMessage2, settingsError1, settingsError2, settingsError3, settingsError4);
            ArmadilloAdmin.settingsNotification();
            $(function(){
                $('.qtooltip').qtip({
                    content: false,
                    position: {
                        my: 'bottom center',
                        at: 'top center'
                    },
                    style: { classes: 'armadilloTooltip' }
                });
                $('.dblclick').editable(
                    "<?php echo $rootUri; ?>/../core/model/updateArmadilloSettings.php",
                    { 
                        indicator : "<div class='updateTermsProgress'></div>",
                        tooltip   : "Double-click to edit...",
                        submitdata: {action: "updateTerms"},
                        event     : "dblclick",
                        style  : "inherit"
                    }
                );
                $('form input[type="text"]').keydown(function (e) {
                    if (e.keyCode == 13) {
                        e.preventDefault();
                        return false;
                    }
                });
                $('.deleteTerm').click(function(){
                    var termID = $(this).attr('id');

                    $.ajax({
                        type: 'get',
                        cache: false,
                        url: './../../core/model/updateArmadilloSettings.php?action=deleteTerm',
                        data: 'id=' + termID,
                        beforeSend: function(e) {
                            if ( confirm("<?php echo Armadillo_Language::msg('ARM_DELETE_TERM_MESSAGE'); ?>") ) {}
                            else {
                                $('.updateProgress').fadeOut(300);
                                e.abort();
                            }
                        },
                        success: function(result) {
                            if ( $.isNumeric(result) ) {
                                $('#termID_'+result+', #delete_termID_'+result).fadeOut(300, function() { 
                                    $('#termID_'+result+', #delete_termID_'+result).slideUp(300);
                                });
                            }
                        },
                        error:  function() { }
                    });
                });
            });
        </script>
        <?php endif; ?>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/jquery.fancybox-min.js"></script>
        <?php if ( isset($this->data['currentTab']) and $this->data['currentTab'] === 'media' and $this->data['mediaView'] === "uploadForm" ): ?>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/plupload.full.js"></script>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/jquery.plupload.queue.js"></script>
        <script type="text/javascript">
            (function() {
                // Setup html5/html4 file uploader
                $("#mediaUpload").pluploadQueue({
                    // General settings
                    runtimes : 'html5,html4',
                    url : '<?php echo $rootUri; ?>/../core/model/upload.php',
                    max_file_size : '500mb',
                    chunk_size : '1mb',
                    unique_names : false
                });
                $('form').submit(function(e) {
                    var uploader = $('#mediaUpload').pluploadQueue();

                    // Files in queue upload them first
                    if (uploader.files.length > 0) {
                        // When all files are uploaded submit form
                        uploader.bind('StateChanged', function() {
                            if (uploader.files.length === (uploader.total.uploaded + uploader.total.failed)) {
                                $('form')[0].submit();
                            }
                        });

                        uploader.start();
                    } else {
                        alert("<?php echo Armadillo_Language::msg('ARM_MEDIA_UPLOAD_ZERO_FILES_SELECTED_WARNING'); ?>");
                    }

                    return false;
                });
            })();
        </script>
        <?php endif; ?>
    </body>
</html>
