<div class="bg-danger">
    <p>
        <?php echo Armadillo_Language::msg('ARM_DELETE_MEDIA_MESSAGE'); ?>
    </p>
    <form id="confirmDeletion" action="../" method="post">
        <input class="form-control" type="hidden" name="_METHOD" value="DELETE" />
        <input class="form-control" type="hidden" name="filename" value="<?php htmlout($this->data['mediaFilename']); ?>" />
        <input class="btn btn-success" type="submit" name="cancel" value="<?php echo Armadillo_Language::msg('ARM_CANCEL_TEXT'); ?>" />
        <input class="btn btn-danger" type="submit" name="delete" value="<?php echo Armadillo_Language::msg('ARM_DELETE_TEXT'); ?>" />
    </form>
</div>
<div class="selectedItem">
    <?php Armadillo_Media::getSingleItem($this->data['mediaFilename'], $_SESSION['armURL']); ?>
</div>
