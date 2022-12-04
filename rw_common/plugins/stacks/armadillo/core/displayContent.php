<?php
// Check if the config file exists and load it
if ( file_exists( dirname(__FILE__) . '/config.php' ) ) {
    require_once dirname(__FILE__) . '/config.php';
    require_once dirname(__FILE__) . '/connectDB.php';
}

// Pull in the initial Armadillo classes needed
require_once dirname(__FILE__) . '/model/Armadillo_Content.php';
require_once dirname(__FILE__) . '/model/Armadillo_Data.php';
require_once dirname(__FILE__) . '/model/Armadillo_Language.php';
require_once dirname(__FILE__) . '/model/Parsedown.php';

if ( file_exists( dirname(__FILE__) . '/config.php' ) ) {
    $armadilloOptions = isset($armadilloOptions) ? $armadilloOptions : getArmadilloOptions($dbLink);
}

// Set variable to initial false value to check if the main
// Armadillo resource files have been loaded
$armadilloResourcesLoaded = FALSE;

// Install localizations
// Armadillo_Language::installLanguages();

// Check if Armadillo is setup and ready to go
$setupIsComplete = FALSE;

if ( isset($dbHostname, $dbName, $dbUsername, $dbPassword) ) {
    // Set timezone and language so that blog posts with a date set in the future
    // are published correctly based on the local timezone
    $tlQuery = "SELECT timezone, site_language FROM armadillo_options";
    $tlResult = $dbLink->query($tlQuery);
    if ($tlResult) {
        // If the above db query succeeds, setup has most likely been complete
        // Need to find a better way to do this, but need to get v1.7.0 out the door
        $setupIsComplete = TRUE;
        $tlRow = $tlResult->fetch_array();
        date_default_timezone_set($tlRow['timezone']);
        switch ($tlRow['site_language']) {
            case 'bg':
                setlocale(LC_TIME, "bg.UTF-8", "bg_BG.UTF-8", "Bulgarian", "bulgarian");
                break;
            case 'cs':
                setlocale(LC_TIME, "cs.UTF-8", "cs_CS.UTF-8", "cs_CZ.UTF-8", "Czech", "czech");
                break;
            case 'de':
                setlocale(LC_TIME, "de.UTF-8", "de_DE.UTF-8", "German", "german");
                break;
            case 'en':
                setlocale(LC_TIME, "en.UTF-8", "en_EN.UTF-8", "English", "english");
                break;
            case 'es':
                setlocale(LC_TIME, "es.UTF-8", "es_ES.UTF-8", "Spanish", "spanish");
                break;
            case 'fi':
                setlocale(LC_TIME, "fi.UTF-8", "fi_FI.UTF-8", "Finnish", "french");
                break;
            case 'fr':
                setlocale(LC_TIME, "fr.UTF-8", "fr_FR.UTF-8", "French", "french");
                break;
            case 'it':
                setlocale(LC_TIME, "it.UTF-8", "it_IT.UTF-8", "Italian", "italian");
                break;
            case 'ja':
                setlocale(LC_TIME, "ja.UTF-8", "ja_JA.UTF-8", "Japanese", "japanese");
                break;
            case 'nl':
                setlocale(LC_TIME, "nl.UTF-8", "nl_NL.UTF-8", "Dutch", "dutch");
                break;
            case 'pl':
                setlocale(LC_TIME, "pl.UTF-8", "pl_PL.UTF-8", "Polish", "polish");
                break;
            case 'sv':
                setlocale(LC_TIME, "sv.UTF-8", "sv_SV.UTF-8", "sv_SE.UTF-8", "Swedish", "swedish");
                break;
            default:
                setlocale(LC_TIME, "en.UTF-8", "en_EN.UTF-8", "English", "english");
                break;
        }
        
    } else {
        date_default_timezone_set('America/New_York');
        setlocale (LC_TIME, "en", "en_EN", "English");
    }
} else {
    // echo "<p>" . Armadillo_Language::public_msg($armadilloOptions, 'ARM_SETUP_REQUIRED_MESSAGE1')
    //     . "</p><p><a href='$assetPath/armadillo/'>"
    //     . Armadillo_Language::public_msg($armadilloOptions, 'ARM_SETUP_REQUIRED_MESSAGE2') . "</a></p>";
}

// Create the .htaccess file based upon hosting company selected by user
// function htaccessConfirmation( $htaccessDirectory, $hostedCompany )
// {
//     if ( file_exists( dirname( dirname(__FILE__) ) . '/.htaccess' ) ) {
//         return;
//     } else {
//         $rewriteRule = '';
//         switch ($hostedCompany) {
//             case 'crazydomains':
//             case 'dreamhost':
//             case 'dvhost':
//             case 'hoststar_at':
//             case 'littleoak':
//             case 'machighway':
//             case 'mediatemple':
//             case 'name_com':
//             case 'nimblehost':
//             case 'one_com':
//             case 'servint':
//             case 'site5':
//             case 'uk2_net':
//             case 'webfaction':
//                 $rewriteRule = '# ';
//                 break;
//             default:
//                 $rewriteRule = '';
//                 break;
//         }

//         $htaccessContents = "RewriteEngine On" . PHP_EOL . PHP_EOL;

//         $htaccessContents .= "# Some hosts may not need the `RewriteBase` line below." . PHP_EOL;
//         $htaccessContents .= "# To disable the `RewriteBase` rule, comment out (or delete) the line" . PHP_EOL;
//         $htaccessContents .= "# To comment out the line, simply type in a # sign before `RewriteBase` and save the file." . PHP_EOL . PHP_EOL;

//         $htaccessContents .= $rewriteRule . "RewriteBase " . $htaccessDirectory . PHP_EOL . PHP_EOL;

//         $htaccessContents .= "RewriteCond %{REQUEST_FILENAME} !-f" . PHP_EOL;
//         $htaccessContents .= "RewriteRule ^(.*)$ index.php [QSA,L]";

//         $path = dirname(dirname(__FILE__)) . '/.htaccess';

//         $htaccessFile = NULL;

//         if ( $htaccessFile = fopen($path, 'w') ) {
//             if ( fwrite($htaccessFile, $htaccessContents) === FALSE ) {
//                 echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_SETUP_HTACCESS_EDIT_FAILED');
//             } else {
//                 fclose($htaccessFile);
//             }
//         } else {
//             echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_SETUP_HTACCESS_CREATION_FAILED');
//         }
//     }
// }

//Retrieve the stylesheet version from database
function stylesheetVersion( $dbLink )
{
    $query = "SELECT stylesheet_version FROM armadillo_options";

    $result = $dbLink->query($query);

    $stylesheetVersion = '';

    if ($result) {
        $row = $result->fetch_array();

        $stylesheetVersion = $row['stylesheet_version'];
    }

    return $stylesheetVersion;
}

//Retrieve nav options from database
function getArmadilloOptions( $dbLink )
{
    $query = "SELECT * FROM armadillo_options LIMIT 1";

    $result = $dbLink->query($query);

    $armadilloOptions = array();

    if ($result) {
        $row = $result->fetch_array();

        foreach ($row as $option => $value) {
            $armadilloOptions["$option"] = $value;
        }
    }

    return $armadilloOptions;
}

function getBlogOptions( $dbLink, $blog_id )
{
    $query = "SELECT * FROM armadillo_post WHERE id='$blog_id'";

    $result = $dbLink->query($query);

    $armadilloOptions = array();

    if ($result and $dbLink->affected_rows > 0) {
        $row = $result->fetch_array();

        foreach ($row as $option => $value) {
            $blogOptions["$option"] = $value;
        }
    } else {
        echo Armadillo_Language::msg('ARM_CONTENT_SELECTED_ITEM_MISSING');
    }

    return $blogOptions;
}

//TODO: Create options for blog display, e.g., pagination, recent items only, all posts, etc.
if ( isset($_GET['action']) and $_GET['action'] === 'showMorePosts' ) {
    $blog_id = isset($_GET['blog_id']) ? $_GET['blog_id'] : '';
    $category = isset($_GET['category']) ? $_GET['category'] : '';
    $tag = isset($_GET['tag']) ? $_GET['tag'] : '';
    $author = isset($_GET['author']) ? $_GET['author'] : '';
    $archive = isset($_GET['archive']) ? $_GET['archive'] : '';
    //The displayPosts() function accepts an array of options, so populate that array with the relevant values
    $options = array('blog_id' => $blog_id, 'postsPageNumber' => $_GET['postsPageNumber'], 'category' => $category, 'tag' => $tag, 'author' => $author, 'archive' => $archive, 'armadilloOptions' => $armadilloOptions);
    echo displayPosts( $dbLink, $options );
}

