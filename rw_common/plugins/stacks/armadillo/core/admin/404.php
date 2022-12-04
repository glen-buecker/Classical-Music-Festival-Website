<?php
$armadillo = Slim::getInstance();
$rootUri = $armadillo->request()->getRootUri();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo Armadillo_Language::msg('ARM_404_PAGE_TITLE'); ?></title>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.3.0/css/font-awesome.min.css" />
        <link rel="stylesheet" href="<?php echo $rootUri; ?>/core/css/adminStyles.css" />
    </head>
    <body>
        <div id="topPanel">
            <div class="backToSite"><a href="<?php echo $rootUri; ?>/../../../../">&larr;&nbsp;<?php echo Armadillo_Language::msg('ARM_VISIT_SITE_LINK'); ?></a></div>
            <?php echo Armadillo_Language::msg('ARM_404_WANDERING_THE_WEB_TEXT'); ?>
        </div>
        <div id="adminPanelContainer">
            <div style="text-align:center;">
                <?php echo Armadillo_Language::msg('ARM_404_PAGE_MESSAGE_TEXT'); ?>
                <p><a href="<?php echo $rootUri; ?>/"><?php echo Armadillo_Language::msg('ARM_404_PAGE_START_OVER_LINK'); ?></a></p>
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
        <script type="text/javascript" src="https://ajax.googleapis.com/ajax/libs/jquery/1.8/jquery.min.js"></script>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/core/scripts/armadilloAdmin.js"></script>
    </body>
</html>
