<?php

if ( !file_exists(dirname(__FILE__) . '/.tmp')) {
    try {
        if ( mkdir(dirname(__FILE__) . '/.tmp') ) {
            chmod(dirname(__FILE__) . '/.tmp', 0700);
        } else { throw new Exception('failed making directory'); }
    } catch (Exception $result) {
        chmod(dirname(__FILE__), 0777);
        mkdir(dirname(__FILE__) . '/.tmp');
        chmod(dirname(__FILE__) . '/.tmp', 0700);
        chmod(dirname(__FILE__), 0755);
    }
}

session_cache_limiter(false);
ini_set('session.save_handler', 'files');
ini_set('session.save_path', dirname(__FILE__) . '/.tmp');
session_set_cookie_params(36000);
session_start();

if ( file_exists( dirname(__FILE__) . '/core/config.php' ) ) {
    require_once dirname(__FILE__) . '/core/config.php';
    require_once dirname(__FILE__) . '/core/connectDB.php';

    $timezoneQuery = "SELECT timezone FROM armadillo_options";
    $timezoneResult = $dbLink->query($timezoneQuery);
    if ($timezoneResult) {
        $timezoneRow = $timezoneResult->fetch_array();
        date_default_timezone_set($timezoneRow['timezone']);
    } else { date_default_timezone_set('America/New_York'); }
}

require 'Slim/Slim.php';
require 'core/model/Armadillo_Language.php';
require 'core/model/Armadillo_Content.php';
require 'core/model/Armadillo_User.php';
require 'core/model/Armadillo_Media.php';
require 'core/model/Armadillo_Data.php';
require 'core/model/Armadillo_Dashboard.php';
require 'core/model/Parsedown.php';
require 'update_core.php';


//Armadillo_Language::installLanguages();
//Armadillo_Data::installContentEditor();
//Armadillo_Data::installDropboxSupport();
Armadillo_Media::checkMediaFolder();

$armadillo = new Slim(array(
    //'log.enable' => true,
    //'log.path' => 'logs',
    //'log.level' => 4,
    'templates.path' => './core',
    'cookies.secret_key' => 'a>>,*>T&O+y{2/]@YoT[/Vg6|*Ml7NLKJZJia8|x8()dgYd_6#N?IAW/|1XlTMlW'
));

//This build number is used to determine what database updates need to be made, if any.
$armadillo->setName('armadillo_dbBuild_1018');
//This is the version number users actually see.
$GLOBALS['armadilloVersion'] = '2.9.8';

/* ALL GET Routes */

//Main dashboard page
$armadillo->get('/', 'admin');
//Login page
$armadillo->get('/login/', 'showLogin');
//Login Reset Form
$armadillo->get('/login/forgot/', 'forgotLogin');
//Login Reset Form
$armadillo->get('/login/reset/:email/:token/:expire/', 'resetLogin');
//Logout
$armadillo->get('/logout/', 'logout');
//Setup page
$armadillo->get('/setup/', 'setup');
//Create backup function
$armadillo->get('/backup/', 'backup');
//Sync Backup File to Dropbox
$armadillo->get('/dropbox/', 'dropbox');
//Update Armadillo Database
$armadillo->get('/update/', 'update');
$armadillo->get('/update/:no_backup/', 'update');
//List created posts
$armadillo->get('/posts/', 'showPosts');
$armadillo->get('/blogs/', 'showPosts');
$armadillo->get('/blog/:blog_id/posts/', 'showBlogPosts');
//Show new post form
$armadillo->get('/posts/new/', 'newPost');
$armadillo->get('/blog/:blog_id/posts/new/', 'newPost');
//Show edit post form
$armadillo->get('/posts/edit/:id/', 'editPost');
$armadillo->get('/blog/:blog_id/posts/edit/:post_id/', 'editBlogPost');
//Show post delete confirmation page
$armadillo->get('/posts/delete/:id/', 'postToDelete');
$armadillo->get('/blog/:blog_id/posts/delete/:post_id/', 'blogPostToDelete');
//Show new blog form
$armadillo->get('/blogs/new/', 'newBlog');
//Show created pages
$armadillo->get('/pages/', 'showPages');
//Show new page form
$armadillo->get('/pages/new/', 'newPage');
//Show edit page form
$armadillo->get('/pages/edit/:id/', 'editPage');
//Show page delete confirmation page
$armadillo->get('/pages/delete/:id/', 'pageToDelete');
//Show created solo content
$armadillo->get('/content/', 'showSoloContent');
//Show new page form
$armadillo->get('/content/new/', 'newSoloContent');
//Show edit page form
$armadillo->get('/content/edit/:id/', 'editSoloContent');
//Show page delete confirmation page
$armadillo->get('/content/delete/:id/', 'soloContentToDelete');
//Show list of created users
$armadillo->get('/users/', 'showUsers');
//Show new user form
$armadillo->get('/users/new/', 'newUser');
//Show edit user form
$armadillo->get('/users/edit/:id/', 'editUser');
//Show user delete confirmation page
$armadillo->get('/users/delete/:id/', 'userToDelete');
//Show uploaded media
$armadillo->get('/media/', 'showMedia');
//Show the fix media folder dialog
$armadillo->get('/media/fixMediaFolder/', 'fixMediaFolder');
//Show media upload form
$armadillo->get('/media/upload/', 'mediaUploadForm');
//Show media delete confirmation page
$armadillo->get('/media/delete/:filename', 'mediaToDelete');
//Show settings tab
$armadillo->get('/settings/', 'showSettings');

