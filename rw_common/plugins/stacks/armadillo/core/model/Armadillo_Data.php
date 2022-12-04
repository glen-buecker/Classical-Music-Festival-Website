<?php

class Armadillo_Data
{
    public static function configFileExists()
    {
        return file_exists('core/config.php');
    }

    public static function createConfigFile($settings)
    {
        //TODO: Add error checking here, in case we don't have permission to change files on the server.

        //Check if a config file already exists, continue if it doesn't
        if ( Armadillo_Data::configFileExists() and ( !isset($settings['updateConfigInfo']) or !isset($settings['recreateDatabase']) ) ) { return; } else {
            $path = 'core/config.php';

            $configFile = fopen($path, 'w');

            $configData = '<?php' . PHP_EOL . PHP_EOL;
            $configData .= '$dbHostname = \'' . $settings['dbHostname'] . '\';' . PHP_EOL;
            $configData .= '$dbName = \'' . $settings['dbName'] . '\';' . PHP_EOL;
            $configData .= '$dbUsername = \'' . $settings['dbUser'] . '\';' . PHP_EOL;
            $configData .= '$dbPassword = \'' . $settings['dbPassword'] . '\';' . PHP_EOL;
            $configData .= PHP_EOL . '?>';

            fwrite($configFile, $configData);

            fclose($configFile);
        }
    }