//Send required data to import the Armadillo menu onto other RW pages
if ( isset($_GET['action']) and $_GET['action'] === 'importArmadilloMenu' ) {
    $armMenuInfo = array();
    $armMenuInfo['siteMainNav'] = $armadilloOptions['site_main_nav_container'];
    $armMenuInfo['position'] = $armadilloOptions['menu_display_option'];
    //Get URL of Armadillo page
    //$protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
    //$host     = $_SERVER['HTTP_HOST'];
    //$script   = dirname(dirname(dirname(dirname($_SERVER['SCRIPT_NAME']))));

    // Security check, make sure Armadillo URL is valid
    // 2015.03.23 Added check for common XSS patterns like: "'  '"  '>  ">
    if ( preg_match('/"\'/', $_GET['armadilloURL']) || preg_match('/\'"/', $_GET['armadilloURL']) || preg_match('/\'>/i', $_GET['armadilloURL']) || preg_match('/">/i', $_GET['armadilloURL']) ) {
        echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_URL_VALIDATION_ERROR');
    } else {
        // 2015.03.22 Jonathan Head - Removed ftp check as it's not valid for use in this situation
        // Regular Expression for URL validation
        //
        // Author: Diego Perini
        // Updated: 2010/12/05
        // License: MIT
        //
        // Copyright (c) 2010-2013 Diego Perini (http://www.iport.it)
        $valid_url = '_^(?:(?:https?)://)(?:\S+(?::\S*)?@)?(?:(?!(?:10|127)(?:\.\d{1,3}){3})(?!(?:169\.254|192\.168)(?:\.\d{1,3}){2})(?!172\.(?:1[6-9]|2\d|3[0-1])(?:\.\d{1,3}){2})(?:[1-9]\d?|1\d\d|2[01]\d|22[0-3])(?:\.(?:1?\d{1,2}|2[0-4]\d|25[0-5])){2}(?:\.(?:[1-9]\d?|1\d\d|2[0-4]\d|25[0-4]))|(?:(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)(?:\.(?:[a-z\x{00a1}-\x{ffff}0-9]-*)*[a-z\x{00a1}-\x{ffff}0-9]+)*(?:\.(?:[a-z\x{00a1}-\x{ffff}]{2,})))(?::\d{2,5})?(?:/\S*)?$_iuS';
        if ( preg_match($valid_url, $_GET['armadilloURL']) === 1 ) {
            $armUrl = $_GET['armadilloURL'];
            ob_start();
            displayMenu( $dbLink, '', '', 'TRUE', $armUrl );
            $menuHTML = ob_get_contents();
            ob_end_clean();
            $armMenuInfo['menuHTML'] = $menuHTML;
            echo json_encode($armMenuInfo);
        } else {
            echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_URL_VALIDATION_ERROR');
        }
    }
}