function admin()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'dashboard',
            'subPage' => ''
        )
    );
}

function showLogin()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_LOGIN_FORM_TAB_TITLE'),
            'panelToDisplay' => 'admin/login.php',
            'currentTab' => '',
            'subPage' => ''
        )
    );
}

function forgotLogin()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/forgotLogin.php',
        array(
            'pageTitle' => 'Armadillo - ' . Armadillo_Language::msg('ARM_RESET_PASSWORD_TEXT'),
            'loginMode' => 'forgot'
        )
    );
}

function resetLogin($email, $token, $expire)
{
    $armadillo = Slim::getInstance();
    $currentTime = time();
    if ($currentTime < $expire) {
        $armadillo->render(
            'admin/forgotLogin.php',
            array(
                'pageTitle' => 'Armadillo - ' . Armadillo_Language::msg('ARM_RESET_PASSWORD_TEXT'),
                'loginMode' => 'reset',
                'email' => $email,
                'token' => $token,
                'expire' => $expire
            )
        );
    } else {
        $armadillo->flash('notification', Armadillo_Language::msg('ARM_RESET_PASSWORD_LINK_EXPIRED'));
        $armadillo->redirect('./../../../../forgot/');
    }
}

function logout()
{
    $armadillo = Slim::getInstance();
    Armadillo_User::logoutUser();
    $armadillo->redirect('./../');
}

function setup()
{
    $armadillo = Slim::getInstance();
    $setupType = '';
    if ( !Armadillo_Data::configFileExists() and !Armadillo_Data::databaseSetupComplete() ) { $setupType = 'complete'; } elseif ( Armadillo_Data::configFileExists() and !Armadillo_Data::databaseSetupComplete() and !Armadillo_Data::adminUserAlreadyExists()  ) { $setupType = 'dbAndAdminUser'; } elseif ( Armadillo_Data::configFileExists() and Armadillo_Data::databaseSetupComplete() and !Armadillo_Data::adminUserAlreadyExists() ) { $setupType = 'adminUserOnly'; }
    $armadillo->render(
        'admin/setup.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_SETUP_PAGE_TITLE'),
            'panelToDisplay' => 'admin/setup.php',
            'setupType' => "$setupType"
        )
    );
}

function backup()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_BACKUP_DATABASE_TEXT'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'backup',
            'subPage' => '',
            'contentType' => ''
        )
    );
}

function dropbox()
{
    $armadillo = Slim::getInstance();

    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_DROPBOX_SYNC_BACKUP_TEXT'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'dropbox',
            'subPage' => '',
            'contentType' => ''
        )
    );
}

function update($no_backup='')
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_UPDATE_TEXT'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'update',
            'subPage' => '',
            'contentType' => "$no_backup"
        )
    );
}

function showPosts()
{
    $armadillo = Slim::getInstance();
    if ( isset($_SESSION['selectedBlog']) and is_numeric($_SESSION['selectedBlog']) ) {
        $armadillo->redirect('./../blog/' . $_SESSION['selectedBlog'] . '/posts/');
    } else {
        $blogs = Armadillo_Post::getAllBlogSettings();
        if (!empty($blogs)) {
            $armadillo->redirect('./../blog/' . $blogs[0]['id'] . '/posts/');
        } else {
            $armadillo->redirect('./../blogs/new/');
        }
    }
}

