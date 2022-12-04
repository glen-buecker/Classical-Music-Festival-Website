<?php 
    
if ( $_SESSION['role'] === 'admin' and ( $armOldVersion < $armBuildVersion ) ) {
    if ($this->data['contentType'] === 'no_backup') {
        echo Armadillo_Data::updateArmadillo("force", $armOldVersion);
    } 
    else { 
        echo Armadillo_Data::updateArmadillo("default", $armOldVersion); 
    } 
}
else {
    if ( $_SESSION['role'] === 'admin' ) {
        echo '<p>' . Armadillo_Language::msg('ARM_UPDATE_NONE_TEXT') . '</p>';  
    }
    else {
        echo '<p>' .Armadillo_Language::msg('ARM_UPDATE_NOT_ALLOWED_TEXT') . '</p>';
    }
}