<?php

$selectedItem = Armadillo_Post::getSingleItem($this->data['contentType'], $this->data['contentID']);
        
if ( $_SESSION['role'] === 'admin' || $_SESSION['role'] === 'editor' || $selectedItem['author'] === $_SESSION['userID'] ): 

?>
<div class="bg-danger">
    <p>
        <?php echo Armadillo_Language::msg('ARM_DELETE_CONTENT_MESSAGE'); ?>
    </p>
    <?php
        if ( $selectedItem['type'] == 'blog' ) {
            //todo - display message that orphaned posts (if there are any) will be assigned to a different blog
        }
    ?>
    <form id="confirmDeletion" action="./../../" method="post">
        <input class="form-control" type="hidden" name="_METHOD" value="DELETE" />
        <input class="form-control" type="hidden" name="id" value="<?php htmlout($this->data['contentID']); ?>" />
        <?php if ( $selectedItem['type'] == 'blog' ): ?>
        <div class="row" style="margin-top:10px;">
            <div class="col-xs-12">
                <div class="form-inline well well-sm clearfix" style="margin-bottom:0;">
                    <p>
                        <?php echo Armadillo_Language::msg('ARM_BLOG_ORPHANED_POSTS_NOTIFICATION'); ?>
                    </p>
                    <div class="form-group">
                        <label for="blogSelect" class="">
                            <?php echo Armadillo_Language::msg('ARM_SETTINGS_BLOG_SELECT_TEXT'); ?>
                        </label>
                        <select class="form-control" id="blogSelect" name="blogSelect">
                            <?php
                                $blogSettings = Armadillo_Post::getAllBlogSettings();
                                foreach ( $blogSettings as $blogDetails ) {
                                    if ( $this->data['contentID'] == $blogDetails['id'] ) {
                                        continue;
                                    } else {
                                        echo '<option value="' . $blogDetails['id'] . '"' . '>' . $blogDetails['title'] . '</option>';
                                    }
                                }
                            ?>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <?php endif; ?>
        <input class="btn btn-success" type="submit" name="cancel" value="<?php echo Armadillo_Language::msg('ARM_CANCEL_TEXT'); ?>" />
        <input class="btn btn-danger" type="submit" name="delete" value="<?php echo Armadillo_Language::msg('ARM_DELETE_TEXT'); ?>" />
    </form>
</div>
<div class="selectedItem">
    <?php
        if ( $selectedItem['type'] == 'blog' ) {
            //todo - display blog title and number of posts associated with it
            echo "<h3>" . $selectedItem['title'] . "</h3>";
            echo "<div style='font-size: 1.5em;'>" .
                "<strong>" . Armadillo_Post::numberOfPosts($this->data['contentID']) . "</strong> " . 
                Armadillo_Language::msg('ARM_POST_CONTENT_NAME_PLURAL') . 
            "</div>";
        } else {
            echo "<h2>" . $selectedItem['title'] . "</h2>";
            echo "<div>" . $selectedItem['content'] . "</div>";
        }
    ?>
</div>