function showBlogPosts($blog_id)
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_POST_CONTENT_NAME_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'posts',
            'subPage' => '',
            'contentType' => 'post',
            'blog_id' => $blog_id
        )
    );
}

function newPost($blog_id='')
{
    $armadillo = Slim::getInstance();
    $terms = Armadillo_Content::getTerms();
    $armadillo->render(
        'admin/contentForm.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_POST_CREATE_NEW_TEXT'),
            'contentType' => 'post',
            'contentState' => 'new',
            'terms' => $terms,
            'blog_id' => $blog_id
        )
    );
}

function editPost($id)
{
    $armadillo = Slim::getInstance();
    $itemInfo = Armadillo_Post::getSingleItem('post', $id);
    $terms = Armadillo_Content::getTerms();
    $armadillo->render(
        'admin/contentForm.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_POST_EDIT_TEXT'),
            'contentType' => 'post',
            'contentState' => 'edit',
            'contentID' => "$id",
            'itemInfo' => $itemInfo,
            'terms' => $terms
        )
    );
}

function editBlogPost($blog_id,$post_id)
{
    $armadillo = Slim::getInstance();
    $itemInfo = Armadillo_Post::getSingleItem('post', $post_id);
    $terms = Armadillo_Content::getTerms();
    $armadillo->render(
        'admin/contentForm.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_POST_EDIT_TEXT'),
            'contentType' => 'post',
            'contentState' => 'edit',
            'contentID' => "$post_id",
            'itemInfo' => $itemInfo,
            'terms' => $terms
        )
    );
}

function postToDelete($id)
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_POST_DELETE_TEXT'),
            'panelToDisplay' => 'admin/deletePanel.php',
            'contentType' => 'post',
            'contentID' => "$id"
        )
    );
}

function blogPostToDelete($blog_id,$post_id)
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_POST_DELETE_TEXT'),
            'panelToDisplay' => 'admin/deletePanel.php',
            'contentType' => 'post',
            'contentID' => "$post_id"
        )
    );
}

function newBlog()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/contentForm.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_BLOG_CREATE_NEW_TEXT'),
            'contentType' => 'blog',
            'contentState' => 'new'
        )
    );
}

function showPages()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_PAGE_CONTENT_NAME_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'pages',
            'subPage' => '',
            'contentType' => 'page'
        )
    );
}

function newPage()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/contentForm.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_PAGE_CREATE_NEW_TEXT'),
            'contentType' => 'page',
            'contentState' => 'new'
        )
    );
}

function editPage($id)
{
    $armadillo = Slim::getInstance();
    $itemInfo = Armadillo_Post::getSingleItem('page', $id);
    $armadillo->render(
        'admin/contentForm.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_PAGE_EDIT_TEXT'),
            'contentType' => 'page',
            'contentState' => 'edit',
            'contentID' => "$id",
            'itemInfo' => $itemInfo
        )
    );
}

function pageToDelete($id)
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_PAGE_DELETE_TEXT'),
            'panelToDisplay' => 'admin/deletePanel.php',
            'contentType' => 'page',
            'contentID' => "$id"
        )
    );
}

function showSoloContent()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_SOLO_CONTENT_NAME_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'content',
            'subPage' => '',
            'contentType' => 'soloContent'
        )
    );
}

function newSoloContent()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/contentForm.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_SOLO_CONTENT_CREATE_NEW_TEXT'),
            'contentType' => 'soloContent',
            'contentState' => 'new'
        )
    );
}

function editSoloContent($id)
{
    $armadillo = Slim::getInstance();
    $itemInfo = Armadillo_Post::getSingleItem('soloContent', $id);
    $armadillo->render(
        'admin/contentForm.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_SOLO_CONTENT_EDIT_TEXT'),
            'contentType' => 'soloContent',
            'contentState' => 'edit',
            'contentID' => "$id",
            'itemInfo' => $itemInfo
        )
    );
}

function soloContentToDelete($id)
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_SOLO_CONTENT_DELETE_TEXT'),
            'panelToDisplay' => 'admin/deletePanel.php',
            'contentType' => 'soloContent',
            'contentID' => "$id"
        )
    );
}

