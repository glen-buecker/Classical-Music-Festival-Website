<?php
//$armadillo = Slim::getInstance();

if ($_REQUEST['action'] == 'getPHPinfo') { getPHPinfo(); }
if ($_REQUEST['action'] == 'checkDBsettings') { checkDBsettings(); }
if ($_REQUEST['action'] == 'updateDisplayOptions') { updateDisplayOptions(); }
if ($_REQUEST['action'] == 'updateNavOrder') { updateNavOrder(); }
if ($_REQUEST['action'] == 'updateTimezoneLanguageAndColors') { updateTimezoneLanguageAndColors(); }
if ($_REQUEST['action'] == 'updateDefaultContentDisplayed') { updateDefaultContent(); }
if ($_REQUEST['action'] == 'updateCustomStyles') { updateCustomStyles(); }
if ($_REQUEST['action'] == 'updateStylesheet') { updateStylesheet(); }
if ($_REQUEST['action'] == 'updateTerms') { updateTerms('update'); }
if ($_REQUEST['action'] == 'deleteTerm') { updateTerms('delete'); }
if ($_REQUEST['action'] == 'syncBackupToDropbox') { syncBackupToDropbox(); }
if ($_REQUEST['action'] == 'refreshMediaSummary') { refreshMediaSummary($_REQUEST['armURL']); }
if ($_POST['action'] == 'updateContent') { updateContent($_POST['format'],$_POST['contentID'],$_POST['content']); }
if ($_POST['action'] == 'createContent') { createContent($_POST['contentType'],$_POST['contentID']); }

if ($_REQUEST['action'] == 'updateAllOptions') { if ( updateAllOptions() ) { return TRUE; } else { return FALSE; } }

function getPHPinfo() { phpinfo(); }

// Listed on the PHP manual site submitted by user Daniel Klein - http://php.net/manual/en/function.preg-grep.php
function preg_grep_keys($pattern, $input, $flags = 0) {
    return array_intersect_key($input, array_flip(preg_grep($pattern, array_keys($input), $flags)));
}

function checkDBsettings()
{
    include dirname(__FILE__) . '/Armadillo_Data.php';
    $settings = array('dbHostname' => $_REQUEST['dbHostname'], 'dbName' => $_REQUEST['dbName'], 'dbUser' => $_REQUEST['dbUser'], 'dbPassword' => $_REQUEST['dbPassword']);
    if ( Armadillo_Data::dbSettingsAreCorrect($settings) ) { echo "true"; } else { echo "false"; }
}

