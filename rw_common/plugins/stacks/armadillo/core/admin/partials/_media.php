<!-- Media Admin Panel -->
<div class="mediaAdminPanel">
    <?php if ($this->data['mediaView'] === "library"): ?>
    <h2 class="mediaAdminTab"><?php echo Armadillo_Language::msg('ARM_MEDIA_NAME'); ?><a class="btn btn-success btn-sm pull-right" href="./upload/"><i class='fa fa-cloud-upload'></i><span class='text'>&nbsp;<?php echo Armadillo_Language::msg('ARM_DASHBOARD_UPLOAD_MEDIA_TEXT'); ?></span></a></h2>
    <?php endif; ?>
    <?php if ($this->data['mediaView'] === "uploadForm" ): ?>
    <h2 class="mediaAdminTab"><?php echo Armadillo_Language::msg('ARM_MEDIA_NAME'); ?><a class="btn btn-success btn-sm pull-right" href="./../"><i class='fa fa-arrow-left'></i><span class='text'>&nbsp;<?php echo Armadillo_Language::msg('ARM_MEDIA_LIBRARY_VIEW'); ?></span></a></h2>
    <?php endif; ?>
    <?php if ($this->data['mediaView'] === "library") { Armadillo_Media::getSummary(); }
          if ($this->data['mediaView'] === "uploadForm" ): ?>
    <form method="post" action="./">
        <div id="mediaUpload">
            <?php echo Armadillo_Language::msg('ARM_HTML5_WARNING'); ?>
        </div>
        <input type="submit" value="<?php echo Armadillo_Language::msg('ARM_CONTINUE_TEXT'); ?>" id="confirmFileUpload" class="btn btn-success" />&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;<a href="./../" class="btn btn-danger cancel"><?php echo Armadillo_Language::msg('ARM_CANCEL_TEXT'); ?></a>
    </form>
    <?php endif; ?>
</div>