function showUsers()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_USER_TEXT_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'users',
            'subPage' => ''
        )
    );
}

function newUser()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/userForm.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_USER_CREATE_NEW_TEXT'),
            'contentType' => 'user',
            'contentState' => 'new'
        )
    );
}

function editUser( $id )
{
    $armadillo = Slim::getInstance();
    if ( isset($_SESSION['newUserDetails']) ) {
        $_SESSION['newUserDetails'] = NULL;
        unset($_SESSION['newUserDetails']);
    }
    $armadillo->render(
        'admin/userForm.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_USER_EDIT_TEXT'),
            'contentType' => 'user',
            'contentState' => 'edit',
            'userID' => "$id"
        )
    );
}

function userToDelete($id)
{
    $armadillo = Slim::getInstance();
    if ($_SESSION['userID'] !== $id) {
        $armadillo->render(
            'admin/main.php',
            array(
                'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_USER_DELETE_TEXT'),
                'panelToDisplay' => 'admin/deletePanel.php',
                'contentType' => 'user',
                'userID' => "$id"
            )
        );
    } else {
        $armadillo->flash('notification', Armadillo_Language::msg('ARM_USER_DELETE_OWN_ACCOUNT_WARNING'));
        $armadillo->redirect('./../../');
    }
}

function showMedia()
{
    $armadillo = Slim::getInstance();
    Armadillo_Media::checkMediaFolder();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_NAME'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'media',
            'mediaView' => 'library',
            'subPage' => ''
        )
    );
}

function fixMediaFolder()
{
    $armadillo = Slim::getInstance();
    if ( Armadillo_Media::fixMediaFolder() ) { $armadillo->flash('notification', 'The media folder was successfully fixed.'); } else { $armadillo->flash('notification', 'An error occurred - the media folder has not been fixed.'); }
    $armadillo->redirect('./../');
}

function mediaUploadForm()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_DASHBOARD_UPLOAD_MEDIA_TEXT'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'media',
            'mediaView' => 'uploadForm',
            'subPage' => 'upload'
        )
    );
}

function mediaToDelete($filename)
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_MEDIA_DELETE_TEXT'),
            'panelToDisplay' => 'admin/deletePanel.php',
            'contentType' => 'media',
            'mediaFilename' => "$filename"
        )
    );
}

function showSettings()
{
    $armadillo = Slim::getInstance();
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_SETTINGS_TEXT'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'settings',
            'subPage' => '',
            'armadilloURL' => armadilloURL()
        )
    );
}

/* ALL POST/PUT Routes */

//POST Complete Setup & Install
$armadillo->post('/setup/', 'install');
function install()
{
    $armadillo = Slim::getInstance();
    $settings = $armadillo->request()->post();
    // Just create admin user if config file is present and database is already setup
    if ($settings['setupType'] === 'adminUserOnly') {
        $adminUserCreated = Armadillo_Data::createAdminUser($settings);
        if ( $adminUserCreated['result'] === "TRUE" and Armadillo_Data::setupDefaultOptions($settings) ) {
            $armadillo->flash('notification', Armadillo_Language::msg('ARM_SETUP_SUCCESSFUL_NOTIFICATION'));
            $armadillo->redirect('./../');
        } else { $armadillo->flash('notification', Armadillo_Language::msg('ARM_ERROR_NOTIFICATION') . $adminUserCreated['error']); $armadillo->redirect('./'); }

    // If config file is present but nothing else, setup db and create admin user
    } elseif ($settings['setupType'] === 'dbAndAdminUser') {
        if ( Armadillo_Data::setupDB($settings) ) {
            $adminUserCreated = Armadillo_Data::createAdminUser($settings);
            if ( $adminUserCreated['result'] === "TRUE" and Armadillo_Data::setupDefaultOptions($settings) ) {
                Armadillo_Data::createInitialBlogPage();
                $armadillo->flash('notification', Armadillo_Language::msg('ARM_SETUP_SUCCESSFUL_NOTIFICATION'));
                $armadillo->redirect('./../');
            } else { $armadillo->flash('notification', Armadillo_Language::msg('ARM_ERROR_NOTIFICATION') . $adminUserCreated['error']); $armadillo->redirect('./'); }
        } else { $armadillo->flash('notification', Armadillo_Language::msg('ARM_DB_CREATE_ERROR_NOTIFICATION')); $armadillo->redirect('./'); }
    // Setup hasn't been run, so install Armadillo from scratch
    } elseif ($settings['setupType'] === 'complete') {
        if ( Armadillo_Data::installArmadillo($settings) ) { $armadillo->redirect('./../'); } else { $armadillo->render( 'admin/setup.php', array('pageTitle' => Armadillo_Language::msg('ARM_SETUP_PAGE_TITLE')) ); }
    } else {
        $armadillo->render(
            'admin/setup.php',
            array('pageTitle' => Armadillo_Language::msg('ARM_SETUP_PAGE_TITLE'))
        );
    }
}

