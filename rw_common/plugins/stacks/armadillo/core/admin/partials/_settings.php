<!-- Settings Admin Panel -->
<div class="settingsAdminPanel">
    <h2 class="settingsAdminTab">
        <?php echo Armadillo_Language::msg('ARM_SETTINGS_TEXT'); ?>
    </h2>
    <div class="row">
        <div class="col-xs-12 col-sm-12 col-md-12">
            <div id="settingsToggle">
                <p class="text-center">
                    <span class="btn-group margin-center" data-toggle="buttons">
                        <label class="btn btn-default active">
                            <input type="radio" name="settingsToggle" id="generalSettingsToggle" value="general" autocomplete="off" checked>
                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_GENERAL_SETTINGS_TEXT'); ?>
                            </input>
                        </label>
                        <label class="btn btn-default">
                            <input type="radio" name="settingsToggle" id="blogSettingsToggle" value="blog" autocomplete="off">
                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_SETTINGS_TEXT'); ?>
                            </input>
                        </label>
                    </span>
                </p>
            </div>
        </div>
    </div>
    <div id="saveResults" class="bg-success"></div>
    <div class="saveSettingsButton text-center">
        <button type="button" class="btn btn-success saveAllSettings" value="">
            <?php echo Armadillo_Language::msg('ARM_SETTINGS_SAVE_BUTTON_TEXT'); ?>
        </button>
        <div class="updateProgress"></div>
    </div>
    <?php $options = Armadillo_Data::getCurrentOptions(); ?>
    <form class="optionsWrapper leftOption">
        <div class="settingsHint text-center help-block">
            <?php echo Armadillo_Language::msg('ARM_SETTINGS_USAGE_HINT'); ?>
        </div>
        <div class="panel-group" id="settingsAccordion" role="tablist" aria-multiselectable="true">
            <!-- Blog Specific Settings -->
            <div id="blogSettings">
                <?php $blogSettings = Armadillo_Post::getAllBlogSettings(); ?>
                <input type="hidden" name="blogList" value="<?php foreach ( $blogSettings as $blogDetails ) { echo $blogDetails['id'] . ','; } ?>">
                <div class="row">
                    <div class="col-xs-12">
                        <div class="form-inline well well-sm pull-left">
                            <div class="form-group">
                                <label for="blogSelect" class="">
                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_SELECT_TEXT'); ?>
                                </label>
                                <select class="form-control" id="blogSelect" name="blogSelect">
                                    <?php

                                    foreach ( $blogSettings as $blogDetails ) {
                                        echo "<option value='blogID_" . $blogDetails['id'] . "'>" . $blogDetails['title'] . "</option>";
                                    }

                                    ?>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <?php foreach ($blogSettings as $blogDetails): ?>
                <div id="blogID_<?php echo $blogDetails['id']; ?>-Settings" class="blogID_<?php echo $blogDetails['id']; ?>-Settings blogSettings">
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingOne_blogID_<?php echo $blogDetails['id']; ?>">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseOne_blogID_<?php echo $blogDetails['id']; ?>" aria-expanded="true" aria-controls="collapseOne_blogID_<?php echo $blogDetails['id']; ?>">
                                            <!-- blog options header -->
                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_OPTIONS'); ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne_blogID_<?php echo $blogDetails['id']; ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingOne_blogID_<?php echo $blogDetails['id']; ?>">
                                    <div class="panel-body">
                                        <!-- content for blog options -->
                                        <div class="">
                                            <div id="blogUrlSettings" class="settingSection form-group">
                                                <h4 for="blogUrl" class="settingsSectionHeader">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_URL'); ?>
                                                    <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_URL_TOOLTIP'); ?>" class="qtooltip" />
                                                </h4>
                                                <label for="blogUrl" class="blogUrlLabel textLabel sr-only">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_URL'); ?>
                                                </label>
                                                <input class="form-control" type="text" id="blogUrl" name="blogID_<?php echo $blogDetails['id']; ?>-blogUrl" placeholder="http://example.com/blog/index.php" value="<?php echo $blogDetails['blog_url']; ?>"/>
                                            </div>
                                            <hr/>
                                            <div class="blogOptions blogPostsPerPage settingsSection form-group">
                                                <h4 for="blogDateFormat" class="settingsSectionHeader">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_DATE_FORMAT_TEXT'); ?>
                                                </h4>
                                                <p class="help-block">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_DATE_FORMAT_NOTE'); ?>
                                                </p>
                                                <select id="blogDateFormat" name="blogID_<?php echo $blogDetails['id']; ?>-blogDateFormat" class="form-control">
                                                    <option value="WMDY" <?php if ($blogDetails['blog_date_format'] == 'WMDY') { echo "selected='selected'"; } ?>>Weekday, Month Day, Year</option>
                                                    <option value="WDMY" <?php if ($blogDetails['blog_date_format'] == 'WDMY') { echo "selected='selected'"; } ?>>Weekday, Day Month, Year</option>
                                                    <option value="WDpMY" <?php if ($blogDetails['blog_date_format'] == 'WDpMY') { echo "selected='selected'"; } ?>>Weekday, Day. Month Year</option>
                                                    <option value="YMDW" <?php if ($blogDetails['blog_date_format'] == 'YMDW') { echo "selected='selected'"; } ?>>Year Month Day, Weekday</option>
                                                    <option value="MDY" <?php if ($blogDetails['blog_date_format'] == 'MDY') { echo "selected='selected'"; } ?>>Month Day, Year</option>
                                                    <option value="DMY" <?php if ($blogDetails['blog_date_format'] == 'DMY') { echo "selected='selected'"; } ?>>Day Month, Year</option>
                                                    <option value="DpMY" <?php if ($blogDetails['blog_date_format'] == 'DpMY') { echo "selected='selected'"; } ?>>Day. Month Year</option>
                                                    <option value="YMD" <?php if ($blogDetails['blog_date_format'] == 'YMD') { echo "selected='selected'"; } ?>>Year Month Day</option>
                                                    <option value="MY" <?php if ($blogDetails['blog_date_format'] == 'MY') { echo "selected='selected'"; } ?>>Month Year</option>
                                                    <option value="YM" <?php if ($blogDetails['blog_date_format'] == 'YM') { echo "selected='selected'"; } ?>>Year Month</option>
                                                </select>
                                            </div>
                                            <hr/>
                                            <div class="blogOptions blogPostsPerPage settingsSection form-group">
                                                <h4 class="settingsSectionHeader">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_PAGINATION_TEXT'); ?>
                                                </h4>
                                                <p class="help-block">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_PAGINATION_NOTE'); ?>
                                                </p>
                                                <select id="blogPostsPerPage" name="blogID_<?php echo $blogDetails['id']; ?>-blogPostsPerPage" class="form-control">
                                                    <option value="1" <?php if ($blogDetails['blogposts_per_page'] == 1) { echo "selected='selected'"; } ?>>1</option>
                                                    <option value="2" <?php if ($blogDetails['blogposts_per_page'] == 2) { echo "selected='selected'"; } ?>>2</option>
                                                    <option value="3" <?php if ($blogDetails['blogposts_per_page'] == 3) { echo "selected='selected'"; } ?>>3</option>
                                                    <option value="4" <?php if ($blogDetails['blogposts_per_page'] == 4) { echo "selected='selected'"; } ?>>4</option>
                                                    <option value="5" <?php if ($blogDetails['blogposts_per_page'] == 5) { echo "selected='selected'"; } ?>>5</option>
                                                    <option value="10" <?php if ($blogDetails['blogposts_per_page'] == 10) { echo "selected='selected'"; } ?>>10</option>
                                                    <option value="15" <?php if ($blogDetails['blogposts_per_page'] == 15) { echo "selected='selected'"; } ?>>15</option>
                                                    <option value="20" <?php if ($blogDetails['blogposts_per_page'] == 20) { echo "selected='selected'"; } ?>>20</option>
                                                    <option value="25" <?php if ($blogDetails['blogposts_per_page'] == 25) { echo "selected='selected'"; } ?>>25</option>
                                                    <option value="30" <?php if ($blogDetails['blogposts_per_page'] == 30) { echo "selected='selected'"; } ?>>30</option>
                                                </select>
                                            </div>
                                            <hr/>
                                            <div class="blogOptions blogArchiveLinks settingsSection form-group">
                                                <h4 class="settingsSectionHeader">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_ARCHIVE_LINKS_TEXT'); ?>
                                                </h4>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-displayBlogArchiveLinks" value="TRUE" id="showBlogArchiveLinks" <?php if ($blogDetails['display_blog_archive_links'] == TRUE) { echo "checked='checked'"; } ?>/> 
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_ARCHIVE_LINKS_SHOW'); ?>
                                                    </label>
                                                </div>
                                                <div id="blogArchiveLinkSettings" class="settingDetails form-group panel panel-default">
                                                    <div class="panel-body">
                                                        <label for="blogArchiveLinksFormat">
                                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_ARCHIVE_LINKS_FORMAT_TEXT'); ?>
                                                        </label>
                                                        <select id="blogArchiveLinksFormat" name="blogID_<?php echo $blogDetails['id']; ?>-blogArchiveLinksFormat" class="form-control">
                                                            <option value="MY" <?php if ($blogDetails['blog_archive_links_format'] == 'MY') { echo "selected='selected'"; } ?>>Month Year</option>
                                                            <option value="YM" <?php if ($blogDetails['blog_archive_links_format'] == 'YM') { echo "selected='selected'"; } ?>>Year Month</option>
                                                        </select>
                                                    </div>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-displayBlogArchiveLinks" value="FALSE" id="hideBlogArchiveLinks" <?php if ($blogDetails['display_blog_archive_links'] == FALSE) { echo "checked='checked'"; } ?>/> 
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_ARCHIVE_LINKS_HIDE'); ?>
                                                    </label>
                                                </div>
                                            </div>
                                            <hr/>
                                            <div class="blogOptions blogComments settingsSection form-group">
                                                <h4 class="settingsSectionHeader">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_COMMENTS_TEXT'); ?>
                                                </h4>
                                                <p class="help-block">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_COMMENTS_OPTION_SCOPE'); ?>
                                                </p>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-displayBlogComments" value="TRUE" id="showComments" <?php if ($blogDetails['display_blog_comments'] == TRUE) { echo "checked='checked'"; } ?>/>
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_COMMENTS_SHOW'); ?>
                                                    </label>
                                                </div>
                                                <div id="disqusShortnameEntry" class="settingDetails form-group panel panel-default">
                                                    <div class="panel-body">
                                                        <label for="disqusShortname" class="disqusShortnameLabel textLabel">
                                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_COMMENTS_DISQUS_SHORTNAME'); ?>
                                                            <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_COMMENTS_DISQUS_SHORTNAME_TOOLTIP'); ?>" class="qtooltip" />
                                                        </label>
                                                        <input class="form-control" type="text" id="disqusShortname" name="blogID_<?php echo $blogDetails['id']; ?>-disqusShortname" placeholder="" value="<?php echo $blogDetails['disqus_shortname']; ?>"/>
                                                    </div>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-displayBlogComments" value="FALSE" id="hideComments" <?php if ($blogDetails['display_blog_comments'] == FALSE) { echo "checked='checked'"; } ?>/> 
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_COMMENTS_HIDE'); ?>
                                                    </label>
                                                </div> 
                                            </div>
                                            <hr/>
                                            <div class="blogOptions postAuthor settingsSection form-group">
                                                <h4 class="settingsSectionHeader">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_POST_AUTHOR_TEXT'); ?>
                                                </h4>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-displayPostAuthor" value="TRUE" id="showAuthor" <?php if ($blogDetails['display_blog_post_author'] == TRUE) { echo "checked='checked'"; } ?>/> 
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_POST_AUTHOR_SHOW'); ?>
                                                    </label>
                                                </div>
                                                <div id="postAuthorSettings" class="settingDetails panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="radio">
                                                            <label>
                                                                <!-- <span class="postAuthorDisplayName"></span> -->
                                                                <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-postAuthorName" value="username" id="displayAuthorUsername" <?php if ($blogDetails['post_author_display_name'] == 'username') { echo "checked='checked'"; } ?>/> 
                                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_POST_AUTHOR_USERNAME'); ?>
                                                            </label>
                                                            <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_POST_AUTHOR_USERNAME_TOOLTIP'); ?>" class="qtooltip" />
                                                        </div>
                                                        <div class="radio">
                                                            <label>
                                                                <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-postAuthorName" value="fullname" id="displayAuthorFullname" <?php if ($blogDetails['post_author_display_name'] == 'fullname') { echo "checked='checked'"; } ?>/> 
                                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_POST_AUTHOR_FULLNAME'); ?>
                                                            </label>
                                                            <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_POST_AUTHOR_FULLNAME_TOOLTIP'); ?>" class="qtooltip" />
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-displayPostAuthor" value="FALSE" id="hideAuthor" <?php if ($blogDetails['display_blog_post_author'] == FALSE) { echo "checked='checked'"; } ?>/> 
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_POST_AUTHOR_HIDE'); ?>
                                                    </label>
                                                </div>

                                            </div>
                                            <hr/>
                                            <div class="blogOptions postSummaryText settingsSection form-group">
                                                <h4 class="settingsSectionHeader panel-title">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_POST_SUMMARY_TEXT'); ?>
                                                </h4>
                                                <!-- <p><div id="readMoreTextEntry" class="textEntry"> -->
                                                <label for="readMoreText" class="readMoreTextLabel textLabel">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_POST_READ_MORE_LINK'); ?>
                                                    <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_POST_READ_MORE_LINK_TOOLTIP'); ?>" class="qtooltip" />
                                                </label>
                                                <input class="form-control" type="text" id="readMoreText" name="blogID_<?php echo $blogDetails['id']; ?>-readMoreText" placeholder="" value="<?php echo $blogDetails['blog_readmore_text']; ?>"/>
                                                <!-- </div></p> -->
                                            </div>
                                            <hr/>
                                            <div class="blogOptions showMorePostsButton settingsSection form-group">
                                                <h4 class="settingsSectionHeader">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_SHOW_MORE_POSTS_CUSTOMIZATION'); ?>
                                                </h4>
                                                <div id="showMorePostsTextEntry" class="textEntry form-group">
                                                    <label for="showMorePostsButtonText" class="showMorePostsTextLabel textLabel">
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_SHOW_MORE_POSTS_BUTTON_TEXT'); ?>
                                                        <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_SHOW_MORE_POSTS_BUTTON_TOOLTIP'); ?>" class="qtooltip" />
                                                    </label>
                                                    <input class="form-control" type="text" id="showMorePostsButtonText" name="blogID_<?php echo $blogDetails['id']; ?>-showMorePostsButtonText" placeholder="Show more posts" value="<?php echo $blogDetails['showmoreposts_button_text']; ?>"/>
                                                </div>
                                                <div class="colorOption form-group">
                                                    <input class="color colorWell" id="showMorePostsButtonBackground" name="blogID_<?php echo $blogDetails['id']; ?>-showMorePostsButtonBackground" value="<?php echo $blogDetails['showmoreposts_button_bgcolor']; ?>"> <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_SHOW_MORE_POSTS_BG_COLOR'); ?>
                                                </div>
                                                <div class="colorOption form-group">
                                                    <input class="color colorWell" id="showMorePostsButtonTextColor" name="blogID_<?php echo $blogDetails['id']; ?>-showMorePostsButtonTextColor" value="<?php echo $blogDetails['showmoreposts_button_textcolor']; ?>"> <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_SHOW_MORE_POSTS_TEXT_COLOR'); ?>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingTwo_blogID_<?php echo $blogDetails['id']; ?>">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseTwo_blogID_<?php echo $blogDetails['id']; ?>" aria-expanded="true" aria-controls="collapseTwo_blogID_<?php echo $blogDetails['id']; ?>">
                                            <!-- blog rss header -->
                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS'); ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwo_blogID_<?php echo $blogDetails['id']; ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwo_blogID_<?php echo $blogDetails['id']; ?>">
                                    <div class="panel-body">
                                        <!-- content for blog rss options -->
                                        <div class="">
                                            <div class="blogRSS settingsSection">
                                                <div class="form-group">
                                                    <h4 class="settingsSectionHeader">
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_OPTIONS'); ?>
                                                    </h4>
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-enableBlogRSS" value="TRUE" class="enableRSS" <?php if ($blogDetails['enable_blog_rss'] == TRUE) { echo "checked='checked'"; } ?>/>
                                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_ENABLE'); ?>
                                                        </label>
                                                    </div>
                                                    <div class="radio">
                                                        <label>
                                                            <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-enableBlogRSS" value="FALSE" class="enableRSS" <?php if ($blogDetails['enable_blog_rss'] == FALSE) { echo "checked='checked'"; } ?>/>
                                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_DISABLE'); ?>
                                                        </label>
                                                    </div>
                                                </div>
                                                <fieldset>
                                                    <div id="rssFeedSettings" class="textEntry">
                                                        <div class="form-group">
                                                            <label for="blogRSStitle" class=" textLabel">
                                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_TITLE'); ?>
                                                                <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_TITLE_TOOLTIP'); ?>" class="qtooltip" />
                                                            </label>
                                                            <input class="form-control" type="text" id="blogRSStitle" name="blogID_<?php echo $blogDetails['id']; ?>-blogRSStitle" placeholder="" value="<?php echo $blogDetails['blog_rss_title']; ?>"/>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="blogRSSdescription" class=" textLabel" id="rssDescriptionLabel">
                                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_DESCRIPTION'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_DESCRIPTION_TOOLTIP'); ?>" class="qtooltip" /></label>
                                                            <textarea class="form-control" id="blogRSSdescription" name="blogID_<?php echo $blogDetails['id']; ?>-blogRSSdescription" rows="3">
                                                                <?php echo $blogDetails['blog_rss_description']; ?>
                                                            </textarea>
                                                        </div>
                                                        <div class="form-group">
                                                            <label for="blogRSScopyright" class=" textLabel">
                                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_COPYRIGHT'); ?>
                                                                <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_COPYRIGHT_TOOLTIP'); ?>" class="qtooltip" />
                                                            </label>
                                                            <input class="form-control" type="text" id="blogRSScopyright" name="blogID_<?php echo $blogDetails['id']; ?>-blogRSScopyright" placeholder="" value="<?php echo $blogDetails['blog_rss_copyright']; ?>"/>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                                <hr/>
                                                <fieldset>
                                                    <div class="form-group">
                                                        <h4 class="settingsSectionHeader">
                                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_DETAILS'); ?>
                                                        </h4>
                                                        <div id="rssFeedLinkDetails" class="textEntry">
                                                            <div class="form-group">
                                                                <label for="blogRSSlinkName" class=" textLabel">
                                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_LINK'); ?>
                                                                    <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_LINK_TOOLTIP'); ?>" class="qtooltip" />
                                                                </label>
                                                                <input class="form-control" type="text" id="blogRSSlinkName" name="blogID_<?php echo $blogDetails['id']; ?>-blogRSSlinkName" placeholder="" value="<?php echo $blogDetails['blog_rss_linkname']; ?>"/>
                                                            </div>
                                                            <div class="form-group">
                                                                <label for="blogRSSfileName" class=" textLabel">
                                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_FILE'); ?>
                                                                    <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_FILE_TOOLTIP'); ?>" class="qtooltip" />
                                                                </label>
                                                                <input class="form-control blogRSSfileName" type="text" id="blogRSSfileName" name="blogID_<?php echo $blogDetails['id']; ?>-blogRSSfileName" placeholder="feed.rss" value="<?php echo $blogDetails['blog_rss_filename']; ?>"/>
                                                            </div>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                                <hr/>
                                                <fieldset>
                                                    <div class="form-group">
                                                        <h4 class="settingsSectionHeader">
                                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_RSS_FEED_CUSTOMIZATION'); ?>
                                                        </h4>
                                                        <label>
                                                            <input type="checkbox" name="blogID_<?php echo $blogDetails['id']; ?>-blogRSSenableCustomFeed" value="TRUE" id="enableCustomRSSfeed" <?php if ($blogDetails['blog_rss_enable_customfeed'] == TRUE) { echo "checked='checked'"; } ?>/>
                                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_CUSTOM_FEED_OPTION'); ?>
                                                            <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_CUSTOM_FEED_OPTION_TOOLTIP'); ?>" class="qtooltip" />
                                                        </label>
                                                    </div>
                                                    <div class="form-group">
                                                        <div id="rssFeedCustomization" class="textEntry">
                                                            <label for="blogRSScustomFeedURL" class=" textLabel">
                                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_CUSTOM_RSS_URL'); ?>
                                                                <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_CUSTOM_RSS_URL_TOOLTIP'); ?>" class="qtooltip" />
                                                            </label>
                                                            <input class="form-control" type="text" id="blogRSScustomFeedURL" name="blogID_<?php echo $blogDetails['id']; ?>-blogRSScustomFeedURL" placeholder="" value="<?php echo $blogDetails['blog_rss_customfeed_url']; ?>"/>
                                                        </div>
                                                    </div>
                                                </fieldset>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingEleven_blogID_<?php echo $blogDetails['id']; ?>">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseEleven_blogID_<?php echo $blogDetails['id']; ?>" aria-expanded="true" aria-controls="collapseEleven_blogID_<?php echo $blogDetails['id']; ?>">
                                            <!-- blog category options header -->
                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_CATEGORIES_TEXT'); ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseEleven_blogID_<?php echo $blogDetails['id']; ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingEleven_blogID_<?php echo $blogDetails['id']; ?>">
                                    <div class="panel-body">
                                        <!-- content for blog category options -->
                                        <div class="">
                                            <div class="blogOptions blogCategories settingsSection">
                                                <h4 class="settingsSectionHeader">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_CATEGORIES_TEXT'); ?>
                                                </h4>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-displayBlogCategories" value="TRUE" id="showCategories" <?php if ($blogDetails['display_blog_categories'] == TRUE) { echo "checked='checked'"; } ?>/>
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_CATEGORIES_SHOW'); ?>
                                                    </label>
                                                </div>
                                                <div class="settingDetails panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group">
                                                            <label class="categoriesTitleLabel textLabel">
                                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_CATEGORIES_TITLE'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_CATEGORIES_TITLE_TOOLTIP'); ?>" class="qtooltip" /></label>
                                                            <input class="form-control" type="text" id="categoriesTitle" name="blogID_<?php echo $blogDetails['id']; ?>-categoriesTitle" placeholder="" value="<?php echo $blogDetails['blog_categories_title']; ?>"/>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="categoriesTitleSeparator textLabel">
                                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_TERM_SEPARATOR'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_CATEGORIES_SEPARATOR_TOOLTIP'); ?>" class="qtooltip" /></label>
                                                            <input class="form-control" type="text" id="categoriesSeparator" name="blogID_<?php echo $blogDetails['id']; ?>-categoriesSeparator" placeholder="" value="<?php echo $blogDetails['blog_categories_separator']; ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-displayBlogCategories" value="FALSE" id="hideCategoreis" <?php if ($blogDetails['display_blog_categories'] == FALSE) { echo "checked='checked'"; } ?>/>
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_CATEGORIES_HIDE'); ?>
                                                    </label>
                                                </div>
                                                <hr/>
                                                <div id="categoriesTitleEntry" class="textEntry">
                                                    <h3>
                                                        <?php echo Armadillo_Language::msg('ARM_CONTENT_CATEGORIES'); ?>
                                                    </h3>
                                                    <div class="editTerms">
                                                        <?php
                                                        
                                                        $terms = Armadillo_Content::getTerms('',$blogDetails['id']);
                                                        $rowNumber = 0;
                                                        foreach ($terms as $term) {
                                                            if ( $term['termType'] === 'category' ) {
                                                                $rowNumber++;
                                                                $rowClass = ( $rowNumber % 2 ) ? 'oddRow' : 'evenRow';
                                                                echo '<div class="termRow">' . 
                                                                    '<p id="termID_' . $term["termID"] . '" class="edit-inline dblclick ' . $rowClass . '">' . 
                                                                        $term["termName"] . 
                                                                    '</p>' . 
                                                                    '<span id="delete_termID_' . $term["termID"] . '" class="deleteTerm">' . Armadillo_Language::msg('ARM_DELETE_TEXT') . 
                                                                    '</span>' . 
                                                                '</div>';
                                                            } else {
                                                                continue;
                                                            }
                                                        }

                                                        ?>
                                                    </div>
                                                </div>
                                            </div>  
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                        <div class="col-xs-12 col-sm-6 col-md-6">
                            <div class="panel panel-default">
                                <div class="panel-heading" role="tab" id="headingTwelve_blogID_<?php echo $blogDetails['id']; ?>">
                                    <h4 class="panel-title">
                                        <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseTwelve_blogID_<?php echo $blogDetails['id']; ?>" aria-expanded="true" aria-controls="collapseTwelve_blogID_<?php echo $blogDetails['id']; ?>">
                                            <!-- blog tag options header -->
                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_TAGS_TEXT'); ?>
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseTwelve_blogID_<?php echo $blogDetails['id']; ?>" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTwelve_blogID_<?php echo $blogDetails['id']; ?>">
                                    <div class="panel-body">
                                        <div class="">
                                            <div class="blogOptions blogTags settingsSection">
                                                <h4 class="settingsSectionHeader">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_TAGS_TEXT'); ?>
                                                </h4>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-displayBlogTags" value="TRUE" id="showTags" <?php if ($blogDetails['display_blog_tags'] == TRUE) { echo "checked='checked'"; } ?>/>
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_TAGS_SHOW'); ?>
                                                    </label>
                                                </div>
                                                <div class="settingDetails panel panel-default">
                                                    <div class="panel-body">
                                                        <div class="form-group">
                                                            <label class="tagsTitleLabel textLabel">
                                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_TAGS_TITLE'); ?>
                                                                <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_TAGS_TITLE_TOOLTIP'); ?>" class="qtooltip" />
                                                            </label>
                                                            <input class="form-control" type="text" id="tagsTitle" name="blogID_<?php echo $blogDetails['id']; ?>-tagsTitle" placeholder="" value="<?php echo $blogDetails['blog_tags_title']; ?>"/>
                                                        </div>
                                                        <div class="form-group">
                                                            <label class="tagsTitleSeparator textLabel">
                                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_TERM_SEPARATOR'); ?>
                                                                <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_TAGS_SEPARATOR_TOOLTIP'); ?>" class="qtooltip" />
                                                            </label>
                                                            <input class="form-control" type="text" id="tagsSeparator" name="blogID_<?php echo $blogDetails['id']; ?>-tagsSeparator" placeholder="" value="<?php echo $blogDetails['blog_tags_separator']; ?>"/>
                                                        </div>
                                                    </div>
                                                </div>
                                                <div class="radio">
                                                    <label>
                                                        <input type="radio" name="blogID_<?php echo $blogDetails['id']; ?>-displayBlogTags" value="FALSE" id="hideTags" <?php if ($blogDetails['display_blog_tags'] == FALSE) { echo "checked='checked'"; } ?>/>
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_TAGS_HIDE'); ?>
                                                    </label>
                                                </div>
                                                <hr/>
                                                <div id="tagsTitleEntry" class="textEntry">
                                                    <h3>
                                                        <?php echo Armadillo_Language::msg('ARM_CONTENT_TAGS'); ?>
                                                    </h3>
                                                    <div class="editTerms">
                                                        <?php
                                                            $rowNumber = 0;
                                                            foreach ($terms as $term) {
                                                                if ( $term['termType'] === 'tag' ) {
                                                                    $rowNumber++;
                                                                    $rowClass = ( $rowNumber % 2 ) ? 'oddRow' : 'evenRow';
                                                                    echo '<div class="termRow">' .
                                                                        '<p id="termID_' . $term["termID"] . '" class="edit-inline dblclick ' . $rowClass . '">' . 
                                                                            $term["termName"] . 
                                                                        '</p>' . 
                                                                        '<span id="delete_termID_' . $term["termID"] . '" class="deleteTerm">' . Armadillo_Language::msg('ARM_DELETE_TEXT') . 
                                                                        '</span>' .
                                                                    '</div>';
                                                                } else {
                                                                    continue;
                                                                }
                                                            }
                                                        ?>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <?php endforeach; ?>
            </div>
            <!-- General Site Settings -->
            <div id="generalSettings">
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingThirteen">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseThirteen" aria-expanded="true" aria-controls="collapseThirteen">
                                        <!-- enabled content options header -->
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_ENABLED_CONTENT_MODES_TITLE'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseThirteen" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThirteen">
                                <div class="panel-body">
                                    <h4 class="settingsSectionHeader">
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_ENABLED_CONTENT_MODES_TEXT'); ?>
                                        <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_ENABLED_CONTENT_MODES_TOOLTIP'); ?>" class="qtooltip" />
                                    </h4>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enableBlogContent" value="TRUE" id="enableBlogContentMode" <?php if ($options['enable_blog_content'] == TRUE) { echo "checked='checked'"; } ?>/>
                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_ENABLE_BLOG_CONTENT'); ?>
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enablePageContent" value="TRUE" id="enablePageContentMode" <?php if ($options['enable_page_content'] == TRUE) { echo "checked='checked'"; } ?>/>
                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_ENABLE_PAGE_CONTENT'); ?>
                                        </label>
                                    </div>
                                    <div class="checkbox">
                                        <label>
                                            <input type="checkbox" name="enableSoloContent" value="TRUE" id="enableSoloContentMode" <?php if ($options['enable_solo_content'] == TRUE) { echo "checked='checked'"; } ?>/>
                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_ENABLE_SOLO_CONTENT'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingFourteen">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseFourteen" aria-expanded="true" aria-controls="collapseFourteen">
                                        <!-- content editor type options header -->
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_EDITOR_TYPE'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseFourteen" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingFourteen">
                                <div class="panel-body">
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="editorType" value="basictext" id="basicTextEditor" <?php if ($options['editor_type'] == 'basictext') { echo "checked='checked'"; } ?>/> 
                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_EDITOR_TYPE_BASICTEXT'); ?>
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="editorType" value="richtext" id="richTextEditor" <?php if ($options['editor_type'] == 'richtext') { echo "checked='checked'"; } ?>/> 
                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_EDITOR_TYPE_RICHTEXT'); ?>
                                        </label>
                                    </div>
                                    <div class="radio">
                                        <label>
                                            <input type="radio" name="editorType" value="markdown" id="markdownEditor" <?php if ($options['editor_type'] == 'markdown') { echo "checked='checked'"; } ?>/> 
                                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_EDITOR_TYPE_MARKDOWN'); ?>
                                        </label>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingThree">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseThree" aria-expanded="true" aria-controls="collapseThree">
                                        <!-- primary nav header -->
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_TEXT'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseThree" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingThree">
                                <div class="panel-body">
                                    <!-- content for primary nav options -->
                                    <div class="">
                                        <div class="menuDisplayOptions settingsSection">
                                            <h4 class="settingsSectionHeader">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_DISPLAY_OPTIONS_TEXT'); ?>
                                            </h4>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="menuDisplayOptions" value="showMenu" id="showMenu" <?php if ($options['menu_display_option'] == 'showMenu') { echo "checked='checked'"; } ?>/>
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_SHOW_STANDARD'); ?>
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="menuDisplayOptions" value="hideMenu" id="hideMenu" <?php if ($options['menu_display_option'] == 'hideMenu') { echo "checked='checked'"; } ?>/>
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_HIDE'); ?>
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="menuDisplayOptions" value="moveToBefore" id="moveToBefore" <?php if ($options['menu_display_option'] == 'moveToBefore') { echo "checked='checked'"; } ?>/>
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_MOVE_BEFORE'); ?>
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="menuDisplayOptions" value="moveToAfter" id="moveToAfter" <?php if ($options['menu_display_option'] == 'moveToAfter') { echo "checked='checked'"; } ?>/>
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_MOVE_AFTER'); ?>
                                                </label>
                                            </div>
                                            <div id="siteNavContainerEntry" class="form-group">
                                                <div class="form-group">
                                                    <label class="navContainerLabel">
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_MAIN_CONTAINER'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_MAIN_CONTAINER_TOOLTIP'); ?>" class="qtooltip" /></label>
                                                    <input class="form-control" type="text" id="mainNavContainer" name="mainNavContainer" placeholder="#navcontainer" value="<?php echo $options['site_main_nav_container']; ?>"/>
                                                </div>
                                                <div class="form-group">
                                                    <label class="navContainerLabel">
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_SECONDARY_CONTAINER'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_SECONDARY_CONTAINER_TOOLTIP'); ?>" class="qtooltip" /></label>
                                                    <input class="form-control" type="text" id="secondNavContainer" name="secondNavContainer" placeholder="(Optional)" value="<?php echo $options['site_second_nav_container']; ?>" />
                                                </div>
                                                <div class="form-group">
                                                    <label class="navContainerLabel">
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_OTHER_CONTAINER'); ?><img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_OTHER_CONTAINER_TOOLTIP'); ?>" class="qtooltip" /></label>
                                                    <input class="form-control" type="text" id="thirdNavContainer" name="thirdNavContainer" placeholder="(Optional)" value="<?php echo $options['site_third_nav_container']; ?>" />
                                                </div>
                                            </div>
                                        </div>
                                        <hr/>
                                        <div class="menuHierarchy settingsSection form-group">
                                            <h4 class="settingsSectionHeader">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_MENU_HIERARCHY_TEXT'); ?>
                                            </h4>
                                            <p class="help-block">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_MENU_HIERARCHY_NOTE'); ?>
                                            </p>
                                            <?php Armadillo_Data::displayMenu(); ?>
                                        </div>
                                        <hr/>
                                        <div class="menuColors settingsSection">
                                            <h4 class="settingsSectionHeader">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_COLOR_OPTIONS'); ?>
                                            </h4>
                                            <div class="colorOption form-group">
                                                <input class="color colorWell" id="navMenuBgColor" name="navMenuBgColor" value="<?php echo $options['navmenu_bgcolor']; ?>"> 
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_COLOR_BG'); ?>
                                            </div>
                                            <div class="colorOption form-group">
                                                <input class="color colorWell" id="navMenuTextColor" name="navMenuTextColor" value="<?php echo $options['navmenu_textcolor']; ?>"> 
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_COLOR_TEXT'); ?>
                                            </div>
                                            <div class="colorOption form-group">
                                                <input class="color colorWell" id="navMenuHoverColor" name="navMenuHoverColor" value="<?php echo $options['navmenu_hovercolor']; ?>"> 
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_COLOR_HOVERBG'); ?>
                                            </div>
                                            <div class="colorOption form-group">
                                                <input class="color colorWell" id="navMenuCurrentPageBgColor" name="navMenuCurrentPageBgColor" value="<?php echo $options['navmenu_currentpage_bgcolor']; ?>"> 
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_COLOR_CURRENT_BG'); ?>
                                            </div>
                                            <div class="colorOption form-group">
                                                <input class="color colorWell" id="navMenuCurrentPageTextColor" name="navMenuCurrentPageTextColor" value="<?php echo $options['navmenu_currentpage_textcolor']; ?>"> 
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_COLOR_CURRENT_LINK'); ?>
                                            </div>
                                            <div class="colorOption form-group">
                                                <input class="color colorWell" id="navMenuDropDownBgColor" name="navMenuDropDownBgColor" value="<?php echo $options['navmenu_dropdown_bgcolor']; ?>"> 
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_COLOR_DDBG'); ?>
                                            </div>
                                            <div class="colorOption form-group">
                                                <input class="color colorWell" id="navMenuDropDownHoverColor" name="navMenuDropDownHoverColor" value="<?php echo $options['navmenu_dropdown_hovercolor']; ?>"> 
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_COLOR_DDHOVERBG'); ?>
                                            </div>
                                            <div class="colorOption form-group">
                                                <input class="color colorWell" id="navMenuDropDownTextColor" name="navMenuDropDownTextColor" value="<?php echo $options['navmenu_dropdown_textcolor']; ?>"> 
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_PRIMARY_NAV_COLOR_DDTEXT'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingFour">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseFour" aria-expanded="true" aria-controls="collapseFour">
                                        <!-- default content header -->
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_DEFAULT_CONTENT_TEXT'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseFour" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingFour">
                                <div class="panel-body">
                                    <!-- section for default content options -->
                                    <div class="">
                                        <div class="defaultContent settingsSection">
                                            <p class="settingsSectionHeader help-block">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_DEFAULT_CONTENT_NOTE'); ?>
                                            </p>
                                            <?php $pages = Armadillo_Content::getPagesList(); ?>
                                            <select class="form-control" id="defaultContentDisplayed" name="defaultContentDisplayed">
                                                <?php foreach ( $pages as $page ): ?>
                                                    <option value="<?php echo $page['id']; ?>" <?php if ($page['default_content']) { echo "selected='selected'"; } ?>>
                                                        <?php echo $page['title']; ?>
                                                    </option>
                                                <?php endforeach; ?>
                                                    <option value="none" <?php if ($options['default_content'] == 'none') { echo "selected='selected'"; } ?>>
                                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_DEFAULT_CONTENT_DISABLE'); ?>
                                                    </option>
                                            </select>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingFive">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseFive" aria-expanded="true" aria-controls="collapseFive">
                                        <!-- custom styles header -->
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_CUSTOM_STYLES_TEXT'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseFive" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingFive">
                                <div class="panel-body">
                                    <!-- content for custom styles -->
                                    <div class="">
                                        <div class="customStyles settingsSection">
                                            <h4 class="settingsSectionHeader">Custom CSS</h4>
                                            <p class="note help-block">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_CUSTOM_STYLES_NOTE'); ?>
                                            </p>
                                            <textarea class="form-control codeEntry" id="armadilloCustomStyles" name="armadilloCustomStyles" rows="10">
                                                <?php echo $options['armadillo_custom_css']; ?>
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingSix">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseSix" aria-expanded="true" aria-controls="collapseSix">
                                        <!-- admin/login link header -->
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_ADMIN_LINK'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseSix" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingSix">
                                <div class="panel-body">
                                    <!-- content for admin/login link options -->
                                    <div class="">
                                        <div class="adminLink settingsSection">
                                            <h4 class="settingsSectionHeader">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_DISPLAY_OPTIONS_TEXT'); ?>
                                            </h4>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="displayAdminLink" value="TRUE" id="showAdminLink" <?php if ($options['display_admin_link'] == TRUE) { echo "checked='checked'"; } ?>/>
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_ADMIN_LINK_SHOW'); ?>
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="displayAdminLink" value="FALSE" id="hideAdminLink" <?php if ($options['display_admin_link'] == FALSE) { echo "checked='checked'"; } ?>/>
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_ADMIN_LINK_HIDE'); ?>
                                                </label>
                                            </div>
                                            <div id="adminLinkTextEntry" class="textEntry form-group">
                                                <label class="adminLinkTextLabel textLabel">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_ADMIN_LINK_TEXT'); ?>
                                                    <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_ADMIN_LINK_TOOLTIP'); ?>" class="qtooltip" />
                                                </label>
                                                <input class="form-control" type="text" id="adminLinkText" name="adminLinkText" placeholder="Admin" value="<?php echo $options['adminlink_text']; ?>"/>
                                            </div>
                                            <div class="colorOption form-group">
                                                <input class="color colorWell" id="adminLinkColor" name="adminLinkColor" value="<?php echo $options['adminlink_color']; ?>"> <?php echo Armadillo_Language::msg('ARM_SETTINGS_ADMIN_LINK_COLOR'); ?>
                                            </div>
                                            <div class="colorOption form-group">
                                                <input class="color colorWell" id="adminLinkHoverColor" name="adminLinkHoverColor" value="<?php echo $options['adminlink_hovercolor']; ?>"> <?php echo Armadillo_Language::msg('ARM_SETTINGS_ADMIN_LINK_HOVER_COLOR'); ?>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingSeven">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseSeven" aria-expanded="true" aria-controls="collapseSeven">
                                        <!-- timezone/language options header -->
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_TIMEZONE') . ' & ' . Armadillo_Language::msg('ARM_USER_FORM_LANGUAGE'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseSeven" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingSeven">
                                <div class="panel-body">
                                    <!-- content for timezone/language options -->
                                    <div class="">
                                        <div class="localization settingsSection">
                                            <div class="form-group">
                                                <label for="timezone">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_TIMEZONE'); ?>
                                                </label>
                                                <select class="form-control" name="timezone" id="timezone">
                                                    <?php 

                                                    foreach ($worldTimezones as $zone => $name) {
                                                        $selected = '';
                                                        if ($options['timezone'] === $zone) { $selected = "selected='selected'"; }
                                                        echo "<option value='$zone' $selected>$name</option>"; 
                                                    } 

                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="siteLanguage">
                                                    <?php echo Armadillo_Language::msg('ARM_USER_FORM_LANGUAGE'); ?>
                                                </label>
                                                <select class="form-control" name="siteLanguage" id="siteLanguage">
                                                    <option value="default" <?php if ( !isset($options['site_language']) or ($options['site_language'] == 'default') ) { echo "selected=\"selected\""; } ?>>
                                                        <?php echo Armadillo_Language::msg('ARM_USER_FORM_LANGUAGE_AUTODETECT'); ?>
                                                    </option>
                                                    <?php

                                                    $localizations = Armadillo_Language::listInstalledLanguages();

                                                    foreach ($localizations as $lang) {
                                                        $selected = ( isset($options['site_language']) and ($options['site_language'] == $lang['abbr']) ) ? "selected=\"selected\"" : '' ;
                                                        echo '<option value="'.$lang['abbr'].'" '.$selected.'>'.$lang['name'].'</option>';
                                                    } 

                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingEight">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseEight" aria-expanded="true" aria-controls="collapseEight">
                                        <!-- dropbox integration header -->
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_DROPBOX_INTEGRATION'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseEight" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingEight">
                                <div class="panel-body">
                                    <!-- content for dropbox integration options -->
                                    <div class="">
                                        <div class="dropboxSync settingsSection">
                                            <?php if (version_compare(PHP_VERSION, '5.3.1') >= 0): ?>
                                            <h4 class="settingsSectionHeader">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_DROPBOX_INTEGRATION_SECTION_TEXT'); ?>
                                            </h4>
                                            <p class="help-block">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_DROPBOX_INTEGRATION_SECTION_NOTE'); ?>
                                            </p>
                                            <a href='./../dropbox/' id='syncToDropbox' class='btn btn-success greenButton'>
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_DROPBOX_INTEGRATION_BUTTON_TEXT'); ?>
                                            </a>
                                            <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_DROPBOX_INTEGRATION_BUTTON_TOOLTIP'); ?>" class="qtooltip" />
                                            <?php else: ?>
                                            <p class="bg-warning">
                                                <span class="label label-default">Note</span>
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_DROPBOX_INTEGRATION_HOSTING_INCOMPATIBLE'); ?>
                                            </p>
                                            <?php endif; ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="row">
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingNine">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseNine" aria-expanded="true" aria-controls="collapseNine">
                                        <!-- security options header -->
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_SECURITY'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseNine" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingNine">
                                <div class="panel-body">
                                    <!-- content for security options -->
                                    <div class="">
                                        <div class="security settingsSection">
                                            <div class="form-group">
                                                <label for="allowedLoginAttempts">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_ALLOWED_LOGIN_ATTEMPTS'); ?>
                                                    <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_ALLOWED_LOGIN_ATTEMPTS_TOOLTIP'); ?>" class="qtooltip" />
                                                </label>
                                                <select class="form-control" name="allowedLoginAttempts" id="allowedLoginAttempts">
                                                    <?php 

                                                    foreach ($allowedLoginAttempts as $number => $amount) {
                                                        $selected = '';
                                                        if ($options['allowed_login_attempts'] == $number) { 
                                                            $selected = "selected='selected'"; 
                                                        }
                                                        echo "<option value='$number' $selected>$amount</option>"; 
                                                    } 

                                                    ?>
                                                </select>
                                            </div>
                                            <div class="form-group">
                                                <label for="blockedLoginTimeframe">
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOCKED_LOGIN_TIMEFRAME'); ?>
                                                    <img src="./../../core/images/questionmark.png" title="<?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOCKED_LOGIN_TIMEFRAME_TOOLTIP'); ?>" class="qtooltip" />
                                                </label>
                                                <select class="form-control" name="blockedLoginTimeframe" id="blockedLoginTimeframe">
                                                    <?php 

                                                    foreach ($blockedLoginTimeframe as $time => $value) {
                                                        $selected = '';
                                                        if ($options['blocked_login_timeframe'] == $time) { 
                                                            $selected = "selected='selected'"; 
                                                        }
                                                        echo "<option value='$time' $selected>$value</option>"; 
                                                    } 

                                                    ?>
                                                </select>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="col-xs-12 col-sm-6 col-md-6">
                        <div class="panel panel-default">
                            <div class="panel-heading" role="tab" id="headingTen">
                                <h4 class="panel-title">
                                    <a role="button" data-toggle="collapse" data-parent="#settingsAccordion" href="#collapseTen" aria-expanded="true" aria-controls="collapseTen">
                                        <!-- social sharing header -->
                                        <?php echo Armadillo_Language::msg('ARM_SETTINGS_SOCIAL_SHARING'); ?>
                                    </a>
                                </h4>
                            </div>
                            <div id="collapseTen" class="panel-collapse collapse in" role="tabpanel" aria-labelledby="headingTen">
                                <div class="panel-body">
                                    <!-- content for social sharing options -->
                                    <div class="">
                                        <div class="socialSharing settingsSection">
                                            <h4 class="settingsSectionHeader">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_SOCIAL_SHARING'); ?>
                                            </h4>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="displaySocialSharingLinks" value="TRUE" id="showSocialSharingLinks" <?php if ($options['display_social_sharing_links'] == TRUE) { echo "checked='checked'"; } ?>/>
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_SOCIAL_SHARING_LINKS_SHOW'); ?>
                                                </label>
                                            </div>
                                            <div class="radio">
                                                <label>
                                                    <input type="radio" name="displaySocialSharingLinks" value="FALSE" id="hideSocialSharingLinks" <?php if ($options['display_social_sharing_links'] == FALSE) { echo "checked='checked'"; } ?>/>
                                                    <?php echo Armadillo_Language::msg('ARM_SETTINGS_SOCIAL_SHARING_LINKS_HIDE'); ?>
                                                </label>
                                            </div>
                                            <p class="note help-block">
                                                <?php echo Armadillo_Language::msg('ARM_SETTINGS_SOCIAL_SHARING_NOTE'); ?>
                                            </p>
                                            <textarea class="form-control codeEntry" id="armadilloSocialSharingCode" name="armadilloSocialSharingCode" rows="10">
                                                <?php echo $options['social_sharing_code'] == NULL ? '' : $options['social_sharing_code']; ?>
                                            </textarea>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </form>
    <div class="clearer"></div>
    <p class="text-center note">
        <?php Armadillo_Data::backupInfo(); ?>
    </p>
    <p class="text-center">
        <a href="./#topOfPage">
            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BACK_TO_TOP'); ?>
        </a>
    </p>
    <div class="clearer"></div>
</div>