//Generates a menu based upon the pages currently created
function displayMenu( $dbLink, $pageFilename, $currentPage, $importMenu='', $armadilloURL='' )
{
    $query = "SELECT pageid, title FROM armadillo_nav INNER JOIN armadillo_post ON pageid = id WHERE type='page'";

    $result = $dbLink->query($query);

    if (!$result) { echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_DISPLAY_DB_ERROR') . $dbLink->error; }

    $menuItems = $dbLink->affected_rows;

    // Only pass along pageFilename if it's NOT index.php
    $pageFilename = $pageFilename == 'index.php' ? '' : $pageFilename;

    if ($menuItems >= 0) { createNestedList( $dbLink, 0, 0, $pageFilename, $currentPage, $importMenu, $armadilloURL );	}

}

// creates the nested list with ul , li , ul .................. can be use for the navigation and menus.
function createNestedList( $dbLink, $parentid, $counter, $pageFilename, $currentPage, $importMenu='', $armadilloURL='' )
{
    if ( file_exists( dirname(__FILE__) . '/config.php' ) ) {
        if ($counter == 0 )
             echo "<div id='armadilloContentMenu'><ul>";

        $normalizeChars = array(
            'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
            'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
            'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
            'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
            'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
            'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
            'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
        );

        // retrieve all children of $parent
        $query = "SELECT pageid, title, position, type FROM armadillo_nav INNER JOIN armadillo_post ON pageid=id WHERE parentid='$parentid' AND type='page' ORDER BY position ASC";
        $result = $dbLink->query($query);
        while ($row = $result->fetch_array()) {
            $res 	= 	$dbLink->query ( "SELECT pageid, title, position, type FROM armadillo_nav INNER JOIN armadillo_post ON pageid=id WHERE parentid='" . $row['pageid'] . "' AND type='page' ORDER BY position ASC" );
            $tot 	= 	$res->num_rows;
            $ul 	=  	$tot == 0 ? "":'<ul>';
            $_ul 	= 	$tot == 0 ? "":'</ul>';
            //Remove trailing forward slash in $armadilloURL if one exists
            //$armadilloURL = rtrim($armadilloURL, '/');
            //Check if we're importing menu to a non-Armadillo page
            $itemURL = $importMenu == TRUE ? $armadilloURL : '.';
            $titleInURL = preg_replace("/[&,.!?'\"]/", '', $row['title']);
            $titleInURL = strtolower(preg_replace("/[\s]/", '-', $titleInURL));
            $titleInURL = strtr($titleInURL, $normalizeChars);
            //Determine if the page being shown is the current page
            $currentPageCSS = ($currentPage === $row['pageid'] or $currentPage === $row['type']) ? 'current' : '';
            echo "<li class='$currentPageCSS'><a href='" . $itemURL . /*"/" . $pageFilename . */"?page_id=" . $row['pageid'] . "&title=" . $titleInURL . "' class='armadilloMenuItem armadilloPage $currentPageCSS'>" . $row['title'] . "</a>";
            echo "{$ul}";
            createNestedList( $dbLink, $row['pageid'], $counter+1, $pageFilename, $currentPage, $importMenu, $armadilloURL );
            echo "{$_ul}";
            echo "</li>";
        }

        if ($counter == 0 )
            echo "</ul></div>" ;
    }
}

function checkForAndParsePhpInContent($content) {
    // Check if there's any PHP in the content
    if (strpos($content, '<?php') !== FALSE) {
        // Create random file to store content in
        $randomFile = mt_rand();
        $tmpPath = dirname(dirname(dirname(__FILE__))) . '/' . $randomFile . '.php';
        $tmpFile = NULL;

        if ( $tmpFile = fopen($tmpPath, 'w') ) {
            if ( fwrite($tmpFile, $content) !== FALSE ) {
                // Close file
                fclose($tmpFile);
                // Start buffering
                ob_start();
                // Include previously created temp file
                include $tmpPath;
                // Grab its contents from the buffer
                $content = ob_get_contents();
                // Clean up and finish buffer
                ob_end_clean();
                // Remove temp file
                unlink($tmpPath);
            }
        }
    }
    return $content;
}

function displaySoloContent( $dbLink, $contentID, $autoCreate, $armadilloOptions='' )
{
    if ( is_numeric($contentID) ) {
        $contentID = $dbLink->real_escape_string($contentID);
        $query = "SELECT content, type, format, userid, publish FROM armadillo_post WHERE id='$contentID'";

        $result = $dbLink->query($query);

        if (!$result) {
            echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_PAGE_DISPLAY_DB_ERROR') . $dbLink->error;
        } else if ( $result->num_rows > 0 ) {
            $row = $result->fetch_array();

            $row['rawContent'] = $row['content'];
            $row['content'] = checkForAndParsePhpInContent($row['content']);
            $row['content'] = $row['format'] == 'markdown' ? Parsedown::instance()->text($row['content']) : $row['content'];
            
            return $row;
            
        } else {
            // Content ID doesn't exist in database yet
            if ( $autoCreate == 'true' ) {
                $title = '';
                $content = '';
                $sidebarContent = '';
                $summaryContent = '';
                $metaContent = '';
                $date = date('Y-m-d H:i:s');
                $lastEdited = $date;
                $author = '1';
                $status = '1';
                $contentType = 'soloContent';
                $format = $dbLink->real_escape_string($armadilloOptions['editorType']);

                $query = "INSERT INTO armadillo_post SET
                            id='$contentID',
                            title='$title',
                            content='$content',
                            sidebar_content='$sidebarContent',
                            meta_content='$metaContent',
                            summary_content='$summaryContent',
                            date='$date',
                            last_edited='$lastEdited',
                            userid='$author',
                            publish='$status',
                            format='$format',
                            type='$contentType'";

                $result = $dbLink->query($query);

                if (!$result) {
                    echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_PAGE_DISPLAY_DB_ERROR') . $dbLink->error;
                } else if ( $result->num_rows > 0 ) {
                    // Auto creation successfull, return empty string
                    return '';
                } else {
                    // Not sure what situation might trigger this section, so intentionally left blank
                }
            } else {
                return Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_SELECTED_ITEM_MISSING');
            }
            
        }
    } else {
        echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_ID_ERROR');
    }
}

//Retrieves the specified page from the database and displays it
function displayPage( $dbLink, $pageToDisplay='', $armadilloOptions='', $pageOptions='' )
{
    //Displays related posts based upon the selected category/tag, author, or archive date, if specified
    //The displayPosts() function accepts an array of options, so populate that array with the default values
    $options = array('blog_id' => $pageToDisplay, 'postsPageNumber' => 1, 'category' => '', 'tag' => '', 'author' => '', 'archive' => '', 'armadilloOptions' => $armadilloOptions);
    
    // Set category
    if ( isset($_GET['category']) ) {
        $options['category'] = $_GET['category'];
        //echo displayPosts( $dbLink, $options );
    }

    // Set tag
    if ( isset($_GET['tag']) ) {
        $options['tag'] = $_GET['tag'];
        //echo displayPosts( $dbLink, $options );
    }

    // Set author
    if ( isset($_GET['author']) ) {
        $options['author'] = $_GET['author'];
        //echo displayPosts( $dbLink, $options );
    }

    // Set archive
    if ( isset($_GET['archive']) ) {
        $options['archive'] = $_GET['archive'];
        //echo displayPosts( $dbLink, $options );
    }

    // Display specified content
    // Security check, make sure the page_id is numeric
    if ( is_numeric($pageToDisplay) ) {
        $pageToDisplay = $dbLink->real_escape_string($pageToDisplay);
        $query = "SELECT content, type, format, userid, publish FROM armadillo_post WHERE id='$pageToDisplay'";

        $result = $dbLink->query($query);

        if (!$result) {
            echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_PAGE_DISPLAY_DB_ERROR') . $dbLink->error;
        } else if ( $result->num_rows > 0 ) {
            $row = $result->fetch_array();

            //Display Page Content
            if ($row['type'] == 'blog') {
                return displayPosts( $dbLink, $options ); /* Display list of all Blog Posts */
            } elseif ( $row['type'] == 'soloContent' ) {
                $row['rawContent'] = $row['content'];
                $row['content'] = checkForAndParsePhpInContent($row['content']);
                $row['content'] = $row['format'] == 'markdown' ? Parsedown::instance()->text($row['content']) : $row['content'];
                return $row;
            } else {
                $pageContent = checkForAndParsePhpInContent($row['content']);
                $pageContent = $row['format'] == 'markdown' ? Parsedown::instance()->text($pageContent) : $pageContent;
                // Insert social sharing code if specified in settings
                if ($armadilloOptions['display_social_sharing_links']) {
                    $pageContent .= $armadilloOptions['social_sharing_code'];
                }
                return $pageContent;
            }
        } elseif ( $result->num_rows === 0 and (is_array($pageOptions) and $pageOptions['autoCreate'] == 'true') ) {
            // Page ID doesn't exist in database yet
            $title = $dbLink->real_escape_string($pageOptions['newPageTitle']);
            $content = '';
            $sidebarContent = '';
            $summaryContent = '';
            $metaContent = '';
            $date = date('Y-m-d H:i:s');
            $lastEdited = $date;
            $author = '1';
            $status = '1';
            $contentType = 'page';
            $format = $dbLink->real_escape_string($armadilloOptions['editorType']);

            $query = "INSERT INTO armadillo_post SET
                        id='$pageToDisplay',
                        title='$title',
                        content='$content',
                        sidebar_content='$sidebarContent',
                        meta_content='$metaContent',
                        summary_content='$summaryContent',
                        date='$date',
                        last_edited='$lastEdited',
                        userid='$author',
                        publish='$status',
                        format='$format',
                        type='$contentType',
                        blog_url='',
                        blog_date_format='WMDY',
                        blogposts_per_page='5',
                        display_blog_comments=FALSE,
                        display_blog_categories=FALSE,
                        display_blog_tags=FALSE,
                        display_blog_archive_links=TRUE,
                        disqus_shortname='',
                        blog_categories_title='Filed in: ',
                        blog_categories_separator=' | ',
                        blog_tags_title='Tags: ',
                        blog_tags_separator=', ',
                        blog_archive_links_format='MY',
                        post_author_display_name='fullname',
                        blog_readmore_text='Read more',
                        showmoreposts_button_text='Show more posts',
                        showmoreposts_button_bgcolor='666666',
                        showmoreposts_button_textcolor='ffffff',
                        enable_blog_rss=TRUE,
                        blog_rss_title='My RSS Feed',
                        blog_rss_description='',
                        blog_rss_copyright='',
                        blog_rss_linkname='RSS Feed',
                        blog_rss_filename='',
                        blog_rss_enable_customfeed=FALSE,
                        blog_rss_customfeed_url='',
                        blog_rss_summarize_entries=FALSE";

            $newPageResult = $dbLink->query($query);

            if (!$newPageResult) {
                echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_PAGE_DISPLAY_DB_ERROR') . $dbLink->error;
            } else if ( $newPageResult->num_rows > 0 ) {
                // Auto creation successfull, return empty string
                return '';
            } else {
                // Not sure what situation might trigger this section, so intentionally left blank
            }
        } else {
            return Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_SELECTED_ITEM_MISSING');
        }

    } else {
        echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_ID_ERROR');
    }
}

function defaultContent( $dbLink )
{
    $query = "SELECT id FROM armadillo_post WHERE default_content=TRUE";

    $result =  $dbLink->query($query);

    if ($result and ( $dbLink->affected_rows == 1 ) ) {
        $row = $result->fetch_array();

        return $row['id'];
    } else {
        return NULL;
    }
}

// function displayDefaultContent( $dbLink )
// {
//     $query = "SELECT id FROM armadillo_post WHERE default_content=TRUE";

//     $result =  $dbLink->query($query);

//     if (!$result or ( $dbLink->affected_rows == 0 ) ) {
//         echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_DEFAULT_CONTENT_DISPLAY_FAILED');
//     } else {
//         $row = $result->fetch_array();

//         return displayPage( $dbLink, $row['id'] );
//     }
// }

function getPageTitleAndMetaContent( $dbLink, $content )
{

    if ( file_exists(dirname(__FILE__) . '/config.php') ) {
        // Security check, make sure the content ID is numeric or set to "default"
        if (  $content == 'default' || is_numeric($content) ) {
            $query = $content == 'default' ? "SELECT title, meta_content FROM armadillo_post WHERE default_content=TRUE" : "SELECT title, meta_content FROM armadillo_post WHERE id=$content";

            $result = $dbLink->query($query);

            if (!$result) {
                $notification = "<p>" . Armadillo_Language::public_msg($armadilloOptions, 'ARM_POST_DISPLAY_SUMMARY_DB_ERROR') . $dbLink->error . "</p>";
                echo $notification;
                exit();
            } else {
                $row = $result->fetch_array();
                $pageInfo = array('pageTitle' => $row['title'], 'metaContent' => $row['meta_content']);
                //Make sure apostrophes in page title and meta content are escaped
                //$pageInfo['pageTitle'] = str_replace( "'", "\'", $pageInfo['pageTitle'] );
                //$pageInfo['metaContent'] = str_replace( "'", "\'", $pageInfo['metaContent'] );
                $pageInfo['metaContent'] = str_replace( "/>", ">", $pageInfo['metaContent'] );
                $pageInfo['metaContent'] = str_replace( "\r\n", "", $pageInfo['metaContent'] );
                $pageInfo['metaContent'] = str_replace( "\n", "", $pageInfo['metaContent'] );
                return $pageInfo;
            }
        } else {
            $pageInfo = array('pageTitle' => Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_ID_ERROR'));
            return $pageInfo;
        }
    }
}

//Retrieves and displays published posts
function displayPosts( $dbLink, $options )
{
    if ( file_exists(dirname(__FILE__) . '/config.php') ) {

        /*
            $options -> variable passed into the function
            $armadilloOptions -> general armadillo settings
            $blogOptions -> blog-specific settings
        */

        //Get Armadillo Options from the $options variable passed into the function
        $armadilloOptions = $options['armadilloOptions'];//getArmadilloOptions($dbLink);

        //Grab blog_id
        $blog_id = $dbLink->real_escape_string($options['blog_id']);

        //Retrieve blog-specific options
        $blogOptionsQuery = "SELECT * FROM armadillo_post WHERE id='$blog_id' AND type='blog'";
        $blogOptionsResult = $dbLink->query($blogOptionsQuery);

        if (!$blogOptionsResult) {
            echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_PAGE_DISPLAY_DB_ERROR') . $dbLink->error;
        } elseif ( $blogOptionsResult->num_rows > 0 ) {
            //Store blog options
            $blogOptions = $blogOptionsResult->fetch_array();

            //Blog URL as specified by the user
            $blogURL = $blogOptions['blog_url'];
            
            //Grab number of posts to display at one time
            $postsPerPage = $blogOptions['blogposts_per_page'] == NULL ? '5' : $blogOptions['blogposts_per_page'];
            //Determine which posts to fetch based on the current pagination value
            $postsToFetch = $options['postsPageNumber'] == 1 ? 0 : ($options['postsPageNumber'] - 1) * $postsPerPage;

            //Date/time right now
            $now = date('Y-m-d H:i:s');

            //Determine relevant query to display selected content
            $query = '';
            if ($options['category'] === '' and $options['tag'] === '' and $options['author'] === '' and $options['archive'] === '') {
                $query = "SELECT armadillo_post.*, armadillo_user.name AS fullname, armadillo_user.username FROM armadillo_post
                            INNER JOIN armadillo_user ON armadillo_user.id = armadillo_post.userid
                            WHERE publish=TRUE AND type='post' AND armadillo_post.blog_id='$blog_id' AND armadillo_post.date <= '$now' ORDER BY date DESC LIMIT $postsPerPage OFFSET $postsToFetch";
            } elseif ($options['category'] != '') {
                $category = $dbLink->real_escape_string($options['category']);
                $query = "SELECT armadillo_post.*, armadillo_user.name AS fullname, armadillo_user.username, armadillo_term_relationship.*, armadillo_term.name FROM armadillo_post
                            INNER JOIN armadillo_user ON armadillo_user.id = armadillo_post.userid
                            INNER JOIN armadillo_term_relationship ON armadillo_term_relationship.postid = armadillo_post.id
                            INNER JOIN armadillo_term ON armadillo_term.id = armadillo_term_relationship.termid
                            WHERE armadillo_post.publish=TRUE AND armadillo_post.type='post' AND armadillo_post.blog_id='$blog_id' AND armadillo_post.date <= '$now' AND armadillo_term.type = 'category' AND armadillo_term.name = '$category'
                            ORDER BY date DESC LIMIT $postsPerPage OFFSET $postsToFetch";
            } elseif ($options['tag'] != '') {
                $tag = $dbLink->real_escape_string($options['tag']);
                $query = "SELECT armadillo_post.*, armadillo_user.name AS fullname, armadillo_user.username, armadillo_term_relationship.*, armadillo_term.name FROM armadillo_post
                            INNER JOIN armadillo_user ON armadillo_user.id = armadillo_post.userid
                            INNER JOIN armadillo_term_relationship ON armadillo_term_relationship.postid = armadillo_post.id
                            INNER JOIN armadillo_term ON armadillo_term.id = armadillo_term_relationship.termid
                            WHERE armadillo_post.publish=TRUE AND armadillo_post.type='post' AND armadillo_post.blog_id='$blog_id' AND armadillo_post.date <= '$now' AND armadillo_term.type = 'tag' AND armadillo_term.name = '$tag'
                            ORDER BY date DESC LIMIT $postsPerPage OFFSET $postsToFetch";
            } elseif ($options['author'] != '') {
                $author = $dbLink->real_escape_string($options['author']);
                $query = "SELECT armadillo_post.*, armadillo_user.name AS fullname, armadillo_user.username FROM armadillo_post
                            INNER JOIN armadillo_user ON armadillo_user.id = armadillo_post.userid
                            WHERE armadillo_post.publish=TRUE AND armadillo_post.type='post' AND armadillo_post.blog_id='$blog_id' AND armadillo_post.date <= '$now' AND armadillo_user.username = '$author'
                            ORDER BY date DESC LIMIT $postsPerPage OFFSET $postsToFetch";
            } elseif ($options['archive'] != '') {
                $archive = $dbLink->real_escape_string($options['archive']);
                // Make sure the date is valid and what we expect, set it to the current year and month otherwise
                $archive = preg_match('/\A\d{4}-\d{2}\z/', $archive) === 1 ? explode('-', $archive) : array( date('Y',time()), date('m',time()) );
                $query = "SELECT armadillo_post.*, armadillo_user.name AS fullname, armadillo_user.username FROM armadillo_post
                            INNER JOIN armadillo_user ON armadillo_user.id = armadillo_post.userid
                            WHERE publish=TRUE AND type='post' AND armadillo_post.blog_id='$blog_id' AND armadillo_post.date <= '$now' AND YEAR(date) in ($archive[0]) AND MONTH(date) in ($archive[1]) 
                            ORDER BY date DESC LIMIT $postsPerPage OFFSET $postsToFetch";
            }

            $result = $dbLink->query($query);

            if (!$result) {
                $notification = "<p>" . Armadillo_Language::public_msg($armadilloOptions, 'ARM_POST_DISPLAY_SUMMARY_DB_ERROR') . $dbLink->error . "</p>";
                echo $notification;
                exit();
            }

            $listOfPosts = array();
            $postsToDisplay = '';
            $hideButton = ( ($dbLink->affected_rows === 0 and $options['postsPageNumber'] > 1) || ($dbLink->affected_rows < $postsPerPage) ) ? 'hide' : '';

            if ($dbLink->affected_rows === 0 and $options['postsPageNumber'] === 1) {
                //show no posts published message
                $postsToDisplay .= "<p>" . Armadillo_Language::public_msg($armadilloOptions, 'ARM_DISPLAY_CONTENT_NO_POSTS_PUBLISHED') . "</p>";
            } else {
                $postsToDisplay .= $options['postsPageNumber'] > 1 ? "<div class='fetchedPostsContainer'>" : '';
                if ($dbLink->affected_rows === 0 and $options['postsPageNumber'] > 1) {
                    $postsToDisplay .= "<p>" . Armadillo_Language::public_msg($armadilloOptions, 'ARM_DISPLAY_CONTENT_ALL_POSTS_SHOWN') . "</p>";
                } else {
                    $terms = array();
                    $termQuery = "SELECT name, type, postid FROM armadillo_term 
                                    LEFT JOIN armadillo_term_relationship 
                                    ON armadillo_term.id = armadillo_term_relationship.termid 
                                    ORDER BY name";
                    $termResult = $dbLink->query($termQuery);

                    if (!$termResult) {  }

                    while ( $termRow = $termResult->fetch_array() ) {
                        $terms[] = array('termName' => $termRow['name'], 'termType' => $termRow['type'], 'postID' => $termRow['postid']);
                    }

                    while ( $row = $result->fetch_array() ) {
                        $listOfPosts[] = array(
                                            'id' => $row['id'],
                                            'title' => $row['title'],
                                            'content' => $row['content'],
                                            'meta_content' => $row['meta_content'],
                                            'summary_content' => $row['summary_content'],
                                            'date' => $row['date'],
                                            'author' => $row['fullname'],
                                            'username' => $row['username'],
                                            'display_comments' => $row['display_comments'],
                                            'display_summary' => $row['display_summary'],
                                            'format' => $row['format']
                                        );
                    }

                    //Determine blog date format
                    $blogDateFormat = '';

                    //Check for Windows hosting
                    $windowsHosting = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? true : false ;

                    if ( $blogOptions['blog_date_format'] == 'WMDY' ) {
                        $blogDateFormat = $windowsHosting ? '%A, %B %#d, %Y' : '%A, %B %e, %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'WDMY' ) {
                        $blogDateFormat = $windowsHosting ? '%A, %#d %B, %Y' : '%A, %e %B, %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'WDpMY' ) {
                        $blogDateFormat = $windowsHosting ? '%A, %#d. %B %Y' : '%A, %e. %B %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'YMDW' ) {
                        $blogDateFormat = $windowsHosting ? '%Y %B %#d, %A' : '%Y %B %e, %A';
                    } elseif ( $blogOptions['blog_date_format'] == 'MDY' ) {
                        $blogDateFormat = $windowsHosting ? '%B %#d, %Y' : '%B %e, %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'DMY' ) {
                        $blogDateFormat = $windowsHosting ? '%#d %B, %Y' : '%e %B, %Y';
                    } elseif ( $blogOptions['blog_date_format']  == 'DpMY' ) {
                        $blogDateFormat = $windowsHosting ? '%#d. %B %Y' : '%e. %B %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'YMD' ) {
                        $blogDateFormat = $windowsHosting ? '%Y %B %#d' : '%Y %B %e';
                    } elseif ( $blogOptions['blog_date_format'] == 'MY' ) {
                        $blogDateFormat = '%B %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'YM' ) {
                        $blogDateFormat = '%Y %B';
                    }

                    $normalizeChars = array(
                        'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
                        'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
                        'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
                        'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
                        'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
                        'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
                        'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
                        'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
                    );

                    foreach ($listOfPosts as $post) {
                        /* Skip posts with publish dates that are in the future. */
                        if (strtotime( $post['date'] ) > time() ) {
                            continue;
                        }

                        $categories = '';
                        $categorySeparator = '';
                        if ( $blogOptions['display_blog_categories'] ) {
                            $categories = '&nbsp;&nbsp;' . $blogOptions['blog_categories_title'];
                            $categorySeparator = $blogOptions['blog_categories_separator'];
                            foreach ($terms as $term) {
                                $name = $term['termName'];
                                if ($term['postID'] == $post['id'] and $term['termType'] == 'category') {
                                    $categories .= "<span class='blog-entry-category'><a href='?category=$name'>$name</a></span>" . $categorySeparator;
                                }
                            }
                            $categories = rtrim($categories, $categorySeparator);
                        }

                        $tags = '';
                        $tagSeparator = '';
                        if ( $blogOptions['display_blog_tags'] ) {
                            $tags = $blogOptions['blog_tags_title'];
                            $tagSeparator = $blogOptions['blog_tags_separator'];
                            foreach ($terms as $term) {
                                $name = $term['termName'];
                                if ($term['postID'] == $post['id'] and $term['termType'] == 'tag') {
                                    $tags .= "<a href='?tag=$name'>$name</a>" . $tagSeparator;
                                }
                            }
                            $tags = rtrim($tags, $tagSeparator);
                        }

                        $postAuthor = $blogOptions['post_author_display_name'] == 'fullname' ? $post['author'] : $post['username'];
                        $postAuthor = $blogOptions['display_blog_post_author'] == TRUE ? Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_AUTHOR_CREDIT') . '<a href="?author=' . $post['username'] . '" class="blog-entry-author">' . $postAuthor . '</a>' : '';

                        $titleInURL = preg_replace("/[&,.!?'\"]/", '', $post['title']);
                        $titleInURL = strtolower(preg_replace("/[\s]/", '-', $titleInURL));
                        $titleInURL = strtr($titleInURL, $normalizeChars);

                        $postContent = $post['content']; //htmlspecialchars_decode($post['content'], ENT_QUOTES);

                        //Trim post summary, check it hasn't been entirely truncated, fix if it has
                        // $trimAmount = 500;
                        // $trimCount = 0;
                        // $postContentSummary = '';

                        // while ( $postContentSummary === '' && $trimCount <= 10 && $post['display_summary'] == TRUE ) {
                        //     $postContentSummary = mb_substr($postContent, 0, $trimAmount);
                        //     $postContentSummary = substr($postContentSummary, 0, strrpos($postContentSummary, '. '));
                        //     $trimAmount += 100;                        
                        //     $trimCount ++;
                        // }

                        $postContent = $post['display_summary'] == TRUE ? $post['summary_content'] . " <a href='?post_id=" . $post['id'] . "&title=" . $titleInURL . "'>" . $blogOptions['blog_readmore_text'] . "</a>" : $postContent;

                        $postsToDisplay .= "<div class='blog-entry'><h1 class='blog-entry-title'><a href='" . $blogURL . "?post_id="
                                            . $post['id'] . "&title=" . $titleInURL . "' class='blog-permalink'>"
                                            . $post['title'] . "</a></h1>";

                        $postsToDisplay .= "<div class='blog-entry-date'>" . strftime( $blogDateFormat, strtotime($post['date']) )
                                            . "$postAuthor $categories</div>";

                        $postContent = checkForAndParsePhpInContent($postContent);
                        $postContent = $post['format'] == 'markdown' ? Parsedown::instance()->text($postContent) : $postContent;

                        $postsToDisplay .= "<div class='blog-entry-body'>" . $postContent;

                        //Normally, post tags would be displayed in the p tag below, as that is where they are displayed in RW's Blog page.
                        $postsToDisplay .= "<p class='blog-entry-tags'>$tags</p>";

                        //Post comment's container
                        $hideComments = ( !$blogOptions['display_blog_comments'] or !$post['display_comments'] ) ? 'hide' : '';

                        $postsToDisplay .= "<div class='blog-entry-comments $hideComments'>";
                        //Link to post's comments
                        if ($blogOptions['display_blog_comments'] and $post['display_comments']) {
                            $postsToDisplay .= "<a href='?post_id=" . $post['id'] . "&title=" . $titleInURL . "#disqus_thread'>" . Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_COMMENTS_TEXT') . "</a>";
                        }
                        //Close post comments container
                        $postsToDisplay .= "</div>";
                        //Close the blog-entry-body div, and the blog-entry container div
                        $postsToDisplay .= "</div></div><div style='clear: both;'></div>";
                    }

                }
                $postsToDisplay .= $options['postsPageNumber'] > 1 ? "</div>" : '';
                $postsToDisplay .= "<p class='showMorePostsButton armadilloBlog_$blog_id'><a class='$hideButton' rel='" . ($options['postsPageNumber'] + 1) . "'>" . $blogOptions['showmoreposts_button_text'] . "</a></p>";
            }

            // Insert social sharing code if specified in settings, 
            // but only once (not repeatedly, after the "show more posts" button in clicked)
            if ($armadilloOptions['display_social_sharing_links']) {
                $postsToDisplay .= $options['postsPageNumber'] == 1 ? $armadilloOptions['social_sharing_code'] : '';
            }

            return $postsToDisplay;

        } elseif ( $blogOptionsResult->num_rows === 0 and $options['autoCreate'] == 'true' ) {
            // Blog ID doesn't exist in database yet
            $title = $dbLink->real_escape_string($options['newBlogTitle']);
            $content = '';
            $sidebarContent = '';
            $summaryContent = '';
            $metaContent = '';
            $date = date('Y-m-d H:i:s');
            $lastEdited = $date;
            $author = '1';
            $status = '1';
            $contentType = 'blog';
            $format = $dbLink->real_escape_string($armadilloOptions['editorType']);

            $query = "INSERT INTO armadillo_post SET
                        id='$blog_id',
                        title='$title',
                        content='$content',
                        sidebar_content='$sidebarContent',
                        meta_content='$metaContent',
                        summary_content='$summaryContent',
                        date='$date',
                        last_edited='$lastEdited',
                        userid='$author',
                        publish='$status',
                        format='$format',
                        type='$contentType',
                        blog_url='',
                        blog_date_format='WMDY',
                        blogposts_per_page='5',
                        display_blog_comments=FALSE,
                        display_blog_categories=FALSE,
                        display_blog_tags=FALSE,
                        display_blog_archive_links=TRUE,
                        disqus_shortname='',
                        blog_categories_title='Filed in: ',
                        blog_categories_separator=' | ',
                        blog_tags_title='Tags: ',
                        blog_tags_separator=', ',
                        blog_archive_links_format='MY',
                        post_author_display_name='fullname',
                        blog_readmore_text='Read more',
                        showmoreposts_button_text='Show more posts',
                        showmoreposts_button_bgcolor='666666',
                        showmoreposts_button_textcolor='ffffff',
                        enable_blog_rss=TRUE,
                        blog_rss_title='My RSS Feed',
                        blog_rss_description='',
                        blog_rss_copyright='',
                        blog_rss_linkname='RSS Feed',
                        blog_rss_filename='',
                        blog_rss_enable_customfeed=FALSE,
                        blog_rss_customfeed_url='',
                        blog_rss_summarize_entries=FALSE";

            $result = $dbLink->query($query);

            if (!$result) {
                echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_PAGE_DISPLAY_DB_ERROR') . $dbLink->error;
            } else if ( $result->num_rows > 0 ) {
                // Auto creation successfull, return empty string
                return '';
            } else {
                // Not sure what situation might trigger this section, so intentionally left blank
            }
        } else {
            return Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_SELECTED_ITEM_MISSING');
        }

        
    }
}


//Retrieves the specified post from the database and displays it
function displayPost( $dbLink, $postToDisplay, $armadilloOptions )
{
    if ( file_exists(dirname(__FILE__) . '/config.php') ) {
        // Security check, make sure the post_id is numeric
        if ( is_numeric($postToDisplay) ) {

            $postToDisplay = $dbLink->real_escape_string($postToDisplay);
            $query = "SELECT armadillo_post.*, armadillo_user.name, armadillo_user.username FROM armadillo_post
                        INNER JOIN armadillo_user ON armadillo_user.id = armadillo_post.userid
                        WHERE armadillo_post.id='$postToDisplay' AND type='post'";

            $result = $dbLink->query($query);

            if (!$result) {
                $notification = "<p>" . Armadillo_Language::public_msg($armadilloOptions, 'ARM_POST_DISPLAY_SUMMARY_DB_ERROR') . $dbLink->error . "</p>";
                echo $notification;
                exit();
            }

            //$listOfPosts = array();

            if ( empty($result) ) {
                echo "<p>" . Armadillo_Language::public_msg($armadilloOptions, 'ARM_DISPLAY_CONTENT_NO_POSTS_PUBLISHED') . "</p>";
            } else {
                //Specified post details
                $specifiedPost = $result->fetch_array();
                //Grab blog_id
                $blog_id = $specifiedPost['blog_id'];

                //Retrieve blog-specific options
                $blogOptionsQuery = "SELECT * FROM armadillo_post WHERE id='$blog_id' AND type='blog'";
                $blogOptionsResult = $dbLink->query($blogOptionsQuery);

                if (!$blogOptionsResult) {
                    echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_PAGE_DISPLAY_DB_ERROR') . $dbLink->error;
                } elseif ( $blogOptionsResult->num_rows > 0 ) {
                    //Store blog options
                    $blogOptions = $blogOptionsResult->fetch_array();

                    $terms = array();
                    $termQuery = "SELECT name, type, postid FROM armadillo_term LEFT JOIN armadillo_term_relationship ON armadillo_term.id = armadillo_term_relationship.termid WHERE postid = '$postToDisplay' ORDER BY name";
                    $termResult = $dbLink->query($termQuery);

                    if (!$termResult) {  }

                    while ( $termRow = $termResult->fetch_array() ) {
                        $terms[] = array('termName' => $termRow['name'], 'termType' => $termRow['type'], 'postID' => $termRow['postid']);
                    }

                    // while ( $row = $result->fetch_array() ) {
                    //     $listOfPosts[] = array(
                    //                         'id' => $row['id'], 
                    //                         'title' => $row['title'], 
                    //                         'content' => $row['content'], 
                    //                         'date' => $row['date'], 
                    //                         'author' => $row['name'], 
                    //                         'username' => $row['username'], 
                    //                         'display_comments' => $row['display_comments'],
                    //                         'format' => $row['format']
                    //                     );
                    // }

                    //Determine blog date format
                    $blogDateFormat = '';

                    //Check for Windows hosting
                    $windowsHosting = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? true : false ;

                    if ( $blogOptions['blog_date_format'] == 'WMDY' ) {
                        $blogDateFormat = $windowsHosting ? '%A, %B %#d, %Y' : '%A, %B %e, %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'WDMY' ) {
                        $blogDateFormat = $windowsHosting ? '%A, %#d %B, %Y' : '%A, %e %B, %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'WDpMY' ) {
                        $blogDateFormat = $windowsHosting ? '%A, %#d. %B %Y' : '%A, %e. %B %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'YMDW' ) {
                        $blogDateFormat = $windowsHosting ? '%Y %B %#d, %A' : '%Y %B %e, %A';
                    } elseif ( $blogOptions['blog_date_format'] == 'MDY' ) {
                        $blogDateFormat = $windowsHosting ? '%B %#d, %Y' : '%B %e, %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'DMY' ) {
                        $blogDateFormat = $windowsHosting ? '%#d %B, %Y' : '%e %B, %Y';
                    } elseif ( $blogOptions['blog_date_format']  == 'DpMY' ) {
                        $blogDateFormat = $windowsHosting ? '%#d. %B %Y' : '%e. %B %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'YMD' ) {
                        $blogDateFormat = $windowsHosting ? '%Y %B %#d' : '%Y %B %e';
                    } elseif ( $blogOptions['blog_date_format'] == 'MY' ) {
                        $blogDateFormat = '%B %Y';
                    } elseif ( $blogOptions['blog_date_format'] == 'YM' ) {
                        $blogDateFormat = '%Y %B';
                    }

                    //foreach ($listOfPosts as $post) {
                       $categories = '';
                        $categorySeparator = '';
                        if ( $blogOptions['display_blog_categories'] ) {
                            $categories = '&nbsp;&nbsp;' . $blogOptions['blog_categories_title'];
                            $categorySeparator = $blogOptions['blog_categories_separator'];
                            foreach ($terms as $term) {
                                $name = $term['termName'];
                                if ($term['termType'] == 'category') {
                                    $categories .= "<span class='blog-entry-category'><a href='?category=$name'>$name</a></span>" . $categorySeparator;
                                }
                            }
                            $categories = rtrim($categories, $categorySeparator);
                        }

                        $tags = '';
                        $tagSeparator = '';
                        if ( $blogOptions['display_blog_tags'] ) {
                            $tags = $blogOptions['blog_tags_title'];
                            $tagSeparator = $blogOptions['blog_tags_separator'];
                            foreach ($terms as $term) {
                                $name = $term['termName'];
                                if ($term['termType'] == 'tag') {
                                    $tags .= "<a href='?tag=$name'>$name</a>" . $tagSeparator;
                                }
                            }
                            $tags = rtrim($tags, $tagSeparator);
                        }
                        
                        $postAuthor = $blogOptions['post_author_display_name'] == 'fullname' ? $specifiedPost['name'] : $specifiedPost['username'];
                        $postAuthor = $blogOptions['display_blog_post_author'] == TRUE ? Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_AUTHOR_CREDIT') . '<a href="?author='
                                        . $specifiedPost['username'] . '" class="blog-entry-author">' . $postAuthor . '</a>' : '';

                        echo "<div class='blog-entry'><h1 class='blog-entry-title'>" . $specifiedPost['title'] . "</h1>";

                        echo "<div class='blog-entry-date'>" . strftime( $blogDateFormat, strtotime( $specifiedPost['date'] ) )
                            . "$postAuthor $categories</div>";

                        $specifiedPost['content'] = checkForAndParsePhpInContent($specifiedPost['content']);
                        $specifiedPost['content'] = $specifiedPost['format'] == 'markdown' ? Parsedown::instance()->text($specifiedPost['content']) : $specifiedPost['content'];
                        echo "<div class='blog-entry-body'>" . $specifiedPost['content']; //htmlspecialchars_decode($specifiedPost['content'], ENT_QUOTES);

                        //Normally, post tags would be displayed in the p tag below, as that is where they are displayed in RW's Blog page.
                        echo "<p class='blog-entry-tags'>$tags</p>";

                        //Post comment's container
                        $hideComments =  ( !$blogOptions['display_blog_comments'] or !$specifiedPost['display_comments'] ) ? 'hide' : '';
                        echo "<div class='blog-entry-comments $hideComments'>";

                        //Display post comments
                        if ($blogOptions['display_blog_comments'] == TRUE and $specifiedPost['display_comments'] == TRUE) {
                            echo "<div id='disqus_thread'></div>"
                                . "<script type='text/javascript'>var disqus_shortname='" . $blogOptions['disqus_shortname'] . "';"
                                . " (function() {"
                                    . "     var dsq = document.createElement('script'); dsq.type = 'text/javascript'; dsq.async = true;"
                                . "     var disqus_identifier = '" . $specifiedPost['id'] . "';"
                                . "     dsq.src = '//' + disqus_shortname + '.disqus.com/embed.js';"
                                . "     (document.getElementsByTagName('head')[0] || document.getElementsByTagName('body')[0]).appendChild(dsq);"
                                . " })();"
                                . "</script>"
                                . "<noscript>Please enable JavaScript to view the <a href='//disqus.com/?ref_noscript'>comments powered by Disqus.</a></noscript>"
                                . "<a href='//disqus.com' class='dsq-brlink'>blog comments powered by <span class='logo-disqus'>Disqus</span></a>";
                        }
                        //Close post comments container
                        echo "</div>";
                        //Close the blog-entry-body div, and the blog-entry container div
                        echo "</div></div><div style='clear: both;'></div>";
                    //}

                    // Insert social sharing code if specified in settings
                    if ($armadilloOptions['display_social_sharing_links']) {
                        echo $armadilloOptions['social_sharing_code'];
                    }
                } else {
                    // Blog options lookup failed, what would be appropriate action in that case?
                }
            }
        } else {
            echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_ID_ERROR');
        }
    }
}

function displaySidebarContent( $dbLink, $defaultContent, $sidebarToDisplay, $type, $assetPath='' )
{
    // Begin sidebar content div
    echo '<div class="armadilloContent armadilloSidebar">';
    if ( file_exists(dirname(__FILE__) . '/config.php') ) {
        $query = '';
        $termsQuery = '';
        if ($defaultContent == TRUE) {
            $query = "SELECT type, sidebar_content, format FROM armadillo_post WHERE default_content=TRUE";
        } elseif ($type === 'post') {
            $query = "SELECT type, sidebar_content, format FROM armadillo_post WHERE type='blog'";
        } elseif ($defaultContent == FALSE) {
            $query = "SELECT type, sidebar_content, format FROM armadillo_post WHERE id='$sidebarToDisplay'";
        }

        $result = $dbLink->query($query);

        if (!$result) {
            echo "<p>" . Armadillo_Language::public_msg($armadilloOptions, 'ARM_DISPLAY_CONTENT_SIDEBAR_ERROR') . $dbLink->error . "</p>";
            // Close sidebar content div, since we're exiting the script
            echo '</div>';
            exit();
        }

        if ( !empty($result) ) {
            $row = $result->fetch_array();
            $row['sidebar_content'] = checkForAndParsePhpInContent($row['sidebar_content']);
            $row['sidebar_content'] = $row['format'] == 'markdown' ? Parsedown::instance()->text($row['sidebar_content']) : $row['sidebar_content'];
            echo $row['sidebar_content'];
        }
    }
    // Close sidebar content div
    echo '</div>';
}

function pageTypeForID( $dbLink, $pageID )
{
    $query = '';

    if ( $pageID == 'default' ) {
        $query = "SELECT type FROM armadillo_post WHERE default_content=TRUE";
    } else {
        $query = "SELECT type FROM armadillo_post WHERE id='$pageID'";
    }

    $result = $dbLink->query($query);

    if (!$result) {
        exit();
    }

    if ( !empty($result) ) {
        $row = $result->fetch_array();
        return $row['type'];
    }
}

function displayBlogNavigation( $dbLink, $blog_id, $assetPath='' )
{
    // Get options
    $blogOptions = getBlogOptions( $dbLink, $blog_id );

    //Date/time right now
    $now = date('Y-m-d H:i:s');

    /* Queries to get relevant info from database */
    $termQuery = "SELECT name, armadillo_term.type FROM armadillo_term 
                    INNER JOIN armadillo_term_relationship ON armadillo_term.id = armadillo_term_relationship.termid 
                    INNER JOIN armadillo_post ON armadillo_term_relationship.postid = armadillo_post.id 
                    WHERE armadillo_post.publish = 1 AND armadillo_post.blog_id = '$blog_id' AND armadillo_post.date <= '$now' GROUP BY armadillo_term.id ORDER BY name";
    $archiveQuery = "SELECT date FROM armadillo_post WHERE type='post' AND blog_id = '$blog_id' AND date <= '$now' GROUP BY YEAR(date) DESC, MONTH(date) DESC";
    $authorQuery = "SELECT armadillo_user.id, name, username FROM armadillo_user 
                    INNER JOIN armadillo_post ON armadillo_user.id = armadillo_post.userid 
                    WHERE armadillo_post.blog_id = '$blog_id' AND armadillo_post.date <= '$now' 
                    GROUP BY armadillo_user.name ORDER BY name"; 

    // Opening tag for blog navigation container which holds tags, categories, author links, etc.
    $blogNavContent = '<div id="armadilloBlogNav">';

    // Grab results of the terms query
    $termResult = $dbLink->query($termQuery);
    //if (!$termResult) {  } <-- Haven't yet used this in v1.6.5 and earlier, commenting out as of v1.7.0

    // Could possibly have no categories or tags, so only populate $terms array if db query isn't empty
    if ($dbLink->affected_rows > 0) {
        while ( $termRow = $termResult->fetch_array() ) {
                $terms[] = array('termName' => $termRow['name'], 'termType' => $termRow['type']); //, 'postID' => $termRow['postid']); <-- commented out as of v2.5.11 since postID is not being used in this function
        }
    } else {
        $terms = NULL;
    }

    /* Display the blog category links, if enabled in blog options. */
    if ($blogOptions['display_blog_categories'] and !empty($terms)) {
        // Generate markup for category links
        $categories = '<div id="blog-categories">';
        foreach ($terms as $term) {
            $name = $term['termName'];
            if ($term['termType'] === 'category') {
                $categories .= "<a href='?category=$name' class='blog-category-link-enabled'>$name</a><br/>";
            }
        }
        $categories .= '</div>';
        $blogNavContent .= $categories;
    }

    if ($blogOptions['display_blog_archive_links']) {
        // Grab results of the archive query
        $archiveResult = $dbLink->query($archiveQuery);
        // Could also not have any archives (if no posts are yet created) so check to make sure there is one or more rows returned
        if ($dbLink->affected_rows > 0) {
            while ( $archiveRow = $archiveResult->fetch_array() ) {
                $archives[] = array('date' => $archiveRow['date']);
            }

            // Generate markup for archive links
            $archivesMarkup = '<div id="blog-archives">';
            $archiveLinksFormat = '';
            if ( $blogOptions['blog_archive_links_format'] == 'MY' ) {
                $archiveLinksFormat = '%B %Y';
            } elseif ( $blogOptions['blog_archive_links_format'] == 'YM' ) {
                $archiveLinksFormat = '%Y %B';
            }
            foreach ($archives as $archive) {
                $archivesMarkup .= '<a class="blog-archive-link-enabled" href="?archive=' . date( 'Y-m', strtotime( $archive['date'] ) ) . '">'
                                . strftime( $archiveLinksFormat, strtotime( $archive['date'] ) )
                                . '</a><br>';
            }
            $archivesMarkup .= '</div>';
            $blogNavContent .= $archivesMarkup;
        }

    }

    /* Display the blog category links, if enabled in blog options. */
    if ($blogOptions['display_blog_tags'] and !empty($terms)) {
        // Generate markup for tag cloud
        $tags = '<ul class="blog-tag-cloud">';
        foreach ($terms as $term) {
            $name = $term['termName'];
            if ($term['termType'] === 'tag') {
                $tags .= "<li><a href='?tag=$name' title='$name' rel='tag'>$name</a></li>";
            }
        }
        $tags .= '</ul>';
        $blogNavContent .= $tags;
    }

    /* Display the RSS Feed, if enabled in blog options. */
    if ($blogOptions['enable_blog_rss']) {
        $rssLink = $blogOptions['blog_rss_enable_customfeed'] == TRUE ? $blogOptions['blog_rss_customfeed_url'] : $assetPath . "/armadillo/" . $blogOptions['blog_rss_filename'];
        $rssLinkMarkup = "<div id='blog-rss-feeds'>"
            . "<a class='blog-rss-link' href='" . $rssLink
            . "' rel='alternate' type='application/rss+xml' title='"
            . $blogOptions['blog_rss_title'] . "'>"
            . $blogOptions['blog_rss_linkname'] . "</a><br/>"
            . "</div>";
        $blogNavContent .= $rssLinkMarkup;
    }

    // Display links to authors if they are also shown on blog posts
    if ($blogOptions['display_blog_post_author']) {
        $authorResult = $dbLink->query($authorQuery);

        $authorLinksMarkup = '<div id="blog-post-authors"><ul>';

        while ( $authorRow = $authorResult->fetch_array() ) {
            // Determine whether we should display the username or full name
            $postAuthor = $blogOptions['post_author_display_name'] == 'fullname' ? $authorRow['name'] : $authorRow['username'];

            // Create list item
            $authorLinksMarkup .= '<li><a href="?author=' . $authorRow['username'] . '" class="blog-author-link">' . $postAuthor . '</a></li>';
        }

        $authorLinksMarkup .= '</ul></div>';
        $blogNavContent .= $authorLinksMarkup;
    }

    // Closing tag for blog navigation container
    $blogNavContent .= '</div>';

    echo $blogNavContent;
}

function displayBlogHeadlines ( $dbLink, $options )
{
    $armadilloOptions = $options['armadilloOptions'];

    //Date/time right now
    $now = date('Y-m-d H:i:s');

    $blog_id = $dbLink->real_escape_string($options['blog_id']);

    // Get blog settings
    $blogOptions = getBlogOptions( $dbLink, $blog_id );

    $numberOfHeadlines = $dbLink->real_escape_string($options['number_of_headlines']);

    $specificPost = $numberOfHeadlines == 1 ? intval($dbLink->real_escape_string($options['single_post_selection'])) - 1 : 0;

    // Query to filter by category
    $filterByCategory = $options['filter_by_category'] ? " AND ( armadillo_term.type = 'category' AND armadillo_term.name = '" . $dbLink->real_escape_string($options['category_filter']) . "' )" : '' ;

    // Query to filter by tag
    $filterByTag = $options['filter_by_tag'] ? " AND ( armadillo_term.type = 'tag' AND armadillo_term.name = '" . $dbLink->real_escape_string($options['tag_filter']) . "' )" : '' ;

    // Query to filter by author, run checks to see if specific value is a numerical ID or username string
    $authorFilter = is_numeric($options['author_filter']) ? " AND armadillo_user.id = " . $dbLink->real_escape_string($options['author_filter']) : " AND armadillo_user.username = '" . $dbLink->real_escape_string($options['author_filter']) . "'" ;
    $filterByAuthor = $options['filter_by_author'] ? $authorFilter : '' ;
    
    // Query to filter by archive
    $filterByArchive = '';
    $archive = array();
    if ( $options['filter_by_archive'] ) {
        $archiveFilter = $dbLink->real_escape_string($options['archive_filter']);
        // Make sure the date is valid and what we expect, set it to the current year and month otherwise
        $archive = preg_match('/\d{4}-\d{2}/', $archiveFilter) === 1 ? explode('-', $archiveFilter) : array( date('Y',time()), date('m',time()) );
        $filterByArchive = " AND YEAR(date) in ($archive[0]) AND MONTH(date) in ($archive[1])";
    }

    $query = "SELECT armadillo_post.id, title, content, format FROM armadillo_post
                INNER JOIN armadillo_term_relationship ON armadillo_term_relationship.postid = armadillo_post.id 
                INNER JOIN armadillo_term ON armadillo_term.id = armadillo_term_relationship.termid 
                INNER JOIN armadillo_user ON armadillo_user.id = armadillo_post.userid 
                WHERE armadillo_post.publish = TRUE AND armadillo_post.type = 'post' AND armadillo_post.blog_id = '$blog_id' AND armadillo_post.date <= '$now'"
                . $filterByCategory . $filterByTag . $filterByAuthor . $filterByArchive 
                . " GROUP BY armadillo_post.id ORDER BY date " . $options['display_order'] . " LIMIT $numberOfHeadlines OFFSET $specificPost";

    $result = $dbLink->query($query);

    if ($dbLink->affected_rows > 0) {
        //TODO - check options to display as vertical list or responsive grid, and generate markup accordingly
        $listOfHeadlines = array();
        while ( $row = $result->fetch_array() ) {
            $listOfHeadlines[] = $row;
        }

        $headlinesMarkup = '';
        $headingTag = $options['heading_tag'];
        $previewLength = $options['post_preview_length'] + 1;
        $entrySpacing = $options['add_space_between_entries'] ? $options['entry_spacing'] . 'px' : '0' ;
        $previewFontSize = $options['display_post_text_preview'] ? $options['preview_text_font_size'] . 'em' : '' ;
        $readMoreIndicator = $options['display_post_text_preview'] ? $options['read_more_indicator']  : '';

        $normalizeChars = array(
            'Š'=>'S', 'š'=>'s', 'Ð'=>'Dj','Ž'=>'Z', 'ž'=>'z', 'À'=>'A', 'Á'=>'A', 'Â'=>'A', 'Ã'=>'A', 'Ä'=>'A',
            'Å'=>'A', 'Æ'=>'A', 'Ç'=>'C', 'È'=>'E', 'É'=>'E', 'Ê'=>'E', 'Ë'=>'E', 'Ì'=>'I', 'Í'=>'I', 'Î'=>'I',
            'Ï'=>'I', 'Ñ'=>'N', 'Ò'=>'O', 'Ó'=>'O', 'Ô'=>'O', 'Õ'=>'O', 'Ö'=>'O', 'Ø'=>'O', 'Ù'=>'U', 'Ú'=>'U',
            'Û'=>'U', 'Ü'=>'U', 'Ý'=>'Y', 'Þ'=>'B', 'ß'=>'Ss','à'=>'a', 'á'=>'a', 'â'=>'a', 'ã'=>'a', 'ä'=>'a',
            'å'=>'a', 'æ'=>'a', 'ç'=>'c', 'è'=>'e', 'é'=>'e', 'ê'=>'e', 'ë'=>'e', 'ì'=>'i', 'í'=>'i', 'î'=>'i',
            'ï'=>'i', 'ð'=>'o', 'ñ'=>'n', 'ò'=>'o', 'ó'=>'o', 'ô'=>'o', 'õ'=>'o', 'ö'=>'o', 'ø'=>'o', 'ù'=>'u',
            'ú'=>'u', 'û'=>'u', 'ü'=>'u', 'ý'=>'y', 'ý'=>'y', 'þ'=>'b', 'ÿ'=>'y', 'ƒ'=>'f',
            'ă'=>'a', 'î'=>'i', 'â'=>'a', 'ș'=>'s', 'ț'=>'t', 'Ă'=>'A', 'Î'=>'I', 'Â'=>'A', 'Ș'=>'S', 'Ț'=>'T',
        );

        foreach ($listOfHeadlines as $headlineDetails) {
            
            // Process Markdown if needed
            $headlineDetails['content'] = $headlineDetails['format'] == 'markdown' ? Parsedown::instance()->text($headlineDetails['content']) : $headlineDetails['content'] ;

            // Show post test preview? 
            if ($options['display_post_text_preview']) {
                $allowedTags = $options['allow_images'] ? '<img>' : '';
                $postPreview =  strip_tags($headlineDetails['content'], $allowedTags);

                // Shorten post text if necessary
                if (strlen($postPreview) > $previewLength) {
                    $postPreview = preg_replace('/\s+?(\S+)?$/', '', substr($postPreview, 0, $previewLength));
                }
            } elseif ($options['full_post_preview']) {
                $postPreview = $headlineDetails['content'];
            } else {
                $postPreview = '';
            }
            
            $titleInURL = preg_replace("/[&,.!?'\"]/", '', $headlineDetails['title']);
            $titleInURL = strtolower(preg_replace("/[\s]/", '-', $titleInURL));
            $titleInURL = strtr($titleInURL, $normalizeChars);

            $headlinesMarkup .= "<div class=\"armadilloBlogHeadlineEntry\">"
                . "<$headingTag><a href=\"" . $blogOptions['blog_url'] . "?post_id=" . $headlineDetails['id'] . "&title=" . $titleInURL . "\">" . $headlineDetails['title'] . "</a></$headingTag>"
                . "<div class=\"armadilloBlogHeadlinePreview\" style=\"padding-bottom: " . $entrySpacing . ";font-size: " . $previewFontSize . ";\">$postPreview "
                . "<a href=\"" . $blogOptions['blog_url'] . "?post_id=" . $headlineDetails['id'] . "&title=" . $titleInURL . "\">" . $readMoreIndicator . "</a></div>"
                . "</div>";

        }

        echo $headlinesMarkup;

    } else {
        echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_SELECTED_ITEM_MISSING');
    }

}
