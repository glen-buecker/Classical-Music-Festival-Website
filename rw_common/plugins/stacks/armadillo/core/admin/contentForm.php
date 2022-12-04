<?php
$armadillo = Slim::getInstance();
$rootUri = $armadillo->request()->getRootUri();
$resourceUri = $armadillo->request()->getResourceUri();
?>
<!DOCTYPE html>
<html>
    <head>
        <meta charset="UTF-8" />
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <title><?php echo $this->data['pageTitle'] ?></title>
        <link href='//fonts.googleapis.com/css?family=Fira+Sans:300,500,300italic' rel='stylesheet' type='text/css'>
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/bootstrap/3.3.5/css/bootstrap.min.css" />
        <link rel="stylesheet" href="//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css" />
        <link rel="stylesheet" href="<?php echo $rootUri; ?>/../core/css/adminStyles.css" />
        <link rel="stylesheet" href="<?php echo $rootUri; ?>/../core/css/jquery.plugin.css" />
        <?php if ( isset($_COOKIE['armadillo']['editorType']) and $_COOKIE['armadillo']['editorType'] == 'markdown' ): ?>
        <link rel="stylesheet" href="<?php echo $rootUri; ?>/../core/css/armadillo-markdown.css" />
        <?php endif; ?>
        <style type="text/css">
            #contentFormHeight { <?php if ( $this->data['contentType'] !== 'post' and ( isset($this->data['itemInfo']) and $this->data['itemInfo']['type'] !== 'blog' ) ): ?>min-height: 450px;<?php endif; ?> }
            <?php if ( $this->data['contentType'] === 'post' ): ?>
                #jumpToMediaLibrary { top: auto; }
            <?php endif; ?>
        </style>
    </head>
    <body>
        <div id="topPanel" class="affix">
            <div class="backToSite pull-left"><a href="<?php echo $rootUri; ?>/../../../../../">&larr;&nbsp;<?php echo Armadillo_Language::msg('ARM_VISIT_SITE_LINK'); ?></a></div>
            <div class="loggedInUser pull-right"><?php
                if ( isset($_SESSION['loggedIn']) and $_SESSION['loggedIn'] === TRUE ) {
                    echo Armadillo_Language::msg('ARM_GREETING') . ", " . $_SESSION['username'] . "!&nbsp;&nbsp;&nbsp;<a href='" . $rootUri . "/users/edit/" . $_SESSION['userID'] . "/'>" . Armadillo_Language::msg('ARM_PROFILE_EDIT_LINK') . "</a>&nbsp;|&nbsp;<a href='" . $rootUri . "/logout/'>" . Armadillo_Language::msg('ARM_LOGOUT_LINK') . "</a>";
                } else { echo Armadillo_Language::msg('ARM_PLEASE_LOGIN_TEXT'); }
            ?></div>
        </div>
        <div class="adminPanelWrapper">
            <div id="adminPanelContainer" class="container">
            <?php
                if ( isset($_SESSION['loggedIn']) and $_SESSION['loggedIn'] == TRUE ):
                    if ( isset($flash['notification'])) {
                        echo '<p class="notification">' . $flash['notification'] . '</p>';
                    }
                    if ( isset($this->data['itemInfo']) ) {
                        $itemInfo = $this->data['itemInfo'];
                    }
                    if ( !file_exists($_SESSION['armURL'] . 'media' . DIRECTORY_SEPARATOR . 'images.json') || !file_exists($_SESSION['armURL'] . 'media' . DIRECTORY_SEPARATOR . 'files.json') ) {
                        Armadillo_Media::createRedactorJsonFile($_SESSION['armURL']);
                    }
                    if ( ($this->data['contentState'] === 'new' && $_SESSION['role'] === 'blogger' && $this->data['contentType'] === 'post')
                        || ($this->data['contentState'] === 'new' && $_SESSION['role'] === 'contributor') 
                        || $_SESSION['role'] === 'admin' 
                        || $_SESSION['role'] === 'editor' 
                        || ( isset($itemInfo['author']) and $itemInfo['author'] === $_SESSION['userID'] ) ):
            ?>
            <?php if ($this->data['contentState'] === "edit" and ( !isset($itemInfo['format']) or $itemInfo['format'] == 'html' ) ): ?>
            <script src="<?php echo $rootUri; ?>/../core/scripts/to-markdown.js"></script>
            <?php endif; ?>
                <?php if ( ( ( $this->data['contentType'] === 'post' or $this->data['contentType'] === 'blog' ) and ( $_SESSION['enableBlogContent'] or $_SESSION['role'] === 'admin' ) )
                        or ( $this->data['contentType'] === 'page' and ( $_SESSION['enablePageContent'] or $_SESSION['role'] === 'admin' ) ) 
                        or ( $this->data['contentType'] === 'soloContent' and ( $_SESSION['enableSoloContent'] or $_SESSION['role'] === 'admin' ) ) ): ?>
                <div class="contentForm">
                    <form id="contentFormDetails" class="" action="./../<?php if ($this->data['contentState'] === 'edit') { echo "../"; } ?>" method="post">
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div id="contentFormHeight">
                                    <!-- Nav to Toggle Content Data Entry Fields -->
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <div id="contentDataEntryToggle">
                                                <div class="btn-group margin-center">
                                                    <?php if ( $this->data['contentType'] !== 'soloContent' ): ?><div class="btn btn-default metaContent toggle"><?php echo Armadillo_Language::msg('ARM_CONTENT_META_CONTENT_TEXT'); ?></div><?php endif; ?>
                                                    <?php if ( $this->data['contentType'] === 'post' ): ?><div class="btn btn-default postSummary toggle"><?php echo Armadillo_Language::msg('ARM_CONTENT_POST_SUMMARY_TEXT'); ?></div>
                                                    <?php elseif ( $this->data['contentType'] === 'page' or $this->data['contentType'] === 'blog' ): ?><div class="btn btn-default pageSidebar toggle"><?php echo Armadillo_Language::msg('ARM_CONTENT_EDIT_SIDEBAR_TEXT'); ?></div><?php endif; ?>
                                                    <div class="btn btn-default internalLinks toggle"><?php echo Armadillo_Language::msg('ARM_CONTENT_INTERNAL_LINKS_TEXT'); ?></div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <!-- Meta Content Entry -->
                                    <div class="row">
                                        <div class="col-xs-8 col-xs-offset-2 col-sm-8 col-sm-offset-2">
                                            <div id="internalLinksContainer">
                                                <div class="" id="internalLinksHelper">
                                                    <label class="sr-only"><?php echo Armadillo_Language::msg('ARM_CONTENT_INTERNAL_LINKS_TEXT'); ?></label>
                                                    <select class="form-control" id="internalLinks" placeholder="<?php echo Armadillo_Language::msg('ARM_CONTENT_INTERNAL_LINKS_PLACEHOLDER_TEXT'); ?>"></select>
                                                </div>
                                                <input id="selectedContentLink" class="form-control" placeholder="<?php echo Armadillo_Language::msg('ARM_CONTENT_INTERNAL_LINKS_CONTENT_URL_TEXT'); ?>">
                                            </div>
                                        </div>
                                    </div>
                                    <?php if ( $this->data['contentType'] !== 'soloContent' ): ?>
                                    <!-- Meta Content Entry -->
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <div id="metaContentContainer">
                                                <div class="btn-group" id="metaTagHelper">
                                                    <button type="button" class="btn btn-default btn-xs dropdown-toggle" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                                                    <i class="fa fa-plus"></i> <span class="caret"></span>
                                                    </button>
                                                    <ul class="dropdown-menu">
                                                        <li><a href="#" class="metaKeywords">Meta Tag - Keywords</a></li>
                                                        <li><a href="#" class="metaDescription">Meta Tag - Description</a></li>
                                                        <li role="separator" class="divider"></li>
                                                        <li><a href="#" class="facebookOG-Image">Meta Tag - Facebook OG: Image</a></li>
                                                        <li><a href="#" class="facebookOG-Video">Meta Tag - Facebook OG: Video</a></li>
                                                        <li><a href="#" class="facebookOG-Title">Meta Tag - Facebook OG: Title</a></li>
                                                        <li><a href="#" class="facebookOG-Description">Meta Tag - Facebook OG: Description</a></li>
                                                        <li><a href="#" class="facebookOG-URL">Meta Tag - Facebook OG: URL</a></li>
                                                        <li><a href="#" class="facebookOG-SiteName">Meta Tag - Facebook OG: Site Name</a></li>
                                                        <li><a href="#" class="facebookOG-Type">Meta Tag - Facebook OG: Type</a></li>
                                                        <li role="separator" class="divider"></li>
                                                        <li><a href="#" class="twitter-Card">Meta Tag - Twitter: Card</a></li>
                                                        <li><a href="#" class="twitter-Site">Meta Tag - Twitter: Site Username</a></li>
                                                        <li><a href="#" class="twitter-Creator">Meta Tag - Twitter: Creator Username</a></li>
                                                        <li role="separator" class="divider"></li>
                                                        <li><a href="#" class="linkStylesheet">Link Tag - Stylesheet</a></li>
                                                    </ul>
                                                </div>
                                                <textarea id="metaContent" class="form-control codeEntry" name="metaContent" rows="3" placeholder=" <?php htmlout(ucfirst($this->data['contentType'])); ?> Specific Meta Tags"><?php if ($this->data['contentState'] === "edit") { htmlout($itemInfo['metaContent']); } ?></textarea>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ( $this->data['contentType'] === 'post' ): ?>
                                    <!-- Content Summary Entry -->
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-12">
                                            <div id="summaryContentContainer" class="panel panel-default">
                                                <div class="panel-heading">
                                                    <h3 class="panel-title"><?php echo Armadillo_Language::msg('ARM_CONTENT_POST_SUMMARY_TEXT'); ?></h3>
                                                </div>
                                                <div class="panel-body">
                                                    <?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and  ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
                                                    <!-- Rich Text Editor -->
                                                    <textarea class="form-control" id="summaryContent" name="summaryContent" rows="10"><?php if ($this->data['contentState'] === "edit") { if ( isset($itemInfo['format']) and $itemInfo['format'] == 'html' ) { htmlout($itemInfo['summaryContent']); } else { echo Parsedown::instance()->text($itemInfo['summaryContent']); } } ?></textarea>
                                                    <?php else: ?>
                                                    <!-- Markdown Editor -->
                                                    <textarea id="summaryContent" name="summaryContent" data-uk-htmleditor="{markdown:true}"><?php if ($this->data['contentState'] === "edit" and ( isset($itemInfo['format']) and $itemInfo['format'] == 'markdown' ) ) { echo $itemInfo['summaryContent']; } ?></textarea>
                                                    <?php if ($this->data['contentState'] === "edit" and ( !isset($itemInfo['format']) or $itemInfo['format'] == 'html' ) ): ?>
                                                    <script>summaryContent = document.getElementById('summaryContent'); summaryContent.value = toMarkdown('<?php echo str_replace(array("\n", "\r"),'', str_replace("'","\'",$itemInfo['summaryContent'])); ?>');</script>
                                                    <?php endif; ?>
                                                    <?php endif; ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-12 col-md-8">
                                            <?php if ( $this->data['contentType'] === 'post' ): ?>
                                            <div class="postAttributes">
                                                <!-- Post Date -->
                                                <div id="postDate" class="form-inline">
                                                    <h4><?php echo Armadillo_Language::msg('ARM_CONTENT_PUBLISH_DATE_TEXT'); ?><img src="./../../<?php if ($this->data['contentType'] === 'post') { echo "../../"; } if ($this->data['contentState'] === 'edit') { echo "../"; } ?>../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_CONTENT_PUBLISH_DATE_TOOLTIP'); ?>" class="qtooltip" /></h4>
                                                    <div class="form-group">
                                                        <label for="dateMonth" class="sr-only">Publish Month</label>
                                                        <select class="form-control" id="dateMonth" name="dateMonth">
                                                            <?php
                                                                $month = 1;
                                                                while ($month <= 12) {
                                                                    $dateMonth = $this->data['contentState'] === "edit" ? date('n', strtotime($itemInfo['date'])) : date('n');
                                                                    $selected =  $dateMonth == $month ? "selected='selected'" : '';
                                                                    echo "<option value='$month' $selected>" . date('M', mktime(0, 0, 0, $month, 1, 1950)) . "</option>";
                                                                    $month++;
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="dateDay" class="sr-only">Publish Day</label>
                                                        <select class="form-control" id="dateDay" name="dateDay">
                                                            <?php
                                                                $day = 1;
                                                                while ($day <= 31) {
                                                                    $dateDay = $this->data['contentState'] === "edit" ? date('j', strtotime($itemInfo['date'])) : date('j');
                                                                    $selected = $dateDay == $day ? "selected='selected'" : '';
                                                                    echo "<option value='$day' $selected>$day</option>";
                                                                    $day++;
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <div class="form-group">
                                                        <label for="dateYear" class="sr-only">Publish Year</label>
                                                        <select class="form-control" id="dateYear" name="dateYear">
                                                            <?php
                                                                $year = 1950;
                                                                while ( $year <= (date('Y') + 1) ) {
                                                                    $dateYear = $this->data['contentState'] === "edit" ? date('Y', strtotime($itemInfo['date'])) : date('Y');
                                                                    $selected = $dateYear == $year ? "selected='selected'" : '';
                                                                    echo "<option value='$year' $selected>$year</option>";
                                                                    $year++;
                                                                }
                                                            ?>
                                                        </select>
                                                    </div>
                                                    <input type="hidden" name="dateTime" value="<?php if ($this->data['contentState'] === "edit") { echo date('H:i:s', strtotime($itemInfo['date'])); } else { echo date('H:i:s'); } ?>"/>
                                                </div>
                                            </div>
                                            <?php endif; ?>
                                        </div>
                                        <div class="col-xs-12 col-sm-12 col-md-4">
                                            <div id="contentAuthor" class="form-group">
                                                <?php $authorID = $this->data['contentState'] === 'edit' ? $itemInfo['author'] : $_SESSION['userID'] ; ?>
                                                <?php if ( isset($_SESSION['role']) and $_SESSION['role'] == 'admin' ): ?>
                                                <h4><label for="author"><?php echo Armadillo_Language::msg('ARM_CONTENT_AUTHOR_TEXT'); ?></label></h4>
                                                <select class="form-control" id="author" name="author">
                                                    <?php
                                                        $users = Armadillo_User::listUsers('true');
                                                        foreach ( $users as $user ) {
                                                            $selected = $user['id'] == $authorID ? "selected='selected'" : '';
                                                            $userID = $user['id'];
                                                            $username = $user['username'];
                                                            $name = $user['name'] == '' ? "No name provided (" . Armadillo_Language::msg('ARM_USER_SUMMARY_LOGIN_ID_LABEL') . ": $username)" : $user['name'] . " (" . Armadillo_Language::msg('ARM_USER_SUMMARY_LOGIN_ID_LABEL') . ": $username)";
                                                            echo "<option value='$userID' $selected>$name</option>";
                                                        }
                                                    ?>
                                                </select>
                                                <?php else: ?>
                                                <input class="form-control" type="hidden" id="author" name="author" value="<?php echo $authorID; ?>" />
                                                <?php endif; ?>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="<?php if ($this->data['contentType'] === 'post') { echo "postContent"; } ?> form-group">
                                        <?php if ( $this->data['contentType'] !== 'soloContent' ): ?>
                                        <input type="text" class="title form-control input-lg" name="title" placeholder="Enter <?php htmlout(ucfirst($this->data['contentType'])); ?> Title Here" <?php if ($this->data['contentState'] === "edit"): ?>value="<?php htmlout($itemInfo['title']); ?>" <?php endif; ?>/>
                                        <p class="bg-info sidebarNotification"><?php echo Armadillo_Language::msg('ARM_CONTENT_EDIT_SIDEBAR_NOTIFICATION'); ?></p>
                                        <?php else: ?>
                                        <h4><label for="title" class="title soloContent"><?php echo Armadillo_Language::msg('ARM_SOLO_CONTENT_TITLE_TEXT'); ?><img src="./../../<?php if ($this->data['contentType'] === 'post') { echo "../../"; } if ($this->data['contentState'] === 'edit') { echo "../"; } ?>../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SOLO_CONTENT_TITLE_TOOLTIP'); ?>" class="qtooltip soloContent" /></label></h4>
                                        <input type="text" class="title soloContent form-control input-lg" name="title" placeholder="<?php echo Armadillo_Language::msg('ARM_SOLO_CONTENT_LOCATION_PLACEHOLDER_TEXT'); ?>" <?php if ($this->data['contentState'] === "edit"): ?>value="<?php htmlout($itemInfo['title']); ?>" <?php endif; ?>/>
                                        <?php endif; ?>
                                    </div>
                                    <?php if ( $this->data['contentType'] === 'blog' or ( isset($itemInfo['type']) and $itemInfo['type'] === 'blog' ) ): ?>
                                    <?php echo Armadillo_Language::msg('ARM_CONTENT_EDIT_BLOG_MESSAGE'); ?>
                                    <?php else: ?>
                                    <!-- Content Entry -->
                                    <div class="<?php if ($this->data['contentType'] === 'post') { echo "postContent"; } ?>">
                                        <div id='contentEditorContainer'>
                                            <?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
                                            <!-- Rich Text Editor -->
                                            <textarea class="form-control" id="content" name="content" rows="15"><?php if ( $this->data['contentState'] === "edit") { if ( isset($itemInfo['format']) and $itemInfo['format'] == 'html' ) { htmlout($itemInfo['content']); } else { echo Parsedown::instance()->text($itemInfo['content']); } } ?></textarea>
                                            <?php else: ?>
                                            <!-- Markdown Editor -->
                                            <textarea id="content" name="content" data-uk-htmleditor="{markdown:true}"><?php if ( $this->data['contentState'] === "edit" and ( isset($itemInfo['format']) and $itemInfo['format'] == 'markdown' ) ) { echo $itemInfo['content']; } ?></textarea>
                                            <?php if ($this->data['contentState'] === "edit" and ( !isset($itemInfo['format']) or $itemInfo['format'] == 'html' ) ): ?>
                                            <script>content = document.getElementById('content'); content.value = toMarkdown('<?php echo str_replace(array("\n", "\r"),'', str_replace("'","\'",$itemInfo['content'])); ?>');</script>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ( $this->data['contentType'] === 'page' or $this->data['contentType'] === 'blog' or ( isset($itemInfo) and $itemInfo['type'] === 'blog' ) ): ?>
                                    <!-- Sidebar Content Entry -->
                                    <div id="sidebarEditorContainer" class="panel panel-default">
                                        <div class="panel-heading">
                                            <h3 class="panel-title"><?php echo Armadillo_Language::msg('ARM_CONTENT_EDIT_SIDEBAR_TEXT'); ?></h3>
                                        </div>
                                        <div class="panel-body">
                                            <?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
                                            <!-- Rich Text Editor -->
                                            <textarea id="sidebar" name="sidebar"><?php if ($this->data['contentState'] === "edit") { if ( isset($itemInfo['format']) and $itemInfo['format'] == 'html' ) { htmlout($itemInfo['sidebarContent']); } else { echo Parsedown::instance()->text($itemInfo['sidebarContent']); } } ?></textarea>
                                            <?php else: ?>
                                            <!-- Markdown Editor -->
                                            <textarea id="sidebar" name="sidebar" data-uk-htmleditor="{markdown:true}"><?php if ($this->data['contentState'] === "edit" and ( isset($itemInfo['format']) and $itemInfo['format'] == 'markdown' ) ) { echo $itemInfo['sidebarContent']; } ?></textarea>
                                            <?php if ($this->data['contentState'] === "edit" and ( !isset($itemInfo['format']) or $itemInfo['format'] == 'html' ) ): ?>
                                            <script>sidebarContent = document.getElementById('sidebar'); sidebarContent.value = toMarkdown('<?php echo str_replace(array("\n", "\r"),'', str_replace("'","\'",$itemInfo['sidebarContent'])); ?>');</script>
                                            <?php endif; ?>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                    <?php elseif ( $this->data['contentType'] === 'post' ): ?>
                                    <div class="row">
                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                            <div class="postContent">
                                                <div class="postComements">
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="displayPostComments" value="TRUE" <?php if ($this->data['contentState'] === "edit" && $itemInfo['displayComments'] == TRUE) { echo "checked='checked'"; } ?>/>
                                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_COMMENTS_SHOW'); ?>
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="displayPostComments" value="FALSE" <?php if ($this->data['contentState'] === "edit" && $itemInfo['displayComments'] == FALSE) { echo "checked='checked'"; } ?>/>
                                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_COMMENTS_HIDE'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-6 col-sm-6 col-md-6">
                                            <div class="postSummary text-right">
                                                <div class="checkbox">
                                                    <label>
                                                        <input type="checkbox" name="displayPostSummary" value="TRUE" <?php if ($this->data['contentState'] === "edit" && $itemInfo['displaySummary'] == TRUE) { echo "checked='checked'"; } ?>/>
                                                        <?php echo Armadillo_Language::msg('ARM_POST_DISPLAY_SUMMARY_OPTION_TEXT'); ?><img src="./../../<?php if ($this->data['contentType'] === 'post') { echo "../../"; } if ($this->data['contentState'] === 'edit') { echo "../"; } ?>../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_POST_DISPLAY_SUMMARY_OPTION_TOOLTIP'); ?>" class="qtooltip" />
                                                    </label>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="row">
                                        <div class="col-xs-12 col-sm-6 col-md-6">
                                            <!-- Post Categories -->
                                            <div id="postCategories" class="">
                                                <div id="categoriesList">
                                                    <label for="categories"><?php echo Armadillo_Language::msg('ARM_CONTENT_CATEGORIES'); ?><img src="./../../<?php if ($this->data['contentType'] === 'post') { echo "../../"; } if ($this->data['contentState'] === 'edit') { echo "../"; } ?>../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_CONTENT_CATEGORIES_TOOLTIP'); ?>" class="qtooltip" /></label>
                                                    <?php 
                                                        $categories = '';
                                                        if ($this->data['contentState'] === 'edit') {
                                                            foreach ($itemInfo['terms'] as $term) {
                                                                $categories .= ( $term['termType'] == 'category' && $term['postID'] == $itemInfo['id'] ) ? $term['termName'] . "," : '';
                                                            }
                                                        }
                                                    ?>
                                                    <input class="form-control" type="text" id="categories" name="categories" value="<?php echo $categories; ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <div class="col-xs-12 col-sm-6 col-md-6">
                                            <!-- Post Tags -->
                                            <div id="postTags" class="">
                                                <div id="tagsList">
                                                    <label for="tags"><?php echo Armadillo_Language::msg('ARM_CONTENT_TAGS'); ?><img src="./../../<?php if ($this->data['contentType'] === 'post') { echo "../../"; } if ($this->data['contentState'] === 'edit') { echo "../"; } ?>../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_CONTENT_TAGS_TOOLTIP'); ?>" class="qtooltip" /></label>
                                                    <?php 
                                                        $tags = '';
                                                        if ($this->data['contentState'] === 'edit') {
                                                            foreach ($itemInfo['terms'] as $term) {
                                                                $tags .= ( $term['termType'] == 'tag' && $term['postID'] == $itemInfo['id'] ) ? $term['termName'] . "," : '';
                                                            }
                                                        }
                                                    ?>
                                                    <input class="form-control" type="text" id="tags" name="tags" value="<?php echo $tags; ?>" />
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endif; ?>
                                    <?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and $_COOKIE['armadillo']['editorType'] == 'richtext' ) ): ?>
                                    <input type="hidden" name="format" value="html"/>
                                    <?php else: ?>
                                    <input type="hidden" name="format" value="markdown"/>
                                    <?php endif; ?>
                                    <?php if ($this->data['contentState'] === "edit"): ?>
                                    <input type="hidden" name="id" value="<?php htmlout($itemInfo['id']); ?>"/>
                                        <?php if( $this->data['contentType'] === 'page' or $this->data['contentType'] === 'soloContent' ): ?>
                                    <input type="hidden" name="date" value="<?php htmlout($itemInfo['date']); ?>"/>
                                        <?php endif; ?>
                                    <input type="hidden" name="_METHOD" value="PUT"/>
                                    <?php endif; ?>
                                </div>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-xs-12 col-sm-12 col-md-12">
                                <div class="form-group">
                                    <input type="submit" name="saveType" value="<?php echo Armadillo_Language::msg('ARM_PUBLISH_TEXT'); ?>" id="Publish" class="btn btn-green" />&nbsp;&nbsp;&nbsp;<input type="submit" name="saveType" value="<?php echo Armadillo_Language::msg('ARM_SAVE_DRAFT_TEXT'); ?>" id="SaveDraft" class="btn btn-primary"/>&nbsp;&nbsp;&nbsp;<a class="btn btn-danger pull-right" href="./../<?php if ($this->data['contentState'] === 'edit') { echo "../"; } ?>"><?php echo Armadillo_Language::msg('ARM_CANCEL_TEXT'); ?></a>
                                </div>
                            </div>
                        </div>
                    </form>
                </div>
                <?php else: $armadillo->redirect('./../../'); endif; ?>
                <?php else: $armadillo->redirect('./../../'); endif; ?>
            <?php else: $armadillo->redirect('./../../'); endif; ?>
            </div>
        </div>
        <div id="bottomPanel">
            <div class="armadillo_version">Web Version <?php echo $GLOBALS['armadilloVersion']; ?></div>
            <div class="armadillo_info">
                <a href="http://docs.nimblehost.com/"><?php echo Armadillo_Language::msg('ARM_DOCUMENTATION_LINK'); ?></a></a>
            </div>
            <div class="clearer"></div>
        </div>
        <div class="clearer"></div>
        <script src="//code.jquery.com/jquery-2.1.4.min.js"></script>
        <script src="//code.jquery.com/ui/1.11.4/jquery-ui.min.js"></script>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/armadilloAdmin.js"></script>
        <?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/redactor/redactor.min.js"></script>
            <?php if ( $_SESSION['language'] !== 'default' ): ?>
            <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/redactor/_langs/<?php echo $_SESSION['language']; ?>.js"></script>
            <?php endif; ?>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/redactor/plugins.min.js"></script>
        <?php else: ?>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/armadillo-markdown.js"></script>
        <?php endif; ?>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/selectize.min.js"></script>
        <script type="text/javascript">
            $().ready(function() {
                var toolbarButtons = ['html', 'undo', 'redo', 'format', 'bold', 'italic', 'deleted', 'underline',
                                        'lists', 'indent', 'outdent', 'image', 'file', 'link', 'line'];
                $R.options = {
                    autoresize: false,
                    <?php if ( $_SESSION['language'] !== 'default' ): ?>
                    lang: '<?php echo $_SESSION['language']; ?>',
                    <?php endif; ?>
                    deniedTags: ['html', 'head', 'link', 'body', 'meta', 'applet'],
                    replaceDivs: false,
                    // phpTag: true,
                    imageUpload: '<?php echo $rootUri; ?>/../core/model/redactorFunctions.php?armURL=<?php echo $_SESSION['armURL']; ?>',
                    imageUploadErrorCallback: errorCallback,
                    imageManagerJson: '<?php echo $rootUri; ?>/../media/images.json',
                    imageResizable: true,
                    imagePosition: true,
                    fileUpload: '<?php echo $rootUri; ?>/../core/model/redactorFunctions.php?armURL=<?php echo $_SESSION['armURL']; ?>',
                    fileManagerJson: '<?php echo $rootUri; ?>/../media/files.json',
                    <?php if ($_COOKIE['armadillo']['editorType'] == 'basictext'): ?>
                    buttons: ['format', 'bold', 'italic', 'deleted', 'lists', 'link', 'line', 'image', 'file'],
                    plugins: ['imagemanager', 'filemanager']
                    <?php else: ?>
                    buttons: toolbarButtons,
                    plugins: ['imagemanager', 'filemanager', 'video', 'table', 'alignment', 'fontsize', 'fontcolor', 'textdirection', 'counter']
                    <?php endif; ?>
                }
                <?php if ( !isset($itemInfo) or (isset($itemInfo['type']) and $itemInfo['type'] !== 'blog') ): ?>
                    <?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
                $R('#content', {
                    minHeight: '<?php if ( ($this->data['contentType'] === 'post') ): ?>285<?php else: ?>335<?php endif; ?>px',
                });
                $R('#summaryContent', {
                    minHeight: '200px'
                });
                    <?php endif; ?>
                <?php endif; ?>
                <?php if ( ($this->data['contentType'] !== 'post') ): ?>
                    <?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
                $R('#sidebar', {
                    minHeight: '335px'
                });
                    <?php endif; ?>
                <?php else: ?>
                $('#categories').selectize({
                    plugins: ['remove_button'],
                    delimiter: ',',
                    persist: false,
                    valueField: 'category',
                    labelField: 'category',
                    searchField: 'category',
                    options: [
                        <?php 
                            if ( isset($this->data['terms']) ) {
                                $categoryOptions = '';
                                foreach ($terms as $term) {
                                    if ($term['termType'] == 'category') {
                                        $name = str_replace("'", "\'", $term['termName']);
                                        $categoryOptions .= "{category: '$name'},";
                                    }
                                }
                                $categoryOptions = rtrim($categoryOptions, ",");
                                echo $categoryOptions;
                            }
                        ?>
                    ],
                    create: function(input) {
                        return {
                            category: input
                        }
                    }
                });
                $('#tags').selectize({
                    plugins: ['remove_button'],
                    delimiter: ',',
                    persist: false,
                    valueField: 'tag',
                    labelField: 'tag',
                    searchField: 'tag',
                    options: [
                        <?php 
                            if ( isset($this->data['terms']) ) {
                                $tagOptions = '';
                                foreach ($terms as $term) {
                                    if ($term['termType'] == 'tag') {
                                        $name = str_replace("'", "\'", $term['termName']);
                                        $tagOptions .= "{tag: '$name'},";
                                    }
                                }
                                $tagOptions = rtrim($tagOptions, ",");
                                echo $tagOptions;
                            }
                        ?>
                    ],
                    create: function(input) {
                        return {
                            tag: input
                        }
                    }
                });
                <?php endif; ?>
                $('#internalLinks').selectize({
                    persist: false,
                    maxItems: 1,
                    valueField: 'url',
                    labelField: 'title',
                    searchField: ['title', 'url'],
                    onItemAdd: function(value, item) { $('#selectedContentLink').val(value); },
                    onItemRemove: function(value) { $('#selectedContentLink').val(''); },
                    options: [<?php echo Armadillo_Content::getContentList(); ?>],
                    render: {
                        item: function(item, escape) {
                            return "<div class='title'>" + item.title + "</div>";
                        },
                        option: function(item, escape) {
                            var label = item.title || item.url;
                            var caption = item.title ? item.url : null;
                            return '<div>' +
                                '<span class="content label">' + escape(label) + '</span>' +
                                (caption ? '<span class="content caption">' + escape(caption) + '</span>' : '') +
                            '</div>';
                        }
                    },
                    create: function(input) { return false; }
                });
                // $(".mediaLibraryToggle").on('click', function(){
                //     if ( $("#mlWrapperBg").css('bottom') != '0px' ) {
                //         $("#bottomPanel").animate({bottom: '-50'},500,function(){ $("#mlWrapperBg").animate({bottom: '0'},500); });
                //     } else { $("#mlWrapperBg").animate({bottom: '-365'},500,function(){ $("#bottomPanel").animate({bottom: '0'},500); }); }
                // });
            });
            $().ready(function(){ $('.qtooltip').qtip({content: false, position: { my: 'bottom center', at: 'top center' }, style: { classes: 'armadilloTooltip' } }); });
            ArmadilloAdmin.addTagsCategories();
            ArmadilloAdmin.toggleContentDataEntryFields();
            function errorCallback(obj, json) { alert(obj.error); }
        </script>
        <?php if ( $this->data['contentType'] !== 'soloContent' ): ?>
        <script>/* Bootstrap: dropdown.js v3.3.5 Copyright 2011-2015 Twitter, Inc. Licensed under MIT (https://github.com/twbs/bootstrap/blob/master/LICENSE) */ +function(t){"use strict";function o(o){var e=o.attr("data-target");e||(e=o.attr("href"),e=e&&/#[A-Za-z]/.test(e)&&e.replace(/.*(?=#[^\s]*$)/,""));var n=e&&t(e);return n&&n.length?n:o.parent()}function e(e){e&&3===e.which||(t(r).remove(),t(a).each(function(){var n=t(this),r=o(n),a={relatedTarget:this};r.hasClass("open")&&(e&&"click"==e.type&&/input|textarea/i.test(e.target.tagName)&&t.contains(r[0],e.target)||(r.trigger(e=t.Event("hide.bs.dropdown",a)),e.isDefaultPrevented()||(n.attr("aria-expanded","false"),r.removeClass("open").trigger("hidden.bs.dropdown",a))))}))}function n(o){return this.each(function(){var e=t(this),n=e.data("bs.dropdown");n||e.data("bs.dropdown",n=new d(this)),"string"==typeof o&&n[o].call(e)})}var r=".dropdown-backdrop",a='[data-toggle="dropdown"]',d=function(o){t(o).on("click.bs.dropdown",this.toggle)};d.VERSION="3.3.5",d.prototype.toggle=function(n){var r=t(this);if(!r.is(".disabled, :disabled")){var a=o(r),d=a.hasClass("open");if(e(),!d){"ontouchstart"in document.documentElement&&!a.closest(".navbar-nav").length&&t(document.createElement("div")).addClass("dropdown-backdrop").insertAfter(t(this)).on("click",e);var i={relatedTarget:this};if(a.trigger(n=t.Event("show.bs.dropdown",i)),n.isDefaultPrevented())return;r.trigger("focus").attr("aria-expanded","true"),a.toggleClass("open").trigger("shown.bs.dropdown",i)}return!1}},d.prototype.keydown=function(e){if(/(38|40|27|32)/.test(e.which)&&!/input|textarea/i.test(e.target.tagName)){var n=t(this);if(e.preventDefault(),e.stopPropagation(),!n.is(".disabled, :disabled")){var r=o(n),d=r.hasClass("open");if(!d&&27!=e.which||d&&27==e.which)return 27==e.which&&r.find(a).trigger("focus"),n.trigger("click");var i=" li:not(.disabled):visible a",s=r.find(".dropdown-menu"+i);if(s.length){var p=s.index(e.target);38==e.which&&p>0&&p--,40==e.which&&p<s.length-1&&p++,~p||(p=0),s.eq(p).trigger("focus")}}}};var i=t.fn.dropdown;t.fn.dropdown=n,t.fn.dropdown.Constructor=d,t.fn.dropdown.noConflict=function(){return t.fn.dropdown=i,this},t(document).on("click.bs.dropdown.data-api",e).on("click.bs.dropdown.data-api",".dropdown form",function(t){t.stopPropagation()}).on("click.bs.dropdown.data-api",a,d.prototype.toggle).on("keydown.bs.dropdown.data-api",a,d.prototype.keydown).on("keydown.bs.dropdown.data-api",".dropdown-menu",d.prototype.keydown)}(jQuery);$('.dropdown-toggle').dropdown();</script>
        <script>
            $('#metaTagHelper ul.dropdown-menu li a').on('click', function(){
                var metaContent = document.getElementById('metaContent');
                switch ( $(this).attr('class') ) {
                    case 'metaKeywords': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta name="keywords" content="KEYWORDS, COMMA SEPARATED">'; break;
                    case 'metaDescription': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta name="description" content="CHANGE THIS CONTENT">'; break;
                    case 'facebookOG-Image': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta property="og:image" content="HTTP://URL-TO-YOUR-IMAGE-HERE.COM">'; break;
                    case 'facebookOG-Video': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta property="og:video" content="HTTP://URL-TO-YOUR-VIDEO-HERE.COM">'; break;
                    case 'facebookOG-Title': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta property="og:title" content="CONTENT TITLE">'; break;
                    case 'facebookOG-Description': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta property="og:description" content="CONTENT DESCRIPTION">'; break;
                    case 'facebookOG-URL': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta property="og:url" content="HTTP://CANONICAL-URL-TO-YOUR-SITE-HERE.COM">'; break;
                    case 'facebookOG-SiteName': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta property="og:site_name" content="NAME OF YOUR SITE">'; break;
                    case 'facebookOG-Type': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta property="og:type" content="BLOG, ETC.">'; break;
                    case 'twitter-Card': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta property="twitter:card" content="SUMMARY">'; break;
                    case 'twitter-Site': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta property="twitter:site" content="@siteUsername">'; break;
                    case 'twitter-Creator': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta property="twitter:creator" content="@creatorUsername">'; break;
                    case 'linkStylesheet': metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<link rel="stylesheet" href="HTTP://URL-TO-STYLESHEET-HERE.COM">'; break;
                    default: metaContent.value += metaContent.value == '' ? '' : '\n'; metaContent.value += '<meta name="" content="">'; break;
                }
            });
        </script>
        <?php endif; ?>
        <script type="text/javascript" src="<?php echo $rootUri; ?>/../core/scripts/jquery.fancybox-min.js"></script>
    </body>
</html>
