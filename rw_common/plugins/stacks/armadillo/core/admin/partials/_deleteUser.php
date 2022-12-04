<?php 

if ( $_SESSION['role'] === 'admin' ): 

?>
<div class="bg-danger">
    <?php echo Armadillo_Language::msg('ARM_DELETE_USER_MESSAGE'); ?>
    <form id="confirmDeletion" action="./../../" method="post">
        <input class="form-control" type="hidden" name="_METHOD" value="DELETE" />
        <input class="form-control" type="hidden" name="id" value="<?php htmlout($this->data['userID']); ?>" />
        <input class="btn btn-success" type="submit" name="cancel" value="<?php echo Armadillo_Language::msg('ARM_CANCEL_TEXT'); ?>" />
        <input class="btn btn-danger" type="submit" name="delete" value="<?php echo Armadillo_Language::msg('ARM_DELETE_TEXT'); ?>" />
    </form>
</div>
<div class="selectedItem">
    <?php
        $selectedUser = Armadillo_User::getUser($this->data['userID']);
        echo "<p><strong>" . Armadillo_Language::msg('ARM_USER_SUMMARY_NAME_LABEL') . "</strong> " . $selectedUser['name'] . "</p>";
        echo "<p><strong>" . Armadillo_Language::msg('ARM_USER_SUMMARY_EMAIL_LABEL') . "</strong> " . $selectedUser['email'] . "</p>";
        echo "<p><strong>" . Armadillo_Language::msg('ARM_USER_SUMMARY_LOGIN_ID_LABEL') . "</strong> " . $selectedUser['username'] . "</p>";
        echo "<p><strong>" . Armadillo_Language::msg('ARM_USER_SUMMARY_ROLE_LABEL') . "</strong> " . $selectedUser['role'] . "</p>";
    ?>
</div>
<?php

endif;

?>