function updateAllOptions()
{
    if ( file_exists( dirname(__FILE__) . '/../config.php' ) ) {
        require_once dirname(__FILE__) . '/../config.php';
        require_once dirname(__FILE__) . '/../connectDB.php';

        // Display Options
        $enableBlogContent = $_REQUEST['enableBlogContent'] == null ? "FALSE" : "TRUE";
        $enablePageContent = $_REQUEST['enablePageContent'] == null ? "FALSE" : "TRUE";
        $enableSoloContent = $_REQUEST['enableSoloContent'] == null ? "FALSE" : "TRUE";
        $menuDisplayOption = $dbLink->real_escape_string($_REQUEST['menuDisplayOptions']);
        $mainNavContainer = $dbLink->real_escape_string($_REQUEST['mainNavContainer']);
        $secondNavContainer = $dbLink->real_escape_string($_REQUEST['secondNavContainer']);
        $thirdNavContainer = $dbLink->real_escape_string($_REQUEST['thirdNavContainer']);
        $displayAdminLink = $dbLink->real_escape_string($_REQUEST['displayAdminLink']);
        $adminLinkText = $dbLink->real_escape_string($_REQUEST['adminLinkText']);

        //Navigation Menu Order - v1.6.0 moved to a separate ajax call
        //$navOrderSaved = updateNavOrder($dbLink);

        // Timezone and Color Options
        $timezone = $dbLink->real_escape_string($_REQUEST['timezone']);
        $siteLanguage = $dbLink->real_escape_string($_REQUEST['siteLanguage']);
        $navMenuBgColor = $dbLink->real_escape_string($_REQUEST['navMenuBgColor']);
        $navMenuTextColor = $dbLink->real_escape_string($_REQUEST['navMenuTextColor']);
        $navMenuHoverColor = $dbLink->real_escape_string($_REQUEST['navMenuHoverColor']);
        $navMenuCurrentPageBgColor = $dbLink->real_escape_string($_REQUEST['navMenuCurrentPageBgColor']);
        $navMenuCurrentPageTextColor = $dbLink->real_escape_string($_REQUEST['navMenuCurrentPageTextColor']);
        $navMenuDropDownBgColor = $dbLink->real_escape_string($_REQUEST['navMenuDropDownBgColor']);
        $navMenuDropDownTextColor = $dbLink->real_escape_string($_REQUEST['navMenuDropDownTextColor']);
        $navMenuDropDownHoverColor = $dbLink->real_escape_string($_REQUEST['navMenuDropDownHoverColor']);
        $adminLinkColor = $dbLink->real_escape_string($_REQUEST['adminLinkColor']);
        $adminLinkHoverColor = $dbLink->real_escape_string($_REQUEST['adminLinkHoverColor']);

        //Default Content to be Displayed
        $defaultContentSaved = updateDefaultContent($dbLink);
        $defaultContent = $dbLink->real_escape_string($_REQUEST['defaultContentDisplayed']);
        //$defaultContent = is_numeric($defaultContent) ? "enabled" : $defaultContent;

        //Editor Type
        $editorType = $dbLink->real_escape_string($_REQUEST['editorType']);

        //Grab list of blogs and convert to an array
        $blogList = $dbLink->real_escape_string($_REQUEST['blogList']);
        $blogList = explode(',', rtrim($blogList, ','));

        //Variable to store errors which may occur when saving blog options
        $blogOptionsErrors = 0;
        $rssUpdateErrors = 0;

        //Loop through and retrieve respective blog options
        foreach ($blogList as $blogID) {
            //Specify pattern to search $_REQUEST
            $blogIDPattern = '/blogID_' . $blogID . '-/';
            //Specify prefix to actually retrieve values of submitted options
            $blogIDPrefix = 'blogID_' . $blogID . '-';
            //Retrieve only those options for a single blog
            $blogOptions = preg_grep_keys($blogIDPattern, $_REQUEST);

            //Blog Options
            $blogUrl = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogUrl']);
            $blogDateFormat = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogDateFormat']);
            $blogPostsPerPage = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogPostsPerPage']);
            $displayBlogComments = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'displayBlogComments']);
            $displayBlogCategories = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'displayBlogCategories']);
            $displayBlogTags = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'displayBlogTags']);
            $displayBlogArchiveLinks = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'displayBlogArchiveLinks']);
            $displayBlogPostAuthor = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'displayPostAuthor']);
            $disqusShortname = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'disqusShortname']);
            $categoriesTitle = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'categoriesTitle']);
            $categoriesSeparator = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'categoriesSeparator']);
            $tagsTitle = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'tagsTitle']);
            $tagsSeparator = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'tagsSeparator']);
            $blogArchiveLinksFormat = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogArchiveLinksFormat']);
            $postAuthorDisplayName = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'postAuthorName']);
            $readMoreText = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'readMoreText']);
            $showMorePostsButtonText = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'showMorePostsButtonText']);
            $showMorePostsButtonBackground = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'showMorePostsButtonBackground']);
            $showMorePostsButtonTextColor = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'showMorePostsButtonTextColor']);
            $enableBlogRSS = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'enableBlogRSS']);
            $blogRSStitle = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogRSStitle']);
            $blogRSSdescription = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogRSSdescription']);
            $blogRSScopyright = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogRSScopyright']);
            $blogRSSlinkName = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogRSSlinkName']);
            $blogRSSfileName = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogRSSfileName']);
            $blogRSSenableCustomFeed = $blogOptions[$blogIDPrefix . 'blogRSSenableCustomFeed'] == null ? "FALSE" : "TRUE"; //$dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogRSSenableCustomFeed']);
            $blogRSScustomFeedURL = $dbLink->real_escape_string($blogOptions[$blogIDPrefix . 'blogRSScustomFeedURL']);

            //Store query needed to update the relevant blog's settings
            $blogOptionsQuery = "UPDATE armadillo_post SET
                                    blog_url='$blogUrl',
                                    blog_date_format='$blogDateFormat',
                                    blogposts_per_page='$blogPostsPerPage',
                                    display_blog_comments=$displayBlogComments,
                                    display_blog_categories=$displayBlogCategories,
                                    display_blog_tags=$displayBlogTags,
                                    display_blog_archive_links=$displayBlogArchiveLinks,
                                    display_blog_post_author=$displayBlogPostAuthor,
                                    disqus_shortname='$disqusShortname',
                                    blog_categories_title='$categoriesTitle',
                                    blog_categories_separator='$categoriesSeparator',
                                    blog_tags_title='$tagsTitle',
                                    blog_tags_separator='$tagsSeparator',
                                    blog_archive_links_format='$blogArchiveLinksFormat',
                                    post_author_display_name='$postAuthorDisplayName',
                                    blog_readmore_text='$readMoreText',
                                    showmoreposts_button_text='$showMorePostsButtonText',
                                    showmoreposts_button_bgcolor='$showMorePostsButtonBackground',
                                    showmoreposts_button_textcolor='$showMorePostsButtonTextColor',
                                    enable_blog_rss=$enableBlogRSS,
                                    blog_rss_title='$blogRSStitle',
                                    blog_rss_description='$blogRSSdescription',
                                    blog_rss_copyright='$blogRSScopyright',
                                    blog_rss_linkname='$blogRSSlinkName',
                                    blog_rss_filename='$blogRSSfileName',
                                    blog_rss_enable_customfeed=$blogRSSenableCustomFeed,
                                    blog_rss_customfeed_url='$blogRSScustomFeedURL'
                                    WHERE id='$blogID'";

            $blogOptionsResult = $dbLink->query($blogOptionsQuery);

            if ($blogOptionsResult) {
                $rssUpdateErrors = updateRSSfeed($_COOKIE['armadillo']['armURL'], $blogID) ? $rssUpdateErrors : ++$rssUpdateErrors;
            } else {
                ++$blogOptionsErrors;
            }

        }

        //Blog Options
        // $blogUrl = $dbLink->real_escape_string($_REQUEST['blogUrl']);
        // $blogDateFormat = $dbLink->real_escape_string($_REQUEST['blogDateFormat']);
        // $blogPostsPerPage = $dbLink->real_escape_string($_REQUEST['blogPostsPerPage']);
        // $displayBlogComments = $dbLink->real_escape_string($_REQUEST['displayBlogComments']);
        // $displayBlogCategories = $dbLink->real_escape_string($_REQUEST['displayBlogCategories']);
        // $displayBlogTags = $dbLink->real_escape_string($_REQUEST['displayBlogTags']);
        // $displayBlogArchiveLinks = $dbLink->real_escape_string($_REQUEST['displayBlogArchiveLinks']);
        // $displayBlogPostAuthor = $dbLink->real_escape_string($_REQUEST['displayPostAuthor']);
        // $disqusShortname = $dbLink->real_escape_string($_REQUEST['disqusShortname']);
        // $categoriesTitle = $dbLink->real_escape_string($_REQUEST['categoriesTitle']);
        // $categoriesSeparator = $dbLink->real_escape_string($_REQUEST['categoriesSeparator']);
        // $tagsTitle = $dbLink->real_escape_string($_REQUEST['tagsTitle']);
        // $tagsSeparator = $dbLink->real_escape_string($_REQUEST['tagsSeparator']);
        // $blogArchiveLinksFormat = $dbLink->real_escape_string($_REQUEST['blogArchiveLinksFormat']);
        // $postAuthorDisplayName = $dbLink->real_escape_string($_REQUEST['postAuthorName']);
        // $readMoreText = $dbLink->real_escape_string($_REQUEST['readMoreText']);
        // $showMorePostsButtonText = $dbLink->real_escape_string($_REQUEST['showMorePostsButtonText']);
        // $showMorePostsButtonBackground = $dbLink->real_escape_string($_REQUEST['showMorePostsButtonBackground']);
        // $showMorePostsButtonTextColor = $dbLink->real_escape_string($_REQUEST['showMorePostsButtonTextColor']);
        // $enableBlogRSS = $dbLink->real_escape_string($_REQUEST['enableBlogRSS']);
        // $blogRSStitle = $dbLink->real_escape_string($_REQUEST['blogRSStitle']);
        // $blogRSSdescription = $dbLink->real_escape_string($_REQUEST['blogRSSdescription']);
        // $blogRSScopyright = $dbLink->real_escape_string($_REQUEST['blogRSScopyright']);
        // $blogRSSlinkName = $dbLink->real_escape_string($_REQUEST['blogRSSlinkName']);
        // $blogRSSfileName = $dbLink->real_escape_string($_REQUEST['blogRSSfileName']);
        // $blogRSSenableCustomFeed = $_REQUEST['blogRSSenableCustomFeed'] == null ? "FALSE" : "TRUE"; //$dbLink->real_escape_string($_REQUEST['blogRSSenableCustomFeed']);
        // $blogRSScustomFeedURL = $dbLink->real_escape_string($_REQUEST['blogRSScustomFeedURL']);

        // Custom Styles
        $armadilloCustomStyles = $dbLink->real_escape_string($_REQUEST['armadilloCustomStyles']);

        // Social Sharing Code Snippet
        $displaySocialSharingLinks = $dbLink->real_escape_string($_REQUEST['displaySocialSharingLinks']);
        $socialSharingCode = $dbLink->real_escape_string($_REQUEST['armadilloSocialSharingCode']);

        // Security Settings
        $allowedLoginAttempts = $dbLink->real_escape_string($_REQUEST['allowedLoginAttempts']);
        $blockedLoginTimeframe = $dbLink->real_escape_string($_REQUEST['blockedLoginTimeframe']);

        $saveOptionsQuery = "UPDATE armadillo_options SET
                                timezone='$timezone',
                                site_language='$siteLanguage',
                                allowed_login_attempts='$allowedLoginAttempts',
                                blocked_login_timeframe='$blockedLoginTimeframe',
                                enable_blog_content=$enableBlogContent,
                                enable_page_content=$enablePageContent,
                                enable_solo_content=$enableSoloContent,
                                editor_type='$editorType',
                                menu_display_option='$menuDisplayOption',
                                site_main_nav_container='$mainNavContainer',
                                site_second_nav_container='$secondNavContainer',
                                site_third_nav_container='$thirdNavContainer',
                                default_content='$defaultContent',
                                display_admin_link=$displayAdminLink,
                                adminlink_text='$adminLinkText',
                                navmenu_bgcolor='$navMenuBgColor',
                                navmenu_textcolor='$navMenuTextColor',
                                navmenu_hovercolor='$navMenuHoverColor',
                                navmenu_currentpage_bgcolor='$navMenuCurrentPageBgColor',
                                navmenu_currentpage_textcolor='$navMenuCurrentPageTextColor',
                                navmenu_dropdown_bgcolor='$navMenuDropDownBgColor',
                                navmenu_dropdown_textcolor='$navMenuDropDownTextColor',
                                navmenu_dropdown_hovercolor='$navMenuDropDownHoverColor',
                                adminlink_color='$adminLinkColor',
                                adminlink_hovercolor='$adminLinkHoverColor',
                                armadillo_custom_css='$armadilloCustomStyles',
                                display_social_sharing_links=$displaySocialSharingLinks,
                                social_sharing_code='$socialSharingCode'";

        $saveOptionsResult = $dbLink->query($saveOptionsQuery);

        switch ($siteLanguage) {
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

        if ($saveOptionsResult and $defaultContentSaved) { //and $navOrderSaved
            $result = $rssUpdateErrors == 0 ? "true" : "rssNotUpdated";
            updateStylesheet($dbLink);
            unset($_SESSION['dateFormat']);
            unset($_SESSION['siteLanguage']);
            unset($_SESSION['editorType']);
            $_SESSION['dateFormat'] = $blogDateFormat;
            $_SESSION['siteLanguage'] = $siteLanguage;
            $_SESSION['editorType'] = $editorType;
            setrawcookie('armadillo[editorType]', $editorType, time()+28800, '/');
            echo $result;
        } else { echo "false"; }
    } else { echo "false"; }
}

