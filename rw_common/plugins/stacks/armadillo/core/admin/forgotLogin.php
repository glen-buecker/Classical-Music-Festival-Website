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
        <title><?php echo $this->data['pageTitle'] ?></title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="<?php echo $rootUri; ?>/../core/css/adminStyles.css" />
    </head>
    <body>
        <div id="topPanel">
            <div class="backToSite"><a href="<?php echo $rootUri; ?>/../../../../../">&larr;&nbsp;<?php echo Armadillo_Language::msg('ARM_VISIT_SITE_LINK'); ?></a></div>
            &nbsp;
        </div>
        <div class="adminPanelWrapper">
            <div id="adminPanelContainer" class="container">
                <?php
                    if ( isset($flash['notification']) ) { echo '<p class="notification">' . $flash['notification'] . '</p>'; }
                    if ( isset($flash['warning']) ) { echo '<p class="warning">' . $flash['warning'] . '</p>'; }
                ?>
                <div class="row">
                    <div id="setupPanel" class="col-sm-offset-3 col-sm-6">
                        <h3 id="setupDetailsTab" class="text-center"><?php echo Armadillo_Language::msg('ARM_FORGOT_LOGIN_FORM_TITLE'); ?></h3>
                        <hr/>
                        <?php if ( isset($this->data['loginMode']) and $this->data['loginMode'] === 'forgot' ) : ?>
                        <form class="form-horizontal" id="setupDetails" action="./" method="post">
                            <p class="note text-center"><?php echo Armadillo_Language::msg('ARM_FORGOT_LOGIN_FORM_MESSAGE'); ?></p>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_FORGOT_LOGIN_FORM_EMAIL'); ?></label>
                                <div class="col-xs-12 col-sm-8 col-md-8">
                                    <input type="text" class="loginEmail form-control" name="loginEmail" />
                                </div>
                            </div>
                            <input type="hidden" name="loginMode" value="forgot"/>
                            <div class="form-group">
                                <div class="submitButton col-sm-offset-4 col-sm-8">
                                    <input type="submit" value="Submit" class="btn btn-success greenButton" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./../../" class="btn btn-danger cancel"><?php echo Armadillo_Language::msg('ARM_CANCEL_TEXT'); ?></a>
                                </div>
                            </div>
                            <p class="note"></p>
                        </form>
                        <?php elseif ( isset($this->data['loginMode']) and $this->data['loginMode'] === 'reset' ) : ?>
                        <form class="form-horizontal" id="setupDetails" action="./" method="post">
                            <p class="note"></p>
                            <?php $userAccounts = Armadillo_User::retrieveUserAccounts($this->data['email'], $this->data['token']); ?>
                            <?php if ( !empty($userAccounts) ): ?>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-4 col-md-4 control-label resetLoginField"><?php echo Armadillo_Language::msg('ARM_RESET_LOGIN_FORM_SELECT_ACCOUNT'); ?></label>
                                <div class="col-xs-12 col-sm-8 col-md-8">
                                    <select class="form-control" name="userID">
                                    <?php foreach ($userAccounts as $account) {
                                            $id = $account['id'];
                                            $username = $account['username'];
                                            echo "<option value='$id'>$username</option>"; } ?>
                                    </select>
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-4 col-md-4 control-label resetLoginField"><?php echo Armadillo_Language::msg('ARM_RESET_LOGIN_FORM_NEW_PASSWORD'); ?></label>
                                <div class="col-xs-12 col-sm-8 col-md-8">
                                    <input type="password" class="loginPassword form-control" name="loginPassword" />
                                </div>
                            </div>
                            <div class="form-group">
                                <label class="col-xs-12 col-sm-4 col-md-4 control-label resetLoginField"><?php echo Armadillo_Language::msg('ARM_RESET_LOGIN_FORM_CONFIRM_PASSWORD'); ?></label>
                                <div class="col-xs-12 col-sm-8 col-md-8">
                                    <input type="password" class="loginConfirmPassword form-control" name="loginConfirmPassword" />
                                </div>
                            </div>
                            <input type="hidden" name="loginMode" value="reset" />
                            <div class="submitButton col-sm-offset-4 col-sm-8">
                                <input type="submit" value="Submit"  class="btn btn-success greenButton" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./../../../../" class="btn btn-danger cancel">Cancel</a>
                            </div>
                            <?php else: ?>
                            <div class="submitButton">
                                <p class="help-block text-center"><?php echo Armadillo_Language::msg('ARM_RESET_LOGIN_FORM_INVALID_LINK'); ?></p>
                            </div>
                            <?php endif; ?>
                        </form>
                        <?php elseif ( isset($this->data['loginMode']) and $this->data['loginMode'] === 'expired' ) : ?>
                        <div class="submitButton"><p><?php echo Armadillo_Language::msg('ARM_RESET_LOGIN_FORM_EXPIRED_LINK'); ?></p></div>
                        <?php endif; ?>
                    </div>
                </div>
            </div>
        </div>
        <div id="bottomPanel">
            <div class="armadillo_version">Web Version <?php echo $GLOBALS['armadilloVersion']; ?></div>
            <div class="armadillo_info">
                <a href="http://docs.nimblehost.com/armadillo/"><?php echo Armadillo_Language::msg('ARM_DOCUMENTATION_LINK'); ?></a></a>
            </div>
            <div class="clearer"></div>
        </div>
        <div class="clearer"></div>
        <script type="text/javascript" src="//code.jquery.com/jquery-2.1.4.min.js"></script>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/armadilloAdmin.js"></script>
    </body>
</html>
