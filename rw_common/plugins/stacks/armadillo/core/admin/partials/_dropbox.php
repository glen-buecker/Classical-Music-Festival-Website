<?php

echo '<h1>' . Armadillo_Language::msg('ARM_DROPBOX_SYNC_BACKUP_TEXT') . '</h1>';

if ($_SESSION['role'] === 'admin') {
    require_once dirname(dirname(__FILE__)) . "/model/dropboxBackupSync.php";
}
else { 
    echo "This feature is still under development."; 
}