function updateNavOrder()
{
    if ( file_exists( dirname(__FILE__) . '/../config.php' ) ) {
        require_once dirname(__FILE__) . '/../config.php';
        require_once dirname(__FILE__) . '/../connectDB.php';
        if ( isset($_REQUEST['pageID']) ) {
            $saveResult = FALSE;

            $i = 0;
            foreach ($_REQUEST['pageID'] as $key => $value) {
                $pageid = $key;
                $parentid = $value;
                $order = $i;
                $i++;
                    if ($parentid == "root") {
                        $parentid = "0";
                    }

                $query[$pageid] = "UPDATE armadillo_nav SET position = $order, parentid = $parentid WHERE pageid = $pageid";
                $result = $dbLink->query($query[$pageid]);
                if (!$result) { $saveResult = FALSE; } else { $saveResult = TRUE; }

            }

            //if (!$result) { $armadillo->flash('notification', 'There was a problem saving the navigation menu settings.'); }
            //else { $armadillo->flash('notification', 'The navigation menu settings have been successfully saved.'); }
            echo $saveResult;
        } else { echo TRUE; }
    } else { echo FALSE; }
}

function updateDefaultContent($dbLink)
{
    $defaultContent = $dbLink->real_escape_string($_REQUEST['defaultContentDisplayed']);

    $resetDefaultContentQuery = "UPDATE armadillo_post SET default_content=FALSE";
    $updateDefaultContentQuery = $defaultContent === "none" ? 'DESC armadillo_options' : "UPDATE armadillo_post SET default_content=TRUE WHERE id='$defaultContent'";

    $resetResult = $dbLink->query($resetDefaultContentQuery);
    $updateResult = FALSE;

    if ($resetResult) { $updateResult = $dbLink->query($updateDefaultContentQuery); }

    if (!$updateResult) { return FALSE; } else { return TRUE; }
}

