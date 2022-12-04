<?php

$armadillo = Slim::getInstance();
if ( $this->data['contentType'] == 'post' and ( $_SESSION['enableBlogContent'] or $_SESSION['role'] === 'admin' ) ): 

?>
<!-- Content Admin Panel -->
<div class="contentAdminPanel">
    <?php $blogSettings = Armadillo_Post::getAllBlogSettings(); ?>
    <div class="row" style="margin-top:10px;margin-bottom:0;">
        <div class="col-xs-12 col-sm-6 col-sm-offset-3">
            <div class="form-inline well well-sm clearfix" style="margin-bottom:0;">
                <div class="form-group">
                    <label for="blogSelect" class="">
                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_SELECT_TEXT'); ?>
                    </label>
                    <select class="form-control" id="blogSelect" name="blogSelect">
                        <?php
                            foreach ( $blogSettings as $blogDetails ) {
                                $selected = '';
                                if ( $blogDetails['id'] == $this->data['blog_id'] ) {
                                    $_SESSION['blogURL'] = $blogDetails['blog_url'];
                                    $_SESSION['selectedBlog'] = $blogDetails['id'];
                                    $selected = ' selected="selected"';
                                }
                                echo '<option value="' . $blogDetails['id'] . '"' . $selected . '>' . $blogDetails['title'] . '</option>';
                            }
                        ?>
                    </select>
                </div>
                <?php if ( $_SESSION['role'] !== 'blogger' ): ?>
                <a class="btn btn-primary btn-sm pull-right" href="./../../../blogs/new/">
                    <i class='fa fa-plus'></i>
                    <span class='text'>
                        &nbsp;
                        <?php echo Armadillo_Language::msg('ARM_BLOG_CREATE_NEW_TEXT'); ?>
                    </span>
                </a>
                <?php endif; ?>
            </div>
        </div>
    </div>
    <h2 class="contentAdminTab">
        <?php echo Armadillo_Language::msg('ARM_POST_CONTENT_NAME_PLURAL'); ?>
        <a class="btn btn-success btn-sm pull-right" href="./new/">
            <i class='fa fa-plus'></i>
            <span class='text'>
                &nbsp;
                <?php echo Armadillo_Language::msg('ARM_POST_CREATE_NEW_TEXT'); ?>
            </span>
        </a>
    </h2>
    <?php Armadillo_Page::getSummary($this->data['contentType'],$this->data['blog_id']); ?>
</div>

<?php elseif ( $this->data['contentType'] == 'page' and ( $_SESSION['enablePageContent'] or $_SESSION['role'] === 'admin' ) ): ?>

<div class="contentAdminPanel">
    <h2 class="contentAdminTab">
        <?php echo Armadillo_Language::msg('ARM_PAGE_CONTENT_NAME_PLURAL'); ?>
        <a class="btn btn-success btn-sm pull-right" href="./new/">
            <i class='fa fa-plus'></i>
            <span class='text'>
                &nbsp;
                <?php echo Armadillo_Language::msg('ARM_PAGE_CREATE_NEW_TEXT'); ?>
            </span>
        </a>
    </h2>
    <?php Armadillo_Page::getSummary($this->data['contentType']); ?>
</div>

<?php elseif ( $this->data['contentType'] == 'soloContent' and ( $_SESSION['enableSoloContent'] or $_SESSION['role'] === 'admin' ) ): ?>

<div class="contentAdminPanel">
    <h2 class="contentAdminTab">
        <?php echo Armadillo_Language::msg('ARM_SOLO_CONTENT_NAME_PLURAL'); ?>
        <a class="btn btn-success btn-sm pull-right" href="./new/">
            <i class='fa fa-plus'></i>
            <span class='text'>
                &nbsp;
                <?php echo Armadillo_Language::msg('ARM_SOLO_CONTENT_CREATE_NEW_TEXT'); ?>
            </span>
        </a>
    </h2>
    <?php Armadillo_Page::getSummary($this->data['contentType']); ?>
</div>

<?php 

else: $armadillo->redirect('./../'); endif;

?>
