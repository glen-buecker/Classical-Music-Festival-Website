<?php

echo '<h1>' . Armadillo_Language::msg('ARM_BACKUP_DATABASE_TEXT') . '</h1>';

if ($_SESSION['role'] === 'admin') {
    if (Armadillo_Data::backupDatabase()) { 
        echo Armadillo_Language::msg('ARM_BACKUP_DATABASE_SUCCESSFUL_TEXT'); 
    }
    else { 
        echo Armadillo_Language::msg('ARM_BACKUP_DATABASE_FAILED_TEXT'); 
    }
}
else { 
    echo Armadillo_Language::msg('ARM_BACKUP_DATABASE_NOT_ALLOWED_TEXT'); 
}