function updateStylesheet($dbLink)
{
    include_once dirname(__FILE__) . '/Armadillo_Data.php';

    return Armadillo_Data::generateArmadilloStylesheet($dbLink);
}

function updateTerms($action='')
{
    if ( file_exists( dirname(__FILE__) . '/../config.php' ) ) {
        require_once dirname(__FILE__) . '/../config.php';
        require_once dirname(__FILE__) . '/../connectDB.php';

        $termID = explode("_", $_REQUEST['id']);
        $termID = end($termID);

        // Term info to update
        $termID = $dbLink->real_escape_string($termID);

        if ( $action === 'delete' ) {
            $updateTermsQuery = "DELETE at, atr FROM armadillo_term AS at
                                INNER JOIN armadillo_term_relationship AS atr 
                                ON at.id = atr.termid WHERE at.id = $termID";
            $removeTermOnlyQuery = "DELETE FROM armadillo_term WHERE id = $termID";
        } else {
            $termValue = $dbLink->real_escape_string($_REQUEST['value']);
            $updateTermsQuery = "UPDATE armadillo_term SET name='$termValue' WHERE id=$termID";
        }

        $updateTermsResult = $dbLink->query($updateTermsQuery);

        if ($updateTermsResult) {
            if ( $action === 'delete' ) {
                if ( $dbLink->affected_rows == 0 ) {
                    // Term isn't associated with any content, so initial query didn't return any results to delete
                    // Run second query to remove term only 
                    $removeTermOnlyResult = $dbLink->query($removeTermOnlyQuery);
                    if ($removeTermOnlyResult) {
                        echo $termID;
                    }
                } else {
                    echo $termID;
                }
            } else {
                echo $termValue;
            }
        } else {  }

    } else { }
}