    public static function databaseSetupComplete()
    {
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';
            //if (!$dbLink) { echo "there's a problem with the dbLink variable."; return; }
            $postTableExists = $dbLink->query("DESC armadillo_post");
            $termTableExists = $dbLink->query("DESC armadillo_term");
            $termLookupTableExists = $dbLink->query("DESC armadillo_term_relationship");
            $userTableExists = $dbLink->query('DESC armadillo_user');
            $navigationTableExists = $dbLink->query('DESC armadillo_nav');
            $optionsTableExists = $dbLink->query('DESC armadillo_options');

            if ($postTableExists === FALSE or $termTableExists === FALSE or $termLookupTableExists === FALSE or $userTableExists === FALSE or $navigationTableExists === FALSE or $optionsTableExists === FALSE) {
                return FALSE;
            } else { return TRUE; }
        }
    }

    public static function createAdminUser($settings)
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            if ( !Armadillo_Data::adminUserAlreadyExists() ) {
                include 'core/config.php';
                include 'core/connectDB.php';
                $username = $dbLink->real_escape_string("admin");
                $role = $dbLink->real_escape_string("admin");
                $email = $dbLink->real_escape_string($settings['adminEmail']);
                $password = $dbLink->real_escape_string($settings['adminPassword']);
                $password = Armadillo_User::encryptPassword($password);
                $password = $dbLink->real_escape_string($password);
                $createAdmin = "INSERT INTO armadillo_user SET name='', username='$username', role='$role', email='$email', password='$password', failed_login_attempts='0', login_allowed_after='0'";
                if ( $dbLink->query($createAdmin) === FALSE ) {
                    return array('result' => 'FALSE', 'error' => 'Uh oh. There was an error creating the admin user: ' . $dbLink->error . PHP_EOL);
                } else {
                    Armadillo_Data::sendReminderEmail( $settings['adminEmail'], $settings['adminPassword'], 'setup' );

                    return array('result' => 'TRUE', 'error' => ''); }
            }
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function adminUserAlreadyExists()
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $userQuery = "SELECT * FROM armadillo_user WHERE role='admin'";
            $dbLink->query($userQuery);
            if ($dbLink->affected_rows > 0) { return TRUE; } else { return FALSE; }

        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function blogPageAlreadyExists()
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $blogQuery = "SELECT * FROM armadillo_post WHERE type='blog'";
            $dbLink->query($blogQuery);
            if ($dbLink->affected_rows > 0) { return TRUE; } else { return FALSE; }

        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function setupDefaultOptions($settings)
    {
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $timezone = $dbLink->real_escape_string($settings['timezone']);
            $site_language = $dbLink->real_escape_string($settings['site_language']);

            //TODO: Check if default options have already been entered

            $armadillo = Slim::getInstance();
            $armadillo = explode("_", $armadillo->getName());
            $armBuildVersion = end($armadillo);

            $query = "INSERT INTO armadillo_options SET
                        armadillo_build_version='$armBuildVersion',
                        timezone='$timezone',
                        site_language='$site_language',
                        allowed_login_attempts='10',
                        blocked_login_timeframe='120',
                        stylesheet_version='0',
                        enable_blog_content=TRUE,
                        enable_page_content=TRUE,
                        enable_solo_content=TRUE,
                        editor_type='richtext',
                        menu_display_option='showMenu',
                        navmenu_bgcolor='e4e4e4',
                        navmenu_textcolor='333333',
                        navmenu_hovercolor='ffffff',
                        navmenu_currentpage_bgcolor='cccccc',
                        navmenu_currentpage_textcolor='000000',
                        navmenu_dropdown_bgcolor='e4e4e4',
                        navmenu_dropdown_textcolor='000000',
                        navmenu_dropdown_hovercolor='ffffff',
                        default_content='none',
                        display_admin_link=TRUE,
                        adminlink_text='Login',
                        adminlink_color='0000ff',
                        adminlink_hovercolor='0000ff',
                        dropbox_sync_auth=FALSE,
                        display_social_sharing_links=FALSE,
                        social_sharing_code=''";

            $result = $dbLink->query($query);

            if ($result) { Armadillo_Data::generateArmadilloStylesheet($dbLink); return TRUE; } else { return FALSE; }
        }
    }

    public static function getCurrentOptions()
    {
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $query = "SELECT * FROM armadillo_options LIMIT 1";

            $result = $dbLink->query($query);

            $options = array();

            if ($result) { $options = $result->fetch_array(); }

            return $options;
        }
    }

    public static function generateArmadilloStylesheet($dbLink)
    {
        $query = "SELECT * FROM armadillo_options LIMIT 1";

        $result = $dbLink->query($query);

        if ($result) {

            $row = $result->fetch_array();

            $menuDisplay =  $row['menu_display_option'] == 'showMenu' ? 'block' : 'none';
            $adminLinkDisplay = $row['display_admin_link'] == FALSE ? 'none' : 'inline';

            $path = dirname(dirname(__FILE__)) . '/css/armadilloStyles.css';

            $stylesheetFile = fopen($path, 'w');

            $stylesheetData = "/* Base styles for the user-facing Armadillo navigation menu */" . PHP_EOL
                . "/* Armadillo is a self-contained, custom-built CMS for RapidWeavear. */" . PHP_EOL
                . "/* Copyright 2011-2015 NimbleHost. All rights reserved. */" . PHP_EOL
                . "#armadilloContentMenu { margin: 0 0 20px 0; display: " . $menuDisplay . "; }" . PHP_EOL
                . "#armadilloContentMenu ul { margin: 0 !important; padding: 0 !important; position: relative !important; list-style: none !important; display: block; height: 2.6em; border-radius: 3px; }" . PHP_EOL
                . "#armadilloContentMenu ul li { margin: 0 !important; padding: 0 !important; float: left; position: relative; }" . PHP_EOL
                . "#armadilloContentMenu ul li a { display: block; margin: 0.3em; padding: 0 0.5em; line-height: 2em; text-decoration: none; border-radius: 3px; }" . PHP_EOL
                . "#armadilloContentMenu ul .menuSectionLabel { margin: 5px 0;padding: 0;width: auto;line-height: 2em;background-color: transparent;font-size: 1.2em;font-family: sans-serif;text-align: center; }" . PHP_EOL . PHP_EOL 
                . "#armadilloContentMenu ul ul { padding: 0.3em !important; position: absolute !important; z-index: 99999; width: 10.6em; height: auto; }" . PHP_EOL
                . "#armadilloContentMenu ul ul .childIndicator { display: block; position: absolute; right: 5px; top: 0; line-height: 2em; font-weight: bold; }" . PHP_EOL
                . "#armadilloContentMenu ul li ul li { width: 10.6em; float: left; }" . PHP_EOL
                . "#armadilloContentMenu ul ul li a { margin: 0; }" . PHP_EOL . PHP_EOL 
                . "#armadilloContentMenu ul ul ul { left: 10.6em; top: auto; margin-top: -2.3em !important; }" . PHP_EOL . PHP_EOL 
                . ".armadilloClearer { clear: both; }" . PHP_EOL . PHP_EOL 
                . "#armadilloContentContainer { margin: 0 0 10px 0; padding: 0; }" . PHP_EOL . PHP_EOL 
                . "/* Hide Drop Down Menu Items */" . PHP_EOL
                . "#armadilloContentMenu ul ul , #armadilloContentMenu ul li:hover ul ul, #armadilloContentMenu ul li:hover ul ul ul, #armadilloContentMenu ul li:hover ul ul ul ul, #armadilloContentMenu ul li:hover ul ul ul ul ul, #armadilloContentMenu ul li:hover ul ul ul ul ul ul, #armadilloContentMenu ul li:hover ul ul ul ul ul ul ul, #armadilloContentMenu ul li:hover ul ul ul ul ul ul ul ul, #armadilloContentMenu ul li:hover ul ul ul ul ul ul ul ul ul { display: none; }" . PHP_EOL . PHP_EOL 
                . "/* Show on Hover */" . PHP_EOL
                . "#armadilloContentMenu ul li:hover ul, #armadilloContentMenu ul li li:hover ul, #armadilloContentMenu ul li li li:hover ul, #armadilloContentMenu ul li li li li:hover ul, #armadilloContentMenu ul li li li li li:hover ul, #armadilloContentMenu ul li li li li li li:hover ul, #armadilloContentMenu ul li li li li li li li:hover ul, #armadilloContentMenu ul li li li li li li li li:hover ul, #armadilloContentMenu ul li li li li li li li li li:hover ul { display: block; }" . PHP_EOL . PHP_EOL 
                . "/* User defined styles for the Armadillo navigation menu */" . PHP_EOL
                . "#armadilloContentMenu ul { background-color: #" . $row['navmenu_bgcolor'] . "; }" . PHP_EOL
                . "#armadilloContentMenu ul li a { color: #" . $row['navmenu_textcolor'] . "; }" . PHP_EOL
                . "#armadilloContentMenu ul li a:hover { background-color: #" . $row['navmenu_hovercolor'] . "; }" . PHP_EOL
                . "#armadilloContentMenu ul li a.current { background-color: #" . $row['navmenu_currentpage_bgcolor'] . "; color: #" . $row['navmenu_currentpage_textcolor'] . "; }" . PHP_EOL . PHP_EOL 
                . "#armadilloContentMenu ul ul { background-color: #" . $row['navmenu_dropdown_bgcolor'] . "; -webkit-box-shadow: 0 0 3px black; -moz-box-shadow: 0 0 3px black; box-shadow: 0 0 3px black; }" . PHP_EOL
                . "#armadilloContentMenu ul ul li a, #armadilloContentMenu ul ul .childIndicator { color: #" . $row['navmenu_dropdown_textcolor'] . "; }" . PHP_EOL
                . "#armadilloContentMenu ul ul li a:hover { background-color: #" . $row['navmenu_dropdown_hovercolor'] . "; }" . PHP_EOL
                . "#armadilloContentMenu ul ul li a.current { background-color: #" . $row['navmenu_currentpage_bgcolor'] . "; color:  #" . $row['navmenu_currentpage_textcolor'] . "; }" . PHP_EOL . PHP_EOL 
                . "/* Base styles for content displayed by Armadillo */" . PHP_EOL
                . ".hide { display: none; }" . PHP_EOL . PHP_EOL;

                //$stylesheetData .= "#armadilloContentContainer img[style*=\"float: left\"] { margin-right: 15px !important; margin-bottom: 5px !important; }" . PHP_EOL;
                //$stylesheetData .= "#armadilloContentContainer img[style*=\"float: right\"] { margin-left: 15px !important; margin-bottom: 5px !important; }" . PHP_EOL . PHP_EOL 

            // Calling the getAllBlogSettings() function in Armadillo_Content.php returns NULL for some reason
            // hence why we're duplicating the code here (horribly non-DRY, I know) as this at least works
            $blogSettingsQuery = "SELECT * FROM armadillo_post WHERE type='blog'";

            $blogSettingsResult = $dbLink->query($blogSettingsQuery);

            $blogSettings = array();

            if ($blogSettingsResult) {
                while ( $blogRow = $blogSettingsResult->fetch_array() ) {
                    $blogSettings[] = $blogRow;
                }
            }

            foreach ( $blogSettings as $blogDetails ) {

                $blogCategoriesDisplay = $blogDetails['display_blog_categories'] == FALSE ? 'none' : 'block';
                $blogEntryCategoriesDisplay = $blogCategoriesDisplay == 'block' ? 'inline' : 'none';
                $blogArchiveLinksDisplay = $blogDetails['display_blog_archive_links'] == FALSE ? 'none' : 'block';
                $blogTagsDisplay = $blogDetails['display_blog_tags'] == FALSE ? 'none' : 'block';
                $blogAuthorsDisplay = $blogDetails['display_blog_post_author'] == FALSE ? 'none' : 'block';


                $stylesheetData .= "/* Styles for the Armadillo blog with ID of " . $blogDetails['id'] . " and titled: " . $blogDetails['title'] . " */" . PHP_EOL
                    . ".armadilloBlog_" . $blogDetails['id'] . " .fetchedPostsContainer { display: none; opacity: 0; }" . PHP_EOL
                    . ".armadilloBlog_" . $blogDetails['id'] . " .fetchedPostsContainer > p { margin: 0; padding: 0; }" . PHP_EOL . PHP_EOL 
                    . ".armadilloBlog_" . $blogDetails['id'] . " .showMorePostsButton, .showMorePostsButton.armadilloBlog_" . $blogDetails['id'] . " { margin: 0; padding: 20px 0; text-align: center; }" . PHP_EOL
                    . ".armadilloBlog_" . $blogDetails['id'] . " .showMorePostsButton a, .showMorePostsButton.armadilloBlog_" . $blogDetails['id'] . " a { cursor: pointer; margin: 10px auto; padding: 10px; background-color: #" . $blogDetails['showmoreposts_button_bgcolor'] . "; color: #" . $blogDetails['showmoreposts_button_textcolor'] . "; text-decoration: none; }" . PHP_EOL . PHP_EOL 
                    . ".armadilloBlog_" . $blogDetails['id'] . " #blog-categories { display:" . $blogCategoriesDisplay . "; }" . PHP_EOL
                    . ".armadilloBlog_" . $blogDetails['id'] . " .blog-entry-category { display:" . $blogEntryCategoriesDisplay . "; }" . PHP_EOL
                    . ".armadilloBlog_" . $blogDetails['id'] . " #blog-archives { display:" . $blogArchiveLinksDisplay . "; }" . PHP_EOL
                    . ".armadilloBlog_" . $blogDetails['id'] . " ul.blog-tag-cloud, .armadilloBlog_" . $blogDetails['id'] . " p.blog-entry-tags { display:" . $blogTagsDisplay . "; }" . PHP_EOL 
                    . ".armadilloBlog_" . $blogDetails['id'] . " #blog-post-authors ul { display:" . $blogAuthorsDisplay . "; padding-left: 0; }" . PHP_EOL 
                    . ".armadilloBlog_" . $blogDetails['id'] . " #blog-post-authors ul li { list-style:none; }" . PHP_EOL . PHP_EOL;
            }


            $stylesheetData .= ".armadilloAdminLink { display: " . $adminLinkDisplay . "; color: #" . $row['adminlink_color'] . "; }" . PHP_EOL
                . ".armadilloAdminLink:hover { color: #" . $row['adminlink_hovercolor'] . "; }" . PHP_EOL 
                . "html.fancybox-lock { overflow: visible !important; }" . PHP_EOL . PHP_EOL 
                . "/* The following code is user-submitted custom css entered through the web interface. */" . PHP_EOL
                . $row['armadillo_custom_css'];

            fwrite($stylesheetFile, $stylesheetData);

            fclose($stylesheetFile);

            //Update stylesheet version in database
            $newVersion = $row['stylesheet_version'] + 1;
            $versionQuery = "UPDATE armadillo_options SET stylesheet_version='$newVersion'";
            $versionResult = $dbLink->query($versionQuery);
            if ($versionResult) { return TRUE; } else { return FALSE; }
        }
    }

    public static function updateRSSfeed($armadilloURL, $blog_id)
    {
        include dirname(__FILE__) . '/../config.php';
        include dirname(__FILE__) . '/../connectDB.php';
        include_once dirname(__FILE__) . '/Parsedown.php';

        if ($armadilloURL == '') {
            include dirname(__FILE__) . '/Armadillo_Dashboard.php';
            $armadilloURL = armadilloURL();
        }

        $query = "SELECT * FROM armadillo_post WHERE id='$blog_id'";
        $result = $dbLink->query($query);
        if ($result) {
            $options = $result->fetch_array();

            if ($options['enable_blog_rss'] and $options['blog_rss_filename'] != '' and !$options['blog_rss_enable_customfeed']) {
                //Get blog posts that should be added to rss feed
                $blogQuery = "SELECT * FROM armadillo_post WHERE publish=TRUE AND type='post' AND blog_id='$blog_id' ORDER BY date DESC";
                $blogResult = $dbLink->query($blogQuery);
                if ($blogResult) {
                    include_once dirname(__FILE__) . '/Armadillo_Dashboard.php';
                    $listOfPosts = array();
                    while ( $row = $blogResult->fetch_array() ) {
                        $listOfPosts[] = array('id' => $row['id'], 'title' => $row['title'], 'content' => $row['content'], 'date' => $row['date'], 'author' => $row['userid'], 'format' => $row['format']);
                    }

                    $terms = array();
                    $termQuery = "SELECT name, postid FROM armadillo_term
                                    LEFT JOIN armadillo_term_relationship ON armadillo_term.id = armadillo_term_relationship.termid
                                    WHERE type='category' ORDER BY name";
                    $termResult = $dbLink->query($termQuery);

                    if (!$termResult) {  }

                    while ( $termRow = $termResult->fetch_array() ) { $terms[] = array('termName' => $termRow['name'], 'postID' => $termRow['postid']); }

                    // Determine if the connection is secure, so generated links in the RSS feed match the website
                    $isSecure = false;
                    if (isset($_SERVER['HTTPS']) && $_SERVER['HTTPS'] == 'on') {
                        $isSecure = true;
                    }
                    elseif (!empty($_SERVER['HTTP_X_FORWARDED_PROTO']) && $_SERVER['HTTP_X_FORWARDED_PROTO'] == 'https' || !empty($_SERVER['HTTP_X_FORWARDED_SSL']) && $_SERVER['HTTP_X_FORWARDED_SSL'] == 'on') {
                        $isSecure = true;
                    }
                    $protocol = $isSecure ? 'https:' : 'http:';
                    // $siteURL = substr($armadilloURL, 0, strrpos($armadilloURL, '/'));
                    // $siteURL = substr($siteURL, 0, strrpos($siteURL, '/'));
                    // $siteURL = $protocol . $siteURL;
                    $siteURL = $options['blog_url'];

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

                    $rssData = "<?xml version=\"1.0\" encoding=\"UTF-8\"?>" . PHP_EOL;
                    $rssData .= "<rss version=\"2.0\" xmlns:content=\"http://purl.org/rss/1.0/modules/content/\" xmlns:atom=\"http://www.w3.org/2005/Atom\">" . PHP_EOL;
                    $rssData .= "<channel>" . PHP_EOL;
                    $rssData .= "<title><![CDATA[" . $options['blog_rss_title'] . "]]></title>" . PHP_EOL;
                    $rssData .= "<link>" . $protocol . $armadilloURL . "/" . $options['blog_rss_filename'] . "</link>" . PHP_EOL;
                    $rssData .= "<atom:link href=\"" . $protocol . $armadilloURL . "/" . $options['blog_rss_filename'] . "\" rel=\"self\" type=\"application/rss+xml\"/>" . PHP_EOL;
                    $rssData .= "<description><![CDATA[" . $options['blog_rss_description'] . "]]></description>" . PHP_EOL;
                    /* $rssData .= "<language></language>" . PHP_EOL; */
                    $rssData .= "<copyright>" . $options['blog_rss_copyright'] . "</copyright>" . PHP_EOL;
                    $rssData .= "<pubDate>" . date('r') . "</pubDate>" . PHP_EOL;
                    $rssData .= "<lastBuildDate>" . date('r') . "</lastBuildDate>" . PHP_EOL;
                    $rssData .= "<generator>Armadillo CMS for RapidWeaver</generator>" . PHP_EOL;
                    foreach ($listOfPosts as $post) {
                        /* Skip posts with publish dates that are in the future. */
                        if (strtotime( $post['date'] ) > time() ) { continue; }
                        //Convert Markdown to HTML if needed
                        $post['content'] = $post['format'] == 'markdown' ? Parsedown::instance()->text($post['content']) : $post['content'];
                        $titleInURL = preg_replace("/[&,.!?'\"]/", '', $post['title']);
                        $titleInURL = strtolower(preg_replace("/[\s]/", '-', $titleInURL));
                        $titleInURL = strtr($titleInURL, $normalizeChars);
                        $rssData .= "<item>" . PHP_EOL;
                        $rssData .= "<title><![CDATA[" . $post['title'] . "]]></title>" . PHP_EOL;
                        $rssData .= "<link>" . $siteURL . "?post_id&#61;" . $post['id'] . "&amp;title&#61;" . $titleInURL . "</link>" . PHP_EOL;
                        foreach ($terms as $term) { if ($term['postID'] == $post['id']) { $rssData .= "<category><![CDATA[" . $term['termName'] . "]]></category>" . PHP_EOL; } }
                        $rssData .= "<description></description>" . PHP_EOL;
                        $rssData .= "<guid>" . $siteURL . "?post_id&#61;" . $post['id'] . "</guid>" . PHP_EOL;
                        $rssData .= "<content:encoded><![CDATA[" . $post['content'] . "]]></content:encoded>" . PHP_EOL;
                        $rssData .= "<pubDate>" . date('r', strtotime($post['date'])) . "</pubDate>" . PHP_EOL;
                        $rssData .= "</item>" . PHP_EOL;
                    }

                    $rssData .= "</channel>" . PHP_EOL;
                    $rssData .= "</rss>" . PHP_EOL;

                    $feedPath = dirname(dirname(dirname(__FILE__))) . '/' . $options['blog_rss_filename'];
                    $feedFile = fopen($feedPath, 'w');
                    if ($feedFile) {
                        fwrite($feedFile, $rssData);
                        fclose($feedFile);

                        return TRUE;
                    } else { return FALSE; }
                } else { return FALSE; }
            } elseif ($options['enable_blog_rss'] and $options['blog_rss_enable_customfeed']) {
                return TRUE;
            } elseif (!$options['enable_blog_rss']) {
                return TRUE;
            } else { return FALSE; }
        } else { return FALSE; }
    }

    public static function setupDB($settings)
    {
        include 'core/config.php';
        include 'core/connectDB.php';

        $notification = '';
        $tablesSetup = 0;
        $adminUserCreated = FALSE;
        $postTableExists = $dbLink->query("DESC armadillo_post");

        if ($postTableExists === FALSE) {
            $createPostTable = 'CREATE TABLE armadillo_post (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                title VARCHAR(255),
                content MEDIUMTEXT,
                sidebar_content MEDIUMTEXT,
                summary_content TEXT,
                meta_content TEXT,
                date DATETIME NOT NULL,
                last_edited DATETIME NOT NULL,
                publish BOOL NOT NULL,
                userid INT NOT NULL,
                type VARCHAR(255),
                default_content BOOL NOT NULL,
                display_comments BOOL NOT NULL,
                display_summary BOOL NOT NULL,
                format VARCHAR(128),
                blog_id INT NOT NULL,
                blog_url VARCHAR(255),
                blog_date_format VARCHAR(128),
                blogposts_per_page INT,
                display_blog_comments BOOL NOT NULL,
                display_blog_categories BOOL NOT NULL,
                display_blog_tags BOOL NOT NULL,
                display_blog_archive_links BOOL NOT NULL,
                display_blog_post_author BOOL NOT NULL,
                disqus_shortname VARCHAR(255),
                blog_categories_title VARCHAR(128),
                blog_categories_separator VARCHAR(128),
                blog_tags_title VARCHAR(128),
                blog_tags_separator VARCHAR(128),
                blog_archive_links_format VARCHAR(128),
                post_author_display_name VARCHAR(128),
                blog_readmore_text VARCHAR(255),
                showmoreposts_button_text VARCHAR(255),
                showmoreposts_button_bgcolor VARCHAR(255),
                showmoreposts_button_textcolor VARCHAR(255),
                enable_blog_rss BOOL NOT NULL,
                blog_rss_title VARCHAR(255),
                blog_rss_description VARCHAR(255),
                blog_rss_copyright VARCHAR(128),
                blog_rss_linkname VARCHAR(128),
                blog_rss_filename VARCHAR(128),
                blog_rss_enable_customfeed BOOL NOT NULL,
                blog_rss_customfeed_url VARCHAR(255),
                blog_rss_summarize_entries BOOL NOT NULL
                ) DEFAULT CHARACTER SET utf8';

            if ( $dbLink->query($createPostTable) === FALSE ) {
                $notification .= Armadillo_Language::msg('ARM_DB_TABLE_CREATE_ERROR_NOTIFICATION') . $dbLink->error . PHP_EOL;
            } else { ++$tablesSetup; }
        } else { ++$tablesSetup; }

        $termTableExists = $dbLink->query("DESC armadillo_term");
        if ($termTableExists === FALSE) {
            $createTermTable = 'CREATE TABLE armadillo_term (
                id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                name VARCHAR(128),
                type VARCHAR(64),
                CONSTRAINT name_type_uk UNIQUE ( name, type )
                ) DEFAULT CHARACTER SET utf8';

            if ( $dbLink->query($createTermTable) === FALSE ) {
                $notification .= Armadillo_Language::msg('ARM_DB_TABLE_CREATE_ERROR_NOTIFICATION') . $dbLink->error . PHP_EOL;
            } else { ++$tablesSetup; }
        } else { ++$tablesSetup; }

        $termLookupTableExists = $dbLink->query("DESC armadillo_term_relationship");
        if ($termLookupTableExists === FALSE) {
            $createTermLookupTable = 'CREATE TABLE armadillo_term_relationship (
                postid INT NOT NULL,
                termid INT NOT NULL,
                PRIMARY KEY (postid, termid)
                ) DEFAULT CHARACTER SET utf8';

            if ( $dbLink->query($createTermLookupTable) === FALSE ) {
                $notification .= Armadillo_Language::msg('ARM_DB_TABLE_CREATE_ERROR_NOTIFICATION') . $dbLink->error . PHP_EOL;
            } else { ++$tablesSetup; }
        } else { ++$tablesSetup; }

        $userTableExists = $dbLink->query('DESC armadillo_user');
        if ($userTableExists === FALSE) {
            $createTable = 'CREATE TABLE armadillo_user (
                id INT NOT NULL AUTO_INCREMENT,
                name VARCHAR(255) NOT NULL,
                email VARCHAR(255),
                username VARCHAR(255) NOT NULL,
                password TINYBLOB NOT NULL,
                role VARCHAR(255) NOT NULL,
                language VARCHAR(64),
                token VARCHAR(255),
                user_IP VARCHAR(64),
                failed_login_attempts INT NOT NULL,
                login_allowed_after INT NOT NULL,
                PRIMARY KEY (id, username)
                ) DEFAULT CHARACTER SET utf8';

            if ( $dbLink->query($createTable) === FALSE ) {
                $notification .= Armadillo_Language::msg('ARM_DB_TABLE_CREATE_ERROR_NOTIFICATION') . $dbLink->error . PHP_EOL;
            } else { ++$tablesSetup; }
        } else { ++$tablesSetup; }

        $navigationTableExists = $dbLink->query('DESC armadillo_nav');
        if ($navigationTableExists === FALSE) {
            $createTable = 'CREATE TABLE armadillo_nav (
                parentid INT,
                pageid INT NOT NULL,
                position INT NOT NULL
                ) DEFAULT CHARACTER SET utf8';

            if ( $dbLink->query($createTable) === FALSE ) {
                $notification .= Armadillo_Language::msg('ARM_DB_TABLE_CREATE_ERROR_NOTIFICATION') . $dbLink->error . PHP_EOL;
            } else { ++$tablesSetup; }
        } else { ++$tablesSetup; }

        $optionsTableExists = $dbLink->query('DESC armadillo_options');
        if ($optionsTableExists === FALSE) {
            $createTable = 'CREATE TABLE armadillo_options (
                armadillo_build_version INT NOT NULL,
                timezone VARCHAR(255),
                site_language VARCHAR(128),
                allowed_login_attempts INT NOT NULL,
                blocked_login_timeframe INT NOT NULL,
                stylesheet_version INT,
                enable_blog_content BOOL NOT NULL,
                enable_page_content BOOL NOT NULL,
                enable_solo_content BOOL NOT NULL,
                editor_type VARCHAR(128),
                menu_display_option VARCHAR(255),
                site_main_nav_container VARCHAR(255),
                site_second_nav_container VARCHAR(255),
                site_third_nav_container VARCHAR(255),
                navmenu_bgcolor VARCHAR(255),
                navmenu_textcolor VARCHAR(255),
                navmenu_hovercolor VARCHAR(255),
                navmenu_currentpage_bgcolor VARCHAR(255),
                navmenu_currentpage_textcolor VARCHAR(255),
                navmenu_dropdown_bgcolor VARCHAR(255),
                navmenu_dropdown_textcolor VARCHAR(255),
                navmenu_dropdown_hovercolor VARCHAR(255),
                display_admin_link BOOL NOT NULL,
                adminlink_text VARCHAR(255),
                adminlink_color VARCHAR(255),
                adminlink_hovercolor VARCHAR(255),
                default_content VARCHAR(128),
                armadillo_custom_css TEXT,
                dropbox_token TINYTEXT,
                dropbox_sync_auth BOOL NOT NULL,
                display_social_sharing_links BOOL NOT NULL,
                social_sharing_code TEXT
                ) DEFAULT CHARACTER SET utf8';

            if ( $dbLink->query($createTable) === FALSE ) {
                $notification .= Armadillo_Language::msg('ARM_DB_TABLE_CREATE_ERROR_NOTIFICATION') . $dbLink->error . PHP_EOL;
            } else { ++$tablesSetup; }
        } else { ++$tablesSetup; }

        if ($notification !== '' || $tablesSetup !== 6) {
            $armadillo = Slim::getInstance();
            $armadillo->flash("notification", "$notification");

            return FALSE;
        } else { return TRUE; }
    }

    public static function createInitialBlogPage()
    {
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            if ( !Armadillo_Data::blogPageAlreadyExists() ) {
                $pageQuery = "INSERT INTO armadillo_post SET
                                title='Blog',
                                content='',
                                date='" . date('Y-m-d H:i:s') . "',
                                userid='1',
                                publish=TRUE,
                                type='blog',
                                default_content=FALSE,
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
                                blog_rss_filename='feed.rss',
                                blog_rss_enable_customfeed=FALSE,
                                blog_rss_customfeed_url='',
                                blog_rss_summarize_entries=FALSE";

                $pageResult = $dbLink->query($pageQuery);

                if ($pageResult) {
                    $navQuery = "INSERT INTO armadillo_nav SET
                                    parentid='0',
                                    pageid=LAST_INSERT_ID(),
                                    position='0'";
                    $navResult = $dbLink->query($navQuery);
                }
            }
        }
    }

    public static function installContentEditor()
    {
        $scriptsFolder = dirname(dirname(__FILE__)) . '/scripts/';
        if ( file_exists($scriptsFolder . 'redactor.zip') and !file_exists($scriptsFolder . 'redactor') ) {
            if ( class_exists('ZipArchive') ) {
                $zip = new ZipArchive();
                $contentEditor = $zip->open($scriptsFolder . 'redactor.zip');
                if ($contentEditor === true) {
                    $zip->extractTo($scriptsFolder);
                    $zip->close();
                } else {  }
            } else {
                require_once(dirname(__FILE__) . '/pclzip.lib.php');
                $contentEditor = new PclZip($scriptsFolder . 'redactor.zip');
                $contentEditor->extract(PCLZIP_OPT_PATH, $scriptsFolder);
            }
        }
    }

    public static function installDropboxSupport()
    {
        if (version_compare(PHP_VERSION, '5.3.1') >= 0) {
            $modelFolder = dirname(__FILE__);
            if ( file_exists($modelFolder . '/Dropbox.zip') and !file_exists($modelFolder . '/Dropbox') ) {
                if ( class_exists('ZipArchive') ) {
                    $zip = new ZipArchive();
                    $dropbox = $zip->open($modelFolder . '/Dropbox.zip');
                    if ($dropbox === true) {
                        $zip->extractTo($modelFolder);
                        $zip->close();
                    } else {  }
                } else {
                    require_once(dirname(__FILE__) . '/pclzip.lib.php');
                    $dropbox = new PclZip($modelFolder . '/Dropbox.zip');
                    $dropbox->extract(PCLZIP_OPT_PATH, $modelFolder);
                }
            }

            return TRUE;
        } else { return FALSE; }
    }

    public static function installArmadillo($settings)
    {
        $armadillo = Slim::getInstance();
        $formComplete = TRUE;
        foreach ($settings as $formField => $value) {
            $armadillo->view()->setData($formField, $value);
            if ( empty($formField) ) { $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_SETUP_ALL_FORM_FIELDS_REQUIRED')); $formComplete = FALSE; }
        }

        //Check that admin passwords match
        if ($settings['adminPassword'] !== $settings['adminConfirmPassword']) {
            $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_SETUP_ADMIN_PASSWORDS_NO_MATCH'));
            $formComplete = FALSE;
        }

        //If form is complete, check that Armadillo can actually connect to the database with the submitted information
        if ($formComplete) {
            if ( !Armadillo_Data::dbSettingsAreCorrect($settings) ) {
                $formComplete = FALSE;
                $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_SETUP_DB_DETAILS_INCORRECT'));
            }
        }

        //If submitted information is complete, create config file and setup database
        if ($formComplete) {
            Armadillo_Data::createConfigFile($settings);
            Armadillo_Data::setupDB($settings);
            Armadillo_Data::setupDefaultOptions($settings);
            //Create Admin user
            $adminUserCreated = Armadillo_Data::createAdminUser($settings);
            if ($adminUserCreated['result'] === "FALSE") { $armadillo->flash('notification', $adminUserCreated['error']); } else { $armadillo->flash('notification', Armadillo_Language::msg('ARM_SETUP_SUCCESSFUL')); }
            //Create Initial Blog page, add it to the armadillo_post database table
            Armadillo_Data::createInitialBlogPage();
            Armadillo_Data::installContentEditor();
        }

        return $formComplete;
    }

    public static function isFunctionAvailable($func)
    {
        if (ini_get('safe_mode')) return false;
        $disabled = ini_get('disable_functions');
        if ($disabled) {
            $disabled = explode(',', $disabled);
            $disabled = array_map('trim', $disabled);

            return !in_array($func, $disabled);
        }

        return true;
    }

    public static function create_zip($files = array(),$destination = '',$overwrite = false)
    {
        /* Based on the script by David Walsh http://davidwalsh.name/create-zip-php
           Customized by Jonathan Head of NimbleHost to use PclZip if PHP's built-in Zip class isn't available*/

        //if the zip file already exists and overwrite is false, return false
        if (file_exists($destination) && !$overwrite) { return false; }
        //vars
        $valid_files = array();
        //if files were passed in...
        if (is_array($files)) {
            //cycle through each file
            foreach ($files as $file) {
                //make sure the file exists
                if (file_exists($file)) {
                    $valid_files[] = $file;
                }
            }
        }
        //if we have good files...
        if (count($valid_files)) {
            //create the archive
            if ( class_exists('ZipArchive') ) {
                $zip = new ZipArchive();
                if ($zip->open($destination,$overwrite ? ZIPARCHIVE::OVERWRITE : ZIPARCHIVE::CREATE) !== true) {
                    return false;
                }
                //add the files
                foreach ($valid_files as $file) {
                    $new_filename = substr($file,strrpos($file,'/') + 1);
                    $zip->addFile($file,$new_filename);
                }
                //debug
                //echo 'The zip archive contains ',$zip->numFiles,' files with a status of ',$zip->status;

                //close the zip -- done!
                $zip->close();

                //check to make sure the file exists
                return file_exists($destination);
            } else {
                require_once(dirname(__FILE__) . '/pclzip.lib.php');
                $pclZip = new PclZip($destination);
                $pclZip->create($files, PCLZIP_OPT_REMOVE_ALL_PATH);

                return file_exists($destination);
            }
        } else { return false; }
    }

    public static function backupDatabase()
    {
        if ( Armadillo_Data::isFunctionAvailable('exec') ) {
            include 'core/config.php';
            $backupFolder = dirname(dirname(dirname(__FILE__))) . '/backup/';
            $backupDate = date('Y-m-d_H.i.s');
            $makeFolder = mkdir($backupFolder . '/' . $backupDate, 0755, true);
            if ($makeFolder) {
                $backupFile = $backupFolder . $backupDate . '/' . $_SERVER['HTTP_HOST'] . '_armadillo_backup_' . $backupDate . '.sql';
                $command = "mysqldump --host=$dbHostname --user=$dbUsername --password=$dbPassword $dbName > $backupFile";
                exec( $command );
                $fileSaved = ( file_exists($backupFile) and (filesize($backupFile) > 512) ) ? TRUE : FALSE ;
                if ($fileSaved) { echo Armadillo_Language::msg('ARM_BACKUP_DATABASE_PROGRESS'); return TRUE; } else { echo Armadillo_Language::msg('ARM_BACKUP_DATABASE_FAILED_TEXT'); return FALSE; }
            } else { return FALSE; }
        } else { echo Armadillo_Language::msg('ARM_BACKUP_DATABASE_HOSTING_INCOMPATIBLE'); return FALSE; }
    }

    public static function backupInfo()
    {
        $backupFolder = dirname(dirname(dirname(__FILE__))) . '/backup/';
        if (!file_exists($backupFolder)) { mkdir($backupFolder, 0755, true); }
        $listOfBackups = array_diff(scandir($backupFolder, 1), array('..', '.'));
        if (is_array($listOfBackups) and !empty($listOfBackups)) {
            rsort($listOfBackups);
            $dbBackupFile = array_diff(scandir($backupFolder . '/' . $listOfBackups[0], 1), array('..', '.'));
            if ( !empty($dbBackupFile) ) {
                rsort($dbBackupFile);
                $latest = explode('_', $listOfBackups[0]);
                echo Armadillo_Language::msg('ARM_BACKUP_DATABASE_LAST_COMPLETED') . strftime( '%A, %B %e, %G ', strtotime( $latest[0] ) ) . "(" . Armadillo_Language::msg('ARM_BACKUP_DATABASE_LAST_COMPLETED_TIMESTAMP') . " - " . $latest[1] . "). ";
                $dbBackupFilePath = array();
                $dbBackupFilePath[] = $backupFolder . $listOfBackups[0] . '/' . $dbBackupFile[0];
                $dbBackupFileName = substr($dbBackupFile[0],0,-3);
                $destination = substr($dbBackupFilePath[0],0,-3);
                $destination .= 'zip';
                Armadillo_Data::create_zip($dbBackupFilePath, $destination);
                echo "<a href='" . armadilloURL() . "/backup/" . $listOfBackups[0] . '/' . $dbBackupFileName . "zip'>" . Armadillo_Language::msg('ARM_BACKUP_DATABASE_DOWNLOAD_LATEST') . "</a> | ";
            }
        } else { echo Armadillo_Language::msg('ARM_BACKUP_DATABASE_NO_BACKUP_FOUND'); }
        if ( Armadillo_Data::isFunctionAvailable('exec') ) { echo "<a href='" . armadilloURL() . "/index.php/backup/'>" . Armadillo_Language::msg('ARM_BACKUP_DATABASE_BACKUP_NOW') . "</a>"; } else { echo Armadillo_Language::msg('ARM_BACKUP_DATABASE_HOSTING_INCOMPATIBLE'); }
    }

    public static function updateArmadillo($type, $version)
    {
        function rrmdir($dir) {
            if (is_dir($dir)){
                $files = scandir($dir);
                foreach ($files as $file){
                    if ($file != '.' && $file != '..') {
                        if (filetype($dir.'/'.$file) == 'dir') {
                            rrmdir($dir.'/'.$file);
                        } else {
                            unlink($dir.'/'.$file);
                        }
                    }
                }
                reset($files);
                rmdir($dir);
            }
        }

        include 'core/config.php';
        include 'core/connectDB.php';
        $armadillo = Slim::getInstance();
        $databaseUpdates = array();
        $updatedBuild = '';
        // Get options stored in database
        $armadilloOptions = Armadillo_Data::getCurrentOptions();
        if ($version < 38) {
            $updatedBuild = 38;
            //Required Database Updates for Build 38
            $databaseUpdates[] = 'ALTER TABLE armadillo_post ADD COLUMN display_summary BOOL NOT NULL';
            $databaseUpdates[] = 'UPDATE armadillo_post SET display_summary=FALSE';
            $databaseUpdates[] = 'ALTER TABLE armadillo_options
                                    ADD COLUMN armadillo_build_version INT NOT NULL,
                                    ADD COLUMN display_blog_categories BOOL NOT NULL,
                                    ADD COLUMN display_blog_tags BOOL NOT NULL,
                                    ADD COLUMN blog_categories_title VARCHAR(128),
                                    ADD COLUMN blog_categories_separator VARCHAR(128),
                                    ADD COLUMN blog_tags_title VARCHAR(128),
                                    ADD COLUMN blog_tags_separator VARCHAR(128),
                                    ADD COLUMN blog_readmore_text VARCHAR(255)';
            $databaseUpdates[] = "UPDATE armadillo_options SET
                                    armadillo_build_version='$updatedBuild',
                                    display_blog_categories=FALSE,
                                    display_blog_tags=FALSE,
                                    blog_categories_title='Filed in: ',
                                    blog_categories_separator=' | ',
                                    blog_tags_title='Tags: ',
                                    blog_tags_separator=', ',
                                    blog_readmore_text='Read more'";
            $databaseUpdates[] = 'CREATE TABLE armadillo_term (
                                    id INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    name VARCHAR(128),
                                    type VARCHAR(64),
                                    CONSTRAINT name_type_uk UNIQUE ( name, type )
                                    ) DEFAULT CHARACTER SET utf8';
            $databaseUpdates[] = 'CREATE TABLE armadillo_term_relationship (
                                    postid INT NOT NULL,
                                    termid INT NOT NULL,
                                    PRIMARY KEY (postid, termid)
                                    ) DEFAULT CHARACTER SET utf8';
        }

        if ($version < 45) {
            $updatedBuild = 45;
            //Required Database Updates for Build 45
            $databaseUpdates[] = 'ALTER TABLE armadillo_options
                                    ADD COLUMN enable_blog_rss BOOL NOT NULL,
                                    ADD COLUMN blog_rss_title VARCHAR(255),
                                    ADD COLUMN blog_rss_description VARCHAR(255),
                                    ADD COLUMN blog_rss_copyright VARCHAR(128),
                                    ADD COLUMN blog_rss_linkname VARCHAR(128),
                                    ADD COLUMN blog_rss_filename VARCHAR(128),
                                    ADD COLUMN blog_rss_enable_customfeed BOOL NOT NULL,
                                    ADD COLUMN blog_rss_customfeed_url VARCHAR(255),
                                    ADD COLUMN blog_rss_summarize_entries BOOL NOT NULL';
            $databaseUpdates[] = "UPDATE armadillo_options SET
                                    armadillo_build_version='$updatedBuild',
                                    enable_blog_rss=TRUE,
                                    blog_rss_title='My RSS Feed',
                                    blog_rss_description='',
                                    blog_rss_copyright='',
                                    blog_rss_linkname='RSS Feed',
                                    blog_rss_filename='feed.rss',
                                    blog_rss_enable_customfeed=FALSE,
                                    blog_rss_customfeed_url='',
                                    blog_rss_summarize_entries=FALSE";
        }

        if ($version < 48) {
            $updatedBuild = 48;
            //Required Database Updates for Build 48
            $databaseUpdates[] = 'ALTER TABLE armadillo_options
                                    ADD COLUMN display_blog_post_author BOOL NOT NULL,
                                    ADD COLUMN post_author_display_name VARCHAR(128)';
            $databaseUpdates[] = "UPDATE armadillo_options SET
                                    armadillo_build_version='$updatedBuild',
                                    display_blog_post_author=FALSE,
                                    post_author_display_name='fullname'";
        }

        if ($version < 62) {
            $updatedBuild = 62;
            //Required Database Updates for Build 62
            $databaseUpdates[] = 'ALTER TABLE armadillo_user ADD COLUMN language VARCHAR(64)';
            $databaseUpdates[] = "UPDATE armadillo_options SET armadillo_build_version='$updatedBuild'";
        }

        if ($version < 67) {
            $updatedBuild = 67;
            //Required Database Updates for Build 67
            $databaseUpdates[] = 'ALTER TABLE armadillo_options
                                    ADD COLUMN dropbox_token TINYTEXT,
                                    ADD COLUMN dropbox_sync_auth BOOL NOT NULL';
            $databaseUpdates[] = "UPDATE armadillo_options SET
                                    armadillo_build_version='$updatedBuild',
                                    dropbox_sync_auth=FALSE";
        }

        if ($version < 73) {
            $updatedBuild = 73;

            //Remove old localization files
            $langDirectory = dirname(dirname(__FILE__)) . '/lang';

            rrmdir($langDirectory);
            //Install new localization files
            Armadillo_Language::installLanguages();

            $databaseUpdates[] = "UPDATE armadillo_options SET armadillo_build_version='$updatedBuild'";
        }

        if ($version < 79) {
            $updatedBuild = 79;

            //Remove old localization files
            $redactorDirectory = dirname(dirname(__FILE__)) . '/scripts/redactor';

            rrmdir($redactorDirectory);
            //Install new editor files
            Armadillo_Data::installContentEditor();

            $databaseUpdates[] = "UPDATE armadillo_options SET armadillo_build_version='$updatedBuild'";
        }

        if ($version < 213) {
            $updatedBuild = 213;
            //Required Database Updates for Build 213
            $databaseUpdates[] = 'ALTER TABLE armadillo_options
                                    ADD COLUMN blog_date_format VARCHAR(128),
                                    ADD COLUMN site_language VARCHAR(128)';
            $databaseUpdates[] = 'ALTER TABLE armadillo_post ADD COLUMN summary_content TEXT,
                                    ADD COLUMN meta_content TEXT,
                                    MODIFY content MEDIUMTEXT,
                                    MODIFY sidebar_content MEDIUMTEXT';
            $databaseUpdates[] = "UPDATE armadillo_options SET 
                                    armadillo_build_version='$updatedBuild',
                                    blog_date_format='WMDY',
                                    site_language='en'";
        }

        if ($version < 291) {
            $updatedBuild = 291;
            //Required Database Updates for Build 291
            $databaseUpdates[] = 'ALTER TABLE armadillo_options 
                                    ADD COLUMN allowed_login_attempts INT NOT NULL,
                                    ADD COLUMN blocked_login_timeframe INT NOT NULL';
            $databaseUpdates[] = 'ALTER TABLE armadillo_user 
                                    ADD COLUMN user_IP VARCHAR(64),
                                    ADD COLUMN failed_login_attempts INT NOT NULL,
                                    ADD COLUMN login_allowed_after INT NOT NULL';
            $databaseUpdates[] = "UPDATE armadillo_options SET 
                                    armadillo_build_version='$updatedBuild',
                                    allowed_login_attempts='10',
                                    blocked_login_timeframe='120'";
            $databaseUpdates[] = "UPDATE armadillo_user SET 
                                    failed_login_attempts='0',
                                    login_allowed_after='0'";
        }

        if ($version < 402) {
            $updatedBuild = 402;
            //Required Database Updates for Build 402
            $databaseUpdates[] = 'ALTER TABLE armadillo_options 
                                    ADD COLUMN display_blog_archive_links BOOL NOT NULL,
                                    ADD COLUMN blog_archive_links_format VARCHAR(128),
                                    ADD COLUMN display_social_sharing_links BOOL NOT NULL,
                                    ADD COLUMN social_sharing_code TEXT';
            $databaseUpdates[] = "UPDATE armadillo_options SET 
                                    armadillo_build_version='$updatedBuild',
                                    display_blog_archive_links=TRUE,
                                    blog_archive_links_format='MY',
                                    display_social_sharing_links=FALSE,
                                    social_sharing_code=''";
        }

        if ($version < 538) {
            $updatedBuild = 538;
            //Required Database Updates for Build 538
            $databaseUpdates[] = 'ALTER TABLE armadillo_post 
                                    ADD COLUMN last_edited DATETIME NOT NULL';
            $databaseUpdates[] = "UPDATE armadillo_options SET 
                                    armadillo_build_version='$updatedBuild'";
        }

        if ($version < 724) {
            $updatedBuild = 724;
            //Required Database Updates for Build 724
            $databaseUpdates[] = 'ALTER TABLE armadillo_post 
                                    ADD COLUMN format VARCHAR(128)';
            $databaseUpdates[] = 'ALTER TABLE armadillo_options
                                    ADD COLUMN editor_type VARCHAR(128)';
            $databaseUpdates[] = "UPDATE armadillo_post SET
                                    format='html'";
            $databaseUpdates[] = "UPDATE armadillo_options SET 
                                    armadillo_build_version='$updatedBuild',
                                    editor_type='richtext'";
        }

        if ($version < 738) {
            $updatedBuild = 738;
            //Required Database Updates for Build 738
            $databaseUpdates[] = 'ALTER TABLE armadillo_options
                                    ADD COLUMN enable_blog_content BOOL NOT NULL,
                                    ADD COLUMN enable_page_content BOOL NOT NULL,
                                    ADD COLUMN enable_solo_content BOOL NOT NULL,
                                    ADD COLUMN blog_url VARCHAR(255)';
            $databaseUpdates[] = "UPDATE armadillo_options SET 
                                    armadillo_build_version='$updatedBuild',
                                    enable_blog_content=TRUE,
                                    enable_page_content=TRUE,
                                    enable_solo_content=TRUE,
                                    blog_url=''";
        }
        if ($version < 775) {
            $updatedBuild = 775;
            //Required Database Updates for Build 775
            $databaseUpdates[] = 'ALTER TABLE armadillo_options
                                    ADD COLUMN default_content VARCHAR(255)';
            $databaseUpdates[] = "UPDATE armadillo_options SET 
                                    armadillo_build_version='$updatedBuild',
                                    default_content='enabled'";
        }

        if ($version < 1018) {
            $updatedBuild = 1018;
            //Required Database Updates for Build 1018
            $databaseUpdates[] = 'ALTER TABLE armadillo_post
                                    ADD COLUMN blog_id INT NOT NULL,
                                    ADD COLUMN blog_url VARCHAR(255),
                                    ADD COLUMN blog_date_format VARCHAR(128),
                                    ADD COLUMN blogposts_per_page INT,
                                    ADD COLUMN display_blog_comments BOOL NOT NULL,
                                    ADD COLUMN display_blog_categories BOOL NOT NULL,
                                    ADD COLUMN display_blog_tags BOOL NOT NULL,
                                    ADD COLUMN display_blog_archive_links BOOL NOT NULL,
                                    ADD COLUMN display_blog_post_author BOOL NOT NULL,
                                    ADD COLUMN disqus_shortname VARCHAR(255),
                                    ADD COLUMN blog_categories_title VARCHAR(128),
                                    ADD COLUMN blog_categories_separator VARCHAR(128),
                                    ADD COLUMN blog_tags_title VARCHAR(128),
                                    ADD COLUMN blog_tags_separator VARCHAR(128),
                                    ADD COLUMN blog_archive_links_format VARCHAR(128),
                                    ADD COLUMN post_author_display_name VARCHAR(128),
                                    ADD COLUMN blog_readmore_text VARCHAR(255),
                                    ADD COLUMN showmoreposts_button_text VARCHAR(255),
                                    ADD COLUMN showmoreposts_button_bgcolor VARCHAR(255),
                                    ADD COLUMN showmoreposts_button_textcolor VARCHAR(255),
                                    ADD COLUMN enable_blog_rss BOOL NOT NULL,
                                    ADD COLUMN blog_rss_title VARCHAR(255),
                                    ADD COLUMN blog_rss_description VARCHAR(255),
                                    ADD COLUMN blog_rss_copyright VARCHAR(128),
                                    ADD COLUMN blog_rss_linkname VARCHAR(128),
                                    ADD COLUMN blog_rss_filename VARCHAR(128),
                                    ADD COLUMN blog_rss_enable_customfeed BOOL NOT NULL,
                                    ADD COLUMN blog_rss_customfeed_url VARCHAR(255),
                                    ADD COLUMN blog_rss_summarize_entries BOOL NOT NULL';
            $databaseUpdates[] = "UPDATE armadillo_post SET
                                    blog_id=1 WHERE type='post'";

            // Some blog-related armadillo options might not be set yet, depending on what version a person is using
            // when performing a database upgrade. Check for these:
            $armadilloOptions['blog_url'] = isset($armadilloOptions['blog_url']) ? $armadilloOptions['blog_url'] : '' ;
            $armadilloOptions['blog_date_format'] = isset($armadilloOptions['blog_date_format']) ? $armadilloOptions['blog_date_format'] : 'WMDY' ;
            $armadilloOptions['display_blog_archive_links'] = isset($armadilloOptions['display_blog_archive_links']) ? $armadilloOptions['display_blog_archive_links'] : true ;
            $armadilloOptions['blog_archive_links_format'] = isset($armadilloOptions['blog_archive_links_format']) ? $armadilloOptions['blog_archive_links_format'] : 'MY' ;

            $databaseUpdates[] = "UPDATE armadillo_post SET
                                    blog_url='" . $armadilloOptions['blog_url'] . "',
                                    blog_date_format='" . $armadilloOptions['blog_date_format'] . "',
                                    blogposts_per_page=" . $armadilloOptions['blogposts_per_page'] .",
                                    display_blog_comments=" . $armadilloOptions['display_blog_comments'] .",
                                    display_blog_categories=" . $armadilloOptions['display_blog_categories'] .",
                                    display_blog_tags=" . $armadilloOptions['display_blog_tags'] .",
                                    display_blog_archive_links=" . $armadilloOptions['display_blog_archive_links'] .",
                                    display_blog_post_author=" . $armadilloOptions['display_blog_post_author'] .",
                                    disqus_shortname='" . $armadilloOptions['disqus_shortname'] . "',
                                    blog_categories_title='" . $armadilloOptions['blog_categories_title'] . "',
                                    blog_categories_separator='" . $armadilloOptions['blog_categories_separator'] . "',
                                    blog_tags_title='" . $armadilloOptions['blog_tags_title'] . "',
                                    blog_tags_separator='" . $armadilloOptions['blog_tags_separator'] . "',
                                    blog_archive_links_format='" . $armadilloOptions['blog_archive_links_format'] . "',
                                    post_author_display_name='" . $armadilloOptions['post_author_display_name'] . "',
                                    blog_readmore_text='" . $armadilloOptions['blog_readmore_text'] . "',
                                    showmoreposts_button_text='" . $armadilloOptions['showmoreposts_button_text'] . "',
                                    showmoreposts_button_bgcolor='" . $armadilloOptions['showmoreposts_button_bgcolor'] . "',
                                    showmoreposts_button_textcolor='" . $armadilloOptions['showmoreposts_button_textcolor'] . "',
                                    enable_blog_rss=" . $armadilloOptions['enable_blog_rss'] .",
                                    blog_rss_title='" . $armadilloOptions['blog_rss_title'] . "',
                                    blog_rss_description='" . $armadilloOptions['blog_rss_description'] . "',
                                    blog_rss_copyright='" . $armadilloOptions['blog_rss_copyright'] . "',
                                    blog_rss_linkname='" . $armadilloOptions['blog_rss_linkname'] . "',
                                    blog_rss_filename='" . $armadilloOptions['blog_rss_filename'] . "',
                                    blog_rss_enable_customfeed=" . $armadilloOptions['blog_rss_enable_customfeed'] .",
                                    blog_rss_customfeed_url='" . $armadilloOptions['blog_rss_customfeed_url'] . "',
                                    blog_rss_summarize_entries=" . $armadilloOptions['blog_rss_summarize_entries'] ."
                                    WHERE type='blog'";
            $databaseUpdates[] = "UPDATE armadillo_options SET 
                                    armadillo_build_version='$updatedBuild'";
        }

        $complete = false;
        if ($type === 'default') {
            //Continue with update only if backup file is successfully saved.
            if ( Armadillo_Data::backupDatabase() ) {
                $errors = 0;
                foreach ($databaseUpdates as $update) {
                    $result = $dbLink->query($update);
                    if (!$result) { $errors++; }
                }
                if ($errors != 0) { echo Armadillo_Language::msg('ARM_UPGRADE_DATABASE_INCOMPLETE'); } else { echo Armadillo_Language::msg('ARM_UPGRADE_DATABASE_PROGRESS'); $complete = true; }
            } else { echo Armadillo_Language::msg('ARM_UPGRADE_DATABASE_BACKUP_FAILED') . '<p><a href="' . armadilloURL() . '/index.php/update/no_backup/" class="redButton">' . Armadillo_Language::msg('ARM_CONTINUE_TEXT') . '</a></p>'; }
        } elseif ($type === 'force') {
            //Try to backup database, but update will continue even if backup fails
            echo Armadillo_Language::msg('ARM_UPGRADE_DATABASE_TRY_BACKUP_AGAIN');
            echo Armadillo_Data::backupDatabase();
            $errors = 0;
            foreach ($databaseUpdates as $update) {
                $result = $dbLink->query($update);
                if (!$result) { $errors++; }
            }
            if ($errors != 0) { echo Armadillo_Language::msg('ARM_UPGRADE_DATABASE_INCOMPLETE'); } else { echo Armadillo_Language::msg('ARM_UPGRADE_DATABASE_PROGRESS'); $complete = true;  }
        }
        if ($complete) { $_SESSION['armBuildVersion'] = $updatedBuild; echo Armadillo_Language::msg('ARM_UPGRADE_DATABASE_FINISHED'); }
    }

    public static function dbSettingsAreCorrect($settings)
    {
        $dbLink = @new mysqli($settings['dbHostname'], $settings['dbUser'], $settings['dbPassword'], $settings['dbName']);
        if ($dbLink->connect_errno) { return FALSE; } else { return TRUE; }
    }

    // Create a menu with properly nested menu items
    public static function createNestedList( $dbLink, $parentid=0, $counter=0 )
    {
        if ($counter == 0 )
             echo "<div id='armadilloContentMenu'><ol>";

        // retrieve all children of $parent
        $query = "SELECT pageid, title, position FROM armadillo_nav INNER JOIN armadillo_post ON pageid=id WHERE parentid='$parentid' AND type='page' ORDER BY position ASC";
        $result = $dbLink->query($query);
        while ($row = $result->fetch_array()) {
            $res 	= 	$dbLink->query ( "SELECT pageid, title, position FROM armadillo_nav INNER JOIN armadillo_post ON pageid=id WHERE parentid='" . $row['pageid'] . "' AND type='page' ORDER BY position ASC" );
            $tot 	= 	$res->num_rows;
            $ul 	=  	$tot == 0 ? "":'<ol>';
            $_ul 	= 	$tot == 0 ? "":'</ol>';
            echo "<li id='pageID_" . $row['pageid'] . "'><div><a href='#' class='armadilloMenuItem armadilloPage'>" . $row['title'] ."</a></div>";
            echo "{$ul}";
            Armadillo_Data::createNestedList( $dbLink, $row['pageid'], (int)$counter+1 );
            echo "{$_ul}";
            echo "</li>";
        }

        if ($counter == 0 )
            echo "</ol></div>" ;
    }

    //Generates a menu based upon the pages/posts currently published
    public static function displayMenu()
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $query = "SELECT id, title FROM armadillo_post WHERE type='page'";

            $result = $dbLink->query($query);

            if (!$result) {	echo Armadillo_Language::msg('ARM_CONTENT_DISPLAY_DB_ERROR') . $dbLink->error; }

            $menuItems = $dbLink->affected_rows;

            if ($menuItems >= 0) { Armadillo_Data::createNestedList( $dbLink ); }
        }
    }

    public static function sendReminderEmail( $email, $password, $situation )
    {
        $armadillo = Slim::getInstance();

        //include dirname(__FILE__) . "/Armadillo_Dashboard.php";
        $subject = '';
        $message = '';
        $token = '';
        $expire = '';
        $url = '';
        $mailSent = FALSE;

        if ($situation === 'setup') {
            $subject = Armadillo_Language::msg('ARM_SENDMAIL_SETUP_COMPLETE_SUBJECT');
            $url = 'http:' . armadilloUrl() . "/index.php/";
            $message1 = Armadillo_Language::msg('ARM_SENDMAIL_SETUP_COMPLETE_MESSAGE_LINE1') . PHP_EOL . PHP_EOL .
                        Armadillo_Language::msg('ARM_SENDMAIL_SETUP_COMPLETE_MESSAGE_LINE2') . "admin" . PHP_EOL . PHP_EOL .
                        Armadillo_Language::msg('ARM_SENDMAIL_SETUP_COMPLETE_MESSAGE_LINE4') . PHP_EOL . PHP_EOL .
                        Armadillo_Language::msg('ARM_SENDMAIL_SETUP_COMPLETE_MESSAGE_LINE5') . PHP_EOL . PHP_EOL . $url . PHP_EOL . PHP_EOL;

            $message2 = Armadillo_Language::msg('ARM_SENDMAIL_SETUP_COMPLETE_MESSAGE_LINE1') . PHP_EOL . PHP_EOL .
                        Armadillo_Language::msg('ARM_SENDMAIL_SETUP_COMPLETE_MESSAGE_LINE3') . " " . $password . PHP_EOL . PHP_EOL .
                        Armadillo_Language::msg('ARM_SENDMAIL_SETUP_COMPLETE_MESSAGE_LINE4') . PHP_EOL . PHP_EOL .
                        Armadillo_Language::msg('ARM_SENDMAIL_SETUP_COMPLETE_MESSAGE_LINE5') . PHP_EOL . PHP_EOL . $url . PHP_EOL . PHP_EOL;

            if ( mail( $email, $subject, $message1 ) && mail( $email, $subject, $message2 ) ) { $mailSent = TRUE; } else { $mailSent = FALSE; }

        } elseif ($situation === 'reset') {
            if ( file_exists('core/config.php') ) {
                // Security check, make sure email is a valid address
                if ( filter_var($email, FILTER_VALIDATE_EMAIL) ) {
                    include 'core/config.php';
                    include 'core/connectDB.php';

                    $email = $dbLink->real_escape_string($email);

                    $query = "SELECT * FROM armadillo_user WHERE email='$email'";

                    $result = $dbLink->query($query);

                    $numberOfUsers = 0;

                    if ($result) {	$numberOfUsers = $dbLink->affected_rows; }

                    if ($numberOfUsers > 0) {
                        $subject = Armadillo_Language::msg('ARM_SENDMAIL_RESET_LOGIN_SUBJECT');
                        $token = generateRandStr(24);
                        $expire = time() + (60 * 60 * 3);
                        $url = 'http:' . armadilloUrl() . "/index.php/login/reset/$email/$token/$expire/";
                        $message = Armadillo_Language::msg('ARM_SENDMAIL_RESET_LOGIN_MESSAGE_LINE1') . PHP_EOL . PHP_EOL .
                                    Armadillo_Language::msg('ARM_SENDMAIL_RESET_LOGIN_MESSAGE_LINE2') . PHP_EOL . PHP_EOL . $url . PHP_EOL . PHP_EOL .
                                    Armadillo_Language::msg('ARM_SENDMAIL_RESET_LOGIN_MESSAGE_LINE3') . PHP_EOL . PHP_EOL .
                                    Armadillo_Language::msg('ARM_SENDMAIL_RESET_LOGIN_MESSAGE_LINE4');
                        if ( Armadillo_User::saveToken($token, $email) ) {
                            if ( mail( $email, $subject, $message ) ) {
                                $mailSent = TRUE;
                                $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_SENDMAIL_RESET_LOGIN_MESSAGE_SENT'));
                            } else {
                                $mailSent = FALSE;
                                $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_SENDMAIL_RESET_LOGIN_MESSAGE_FAILED'));
                            }
                        } else {
                            $mailSent = FALSE;
                            $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_SENDMAIL_RESET_LOGIN_TOKEN_FAILURE'));
                        }
                    } else {
                        $mailSent = FALSE;
                        $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_SENDMAIL_RESET_LOGIN_ACCOUNT_MISSING'));
                    }
                } else {
                    $mailSent = FALSE;
                    $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_SENDMAIL_RESET_LOGIN_ACCOUNT_MISSING'));
                }
            }
        }

        return $mailSent;
    }

    public static function resetLoginPassword( $userID, $password, $confirmPassword, $token )
    {
        $armadillo = Slim::getInstance();

        $resetComplete = FALSE;

        if ( $password == $confirmPassword and ( $password != '' and $confirmPassword != '' ) ) {
            if ( file_exists('core/config.php') ) {
                include 'core/config.php';
                include 'core/connectDB.php';

                $userID = $dbLink->real_escape_string($userID);
                $password = $dbLink->real_escape_string($password);
                $password = Armadillo_User::encryptPassword($password);
                $password = $dbLink->real_escape_string($password);
                $token = $dbLink->real_escape_string($token);

                $resetQuery = "UPDATE armadillo_user SET password='$password', token='' WHERE id='$userID' AND token='$token'";
                $resetResult = $dbLink->query($resetQuery);

                $clearTokenQuery = "UPDATE armadillo_user SET token='' WHERE token='$token'";
                $clearTokenResult = $dbLink->query($clearTokenQuery);

                if ($resetResult and $clearTokenResult) { $resetComplete = TRUE; } else { $resetComplete = FALSE; }
            }
        } else {
            if ($password != $confirmPassword) { $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_RESET_LOGIN_FORM_PASSWORD_MISMATCH')); } else { $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_RESET_LOGIN_FORM_BOTH_FIELDS_REQUIRED')); }
            $resetComplete = FALSE;
        }

        return $resetComplete;
    }

}
