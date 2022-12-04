<?php

class Armadillo_Media
{
    public $filename;
    public $fileExt;
    public $fileType;

    public function getFilename()
    {
        //return $this->filename;
    }

    public function setFilename($filename)
    {
        //$this->filename = $filename;
    }

    public static function getFileExt($filename)
    {
        return pathinfo($filename, PATHINFO_EXTENSION);
    }

    public static function getFileType($fileExt)
    {
        $fileExt = strtolower($fileExt);
        $fileType = '';
        switch ($fileExt) {
            case 'png': case 'jpg': case 'jpeg': case 'tiff': case 'tif': case 'gif': case 'bmp':
                $fileType = "Image";
                break;
            case 'avi': case 'mp4': case 'm4v': case 'ogg': case 'ogv': case 'webm': case 'wmv':
                $fileType = "Video";
                break;
            case 'mp3': case 'mid': case 'midi': case 'oga': case 'wma': case 'aac':
                $fileType = "Audio";
                break;
            case 'psd':
                $fileType = "Photoshop";
                break;
            case 'pdf':
                $fileType = "PDF";
                break;
            case 'zip': case 'tar': case 'gz':
                $fileType = "Compressed Archive";
                break;
            case 'html': case 'htm': case 'xhtml': case 'xhtm': case 'shtml': case 'shtm':
                $fileType = "Web Document";
                break;
            case 'php':
                $fileType = "PHP";
                break;
            case 'xml':
                $fileType = "XML";
                break;
            case 'txt':
                $fileType = "Plain Text";
                break;
            case 'csv':
                $fileType = "CSV";
                break;
            case 'js':
                $fileType = "Javascript";
                break;
            case 'rb':
                $fileType = "Ruby";
                break;
            case 'py':
                $fileType = "Python";
                break;
            case 'pl':
                $fileType = "Perl";
                break;
            case 'doc': case 'docx': case 'rtf':
                $fileType = "Document";
                break;
            default:
                $fileType = "Not Recognized";
                break;
        }

        return $fileType;
    }

    public static function getSummary($draggable='', $armadilloURL='')
    {
        if ( !class_exists('Armadillo_Language') ) { include dirname(__FILE__) . "/Armadillo_Language.php"; }

        $mediaFolder = dirname(dirname(dirname(__FILE__))) . '/media';
        // Media Library HTML Container
        $mediaLibrary = "<div id='mediaLibrary'>";
        if (file_exists($mediaFolder)) {

            $handle = opendir($mediaFolder);
            $listMedia = array();

            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != "..") {
                    $listMedia[] = $file;
                }
            }
            closedir($handle);

            $armadilloURL = $armadilloURL != '' ? $armadilloURL : $_SESSION['armURL'];

            if ( count($listMedia) == 0 or ( count($listMedia) == 2 and in_array('images.json', $listMedia) and in_array('files.json', $listMedia) ) ) {
                echo "<p>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_EMPTY') . "<a href='" . $armadilloURL . "/index.php/media/upload/' class='toggleMediaView'>" . Armadillo_Language::msg('ARM_DASHBOARD_UPLOAD_MEDIA_TEXT') . "</a>.</p>";
            } else {
                sort($listMedia);

                // Start table markup
                $mediaLibrary .= "<table id='summaryList' class='table table-striped table-bordered responsive nowrap' cellspacing='0' width='100%'><thead><tr><th>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_FILE_NAME_LABEL') ."</th><th>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_FILE_TYPE_LABEL') . "</th><th class='disabled'>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_ACTIONS_LABEL') . "</th></tr></thead>"
                                    . "<tbody>";
                                    //. "<tfoot><tr><th>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_FILE_NAME_LABEL') ."</th><th>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_FILE_TYPE_LABEL') . "</th><th>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_ACTIONS_LABEL') . "</th></tr></tfoot>"
                                    

                $rowNumber = 0;