$armadillo->post('/setup/redo/', 'redoInstall');
function redoInstall()
{
    $armadillo = Slim::getInstance();
    $settings = $armadillo->request()->post();
    if ( $settings['setupType'] === 'redo' and !Armadillo_Data::databaseSetupComplete() ) {
        if ( Armadillo_Data::dbSettingsAreCorrect($settings) ) {
            Armadillo_Data::createConfigFile($settings);
            $armadillo->redirect('./../../');
        }
    } else {
        $armadillo->render(
            'admin/setup.php',
            array('pageTitle' => 'Redo Armadillo Setup')
        );
    }
}

//POST Login the specified user
$armadillo->post('/', 'login');
function login()
{
    $armadillo = Slim::getInstance();
    if ( $armadillo->request()->params('username') === '' || $armadillo->request()->params('password') === '') {
        $armadillo->flash('notification', 'Please fill in both fields to login.');
    } else { Armadillo_User::loginUser( $armadillo->request()->params('username'), $armadillo->request()->params('password'), $armadillo->request()->params('armadilloURL') ); }
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'dashboard',
            'subPage' => ''
        )
    );
}

//POST Send Login Reset Link
$armadillo->post('/login/forgot/', 'resetLoginLink');
function resetLoginLink()
{
    $armadillo = Slim::getInstance();
    Armadillo_Data::sendReminderEmail($armadillo->request()->params('loginEmail'), '', 'reset');
    $armadillo->render(
        'admin/forgotLogin.php',
        array(
            'pageTitle' => 'Armadillo - ' . Armadillo_Language::msg('ARM_RESET_PASSWORD_TEXT'),
            'loginMode' => 'forgot'
        )
    );
}

//POST Reset Login Password for Selected User
$armadillo->post('/login/reset/:email/:token/:expire/', 'resetLoginPassword');
function resetLoginPassword($email, $token, $expire)
{
    $armadillo = Slim::getInstance();
    $resetComplete = Armadillo_Data::resetLoginPassword($armadillo->request()->params('userID'), $armadillo->request()->params('loginPassword'), $armadillo->request()->params('loginConfirmPassword'), $token);
    if ($resetComplete) {
        $armadillo->flash('notification', Armadillo_Language::msg('ARM_RESET_PASSWORD_SUCCESSFUL_NOTIFICATION'));
        $armadillo->redirect('./../../../../');
    } else {
        $armadillo->render(
            'admin/forgotLogin.php',
            array(
                'pageTitle' => 'Armadillo - ' . Armadillo_Language::msg('ARM_RESET_PASSWORD_TEXT'),
                'loginMode' => 'reset',
                'email' => $email,
                'token' => $token,
                'expire' => $expire
            )
        );
    }
}

//POST & PUT Save/Edit posts
$armadillo->post('/posts/', 'savePost');

$armadillo->put('/posts/', 'savePost');

$armadillo->post('/blog/:blog_id/posts/', 'savePost');

$armadillo->put('/blog/:blog_id/posts/', 'savePost');