function updateRSSfeed($armadilloURL, $blog_id)
{
    include_once dirname(__FILE__) . '/Armadillo_Data.php';

    return Armadillo_Data::updateRSSfeed($armadilloURL, $blog_id);
}

function syncBackupToDropbox()
{
    $dropboxSyncFile = dirname(__FILE__) . '/Dropbox/dropboxBackupSync.php';
    if ( file_exists($dropboxSyncFile) ) {
        require_once $dropboxSyncFile;
        $syncResult = syncToDropbox();
        echo "true";
    } else { echo "false"; }
}

function refreshMediaSummary($armadilloURL='')
{
    include_once dirname(__FILE__) . '/Armadillo_Media.php';
    $armadilloURL = $armadilloURL != '' ? $armadilloURL : $_SESSION['armURL'];
    echo Armadillo_Media::getSummary('', $armadilloURL);
}

function updateContent( $format, $contentID, $content )
{
    if ( file_exists( dirname(__FILE__) . '/../config.php' ) ) {
        require_once dirname(__FILE__) . '/../config.php';
        require_once dirname(__FILE__) . '/../connectDB.php';

        $format = $dbLink->real_escape_string($format);
        $content = $dbLink->real_escape_string($content);
        $contentID = $dbLink->real_escape_string($contentID);
        $now = date('Y-m-d H:i:s');
        $query = "UPDATE armadillo_post SET content='$content', format='$format', last_edited='$now' WHERE id='$contentID'";
        $result = $dbLink->query($query);

        if ($result and ( $dbLink->affected_rows == 1 ) ) {
            echo "true";
        } else {
            echo "false: " . $dbLink->error;
        }
    } else {
        echo "config file missing";
    }
}

