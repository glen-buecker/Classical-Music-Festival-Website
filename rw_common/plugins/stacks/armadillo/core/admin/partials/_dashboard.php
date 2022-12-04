<!-- Dashboard Admin Panel -->
<div class="dashboardAdminPanel">
    <h2 class="dashboardAdminTab">
        <?php echo Armadillo_Language::msg('ARM_DASHBOARD_TAB_TITLE'); ?>    
    </h2>
    <div id="dashboardMenu" class="row">
        <div class="col-xs-12 col-sm-3 col-md-3">
            <?php if ( $_SESSION['enableBlogContent'] or $_SESSION['role'] === 'admin' ): ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="text-center">
                        <a href="./posts/" style="color:black;"><i class="fa fa-list-ul fa-4x"></i></a>
                    </p>
                </div>
                <div class="panel-footer">
                    <h3 class="panel-title text-center">
                        <a href="./posts/">
                            <?php echo Armadillo_Language::msg('ARM_DASHBOARD_NEW_POST_TEXT'); ?>
                        </a>
                    </h3>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3">
            <?php if (( $_SESSION['enablePageContent'] or $_SESSION['role'] === 'admin' ) and $_SESSION['role'] !== 'blogger' ): ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="text-center">
                        <a href="./pages/new/" style="color:black;"><i class="fa fa-file-o fa-4x"></i></a>
                    </p>
                </div>
                <div class="panel-footer">
                    <h3 class="panel-title text-center">
                        <a href="./pages/new/">
                            <?php echo Armadillo_Language::msg('ARM_DASHBOARD_NEW_PAGE_TEXT'); ?>
                        </a>
                    </h3>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3">
            <?php if (( $_SESSION['enableSoloContent'] or $_SESSION['role'] === 'admin' ) and $_SESSION['role'] !== 'blogger'): ?>
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="text-center">
                        <a href="./content/new/" style="color:black;"><i class="fa fa-thumb-tack fa-4x"></i></a>
                    </p>
                </div>
                <div class="panel-footer">
                    <h3 class="panel-title text-center">
                        <a href="./content/new/">
                            <?php echo Armadillo_Language::msg('ARM_DASHBOARD_NEW_SOLO_CONTENT_TEXT'); ?>
                        </a>
                    </h3>
                </div>
            </div>
            <?php endif; ?>
        </div>
        <div class="col-xs-12 col-sm-3 col-md-3">
            <div class="panel panel-default">
                <div class="panel-body">
                    <p class="text-center">
                        <a href="./media/upload/" style="color:black;"><i class="fa fa-cloud-upload fa-4x"></i></a>
                    </p>
                </div>
                <div class="panel-footer">
                    <h3 class="panel-title text-center">
                        <a href="./media/upload/">
                            <?php echo Armadillo_Language::msg('ARM_DASHBOARD_UPLOAD_MEDIA_TEXT'); ?>
                        </a>
                    </h3>
                </div>
            </div>
        </div>
    </div>
</div>