function savePost($blog_id='')
{
    $armadillo = Slim::getInstance();
    $post = new Armadillo_Post();
    if ( $armadillo->request()->isPut() ) { $post->setID($_POST['id']); }
    $post->setBlogID($blog_id);
    if ( isset($_POST['title']) ) { $post->setTitle($_POST['title']); }
    if ( isset($_POST['content']) ) { $post->setContent($_POST['content']); }
    if ( isset($_POST['metaContent']) ) { $post->setMetaContent($_POST['metaContent']); }
    if ( isset($_POST['summaryContent']) ) { $post->setSummaryContent($_POST['summaryContent']); }
    $date = $_POST['dateYear'] . "-" . $_POST['dateMonth'] . "-" . $_POST['dateDay'] . " " . $_POST['dateTime'];
    $post->setDate($date);
    $post->setLastEdited(date('Y-m-d H:i:s'));
    if ( isset($_POST['categories']) ) { $post->setCategories(explode(',', $_POST['categories'])); }
    if ( isset($_POST['tags']) ) { $post->setTags(explode(',', $_POST['tags'])); }
    $post->setAuthor($_POST['author']);
    $post->setFormat($_POST['format']);
    if ( $armadillo->request()->params('displayPostComments') == "TRUE" ) { $post->setDisplayComments(TRUE); } else { $post->setDisplayComments(FALSE); };
    if ( $armadillo->request()->params('displayPostSummary') == "TRUE" ) { $post->setDisplaySummary(TRUE); } else { $post->setDisplaySummary(FALSE); };
    if ( $_POST['saveType'] === Armadillo_Language::msg('ARM_PUBLISH_TEXT') ) { $post->setStatus(TRUE); } // A "TRUE" status means publish
    elseif ( $_POST['saveType'] === Armadillo_Language::msg('ARM_SAVE_DRAFT_TEXT') ) { $post->setStatus(FALSE); } // A "FALSE" status means draft
    else { $post->setStatus(FALSE); }
    $post->saveItem("post", $post);
    if ( $post->getStatus() == TRUE ) { Armadillo_Data::updateRSSfeed(armadilloURL(),$blog_id); }
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_POST_CONTENT_NAME_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'posts',
            'subPage' => '',
            'contentType' => 'post',
            'blog_id' => $blog_id
        )
    );
}

//POST Save new Blog, which is just a special type of page
$armadillo->post('/blogs/', 'saveBlog');

function saveBlog()
{
    $armadillo = Slim::getInstance();
    $page = new Armadillo_Page();
    if ( $armadillo->request()->isPut() ) { $page->setID($_POST['id']); }
    if ( isset($_POST['title']) ) { $page->setTitle($_POST['title']); }
    if ( isset($_POST['sidebar']) ) { $page->setSidebarContent($_POST['sidebar']); }
    if ( isset($_POST['metaContent']) ) { $page->setMetaContent($_POST['metaContent']); }
    if ( $armadillo->request()->isPost() ) { $page->setDate(date('Y-m-d H:i:s')); } else { $page->setDate($_POST['date']); }
    $page->setLastEdited(date('Y-m-d H:i:s'));
    $page->setAuthor($_POST['author']);
    $page->setFormat($_POST['format']);
    if ( $_POST['saveType'] === Armadillo_Language::msg('ARM_PUBLISH_TEXT') ) { $page->setStatus(TRUE); } // A "TRUE" status means publish
    elseif ( $_POST['saveType'] === Armadillo_Language::msg('ARM_SAVE_DRAFT_TEXT') ) { $page->setStatus(FALSE); } // A "FALSE" status means draft
    else { $page->setStatus(FALSE); }
    $itemID = $page->saveItem("blog", $page);
    if ( $armadillo->request()->isPost() ) {
        $armadillo->redirect('./../blog/' . $itemID . '/posts/');
    } else {
        $armadillo->render(
            'admin/main.php',
            array(
                'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_PAGE_CONTENT_NAME_PLURAL'),
                'panelToDisplay' => 'admin/adminPanel.php',
                'currentTab' => 'pages',
                'subPage' => '',
                'contentType' => 'page'
            )
        );
    }
}

//POST & PUT Save/Edit pages
$armadillo->post('/pages/', 'savePage');

$armadillo->put('/pages/', 'savePage');