//Creates specified content
function createContent($type,$id='')
{
    if ( file_exists( dirname(__FILE__) . '/../config.php' ) ) {
        require_once dirname(__FILE__) . '/../config.php';
        require_once dirname(__FILE__) . '/../connectDB.php';

        if ( ( isset($_COOKIE['armadillo']['bootMe']) and $_COOKIE['armadillo']['bootMe'] > time() 
                and isset($_COOKIE['armadillo']['loggedIn']) and $_COOKIE['armadillo']['loggedIn'] == 'TRUE' ) 
            and ( isset($_COOKIE['armadillo']['role']) and isset($_COOKIE['armadillo']['userID']) ) 
            and ( $_COOKIE['armadillo']['role'] != 'blogger' ) ) {
            
                if ( is_numeric($id) ) {
                    $id = $dbLink->real_escape_string($id);
                    $title = '';
                    $content = '';
                    $sidebarContent = '';
                    $summaryContent = '';
                    $metaContent = '';
                    $date = date('Y-m-d H:i:s');
                    $lastEdited = $date;
                    $author = $dbLink->real_escape_string($_COOKIE['armadillo']['userID']);
                    $status = '';
                    $contentType = $dbLink->real_escape_string($type);
                    $format = $dbLink->real_escape_string($_COOKIE['armadillo']['editorType']);

                    $query = "INSERT INTO armadillo_post SET
                                id='$id',
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
                        echo Armadillo_Language::msg('ARM_CONTENT_SAVE_DB_ERROR') . $dbLink->error;
                    } else {
                        echo 'success';
                    }
                } else {
                    echo 'integer required';
                }
        } else {
            echo 'permission required';
        }
    }
}