                foreach ($listMedia as $file) {

                    $rowNumber++;
                    $rowClass = ( $rowNumber % 2 ) ? 'oddRow' : 'evenRow';

                    $fileURL = $armadilloURL . '/media/' . $file;
                    $fileExt = Armadillo_Media::getFileExt($file);
                    $fileType = Armadillo_Media::getFileType($fileExt);

                    if ( $fileExt == 'json' ) {
                        continue;
                    } 

                    $deletePreviewableFile = ($_SESSION['role'] === 'editor' || $_SESSION['role'] === 'admin') ? "<span class=''><a href='./delete/" . $file . "' class='btn btn-danger btn-sm' title='" . Armadillo_Language::msg('ARM_DELETE_TEXT') . "'><i class='fa fa-times fa-lg'></i></a></span>" : '';
                    $deleteFile = ($_SESSION['role'] === 'editor' || $_SESSION['role'] === 'admin') ? "<span class=''><a href='./delete/" . $file . "' class='btn btn-danger btn-sm' title='" . Armadillo_Language::msg('ARM_DELETE_TEXT') . "'><i class='fa fa-times fa-lg'></i></a></span>" : '';
                    //$previewMediaFile = "<div class='fileActions'><a href='" . $fileURL . "' class='previewMediaFile' rel='mediaFile' title='" . $fileURL . "'>" . Armadillo_Language::msg('ARM_PREVIEW_TEXT') . "</a>" . $deletePreviewableFile . "</div>";
                    $previewMediaFile = "<td><a href='" . $fileURL . "' class='btn btn-purple btn-sm previewMediaFile fancybox.ajax' rel='mediaFile' title='" . $fileURL . "'><i class='fa fa-eye fa-lg'></i></a>" . $deletePreviewableFile . "</td>";

                    $dragDiv = ($draggable == 'draggable' and $fileType == 'Image') ? "<div id='$fileURL' class='draggableMediaItem'></div>" : '';

                    //Add file name to Media Library
                    $mediaLibrary .= "<tr><td>" . $dragDiv . $file . "<span class='linkToFile'> (<a href='" . $fileURL . "'>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_LINK_TO_FILE_TEXT') . "</a>)</span></td>";

                    if ($fileType === "Image") {
                        $mediaLibrary .= "<td>Image</td>";
                        //Display Preview link for images
                        $mediaLibrary .= "<td><a href='" . $fileURL . "' class='btn btn-purple btn-sm previewMediaImage' rel='mediaFile' title='" . $fileURL . "'><i class='fa fa-eye fa-lg'></i></a>" . $deletePreviewableFile . "</td>";
                    } elseif ($fileType === "Video") {
                        $mediaLibrary .= "<td>Video</td>";
                        //No Preview link for video
                        $mediaLibrary .= "<td>" . $deleteFile . "</td>";
                    } elseif ($fileType === "Audio") {
                        $mediaLibrary .= "<td>Audio</td>";
                        //No Preview link for audio
                        $mediaLibrary .= "<td>" . $deleteFile . "</td>";
                    } elseif ($fileType === "PDF") {
                        $mediaLibrary .= "<td>PDF</td>";
                        //No Preview link for pdf
                        $mediaLibrary .= "<td>" . $deleteFile . "</td>";
                    } elseif ($fileType === "Compressed Archive") {
                        $mediaLibrary .= "<td>Compressed Archive</td>";
                        //No Preview link for compressed files
                        $mediaLibrary .= "<td>" . $deleteFile . "</td>";
                    } elseif ($fileType === "Web Document") {
                        $mediaLibrary .= "<td>Web Document</td>";
                        //Display preview of web file
                        $mediaLibrary .= $previewMediaFile;
                    } elseif ($fileType === "XML") {
                        $mediaLibrary .= "<td>XML</td>";
                        //Display preview of XML file
                        $mediaLibrary .= $previewMediaFile;
                    } elseif ($fileType === "Plain Text") {
                        $mediaLibrary .= "<td>Plain Text</td>";
                        //Display preview of text file
                        $mediaLibrary .= $previewMediaFile;
                    } elseif ($fileType === "Javascript") {
                        $mediaLibrary .= "<td>Javascript</td>";
                        //Display preview of javascript file
                        $mediaLibrary .= $previewMediaFile;
                    } elseif ($fileType === "Ruby") {
                        $mediaLibrary .= "<td>Ruby</td>";
                        //No Preview link for server side scripting languages
                        $mediaLibrary .= "<td>" . $deleteFile . "</td>";
                    } elseif ($fileType === "Python") {
                        $mediaLibrary .= "<td>Python</td>";
                        //No Preview link for server side scripting languages
                        $mediaLibrary .= "<td>" . $deleteFile . "</td>";
                    } elseif ($fileType === "Perl") {
                        $mediaLibrary .= "<td>Perl</td>";
                        //No Preview link for server side scripting languages
                        $mediaLibrary .= "<td>" . $deleteFile . "</td>";
                    } elseif ($fileType === "Document") {
                        $mediaLibrary .= "<td>Document</td>";
                        //No Preview link for rich text documents
                        $mediaLibrary .= "<td>" . $deleteFile . "</td>";
                    } else {
                        $mediaLibrary .= "<td>Not Sure</td>";
                        //No Preview link for unknown files
                        $mediaLibrary .= "<td>" . $deleteFile . "</td>";
                    }

