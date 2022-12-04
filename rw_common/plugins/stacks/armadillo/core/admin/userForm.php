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
        <link href='//fonts.googleapis.com/css?family=Fira+Sans:300,500,300italic' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="<?php echo $rootUri; ?>/../core/css/adminStyles.css" />
    </head>
    <body>
        <div id="topPanel">
            <div class="backToSite pull-left"><a href="<?php echo $rootUri; ?>/../../../../../">&larr;&nbsp;<?php echo Armadillo_Language::msg('ARM_VISIT_SITE_LINK'); ?></a></div>
            <div class="loggedInUser pull-right"><?php
                if ( isset($_SESSION['loggedIn']) and $_SESSION['loggedIn'] === TRUE ) {
                    echo Armadillo_Language::msg('ARM_GREETING') . ", " . $_SESSION['username'] . "!&nbsp;&nbsp;&nbsp;<a href='" . $rootUri . "/users/edit/" . $_SESSION['userID'] . "/'>" . Armadillo_Language::msg('ARM_PROFILE_EDIT_LINK') . "</a>&nbsp;|&nbsp;<a href='" . $rootUri . "/logout/'>" . Armadillo_Language::msg('ARM_LOGOUT_LINK') . "</a>";
                } else { echo Armadillo_Language::msg('ARM_PLEASE_LOGIN_TEXT'); }
            ?></div>
        </div>
        <div class="adminPanelWrapper">
            <div id="adminPanelContainer" class="container">
                <?php
                    if (isset($_SESSION['loggedIn']) and $_SESSION['loggedIn'] == TRUE):
                        if ( isset($flash['notification']) ) { echo '<p class="notification">' . $flash['notification'] . '</p>'; }
                        $newUserDetails = NULL;
                        if ( isset($_SESSION['newUserDetails']) ) { $newUserDetails = $_SESSION['newUserDetails']; }
                        if ($this->data['contentState'] === 'edit') { $userInfo = Armadillo_User::getUser($this->data['userID']); }
                        if ( $_SESSION['role'] === 'admin' || ( isset($userInfo['id']) and $userInfo['id'] === $_SESSION['userID'] ) ):
                ?>
                <div class="row">
                    <div class="userFormContent col-sm-offset-3 col-sm-6">
                        <h3 class="userFormTab text-center"><?php if ($this->data['contentState'] === "edit") { echo Armadillo_Language::msg('ARM_USER_EDIT_TEXT'); } else { echo Armadillo_Language::msg('ARM_USER_CREATE_NEW_TEXT'); } ?></h3>
                        <hr/>
                        <div class="userFormEntry">
                            <form id="userEntryDetails" class="form-horizontal" action="./../<?php if ($this->data['contentState'] === 'edit') { echo "../"; } ?>" method="post">
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_USERNAME'); ?></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8">
                                        <input type="text" class="username form-control" name="username" placeholder="Enter Desired Username"
                                    value="<?php if ($this->data['contentState'] === "edit") { htmlout($userInfo['username']); }
                                                 if ($newUserDetails !== NULL) { htmlout($newUserDetails['username']); }?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_PASSWORD'); ?></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8">
                                        <input type="password" class="password form-control" name="password" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_CONFIRM_PASSWORD'); ?></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8">
                                        <input type="password" class="password form-control" name="confirmPassword" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_FULLNAME'); ?></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8">
                                        <input type="text" class="name form-control" name="name" placeholder="e.g., John Smith"
                                    value="<?php if ($this->data['contentState'] === "edit") { htmlout($userInfo['name']); }
                                                 if ($newUserDetails !== NULL) { htmlout($newUserDetails['name']); }?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_EMAIL'); ?></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8">
                                        <input type="text" class="email form-control" name="email" placeholder="email@example.com"
                                    value="<?php if ($this->data['contentState'] === "edit") { htmlout($userInfo['email']); }
                                                 if ($newUserDetails !== NULL) { htmlout($newUserDetails['email']); }?>" />
                                    </div>
                                </div>
                                <div class="form-group">
                                    <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_LANGUAGE'); ?></label>
                                    <div class="col-xs-12 col-sm-8 col-md-8">
                                        <select class="form-control" name="language">
                                            <option value="default" <?php if ( !isset($userInfo['language']) or ($userInfo['language'] == 'default') ) { echo "selected=\"selected\""; } ?>><?php echo Armadillo_Language::msg('ARM_USER_FORM_LANGUAGE_AUTODETECT'); ?></option>
                                            <?php $localizations = Armadillo_Language::listInstalledLanguages();
                                                foreach ($localizations as $lang) {
                                                    $selected = ( ($this->data['contentState'] === "edit") and ( isset($userInfo['language']) and ($userInfo['language'] == $lang['abbr']) ) ) ? "selected=\"selected\"" : '' ;
                                                    echo '<option value="'.$lang['abbr'].'" '.$selected.'>'.$lang['name'].'</option>';
                                                } ?>
                                        </select>
                                    </div>
                                </div>
                                <?php if ( $_SESSION['role'] === 'admin' ): ?>
                                    <div class="form-group">
                                        <label class="col-xs-12 col-sm-4 col-md-4 control-label"><?php echo Armadillo_Language::msg('ARM_USER_FORM_ROLE'); ?></label>
                                        <div class="col-xs-12 col-sm-8 col-md-8">
                                            <select class="form-control" name="role">
                                                <option value="admin"
                                                    <?php if ( ( ($this->data['contentState'] === "edit") and ($userInfo['role'] == 'admin') ) or ( $newUserDetails !== NULL and $newUserDetails['role'] == 'admin' ) ): ?>
                                                    selected="selected"
                                                    <?php endif; ?>
                                                ><?php echo Armadillo_Language::msg('ARM_USER_FORM_ROLE_ADMIN'); ?></option>
                                                <option value="editor"
                                                    <?php if ( ( ($this->data['contentState'] === "edit") and ($userInfo['role'] == 'editor') ) or ( $newUserDetails !== NULL and $newUserDetails['role'] == 'editor' ) ): ?>
                                                    selected="selected"
                                                    <?php endif; ?>
                                                ><?php echo Armadillo_Language::msg('ARM_USER_FORM_ROLE_EDITOR'); ?></option>
                                                <option value="contributor"
                                                    <?php if ( ( ($this->data['contentState'] === "edit") and ($userInfo['role'] == 'contributor') ) or ( $newUserDetails !== NULL and $newUserDetails['role'] == 'contributor' ) ): ?>
                                                    selected="selected"
                                                    <?php endif; ?>
                                                ><?php echo Armadillo_Language::msg('ARM_USER_FORM_ROLE_CONTRIBUTOR'); ?></option>
                                                <option value="blogger"
                                                    <?php if ( ( ($this->data['contentState'] === "edit") and ($userInfo['role'] == 'blogger') ) or ( $this->data['contentState'] !== "edit" ) or ( $newUserDetails !== NULL and $newUserDetails['role'] == 'blogger' ) ): ?>
                                                    selected="selected"
                                                    <?php endif; ?>
                                                ><?php echo Armadillo_Language::msg('ARM_USER_FORM_ROLE_BLOGGER'); ?></option>
                                            </select>
                                        </div>
                                    </div>
                                <?php endif; ?>
                                <?php if ($this->data['contentState'] === "edit") { echo '<input type="hidden" name="id" value="' . $userInfo['id'] . '"/>'; } ?>
                                <?php if ($this->data['contentState'] === "edit"): ?><input type="hidden" name="_METHOD" value="PUT"/><?php endif; ?>
                                <div class="form-group">
                                    <div class="submitButton col-sm-offset-4 col-sm-8">
                                        <input class="btn btn-success" type="submit" value="<?php if ($this->data['contentState'] === "edit") { echo Armadillo_Language::msg('ARM_USER_SAVE_TEXT'); } else { echo Armadillo_Language::msg('ARM_USER_CREATE_TEXT'); } ?>" class="saveUser" />&nbsp;&nbsp;&nbsp;<input class="btn btn-danger" type="submit" name="cancel" value="<?php echo Armadillo_Language::msg('ARM_CANCEL_TEXT'); ?>" class="cancel" />
                                    </div>
                                </div>
                                <?php if ( $_SESSION['role'] === 'admin' ): ?>
                                <p class="note help-block text-center"><?php echo Armadillo_Language::msg('ARM_USER_FORM_ROLE_NOTE'); ?></p>
                                <?php endif; ?>
                            </form>
                        </div>
                    </div>
                </div>
                <?php else: $armadillo->redirect('./../../'); endif; ?>
            <?php else: $armadillo->redirect('./../../'); endif; ?>
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
        <script>$(document).ready(function() { $('#adminPassword').passField({}); });</script>
    </body>
</html>
