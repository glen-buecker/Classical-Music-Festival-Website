<?php

// Prevent access via command line interface
if (PHP_SAPI === 'cli') {
    exit('That file must not be run via the command line interface');
}

// Don't allow direct access to the boostrap
if (basename($_SERVER['REQUEST_URI']) == 'dropboxBackupSync.php') {
    exit('Direct access to this file is not allowed.');
}

require_once 'autoload.php';

use Kunnu\Dropbox\Dropbox;
use Kunnu\Dropbox\DropboxApp;
use Kunnu\Dropbox\DropboxFile;

include dirname(dirname(__FILE__)) . '/config.php';
include dirname(dirname(__FILE__)) . '/connectDB.php';

$getTokenQuery = "SELECT dropbox_token, dropbox_sync_auth FROM armadillo_options LIMIT 1";
$getTokenResult = $dbLink->query($getTokenQuery);
$savedToken = null;
$readyToSync = false;

if ($getTokenResult) {
    $savedToken = $getTokenResult->fetch_array();
}

$consumerKey = 'v0d4bfcpsuiq2iq';
$consumerSecret = 'iiziyhuw08hwm00';

// Check if we have a stored token and instantiate the connection object with it
if ($savedToken['dropbox_sync_auth'] == TRUE) {
    $dropboxToken = $savedToken['dropbox_token'] ? unserialize($savedToken['dropbox_token']) : null;

    $app = new DropboxApp($consumerKey, $consumerSecret, $dropboxToken['token']);

    //Configure Dropbox service
    $dropbox = new Dropbox($app);

    $readyToSync = true;

} else {
    // Check whether to use HTTPS and set the callback URL
    // $isSecure = false;
    // if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
    //     $isSecure = true;
    // }
    // elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
    //     $isSecure = true;
    // }
    // $protocol = $isSecure ? 'https' : 'http';
    // $callbackUrl = $protocol . '://' . $_SERVER['HTTP_HOST'] . $_SERVER['REQUEST_URI'];

    // Don't have the access token yet, so instantiate without it
    $app = new DropboxApp($consumerKey, $consumerSecret);

    //Configure Dropbox service
    $dropbox = new Dropbox($app);

    //DropboxAuthHelper
    $authHelper = $dropbox->getAuthHelper();

    if (isset($_GET['dropboxAuthCode'])) {    
        //Get the code and state so we can retrieve the access token
        $code = $dbLink->real_escape_string($_GET['dropboxAuthCode']);

        try {
            //Fetch the AccessToken
            $accessToken = $authHelper->getAccessToken($_GET['dropboxAuthCode']);

            //check that getting token was successful
            if ($accessToken->getToken()) {
                
                //Store access token in database
                $token = serialize(array(
                    'token' => $dbLink->real_escape_string($accessToken->getToken()),
                    'consumerKey' => $consumerKey,
                    'consumerSecret' => $consumerSecret,
                ));

                $saveTokenQuery = "UPDATE armadillo_options SET dropbox_token='$token', dropbox_sync_auth=TRUE";
                if ( $dbLink->query($saveTokenQuery) === FALSE ) {
                    // TODO: display an error message here
                }
                else {
                    header('Location: ' . $_SERVER['PHP_SELF']);
                    die;
                }

            } else {
                // error out, inform user that authencation failed for some reason
            }
        } catch (Exception $e) {
            $errors = json_decode($e->getMessage());
            echo "<p>An error occurred attempting to authenticate the code provided. The error message returned by Dropbox is as follows:</p><p style='padding-top: 10px;'><span style='background-color: #efefef;color: red;padding: 10px;border-radius: 3px;'>" . $errors->{'error_description'} . "</span></p>";
        }

    } else {
        //Fetch the Authorization/Login URL
        $authUrl = $authHelper->getAuthUrl();

        //Have users click on this authUrl to give Armadillo access to their Dropbox
        echo "<p><a class='btn btn-info' href='" . $authUrl . "' target='_blank'>Log in to Dropbox to authorize Armadillo</a></p>";

        //Enter authorization code from Dropbox
        echo "<hr><p>Already have your authorization code from Dropbox? Paste it below and click the save button</p>"
            . "<div class='form-group'><form action='.'><input type='text' class='form-control' name='dropboxAuthCode'>"
            . "<p><br><button type='submit' class='btn btn-success'>Save Authorization Code</button></p></form></div>";
    }
}


// Find backup folder and get a list of backups made so far
$backupFolder = dirname(dirname(dirname(__FILE__))) . '/backup/';
if ( file_exists($backupFolder) && $readyToSync ) {
    // Check that backup folders have been created
    $listOfBackups = array_diff(scandir($backupFolder, 1), array('..', '.'));
    if (is_array($listOfBackups) and !empty($listOfBackups)) {
        // Check to make sure the latest backup folder isn't empty
        rsort($listOfBackups);
        $dbBackupFile = array_diff(scandir($backupFolder . '/' . $listOfBackups[0], 1), array('..', '.'));
        if ( !empty($dbBackupFile) ) {
            // Upload the latest backup file
            rsort($dbBackupFile);

            try {
                $dropboxFile = new DropboxFile($backupFolder . $listOfBackups[0] . '/' . $dbBackupFile[0]);

                if ($dropbox->upload($dropboxFile, '/' . $dbBackupFile[0], ['autorename' => true])) {
                    echo '<p>' . Armadillo_Language::msg('ARM_DROPBOX_SYNC_COMPLETE') . '</p>';
                } else {
                    $armadillo = Slim::getInstance();
                    throw new Exception($armadillo->response()->status());
                }

            } catch (Exception $status) {
                $status = preg_replace('/[^0-9]/', '', $status->getMessage());
                // Let the user know what happened
                switch ($status) {
                    case '401':
                        $resetTokenQuery = "UPDATE armadillo_options SET dropbox_sync_auth=FALSE";
                        if ( $dbLink->query($resetTokenQuery) === FALSE ) { }
                        unset($_SESSION['dropbox_api']);
                        echo Armadillo_Language::msg('ARM_DROPBOX_SYNC_EXPIRED_TOKEN');
                        break;
                    case '503':
                        echo Armadillo_Language::msg('ARM_DROPBOX_SYNC_TOO_MANY_REQUESTS');
                        break;
                    case '507':
                        echo Armadillo_Language::msg('ARM_DROPBOX_SYNC_USER_OVER_QUOTA');
                        break;
                    default:
                        echo Armadillo_Language::msg('ARM_DROPBOX_SYNC_UNKNOWN_RESPONSE');
                        break;
                }
            }
        } else {
            //notify user there's no backup file to upload to dropbox
        }
    }
}