function savePage()
{
    $armadillo = Slim::getInstance();
    $page = new Armadillo_Page();
    if ( $armadillo->request()->isPut() ) { $page->setID($_POST['id']); }
    if ( isset($_POST['title']) ) { $page->setTitle($_POST['title']); }
    if ( isset($_POST['content']) ) { $page->setContent($_POST['content']); }
    if ( isset($_POST['sidebar']) ) { $page->setSidebarContent($_POST['sidebar']); }
    if ( isset($_POST['metaContent']) ) { $page->setMetaContent($_POST['metaContent']); }
    if ( $armadillo->request()->isPost() ) { $page->setDate(date('Y-m-d H:i:s')); } else { $page->setDate($_POST['date']); }
    $page->setLastEdited(date('Y-m-d H:i:s'));
    $page->setAuthor($_POST['author']);
    $page->setFormat($_POST['format']);
    if ( $_POST['saveType'] === Armadillo_Language::msg('ARM_PUBLISH_TEXT') ) { $page->setStatus(TRUE); } // A "TRUE" status means publish
    elseif ( $_POST['saveType'] === Armadillo_Language::msg('ARM_SAVE_DRAFT_TEXT') ) { $page->setStatus(FALSE); } // A "FALSE" status means draft
    else { $page->setStatus(FALSE); }
    $page->saveItem("page", $page);
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_PAGE_CONTENT_NAME_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'pages',
            'subPage' => '',
            'contentType' => 'page'
        )
    );
}

//POST & PUT Save/Edit solo content
$armadillo->post('/content/', 'saveSoloContent');

$armadillo->put('/content/', 'saveSoloContent');

function saveSoloContent()
{
    $armadillo = Slim::getInstance();
    $soloContent = new Armadillo_Solo_Content();
    if ( $armadillo->request()->isPut() ) { $soloContent->setID($_POST['id']); }
    if ( isset($_POST['title']) ) { $soloContent->setTitle($_POST['title']); }
    if ( isset($_POST['content']) ) { $soloContent->setContent($_POST['content']); }
    //$soloContent->setSidebarContent($_POST['sidebar']);
    //$soloContent->setMetaContent($_POST['metaContent']);
    if ( $armadillo->request()->isPost() ) { $soloContent->setDate(date('Y-m-d H:i:s')); } else { $soloContent->setDate($_POST['date']); }
    $soloContent->setLastEdited(date('Y-m-d H:i:s'));
    $soloContent->setAuthor($_POST['author']);
    $soloContent->setFormat($_POST['format']);
    if ( $_POST['saveType'] === Armadillo_Language::msg('ARM_PUBLISH_TEXT') ) { $soloContent->setStatus(TRUE); } // A "TRUE" status means publish
    elseif ( $_POST['saveType'] === Armadillo_Language::msg('ARM_SAVE_DRAFT_TEXT') ) { $soloContent->setStatus(FALSE); } // A "FALSE" status means draft
    else { $soloContent->setStatus(FALSE); }
    $soloContent->saveItem("soloContent", $soloContent);
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_PAGE_CONTENT_NAME_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'content',
            'subPage' => '',
            'contentType' => 'soloContent'
        )
    );
}

//POST & PUT Save/Edit users
$armadillo->post('/users/', 'saveUser');

$armadillo->put('/users/', 'saveUser');

function saveUser()
{
    $armadillo = Slim::getInstance();

    $pageToRender = 'admin/main.php';
    $panelToDisplay = 'admin/userForm.php';
    $pageParams = array(
                    'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_USER_TEXT_PLURAL'),
                    'panelToDisplay' => "$panelToDisplay",
                    'currentTab' => 'users',
                    'subPage' => '' );

    $newUserDetails = ($armadillo->request()->isPost()) ? $armadillo->request()->post() : $armadillo->request()->put();
    $_SESSION['newUserDetails'] = $newUserDetails;

    if ( !isset($_POST['cancel']) or $_POST['cancel'] === NULL ) {

        $pageParams['contentState'] = $armadillo->request()->isPost() ? 'new' : 'edit';
        //TODO: Figure out why the flash message isn't being displayed - FIXED, route needed a forward slash "/" at the end when being redirected
        $pageParams = Armadillo_User::checkNewUserDetails($newUserDetails, $pageParams);

        $user = new Armadillo_User();
        if ( $armadillo->request()->isPut() ) { $user->setID($armadillo->request()->params('id')); }
        $user->setUsername($armadillo->request()->params('username'));
        $user->setRealname($armadillo->request()->params('name'));
        $user->setEmail($armadillo->request()->params('email'));
        $user->setRole($armadillo->request()->params('role'));
        $user->setLanguage($armadillo->request()->params('language'));
        $password = $armadillo->request()->params('password');
        $confirmPassword = $armadillo->request()->params('confirmPassword');
        if ($password === $confirmPassword) {
            $user->setPassword($password);
        }

        $user->saveUser( $user );
    }

    $_SESSION['newUserDetails'] = NULL;
    unset($_SESSION['newUserDetails']);
    session_write_close();
    $armadillo->redirect('.');

}

