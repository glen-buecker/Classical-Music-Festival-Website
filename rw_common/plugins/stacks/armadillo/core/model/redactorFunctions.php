<?php
require 'Armadillo_Media.php';
 
$types = ['image/png',
'image/jpg',
'image/gif',
'image/jpeg',
'image/pjpeg',
'image/jif',
'image/jfif',
'image/bmp',
'image/svg',
'image/tif',
'image/tiff',
'image/svg+xml',
'image/vnd.adobe.photoshop',
'image/x-icon',
'text/html',
'application/xhtml+xml',
'text/css',
'text/csv',
'text/plain',
'text/tab-separated-values',
'text/richtext',
'application/rtf',
'application/rss+xml',
'application/rss+xml',
'application/pdf',
'application/epub+zip',
'text/calendar',
'application/x-font-ttf',
'application/vnd.ms-fontobject',
'application/vnd.ms-powerpoint',
'application/vnd.openxmlformats-officedocument.presentationml.presentation',
'application/vnd.ms-excel',
'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet',
'application/msword',
'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
'application/zip',
'application/x-rar-compressed',
'audio/x-aiff',
'audio/x-aac',
'audio/mpeg',
'audio/midi',
'audio/mp4',
'audio/ogg',
'audio/webm',
'video/quicktime',
'video/x-m4v',
'video/h264',
'video/mp4',
'video/ogg',
'video/mpeg',
'video/x-msvideo',
'video/x-ms-wmv',
'video/webm'
];

// files storage folder
$targetDir = dirname(dirname(dirname(__FILE__))) . '/media/';

$files = [];

if (isset($_FILES['file'])) {
    // var_dump($_FILES['file']);
    $fileCount = 0;
    foreach ($_FILES['file']['name'] as $key => $name) {
        ++$fileCount;
        $type = strtolower($_FILES['file']['type'][$key]);
        if (in_array($type, $types))
        {
            // Clean the fileName for security reasons
            $fileName = preg_replace('/[^\w\._]+/', '', $_FILES['file']['name'][$key]);
        
            $ext = strrpos($fileName, '.');
            $fileName_a = substr($fileName, 0, $ext);
            $fileName_b = substr($fileName, $ext);
        
            // Make sure the fileName is unique but only if chunking is disabled
            if ( file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName) ) {
                $count = 1;
                while (file_exists($targetDir . DIRECTORY_SEPARATOR . $fileName_a . '_' . $count . $fileName_b)) {
                    $count++;
                }
        
                $fileName = $fileName_a . '_' . $count . $fileName_b;
            }

            $file = $targetDir . $fileName;
            
            if (move_uploaded_file($_FILES['file']['tmp_name'][$key], $file)) {
                // displaying file
                $files['file-'.$fileName_a] = array(
                    'url' => $_REQUEST['armURL'] . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $fileName,
                    'id' => $fileName_a
                );
            } else {
                $files['file-'.$fileName_a] = array('error' => 'Unable to save that file to the media folder. Please contact support and give them this error code: ' . $_FILES['file']['error']);
            }
        }
        else {
            $files['file-'.$fileCount] = array(
                'error' => 'Uploading that type of file is not supported.'
            );
            echo stripslashes(json_encode($array));
        }
    }
    echo stripslashes(json_encode($files));
    Armadillo_Media::createRedactorJsonFile($_REQUEST['armURL']);
}

?>