                    $mediaLibrary .= "</tr>";
                }

                // Close table markup
                $mediaLibrary .= "</tbody></table>";

            }

            
        } else { echo "<p>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_EMPTY') . "<a href='" . $armadilloURL . "/media/upload/' class='toggleMediaView'>" . Armadillo_Language::msg('ARM_DASHBOARD_UPLOAD_MEDIA_TEXT') . "</a>.</p>"; }
        //Close Media Library HTML Container
        $mediaLibrary .= "</div>";
        $mediaLibrary .= "<div class='clearer'></div>";
        echo $mediaLibrary;
    }

    public static function getSingleItem($filename, $armadilloURL='')
    {
        // Media Library HTML Container
        $mediaLibrary = "<div id='mediaLibrary'>";
        // Start table markup
        $mediaLibrary .= "<table id='summaryList' class='table table-striped table-bordered responsive nowrap' cellspacing='0' width='100%'><thead><tr><th>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_FILE_NAME_LABEL') ."</th><th>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_FILE_TYPE_LABEL') . "</th><th>" . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_ACTIONS_LABEL') . "</th></tr></thead>"
                            . "<tbody>";

        $armadilloURL = $armadilloURL != '' ? $armadilloURL : $_SESSION['armURL'];

        $fileURL = $armadilloURL . '/media/' . $filename;
        $fileExt = Armadillo_Media::getFileExt($filename);
        $fileType = Armadillo_Media::getFileType($fileExt);
        //Add file name to Media Library
        $mediaLibrary .= "<td>" . $filename . "</td>";
        $previewMediaFile = "<td><a href='" . $fileURL . "' class='btn btn-purple btn-sm previewMediaFile fancybox.ajax' rel='mediaFile' title='" . $fileURL . "'><i class='fa fa-eye fa-lg'></i></a></td>";

        if ($fileType === "Image") {
            $mediaLibrary .= "<td>Image</td>";
            //Display Preview link for images
            $mediaLibrary .= "<td><a href='" . $fileURL . "' class='btn btn-purple btn-sm previewMediaImage' rel='mediaFile' title='" . $fileURL . "'><i class='fa fa-eye fa-lg'></i></a></td>";
        } elseif ($fileType === "Video") {
            $mediaLibrary .= "<td>Video</td>";
            //No Preview link for video
            $mediaLibrary .= "<td>&nbsp;</td>";
        } elseif ($fileType === "Audio") {
            $mediaLibrary .= "<td>Audio</td>";
            //No Preview link for audio
            $mediaLibrary .= "<td>&nbsp;</td>";
        } elseif ($fileType === "PDF") {
            $mediaLibrary .= "<td>PDF</td>";
            //No Preview link for pdf
            $mediaLibrary .= "<td>&nbsp;</td>";
        } elseif ($fileType === "Compressed Archive") {
            $mediaLibrary .= "<td>Compressed Archive</td>";
            //No Preview link for compressed files
            $mediaLibrary .= "<td>&nbsp;</td>";
        } elseif ($fileType === "Web Document") {
            $mediaLibrary .= "<td>Web Document</td>";
            //Display preview of web file
            $mediaLibrary .= $previewMediaFile;
        } elseif ($fileType === "XML") {
            $mediaLibrary .= "<td>XML</td>";
            //Display preview of XML file
            $mediaLibrary .= $previewMediaFile;
        } elseif ($fileType === "Plain Text") {
            $mediaLibrary .= "<td>Plain Text</td>";
            //Display preview of text file
            $mediaLibrary .= $previewMediaFile;
        } elseif ($fileType === "Javascript") {
            $mediaLibrary .= "<td>Javascript</td>";
            //Display preview of javascript file
            $mediaLibrary .= $previewMediaFile;
        } elseif ($fileType === "Ruby") {
            $mediaLibrary .= "<td>Ruby</td>";
            //No Preview link for server side scripting languages
            $mediaLibrary .= "<td>&nbsp;</td>";
        } elseif ($fileType === "Python") {
            $mediaLibrary .= "<td>Python</td>";
            //No Preview link for server side scripting languages
            $mediaLibrary .= "<td>&nbsp;</td>";
        } elseif ($fileType === "Perl") {
            $mediaLibrary .= "<td>Perl</td>";
            //No Preview link for server side scripting languages
            $mediaLibrary .= "<td>&nbsp;</td>";
        } elseif ($fileType === "Document") {
            $mediaLibrary .= "<td>Document</td>";
            //No Preview link for rich text documents
            $mediaLibrary .= "<td>&nbsp;</td>";
        } else {
            $mediaLibrary .= "<td>Not Sure</td>";
            //No Preview link for unknown files
            $mediaLibrary .= "<td>&nbsp;</td>";
        }

        // Close table markup
        $mediaLibrary .= "</tr></tbody></table>";
        // Close Media Library HTML Container
        $mediaLibrary .= "</div>";
        echo $mediaLibrary;

    }

    /**
     * Return human readable sizes
     *
     * @author      Aidan Lister <aidan@php.net>
     * @version     1.3.0
     * @link        http://aidanlister.com/2004/04/human-readable-file-sizes/
     * @param       int     $size        size in bytes
     * @param       string  $max         maximum unit
     * @param       string  $system      'si' for SI, 'bi' for binary prefixes
     * @param       string  $retstring   return string format
     */
    public static function size_readable($size, $max = null, $system = 'si', $retstring = '%01.2f %s')
    {
        // Pick units
        $systems['si']['suffix'] = array('B', 'K', 'MB', 'GB', 'TB', 'PB');
        $systems['si']['size']   = 1000;
        $systems['bi']['suffix'] = array('B', 'KiB', 'MiB', 'GiB', 'TiB', 'PiB');
        $systems['bi']['size']   = 1024;
        $sys = isset($systems[$system]) ? $systems[$system] : $systems['si'];
     
        // Max unit to display
        $depth = count($sys['suffix']) - 1;
        if ($max && false !== $d = array_search($max, $sys['suffix'])) {
            $depth = $d;
        }
     
        // Loop
        $i = 0;
        while ($size >= $sys['size'] && $i < $depth) {
            $size /= $sys['size'];
            $i++;
        }
     
        return sprintf($retstring, $size, $sys['suffix'][$i]);
    }

    public static function createRedactorJsonFile($armadilloURL='') {
        $mediaFolder = dirname(dirname(dirname(__FILE__))) . '/media';

        if (file_exists($mediaFolder)) {

            $handle = opendir($mediaFolder);
            $listMedia = array();
            $images = array();
            $files = array();

            while (false !== ($file = readdir($handle))) {
                if ($file != "." && $file != ".." && $file != 'images.json' && $file != 'files.json' ) {
                    $listMedia[] = $file;
                }
            }
            closedir($handle);

            $armadilloURL = $armadilloURL != '' ? $armadilloURL : $_SESSION['armURL'];

            if ( count($listMedia) != 0) {
                sort($listMedia);
            }

            foreach ($listMedia as $file) {

                $fileURL = $armadilloURL . '/media/' . $file;
                $fileExt = Armadillo_Media::getFileExt($file);
                $fileType = Armadillo_Media::getFileType($fileExt);

                //Adjust HTML based upon what type of file it is
                if ($fileType === "Image") {
                    $images[] = array( 'thumb' => $fileURL , 'url' => $fileURL, 'title' => $file, 'id' => $file );
                }

                $filesize = self::size_readable( filesize( dirname(dirname(dirname(__FILE__))) . DIRECTORY_SEPARATOR . 'media' . DIRECTORY_SEPARATOR . $file ) );

                $files[] = array( 'title' => $file, 'name' => $file, 'url' => $fileURL, 'id' => $file, 'size' => $filesize );
                
            }

            //encode array to json format and save to file
            $imagesPath = $mediaFolder . '/images.json';
            $imagesFileContent = stripslashes(json_encode($images));
            if ( $imagesFile = fopen($imagesPath, 'w') ) {
                if ( fwrite($imagesFile, $imagesFileContent) ) {
                    fclose($imagesFile);
                }
            }

            $filesPath = $mediaFolder . '/files.json';
            $filesFileContent = stripslashes(json_encode($files));
            if ( $filesFile = fopen($filesPath, 'w') ) {
                if ( fwrite($filesFile, $filesFileContent) ) {
                    fclose($filesFile);
                }
            }
        }
    }

    public static function checkMediaFolder()
    {
        $armadillo = Slim::getInstance();
        $mediaFolder = dirname(dirname(dirname(__FILE__))) . '/media';
        if ( !file_exists($mediaFolder) ) {
            if ( class_exists('ZipArchive') ) {
                $zip = new ZipArchive();
                $media = $zip->open(dirname(dirname(dirname(__FILE__))) . '/media.zip');
                if ($media === true) {
                    $zip->extractTo(dirname(dirname(dirname(__FILE__))));
                    $zip->close();
                } else {
                    $armadillo->flashNow(
                        'warning', 
                        Armadillo_Language::msg('ARM_MEDIA_FOLDER_NOT_WRITABLE') . '<a href="./fixMediaFolder/" class="fixMediaFolderPermissions armadilloAjax btn btn-default">' . Armadillo_Language::msg('ARM_MEDIA_FOLDER_NOT_WRITABLE_CORRECT_NOW') . '</a>'
                    );
                }
            } else {
                require_once(dirname(__FILE__) . '/pclzip.lib.php');
                $media = new PclZip(dirname(dirname(dirname(__FILE__))) . '/media.zip');
                $media->extract(PCLZIP_OPT_PATH, dirname(dirname(dirname(__FILE__))));
            }
        }
        if ( !is_writable($mediaFolder) ) { 
            if ( is_object($armadillo) ) {
                $armadillo->flashNow(
                    'warning', 
                    Armadillo_Language::msg('ARM_MEDIA_FOLDER_NOT_WRITABLE') . '<a href="./fixMediaFolder/" class="fixMediaFolderPermissions armadilloAjax btn btn-default">' . Armadillo_Language::msg('ARM_MEDIA_FOLDER_NOT_WRITABLE_CORRECT_NOW') . '</a>'
                );
            } else {
                echo '<p>' . Armadillo_Language::msg('ARM_MEDIA_FOLDER_NOT_WRITABLE') . '<a href="./fixMediaFolder/" class="fixMediaFolderPermissions armadilloAjax btn btn-default">' . Armadillo_Language::msg('ARM_MEDIA_FOLDER_NOT_WRITABLE_CORRECT_NOW') . '</a></p>';
            }
        }
    }

    public static function fixMediaFolder()
    {
        $mediaFolder = dirname(dirname(dirname(__FILE__))) . '/media';
        if ( chmod($mediaFolder, 0755) ) { 
            return TRUE; 
        } else { 
            return FALSE; 
        }
    }

    public static function deleteMedia($filename)
    {
        $armadillo = Slim::getInstance();
        $filePath = './media/' . $filename;
        if ( @unlink($filePath) ) { 
            $armadillo->flashNow(
                'notification', 
                Armadillo_Language::msg('ARM_MEDIA_DELETE_SUCCESSFUL') . $filename
            ); 
        } else { 
            $armadillo->flashNow(
                'warning', 
                Armadillo_Language::msg('ARM_MEDIA_DELETE_FAILED') . $filename
            ); 
        }
    }
}