//POST Upload Media files
$armadillo->post('/media/upload/', 'uploadMedia');

function uploadMedia()
{
    $armadillo = Slim::getInstance();
    $uploadCount = $armadillo->request()->params('mediaUpload_count');
    if ($uploadCount > 0) {
        if ($uploadCount === 1) { $armadillo->flash('notification', $uploadCount . Armadillo_Language::msg('ARM_MEDIA_UPLOAD_SINGLE_FILE_NOTIFICATION')); } else { $armadillo->flash('notification', $uploadCount . Armadillo_Language::msg('ARM_MEDIA_UPLOAD_MULTIPLE_FILES_NOTIFICATION')); }
    } else { $armadillo->flash('notification', Armadillo_Language::msg('ARM_MEDIA_UPLOAD_ZERO_FILE_NOTIFICATION')); }

    $armadillo->redirect('./../');
}

/* ALL DELETE Routes */

//DELETE Delete the specified post
$armadillo->delete('/posts/', 'deletePost');

$armadillo->delete('/blog/:blog_id/posts/', 'deletePost');

function deletePost($blog_id='')
{
    $armadillo = Slim::getInstance();
    if ( $armadillo->request()->params('cancel') === NULL ) { 
        Armadillo_Post::deleteItem("post", $armadillo->request()->params('id')); 
    }
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_POST_CONTENT_NAME_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'posts',
            'subPage' => '',
            'contentType' => 'post',
            'blog_id' => $blog_id
        )
    );
}

//DELETE Delete the specified page
$armadillo->delete('/pages/', 'deletePage');

function deletePage()
{
    $armadillo = Slim::getInstance();
    if ( $armadillo->request()->params('cancel') === NULL ) { 
        // Reassign posts to newly selected blog if page being deleted is an existing blog page
        if ( isset($_REQUEST['blogSelect']) ) {
             Armadillo_Page::reassignBlogPosts($armadillo->request()->params('id'), $armadillo->request()->params('blogSelect'));
        }
        // Delete the selected page
        Armadillo_Page::deleteItem("page", $armadillo->request()->params('id'));
    }
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_PAGE_CONTENT_NAME_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'pages',
            'subPage' => '',
            'contentType' => 'page'
        )
    );
}

//DELETE Delete the specified content
$armadillo->delete('/content/', 'deleteSoloContent');

function deleteSoloContent()
{
    $armadillo = Slim::getInstance();
    if ( $armadillo->request()->params('cancel') === NULL ) { Armadillo_Solo_Content::deleteItem("soloContent", $armadillo->request()->params('id')); }
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_PAGE_CONTENT_NAME_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'content',
            'subPage' => '',
            'contentType' => 'soloContent'
        )
    );
}

//DELETE Delete the specified user
$armadillo->delete('/users/', 'deleteUser');

function deleteUser()
{
    $armadillo = Slim::getInstance();
    if ( $armadillo->request()->params('cancel') === NULL ) { Armadillo_User::deleteUser($armadillo->request()->params('id')); }
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_USER_TEXT_PLURAL'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'users',
            'subPage' => ''
        )
    );
}

//DELETE Delete the specified media file
$armadillo->delete('/media/', 'deleteMedia');

function deleteMedia()
{
    $armadillo = Slim::getInstance();
    if ( $armadillo->request()->params('cancel') === NULL ) { Armadillo_Media::deleteMedia($armadillo->request()->params('filename')); }
    $armadillo->render(
        'admin/main.php',
        array(
            'pageTitle' => Armadillo_Language::msg('ARM_DASHBOARD_BROWSER_PAGE_TITLE') . ' - ' . Armadillo_Language::msg('ARM_MEDIA_LIBRARY_NAME'),
            'panelToDisplay' => 'admin/adminPanel.php',
            'currentTab' => 'media',
            'mediaView' => 'library',
            'subPage' => ''
        )
    );
}

//404 Not Found Error page
$armadillo->notFound('armadilloNotFound');
function armadilloNotFound()
{
    global $armadillo;
    $armadillo->render('admin/404.php');
}

//Run Armadillo
$armadillo->run();
