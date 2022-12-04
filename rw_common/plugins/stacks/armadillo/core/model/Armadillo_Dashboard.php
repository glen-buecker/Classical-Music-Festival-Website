<?php

// if (get_magic_quotes_gpc()) {
//     function stripslashes_deep($value)
//     {
//         $value = is_array($value) ?
//             array_map('stripslashes_deep', $value) :
//             stripslashes($value);
// 
//         return $value;
//     }
// 
//     $_POST = array_map('stripslashes_deep', $_POST);
//     $_GET = array_map('stripslashes_deep', $_GET);
//     $_COOKIE = array_map('stripslashes_deep', $_COOKIE);
//     $_REQUEST = array_map('stripslashes_deep', $_REQUEST);
// }

function adminPanelTabs($currentTab, $subPage)
{
    $dashboardTab = '';
    $postTab = '';
    $pageTab = '';
    $contentTab = '';
    $mediaTab = '';
    $userTab = '';
    $settingsTab = '';

    if ($currentTab == 'dashboard') {
        $dashboardTab = "<li class='active'><a href='.'><i class='fa fa-tachometer fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_DASHBOARD_TAB_TITLE') . "</span></a></li>";
    } else { $dashboardTab = "<li><a href='" . armadilloURL() .  "/index.php/'><i class='fa fa-tachometer fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_DASHBOARD_TAB_TITLE') . "</span></a></li>"; }

    if ($currentTab == 'posts') {
        $postTab = "<li class='active'><a href='.'><i class='fa fa-list-ul fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_POST_CONTENT_NAME_PLURAL') . "</span></a></li>";
    } else { $postTab = "<li><a href='" . armadilloURL() .  "/index.php/posts/'><i class='fa fa-list-ul fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_POST_CONTENT_NAME_PLURAL') . "</span></a></li>"; }

    if ($currentTab == 'pages') {
        $pageTab = "<li class='active'><a href='.'><i class='fa fa-file-o fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_PAGE_CONTENT_NAME_PLURAL') . "</span></a></li>";
    } else { $pageTab = "<li><a href='" . armadilloURL() .  "/index.php/pages/'><i class='fa fa-file-o fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_PAGE_CONTENT_NAME_PLURAL') . "</span></a></li>"; }

    if ($currentTab == 'content') {
        $contentTab = "<li class='active'><a href='.'><i class='fa fa-thumb-tack fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_SOLO_CONTENT_NAME_PLURAL') . "</span></a></li>";
    } else { $contentTab = "<li><a href='" . armadilloURL() .  "/index.php/content/'><i class='fa fa-thumb-tack fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_SOLO_CONTENT_NAME_PLURAL') . "</span></a></li>"; }

    if ($currentTab == 'media') {
        if ($subPage == '') { $mediaTab = "<li class='active'><a href='.'><i class='fa fa-picture-o fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_MEDIA_NAME') . "</span></a></li>"; } else { $mediaTab = "<li class='active'><a href='./../'><i class='fa fa-picture-o fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_MEDIA_NAME') . "</span></a></li>"; }
    } else { $mediaTab = "<li><a href='" . armadilloURL() .  "/index.php/media/'><i class='fa fa-picture-o fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_MEDIA_NAME') . "</span></a></li>"; }

    if ($currentTab == 'users') {
        $userTab = "<li class='active'><a href='.'><i class='fa fa-users fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_USER_TEXT_PLURAL') . "</span></a></li>";
    } else { $userTab = "<li><a href='" . armadilloURL() .  "/index.php/users/'><i class='fa fa-users fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_USER_TEXT_PLURAL') . "</span></a></li>"; }

    if ($currentTab == 'settings') {
        $settingsTab = "<li class='active'><a href='.'><i class='fa fa-cogs fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_SETTINGS_TEXT') . "</span></a></li>";
    } else { $settingsTab = "<li><a href='" . armadilloURL() .  "/index.php/settings/'><i class='fa fa-cogs fa-lg'></i><span class='text'>&nbsp;" . Armadillo_Language::msg('ARM_SETTINGS_TEXT') . "</span></a></li>"; }

    $postTab = ( ( isset($_SESSION['enableBlogContent']) and $_SESSION['enableBlogContent'] == true ) or ( isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ) ) ? $postTab : "";
    $pageTab = ( ( isset($_SESSION['enablePageContent']) and $_SESSION['enablePageContent'] == true ) or ( isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ) ) ? $pageTab : "";
    $contentTab = ( ( isset($_SESSION['enableSoloContent']) and $_SESSION['enableSoloContent'] == true ) or ( isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ) ) ? $contentTab : "";

    if ( isset($_SESSION['role']) && $_SESSION['role'] === 'admin' ) {
        echo "<ul class='nav nav-tabs navbar-inverse'>" . $dashboardTab . $postTab . $pageTab . $contentTab . $mediaTab . $userTab . $settingsTab . "</ul>";
    } elseif ( isset($_SESSION['role']) and ( $_SESSION['role'] === 'editor' || $_SESSION['role'] === 'contributor' ) ) {
        echo "<ul class='nav nav-tabs navbar-inverse'>" . $dashboardTab . $postTab . $pageTab . $contentTab . $mediaTab . "</ul>";
    } else { echo "<ul class='nav nav-tabs navbar-inverse'>" . $dashboardTab . $postTab . $mediaTab . "</ul>"; }
}

function html($text)
{
    return htmlspecialchars($text, ENT_QUOTES, 'UTF-8');
}

function htmlout($text)
{
    echo html($text);
}

function armadilloURL($base='')
{
    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
    $host     = $_SERVER['HTTP_HOST'];
    $script   = $base == "base" ? '' : dirname($_SERVER['SCRIPT_NAME']);

    $currentUrl = '//' . $host . $script; 
    // Originally was as is shown below, but it appears that $_SERVER['SERVER_PROTOCOL']
    // does not report the correct value as expected in some hosting configurations:
    // $currentUrl = $protocol . '://' . $host . $script;

    return $currentUrl;
}

function generateRandStr($length)
{
    $randstr = "";
    for ($i=0; $i<$length; $i++) {
     $randnum = mt_rand(0,61);

     if ($randnum < 10) {
        $randstr .= chr($randnum+48);
     } elseif ($randnum < 36) {
        $randstr .= chr($randnum+55);
     } else {
        $randstr .= chr($randnum+61);
     }
    }

  return $randstr;
}
