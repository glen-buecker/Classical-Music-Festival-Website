<?php
$armadillo = Slim::getInstance();
$rootUri = $armadillo->request()->getRootUri();
$resourceUri = $armadillo->request()->getResourceUri();
$worldTimezones = array(
    'America/New_York' => 'America/New York',
    'America/Chicago' => 'America/Chicago',
    'America/Denver' => 'America/Denver',
    'America/Phoenix' => 'America/Phoenix',
    'America/Los_Angeles' => 'America/Los Angeles',
    'America/Anchorage' => 'America/Anchorage',
    'Pacific/Honolulu' => 'America/Honolulu',
    'America/Moncton' => 'Canada/Moncton',
    'America/Toronto' => 'Canada/Toronto',
    'America/Winnipeg' => 'Canada/Winnipeg',
    'America/Calgary' => 'Canada/Calgary',
    'America/Vancouver' => 'Canada/Vancouver',
    'Europe/London' => 'Europe/London',
    'Europe/Berlin' => 'Europe/Berlin',
    'Europe/Paris' => 'Europe/Paris',
    'Europe/Madrid' => 'Europe/Madrid',
    'Europe/Rome' => 'Europe/Rome',
    'Europe/Stockholm' => 'Europe/Stockholm',
    'Europe/Helsinki' => 'Europe/Helsinki',
    'Europe/Moscow' => 'Europe/Moscow',
    'Australia/Canberra' => 'Australia/Canberra',
    'Australia/Adelaide' => 'Australia/Adelaide',
    'Australia/Perth' => 'Australia/Perth',
    'Pacific/Auckland' => 'New Zealand/Auckland',
    'Asia/Tokyo' => 'Asia/Tokyo',
    'Asia/Seoul' => 'Asia/Seoul',
    'Asia/Taipei' => 'Asia/Taipei',
    'Asia/Manila' => 'Asia/Manila',
    'Asia/Singapore' => 'Asia/Singapore',
    'Asia/Jakarta' => 'Asia/Jakarta',
    'Asia/Bangkok' => 'Asia/Bangkok',
    'Asia/Calcutta' => 'India/Kolkata',
    'Asia/Karachi' => 'Middle East/Karachi',
    'Asia/Tehran' => 'Middle East/Tehran',
    'Asia/Jerusalem' => 'Middle East/Jerusalem'
);
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
    </head>
    <body>
        <div id="topPanel">
            <div class="backToSite pull-left"><a href="<?php echo $rootUri; ?>/../../../../../">&larr;&nbsp;<?php echo Armadillo_Language::msg('ARM_VISIT_SITE_LINK'); ?></a></div>
            <div class="pull-right"><?php echo Armadillo_Language::msg('ARM_SETUP_WELCOME'); ?></div>
        </div>
        <div class="adminPanelWrapper">
            <div id="adminPanelContainer" class="container">
                <div class="row">
                    <div class="userFormContent col-sm-offset-2 col-sm-8">
                        <?php
                            if ( isset($flash['notification']) ) { echo '<p class="notification bg-info">' . $flash['notification'] . '</p>'; }
                            if ( isset($flash['warning']) ) { echo '<p class="warning bg-danger">' . $flash['warning'] . '</p>'; }
                        ?>
                        <div id="setupPanel">
                            <h1 class="text-center" id="setupDetailsTab"><?php echo Armadillo_Language::msg('ARM_SETUP_DETAILS_TAB'); ?></h1>
                            <hr/>
                            <?php if ( $this->data['setupType'] === 'complete' or $this->data['setupType'] === 'redo' ) : ?>
                            <form class="form-horizontal" id="setupDetails" action="./" method="post">
                                <div id="setupStep1" class="setupSection">
                                    <h2><?php echo Armadillo_Language::msg('ARM_SETUP_STEP1'); ?></h2>
                                    <hr/>
                                    <?php if ( $this->data['setupType'] === 'redo' ): ?>
                                    <p class="note warning bg-danger">PROCEED WITH CAUTION. This form will allow you to update/reset configuration info, which may result in your database becoming inaccessible.</p>
                                    <?php else: ?>
                                    <p class="note" style="color: black;"><?php echo Armadillo_Language::msg('ARM_SETUP_WELCOME_INTRO'); ?></p>
                                    <?php endif; ?>
                                    <p class="note"><?php echo Armadillo_Language::msg('ARM_SETUP_WELCOME_INTRO_NOTE'); ?></p>
                                    <div class="text-center"><span id="dbDetailsButton" class="btn btn-default"><?php echo Armadillo_Language::msg('ARM_SETUP_ENTER_DB_DETAILS'); ?></span></div>
                                    <br/>
                                    <div id="databaseDetails">
                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_DB_HOSTNAME'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_DB_HOSTNAME_TOOLTIP'); ?>" class="qtooltip" /></label>
                                            <div class="col-xs-12 col-sm-8 col-md-8">
                                                <input type="text" class="form-control dbHostname" name="dbHostname" id="dbHostname" placeholder="Enter Hostname" value="<?php if ( isset($flash['notification']) && isset($this->data['dbHostname']) ) { echo $this->data['dbHostname']; } else { echo "localhost"; } ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_DB_NAME'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_DB_NAME_TOOLTIP'); ?>" class="qtooltip" /></label>
                                            <div class="col-xs-12 col-sm-8 col-md-8">
                                                <input type="text" class="form-control dbName" name="dbName" id="dbName" value="<?php if ( isset($flash['notification']) && isset($this->data['dbName']) ) { echo $this->data['dbName']; } ?>"/>
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_DB_USERNAME'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_DB_USERNAME_TOOLTIP'); ?>" class="qtooltip" /></label>
                                            <div class="col-xs-12 col-sm-8 col-md-8">
                                                <input type="text" class="form-control dbUser" name="dbUser" id="dbUser" value="<?php if ( isset($flash['notification']) && isset($this->data['dbUser']) ) { echo $this->data['dbUser']; } ?>" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_DB_PASSWORD'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_DB_PASSWORD_TOOLTIP'); ?>" class="qtooltip" /></label>
                                            <div class="col-xs-12 col-sm-8 col-md-8">
                                                <input type="password" class="form-control password" name="dbPassword" id="dbPassword" />
                                            </div>
                                        </div>
                                        <div class="form-group">
                                            <div class="submitButton col-sm-offset-4 col-sm-8">
                                                <div id="goToStep2" class="btn btn-success nextButton"><?php echo Armadillo_Language::msg('ARM_NEXT_TEXT'); ?></div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <div id="setupStep2" class="setupSection">
                                    <h2><?php echo Armadillo_Language::msg('ARM_SETUP_STEP2'); ?></h2>
                                    <hr/>
                                    <p class="note"><?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_PASSWORD_NOTE'); ?></p>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_USERNAME'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_USERNAME_TOOLTIP'); ?>" class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <input type="text" class="form-control" value="admin" disabled="disabled" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_PASSWORD'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_PASSWORD_TOOLTIP'); ?>." class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <input type="password" class="form-control password" name="adminPassword" id="adminPassword" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_PASSWORD_CONFIRM'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_PASSWORD_CONFIRM_TOOLTIP'); ?>" class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <input type="password" class="form-control password" name="adminConfirmPassword" id="adminConfirmPassword" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_EMAIL'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_EMAIL_TOOLTIP'); ?>" class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <input type="text" class="form-control adminEmail" name="adminEmail" id="adminEmail" value="<?php if ( isset($flash['notification']) && isset($this->data['adminEmail']) ) { echo $this->data['adminEmail']; } ?>" />
                                            <p class="note help-block"><?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_EMAIL_NOTE'); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <div class="submitButton col-sm-offset-4 col-sm-8">
                                            <div id="goToStep3" class="btn btn-success nextButton"><?php echo Armadillo_Language::msg('ARM_NEXT_TEXT'); ?></div>
                                        </div>
                                    </div>
                                </div>
                                <div id="setupStep3" class="setupSection">
                                    <h2><?php echo Armadillo_Language::msg('ARM_SETUP_STEP3'); ?></h2>
                                    <hr/>
                                    <p class="center text-center"><?php echo Armadillo_Language::msg('ARM_SETUP_TIMEZONE_NOTE'); ?></p>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_TIMEZONE'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_TIMEZONE_TOOLTIP'); ?>" class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-4">
                                            <select class="form-control" name="timezone">
                                            <?php foreach ($worldTimezones as $zone => $name) {
                                                $selected = '';
                                                if ( isset($flash['notification']) && isset($this->data['timezone']) ) { if ($this->data['timezone'] === $zone) { $selected = "selected='selected'"; } }
                                                echo "<option value='$zone' $selected>$name</option>"; } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_SITE_LANGUAGE'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_TIMEZONE_TOOLTIP'); ?>" class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-4">
                                            <select class="form-control" name="site_language">
                                            <option value="default"><?php echo Armadillo_Language::msg('ARM_USER_FORM_LANGUAGE_AUTODETECT'); ?></option>
                                            <?php $siteLanguage = Armadillo_Language::listInstalledLanguages();
                                                foreach ($siteLanguage as $lang) {
                                                    echo '<option value="'.$lang['abbr'].'">'.$lang['name'].'</option>';
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="setupType" value="<?php echo $this->data['setupType']; ?>"/>
                                    <div class="form-group">
                                        <div class="submitButton col-sm-offset-4 col-sm-8">
                                            <input type="submit" value="<?php echo Armadillo_Language::msg('ARM_FINISH_TEXT'); ?>" class="btn btn-success finishSetup"/>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./../../../" class="btn btn-danger cancel"><?php echo Armadillo_Language::msg('ARM_CANCEL_TEXT'); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <p class="note"></p>
                            </form>
                            <?php elseif ( $this->data['setupType'] === 'dbAndAdminUser' or $this->data['setupType'] === 'adminUserOnly' ) : ?>
                            <form class="form-horizontal" id="setupDetails" action="./" method="post">
                                <div class="setupSection">
                                    <p class="note bg-info"><?php echo Armadillo_Language::msg('ARM_SETUP_CONFIG_FILE_FOUND_NOTE'); ?></p>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_USERNAME'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_USERNAME_TOOLTIP'); ?>" class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <input type="text" class="form-control" value="admin" disabled="disabled" />
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_PASSWORD'); ?></label>
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <input type="password" class="form-control password" name="adminPassword" id="adminPassword"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_PASSWORD_CONFIRM'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_PASSWORD_CONFIRM_TOOLTIP'); ?>" class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <input type="password" class="form-control password" name="adminConfirmPassword" id="adminConfirmPassword"/>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_EMAIL'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_EMAIL_TOOLTIP'); ?>" class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <input type="text" class="form-control adminEmail" name="adminEmail" id="adminEmail" value="<?php if ( isset($flash['notification']) && isset($this->data['adminEmail']) ) { echo $this->data['adminEmail']; } ?>"/>
                                            <p class="note help-block"><?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_EMAIL_NOTE'); ?></p>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_TIMEZONE'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_TIMEZONE_TOOLTIP'); ?>" class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-4">
                                            <select class="form-control" name="timezone">
                                            <?php foreach ($worldTimezones as $zone => $name) {
                                                $selected = '';
                                                if ( isset($flash['notification']) && isset($this->data['timezone']) ) { if ($this->data['timezone'] === $zone) { $selected = "selected='selected'"; } }
                                                echo "<option value='$zone' $selected>$name</option>"; } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_SETUP_SITE_LANGUAGE'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETUP_TIMEZONE_TOOLTIP'); ?>" class="qtooltip" /></label>
                                        <div class="col-xs-12 col-sm-8 col-md-4">
                                            <select class="form-control" name="site_language">
                                            <option value="default"><?php echo Armadillo_Language::msg('ARM_USER_FORM_LANGUAGE_AUTODETECT'); ?></option>
                                            <?php $siteLanguage = Armadillo_Language::listInstalledLanguages();
                                                foreach ($siteLanguage as $lang) {
                                                    echo '<option value="'.$lang['abbr'].'">'.$lang['name'].'</option>';
                                                } ?>
                                            </select>
                                        </div>
                                    </div>
                                    <input type="hidden" name="setupType" value="<?php echo $this->data['setupType']; ?>"/>
                                    <div class="form-group">
                                        <div class="submitButton col-sm-offset-4 col-sm-8">
                                            <input type="submit" value="<?php echo Armadillo_Language::msg('ARM_FINISH_TEXT'); ?>" class="btn btn-success finishSetup" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./../../../../" class="btn btn-danger cancel"><?php echo Armadillo_Language::msg('ARM_CANCEL_TEXT'); ?></a>
                                        </div>
                                    </div>
                                </div>
                                <p class="note"></p>
                            </form>
                            <?php else: $armadillo->redirect('./../'); endif; ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div id="bottomPanel">
            <div class="armadillo_version">Web Version <?php echo $GLOBALS['armadilloVersion']; ?></div>
            <div class="armadillo_info"><a href="http://docs.nimblehost.com/armadillo"><?php echo Armadillo_Language::msg('ARM_DOCUMENTATION_LINK'); ?></a></div>
        </div>
        <script type="text/javascript" src="//code.jquery.com/jquery-2.1.4.min.js"></script>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/armadilloAdmin.js"></script>
        <script type="text/javascript">
            var dbMessage1 = "<?php echo Armadillo_Language::msg('ARM_SETUP_DB_DETAILS_INCORRECT'); ?>";
            var dbMessage2 = "<?php echo Armadillo_Language::msg('ARM_SETUP_DB_DETAILS_VERIFICATION_ERROR'); ?>";
            var psswrdMessage1 = "<?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_EMAIL_REQUIRED'); ?>";
            var psswrdMessage2 = "<?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_PASSWORDS_NO_MATCH'); ?>";
            var emailMessage = "<?php echo Armadillo_Language::msg('ARM_SETUP_ADMIN_EMAIL_NOT_VALID'); ?>";
            ArmadilloAdmin.armadilloSetup(dbMessage1, dbMessage2, psswrdMessage1, psswrdMessage2, emailMessage);
            $(function(){
                $('.qtooltip').qtip({
                    content: false, 
                    position: {
                        my: 'bottom center',
                        at: 'top center'
                    },
                    style: { classes: 'armadilloTooltip' }
                }); 
            });
        </script>
        <?php if ( $this->data['setupType'] === 'redo' ): ?>
        <script type="text/javascript">
        </script>
        <?php endif; ?>
    </body>
</html>
