<?php

abstract class Armadillo_Content
{
    protected $id;
    protected $blog_id;
    protected $title;
    protected $content;
    protected $sidebarContent;
    protected $metaContent;
    protected $summaryContent;
    protected $date;
    protected $author;
    protected $status;
    protected $format;

    public function __construct( $id = '', $blog_id = '', $title = '', $content = '', $sidebarContent = '', $metaContent = '', $summaryContent = '', $date = '', $lastEdited = '', $author = '', $status = '', $format = '' )
    {
        $this->id = $id;
        $this->blog_id = $blog_id;
        $this->title = $title;
        $this->content = $content;
        $this->sidebarContent = $sidebarContent;
        $this->metaContent = $metaContent;
        $this->summaryContent = $summaryContent;
        $this->date = $date;
        $this->lastEdited = $lastEdited;
        $this->author = $author;
        $this->status = $status;
        $this->format = $format;
    }

    public function getID()
    {
        return $this->id;
    }

    public function setID( $newID )
    {
        return $this->id = $newID;
    }

    public function getBlogID()
    {
        return $this->blog_id;
    }

    public function setBlogID( $newBlogID )
    {
        return $this->blog_id = $newBlogID;
    }

    public function getTitle()
    {
        return $this->title;
    }

    public function setTitle( $newTitle )
    {
        $this->title = $newTitle;
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent( $newContent )
    {
        $this->content = $newContent;
    }

    public function getSidebarContent()
    {
        return $this->sidebarContent;
    }

    public function setSidebarContent( $newSidebarContent )
    {
        $this->sidebarContent = $newSidebarContent;
    }

    public function getMetaContent()
    {
        return $this->metaContent;
    }

    public function setMetaContent( $newMetaContent )
    {
        $this->metaContent = $newMetaContent;
    }

    public function getSummaryContent()
    {
        return $this->summaryContent;
    }

    public function setSummaryContent( $newSummaryContent )
    {
        $this->summaryContent = $newSummaryContent;
    }

    public function getDate()
    {
        return $this->date;
    }

    public function setDate( $newDate )
    {
        //$newDate = date('Y-m-d' ,strtotime($newDate));//convert submitted date to proper format for storage in database, IF NECESSARY
        $this->date = $newDate;
    }

    public function getLastEdited()
    {
        return $this->lastEdited;
    }

    public function setLastEdited( $newLastEdited )
    {
        //$newDate = date('Y-m-d' ,strtotime($newDate));//convert submitted date to proper format for storage in database, IF NECESSARY
        $this->lastEdited = $newLastEdited;
    }

    public function getAuthor()
    {
        return $this->author;
    }

    public function setAuthor( $newAuthor )
    {
        $this->author = $newAuthor;
    }

    public function getStatus()
    {
        return $this->status;
    }

    public function setStatus( $newStatus )
    {
        $this->status = $newStatus;
    }

    public function getFormat()
    {
        return $this->format;
    }

    public function setFormat( $newFormat )
    {
        $this->format = $newFormat;
    }

    public static function getSummary( $contentType, $blog_id = '' )
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            
            // Display warning that database update is needed, if necessary, and don't show summary to prevent errors from occurring
            $armadillo = Slim::getInstance();
            $armBuildName = $armadillo->getName();
            $armBuildVersion = explode("_", $armBuildName);
            $armBuildVersion = end($armBuildVersion);
            $armOldVersion = isset($_SESSION['armBuildVersion']) ? $_SESSION['armBuildVersion'] : 29;
            if ($armOldVersion < $armBuildVersion) {
                echo Armadillo_Language::msg('ARM_UPDATE_AVAILABLE_GENERAL_ANNOUCEMENT');
                return;
            }


            include 'core/config.php';
            include 'core/connectDB.php';
            $contentType = $dbLink->real_escape_string($contentType);
            $blogPageQuery = $contentType === 'page' ? " OR type='blog'" : '';
            $blogSpecificPostsQuery = $contentType === 'post' ? " AND blog_id='" . $blog_id . "' ORDER BY date DESC" : '';

            $query = "SELECT armadillo_post.id, title, date, last_edited, userid, publish, type, armadillo_user.name, armadillo_user.username FROM armadillo_post
                INNER JOIN armadillo_user ON armadillo_post.userid = armadillo_user.id WHERE type='$contentType'" . $blogPageQuery . $blogSpecificPostsQuery;

            $result = $dbLink->query($query);

            if (!$result) {
                
                if ($contentType == 'post') {
                    $notification = Armadillo_Language::msg('ARM_POST_DISPLAY_SUMMARY_DB_ERROR');
                } elseif ($contentType == 'page') {
                    $notification = Armadillo_Language::msg('ARM_PAGE_DISPLAY_SUMMARY_DB_ERROR');
                } else {
                    $notification = Armadillo_Language::msg('ARM_SOLO_CONTENT_DISPLAY_SUMMARY_DB_ERROR');
                }

                $armadillo->flashNow('notification', $notification . " " . $dbLink->error);
            }

            $summary = array();
            $numberOfItems = $dbLink->affected_rows;

            if ($numberOfItems === 0) {

                if ($contentType == 'post') {
                    $notification = Armadillo_Language::msg('ARM_POST_DISPLAY_SUMMARY_EMPTY'); 
                } elseif ($contentType == 'page') {
                    $notification = Armadillo_Language::msg('ARM_PAGE_DISPLAY_SUMMARY_EMPTY');
                } else {
                    $notification = Armadillo_Language::msg('ARM_SOLO_CONTENT_DISPLAY_SUMMARY_EMPTY');
                }
                echo $notification;
            } else {
                while ( $row = $result->fetch_array() ) {
                    $summary[] = array(
                                    'id' => $row['id'], 
                                    'title' => $row['title'], 
                                    'date' => $row['date'],
                                    'author' => $row['name'],
                                    'username' => $row['username'],
                                    'authorID' => $row['userid'],
                                    'lastEdited' => $row['last_edited'],
                                    'publish' => $row['publish'],
                                    'type' => $row['type']
                                );
                }

                foreach ($summary as $key => $row) {
                    $sortByDate[$key] = strtotime($row['date']);
                }

                array_multisort($sortByDate, SORT_DESC, $summary);

                switch ($_SESSION['siteLanguage']) {
                    case 'bg':
                        setlocale(LC_TIME, "bg.UTF-8", "bg_BG.UTF-8", "Bulgarian");
                        break;
                    case 'cs':
                        setlocale(LC_TIME, "cs.UTF-8", "cs_CS.UTF-8", "cs_CZ.UTF-8", "Czech", "czech");
                        break;
                    case 'de':
                        setlocale(LC_TIME, "de.UTF-8", "de_DE.UTF-8", "German");
                        break;
                    case 'en':
                        setlocale(LC_TIME, "en.UTF-8", "en_EN.UTF-8", "English");
                        break;
                    case 'es':
                        setlocale(LC_TIME, "es.UTF-8", "es_ES.UTF-8", "Spanish");
                        break;
                    case 'fi':
                        setlocale(LC_TIME, "fi.UTF-8", "fi_FI.UTF-8", "Finnish");
                        break;
                    case 'fr':
                        setlocale(LC_TIME, "fr.UTF-8", "fr_FR.UTF-8", "French");
                        break;
                    case 'it':
                        setlocale(LC_TIME, "it.UTF-8", "it_IT.UTF-8", "Italian");
                        break;
                    case 'ja':
                        setlocale(LC_TIME, "ja.UTF-8", "ja_JA.UTF-8", "Japanese");
                        break;
                    case 'nl':
                        setlocale(LC_TIME, "nl.UTF-8", "nl_NL.UTF-8", "Dutch");
                        break;
                    case 'pl':
                        setlocale(LC_TIME, "pl.UTF-8", "pl_PL.UTF-8", "Polish");
                        break;  
                    default:
                        setlocale(LC_TIME, "en.UTF-8", "en_EN.UTF-8", "English");
                        break;
                }

                $contentSummary = "<div id='contentSummary'><table id='summaryList' class='table table-striped table-bordered responsive nowrap' cellspacing='0' width='100%'>";

                //Title Row
                if ($contentType == 'post') {
                    $contentTitleLabel = Armadillo_Language::msg('ARM_POST_TITLE_TEXT');
                } elseif ($contentType == 'page') {
                    $contentTitleLabel = Armadillo_Language::msg('ARM_PAGE_TITLE_TEXT');
                } else {
                    $contentTitleLabel = Armadillo_Language::msg('ARM_SOLO_CONTENT_TITLE_TEXT');
                }
                //$contentSummary .= "<div class='titleRow'><div class='contentTitle'>" . $contentTitleLabel ."</div><div class='contentStatus'>" . Armadillo_Language::msg('ARM_CONTENT_STATUS_LABEL') . "</div><div class='contentActions'>" . Armadillo_Language::msg('ARM_CONTENT_ACTIONS_LABEL') . "</div></div>";
                //$contentSummary .= "<div class='clearer'></div>";

                $contentSummary .= "<thead><tr><th>ID</th><th>" . $contentTitleLabel ."</th><th>" . Armadillo_Language::msg('ARM_CONTENT_AUTHOR_TEXT') . "</th><th>" . Armadillo_Language::msg('ARM_CONTENT_PUBLISH_DATE_TEXT') . "</th><th>" . Armadillo_Language::msg('ARM_CONTENT_LAST_EDITED_TEXT') . "</th><th>" . Armadillo_Language::msg('ARM_CONTENT_STATUS_LABEL') . "</th><th class='disabled'>" . Armadillo_Language::msg('ARM_CONTENT_ACTIONS_LABEL') . "</th></tr></thead>"
                                . "<tbody>";
                                //. "<tfoot><tr><th>" . $contentTitleLabel ."</th><th>" . Armadillo_Language::msg('ARM_CONTENT_AUTHOR_TEXT') . "</th><th>" . Armadillo_Language::msg('ARM_CONTENT_PUBLISH_DATE_TEXT') . "</th><th>" . Armadillo_Language::msg('ARM_CONTENT_LAST_EDITED_TEXT') . "</th><th>" . Armadillo_Language::msg('ARM_CONTENT_STATUS_LABEL') . "</th><th>" . Armadillo_Language::msg('ARM_CONTENT_ACTIONS_LABEL') . "</th></tr></tfoot>"
                                

                $rowNumber = 0;

                $dateFormat = '';

                //Check for Windows hosting
                $windowsHosting = strtoupper(substr(PHP_OS, 0, 3)) == 'WIN' ? true : false ;

                if ( $_SESSION['dateFormat'] == 'WMDY' ) {
                    $dateFormat = $windowsHosting ? '%A, %B %#d, %Y' : '%A, %B %e, %Y';
                } elseif ( $_SESSION['dateFormat'] == 'WDMY' ) {
                    $dateFormat = $windowsHosting ? '%A, %#d %B, %Y' : '%A, %e %B, %Y';
                } elseif ( $_SESSION['dateFormat'] == 'WDpMY' ) {
                    $dateFormat = $windowsHosting ? '%A, %#d. %B %Y' : '%A, %e. %B %Y';
                } elseif ( $_SESSION['dateFormat'] == 'YMDW' ) {
                    $dateFormat = $windowsHosting ? '%Y %B %#d, %A' : '%Y %B %e, %A';
                } elseif ( $_SESSION['dateFormat'] == 'MDY' ) {
                    $dateFormat = $windowsHosting ? '%B %#d, %Y' : '%B %e, %Y'; 
                } elseif ( $_SESSION['dateFormat'] == 'DMY' ) {
                    $dateFormat = $windowsHosting ? '%#d %B, %Y' : '%e %B, %Y';
                } elseif ( $_SESSION['dateFormat']  == 'DpMY' ) {
                    $dateFormat = $windowsHosting ? '%#d. %B %Y' : '%e. %B %Y';
                } elseif ( $_SESSION['dateFormat'] == 'YMD' ) {
                    $dateFormat = $windowsHosting ? '%Y %B %#d' : '%Y %B %e';
                } elseif ( $_SESSION['dateFormat'] == 'MY' ) {
                    $dateFormat = '%B %Y';
                } elseif ( $_SESSION['dateFormat'] == 'YM' ) {
                    $dateFormat = '%Y %B';
                }

                foreach ($summary as $content) {
                    $rowNumber++;
                    $rowClass = ( $rowNumber % 2 ) ? 'oddRow' : 'evenRow';

                    if ( ( $_SESSION['role'] === 'blogger' or $_SESSION['role'] === 'contributor' ) and $_SESSION['userID'] != $content['authorID'] ) {
                        continue;
                    } else {
                        $contentLink = ($_SESSION['role'] === 'editor' || $_SESSION['role'] === 'admin'|| $content['authorID'] === $_SESSION['userID']) ? "<a href='./edit/" . $content['id'] . "/'>" : "<a href=''>";
                        $contentLastEdited = $content['lastEdited'] == '0000-00-00 00:00:00' ? $content['date'] : $content['lastEdited'];
                        $contentStatus = $content['publish'] == TRUE ? Armadillo_Language::msg('ARM_CONTENT_STATUS_PUBLISHED') : Armadillo_Language::msg('ARM_CONTENT_STATUS_DRAFT');
                        $previewDisabled = $_SESSION['blogURL'] == '' ? "disabled='disabled'" : '';
                        $previewURL = '';
                        if ( $_SESSION['blogURL'] != '' ) {
                            $lastChar = substr($_SESSION['blogURL'], -1);
                            $lastFourChars = substr($_SESSION['blogURL'], -4);
                            if ( $lastChar == '/' or $lastFourChars == '.php' ) {
                                $previewURL = $_SESSION['blogURL'];
                            } else {
                                $previewURL = $_SESSION['blogURL'] . '/';
                            }
                        }
                        $previewContent = ( ($contentType != 'soloContent') and $contentStatus == Armadillo_Language::msg('ARM_CONTENT_STATUS_DRAFT') and ( $_SESSION['role'] === 'editor' || $_SESSION['role'] === 'admin' || $content['authorID'] === $_SESSION['userID'] ) ) ? '<a href="' . $previewURL . '?' . $content['type'] . '_id=' . $content['id'] . '" class="btn btn-purple btn-sm previewContent" rel="draftPreview" title="' . Armadillo_Language::msg('ARM_PREVIEW_TEXT') . '" $previewDisabled><i class="fa fa-eye fa-lg"></i></a>' : '';
                        $deleteContent = ( ( $_SESSION['role'] === 'editor' || $_SESSION['role'] === 'admin'|| $content['authorID'] === $_SESSION['userID'] ) ) ? "<a href='./delete/" . $content['id'] . "/' class='btn btn-danger btn-sm' title='" . Armadillo_Language::msg('ARM_DELETE_TEXT') . "'><i class='fa fa-times fa-lg'></i></a>" : '';
                        $editContent = ($_SESSION['role'] === 'editor' || $_SESSION['role'] === 'admin' || $content['authorID'] === $_SESSION['userID']) ? "<a href='./edit/" . $content['id'] . "/' class='btn btn-primary btn-sm' title='" . Armadillo_Language::msg('ARM_EDIT_TEXT') . "'><i class='fa fa-pencil-square-o fa-lg'></i></a>" : '';
                        $contentActions = "<div class='contentActions'>" . $editContent . $previewContent . $deleteContent . "</div>";
                        $blogHint = $content['type'] == 'blog' ? '<i class="fa fa-list-ul pull-right" style="color:#bbb;"></i>' : '';

                        //Add content to Summary List
                        // $contentSummary .= "<div class='contentRow " . $rowClass . "'>";
                        // $contentSummary .= "<div class='contentTitle'><span class='contentDetails'>" . $contentLink . $content['title'] . "</a><br/>";
                        // $contentSummary .= "<span class='contentInfo'>" . Armadillo_Language::msg('ARM_CONTENT_PUBLISH_DATE_TEXT') . ": " . strftime( $dateFormat, strtotime( $content['date'] ) ) . Armadillo_Language::msg('ARM_CONTENT_AUTHOR_CREDIT') . $content['author'] . "</span></span></div>";
                        // $contentSummary .= "<div class='contentStatus'>" . $contentStatus . "</div>";
                        // $contentSummary .= $contentActions;
                        // $contentSummary .= "<div class='clearer'></div>";
                        // $contentSummary .= "</div>";

                        $contentSummary .= "<tr><td>" . $content['id'] . "</td><td>" . $contentLink . $content['title'] . "</a>" . $blogHint . "</td><td>" . $content['username'] . "</td><td>" . $content['date'] . "</td><td>" . $contentLastEdited . "</td><td>" . $contentStatus . "</td><td>" . $contentActions . "</td></tr>";
                    }
                }
                
                //$contentSummary .= "</div>";
                //$contentSummary .= "<div class='clearer'></div>";

                //Close Content Summary table and HTML Container
                $contentSummary .= "</tbody></table></div><div class='clearer'></div>";
                echo $contentSummary;
            }
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}

    }

    // Echoes the number of posts associated with a particular blog
    public static function numberOfPosts($blog_id)
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            if ( is_numeric($blog_id) ) {

                $query = "SELECT * FROM armadillo_post WHERE type='post' AND blog_id='$blog_id'";

                $result = $dbLink->query($query);

                if (!$result) {
                    return Armadillo_Language::msg('ARM_POST_DISPLAY_SUMMARY_DB_ERROR') . $dbLink->error; 
                } else {
                    return $dbLink->affected_rows;
                }

            } else {
                return Armadillo_Language::msg('ARM_CONTENT_ID_ERROR');
            }
        }
    }

    // Retrieves a complete list of all posts and pages created in Armadillo
    public static function getContentList()
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $query = "SELECT id, title, type FROM armadillo_post WHERE type!='soloContent'";

            $result = $dbLink->query($query);

            if (!$result) { return '{ title: "' . Armadillo_Language::msg('ARM_PAGE_DISPLAY_SUMMARY_DB_ERROR') . '", url: "'. $dbLink->error . '" }'; }

            $contentList = '';
            $numberOfItems = $dbLink->affected_rows;

            if ($numberOfItems === 0) {
                return '{ id: "", title: "No content available", type: "", url: "" }';
            } else {
				$contentURL = '';
                // Check blog URL exists and if cruftless or not
                if ( $_SESSION['blogURL'] != '' ) {
                    $lastChar = substr($_SESSION['blogURL'], -1);
                    $lastFourChars = substr($_SESSION['blogURL'], -4);
                    if ( $lastChar == '/' or $lastFourChars == '.php' ) {
                        $contentURL = $_SESSION['blogURL'];
                    } else {
                        $contentURL = $_SESSION['blogURL'] . '/';
                    }
                }

                // Filter content title so it's url safe and matches the same title created elsewhere in Armadillo
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

                while ( $row = $result->fetch_array() ) {
                    $type = $row['type'] == 'post' ? 'post' : 'page';
                    $titleInURL = preg_replace("/[&,.!?'\"]/", '', $row['title']);
                    $titleInURL = strtolower(preg_replace("/[\s]/", '-', $titleInURL));
                    $titleInURL = strtr($titleInURL, $normalizeChars);
                    $urlQuery = '?' . $type. '_id=' . $row['id'] . '&title=' . $titleInURL;
                    $row['title'] = str_replace("'", "\'", $row['title']);
                    $contentList .= "{id: '". $row['id'] . "', title: '" . $row['title'] . "', type: '" . $row['type'] . "', url: '" . $contentURL . $urlQuery . "'},";
                }

                $contentList = rtrim($contentList, ",");

                return $contentList;
            }
        } else { return '{ id: "", title: "No content available", type: "", url: "" }'; }
    }

    public static function getPagesList()
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $query = "SELECT id, title, default_content FROM armadillo_post WHERE type='page'";

            $result = $dbLink->query($query);

            if (!$result) { $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_PAGE_DISPLAY_SUMMARY_DB_ERROR') . $dbLink->error); }

            $pagesList = array();
            $numberOfPages = $dbLink->affected_rows;

            if ($numberOfPages === 0) { return $pagesList; } else {
                while ( $row = $result->fetch_array() ) {
                    $pagesList[] = array('id' => $row['id'], 'title' => $row['title'], 'default_content' => $row['default_content']);
                }

                return $pagesList;
            }
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function getSingleItem( $contentType, $id )
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';
            $contentType = $dbLink->real_escape_string($contentType);
            $id = $dbLink->real_escape_string($id);

            $terms = array();
            if ($contentType == 'post') { $terms = Armadillo_Content::getTerms($id); }

            $itemQuery = "SELECT * FROM armadillo_post WHERE id = $id";

            $itemResult = $dbLink->query($itemQuery);

            if (!$itemResult) {
                $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_CONTENT_DISPLAY_DB_ERROR') . $dbLink->error);
            }

            $itemRow = $itemResult->fetch_array();
            $itemInfo = array('id' => $itemRow['id'], 'blog_id' => $itemRow['blog_id'], 'title' => $itemRow['title'], 'content' => $itemRow['content'], 'sidebarContent' => $itemRow['sidebar_content'], 'metaContent' => $itemRow['meta_content'], 'summaryContent' => $itemRow['summary_content'], 'date' => $itemRow['date'], 'author' => $itemRow['userid'], 'publish' => $itemRow['publish'], 'type' => $itemRow['type'], 'terms' => $terms, 'displayComments' => $itemRow['display_comments'], 'displaySummary' => $itemRow['display_summary'], 'format' => $itemRow['format']);

            return $itemInfo;
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public function saveItem( $contentType, $item )
    {
        $armadillo = Slim::getInstance();
        /* Saves/Updates a page/post, making sure to first escape any submitted content to protect the database from injection attacks. */
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            if ($armadillo->request()->isPut()) { $id = $dbLink->real_escape_string($item->getID()); }
            $blog_id = $contentType == 'post' ? $dbLink->real_escape_string($item->getBlogID()) : '';
            $title = $dbLink->real_escape_string($item->getTitle());
            $content = $dbLink->real_escape_string($item->getContent());
            $sidebarContent = ( $contentType == 'page' or $contentType == 'blog' ) ? $dbLink->real_escape_string($item->getSidebarContent()) : '';
            $summaryContent = $contentType == 'post' ? $dbLink->real_escape_string($item->getSummaryContent()) : '';
            $metaContent = $dbLink->real_escape_string($item->getMetaContent());
            $date = $dbLink->real_escape_string($item->getDate());
            $lastEdited = $dbLink->real_escape_string($item->getLastEdited());
            $author = $dbLink->real_escape_string($item->getAuthor());
            $status = $dbLink->real_escape_string($item->getStatus());
            $contentType = $dbLink->real_escape_string($contentType);
            $format = $dbLink->real_escape_string($item->getFormat());
            $displayComments = $contentType == 'post' ? $dbLink->real_escape_string($item->getDisplayComments()) : '';
            $displayCommentsQuery = $contentType == 'post' ? ", display_comments='$displayComments'" : '';
            $displaySummary = $contentType == 'post' ? $dbLink->real_escape_string($item->getDisplaySummary()) : '';
            $displaySummaryQuery = $contentType == 'post' ? ", display_summary='$displaySummary'" : '';
            $blogDefaults = $contentType == 'blog' ? ", blog_url='', blog_date_format='WMDY', blogposts_per_page='5', display_blog_comments=FALSE, display_blog_categories=FALSE, display_blog_tags=FALSE, display_blog_archive_links=TRUE, disqus_shortname='', blog_categories_title='Filed in: ', blog_categories_separator=' | ', blog_tags_title='Tags: ', blog_tags_separator=', ', blog_archive_links_format='MY', post_author_display_name='fullname', blog_readmore_text='Read more', showmoreposts_button_text='Show more posts', showmoreposts_button_bgcolor='666666', showmoreposts_button_textcolor='ffffff', enable_blog_rss=TRUE, blog_rss_title='My RSS Feed', blog_rss_description='', blog_rss_copyright='', blog_rss_linkname='RSS Feed', blog_rss_filename='', blog_rss_enable_customfeed=FALSE, blog_rss_customfeed_url='', blog_rss_summarize_entries=FALSE" : '' ;

            // Check if date is set in future, reset hours/minutes/seconds to zero so content is published at midnight local time on that date
            if ( strtotime($date) > time()) {
                $date = date('Y-n-j', strtotime($date)) . ' 00:00:00';
            }

            if ($armadillo->request()->isPost()) {
                $query = "INSERT INTO armadillo_post SET
                            blog_id='$blog_id',
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
                            type='$contentType'" . $displayCommentsQuery . $displaySummaryQuery . $blogDefaults;
            }

            if ($armadillo->request()->isPut()) {
                $query = "UPDATE armadillo_post SET
                            blog_id='$blog_id',
                            title='$title',
                            content='$content',
                            sidebar_content='$sidebarContent',
                            meta_content='$metaContent',
                            summary_content='$summaryContent',
                            date='$date',
                            last_edited='$lastEdited',
                            userid='$author',
                            format='$format',
                            publish='$status'" . $displayCommentsQuery . $displaySummaryQuery . " WHERE id='$id'";
            }

            $result = $dbLink->query($query);
            $itemID = '';
            if ( $armadillo->request()->isPost() ) { $itemID = $dbLink->insert_id; } elseif ( $armadillo->request()->isPut() ) { $itemID = $id; }

            //Save Categories and Tags if the content is a post, and update RSS feed
            if ($contentType == 'post') {	Armadillo_Content::saveTerms($itemID, $item->getCategories(), $item->getTags()); }

            if (!$result) {
                $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_CONTENT_SAVE_DB_ERROR') . $dbLink->error);
            } else {
                $navItemCheck = '';
                //Check if page is new, add to nav menu if so, and store result
                if ($contentType == 'page') { $navItemCheck = Armadillo_Content::checkNavItem($itemID); } else { $navItemCheck = TRUE; } //We're saving a blog post, so set navItemCheck to true as it doesn't need to be added to the nav menu.

                //Check if stored result was a successful save/delete action
                if ($navItemCheck) { 
                    $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_CONTENT_SAVE_SUCCESS')); 
                } else { 
                    $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_CONTENT_SAVE_SUCCESS_NAVUPDATE_FAILED')); 
                }
                return $itemID;
            }
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function getTerms($post_id='',$blog_id='')
    {
        $armadillo = Slim::getInstance();
        
        include 'core/config.php';
        include 'core/connectDB.php';
        
        $termQuery = '';
        
        if ($post_id !== '' and is_numeric($post_id)) {
            $termQuery = "SELECT name, type, postid FROM armadillo_term LEFT JOIN armadillo_term_relationship
                            ON armadillo_term.id = armadillo_term_relationship.termid AND armadillo_term_relationship.postid=$post_id
                            ORDER BY name";
        } else if ($blog_id !== '' and is_numeric($blog_id)) {
            $termQuery = "SELECT name, armadillo_term.type, armadillo_term.id FROM armadillo_term 
                            INNER JOIN armadillo_term_relationship ON armadillo_term.id = armadillo_term_relationship.termid 
                            INNER JOIN armadillo_post ON armadillo_term_relationship.postid = armadillo_post.id 
                            WHERE armadillo_post.blog_id = '$blog_id' GROUP BY name ORDER BY name";
        } else { 
            $termQuery = "SELECT * FROM armadillo_term ORDER BY name"; 

        }
        $termResult = $dbLink->query($termQuery);

        if (!$termResult) { 
            $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_CONTENT_TERMS_DB_ERROR') . $dbLink->error); 
        }
        
        $terms = array();
        
        while ( $termRow = $termResult->fetch_array() ) {
            if ($post_id !== '' and is_numeric($post_id)) { 
                $terms[] = array('termName' => $termRow['name'], 'termType' => $termRow['type'], 'postID' => $termRow['postid']); 
            } else { 
                $terms[] = array('termName' => $termRow['name'], 'termType' => $termRow['type'], 'termID' => $termRow['id']); 
            }
        }

        return $terms;
    }

    public static function saveTerms($postID, $categories, $tags)
    {
        include 'core/config.php';
        include 'core/connectDB.php';

        function cleanArrayValues($array, $dbLink)
        {
            if (!empty($array)) { foreach ($array as $value) { $value = $dbLink->real_escape_string($value); } }

            return $array;
        }

        $categories = cleanArrayValues($categories, $dbLink);
        $tags = cleanArrayValues($tags, $dbLink);

        $errors = 0;

        //Reset the post's categories and tags first, so updates and changes are properly reflected later after being saved
        $removePostCat = "DELETE FROM armadillo_term_relationship WHERE postid = '$postID'";
        $removePostCatResult = $dbLink->query($removePostCat);
        if (!$removePostCatResult) { $errors++; }

        if (!empty($categories)) {
            foreach ($categories as $catTerm) {
                $catQuery = "INSERT INTO armadillo_term SET name='$catTerm', type='category' ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
                $catResult = $dbLink->query($catQuery);
                $savedCat = $dbLink->insert_id;

                $relCatQuery = "INSERT IGNORE INTO armadillo_term_relationship SET postid='$postID', termid='$savedCat'";
                $relCatResult = $dbLink->query($relCatQuery);
                if (!$catResult or !$relCatResult) { $errors++; }
            }
        }

        if (!empty($tags)) {
            foreach ($tags as $tagTerm) {
                $tagQuery = "INSERT INTO armadillo_term SET name='$tagTerm', type='tag' ON DUPLICATE KEY UPDATE id=LAST_INSERT_ID(id)";
                $tagResult = $dbLink->query($tagQuery);
                $savedTag = $dbLink->insert_id;

                $relTagQuery = "INSERT IGNORE INTO armadillo_term_relationship SET postid='$postID', termid='$savedTag'";
                $relTagResult = $dbLink->query($relTagQuery);
                if (!$tagResult or !$relTagResult) { $errors++; }
            }
        }

        if ($errors == 0) { return true; } else { return false; }

    }

    public static function deleteItem( $contentType, $id )
    {
        $armadillo = Slim::getInstance();
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';
            $contentType = $dbLink->real_escape_string($contentType);
            $id = $dbLink->real_escape_string($id);

            $query = "DELETE FROM armadillo_post WHERE id='$id'";
            $termsQuery = "DELETE FROM armadillo_term_relationship WHERE postid='$id'";

            $result = $dbLink->query($query);
            $termsResult = $dbLink->query($termsQuery);

            if (!$result) {
                $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_CONTENT_DELETE_DB_ERROR') . $dbLink->error);
            } else {
                $navItemCheck = '';
                //Delete page from nav menu if needed, and store result
                if ($contentType == 'page') {
                    $navItemCheck = Armadillo_Content::deleteNavItem($id); 
                }

                //Check if stored result was a successful delete action
                if ($navItemCheck || $contentType == 'post' || $contentType == 'soloContent') { 
                    $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_CONTENT_DELETE_SUCCESS')); 
                } else { 
                    $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_CONTENT_DELETE_SUCCESS_NAVUPDATE_FAILED')); 
                }
            }
        } else { $armadillo->redirect($armadillo->request()->getRootUri());	}
    }

    public static function checkNavItem($itemID)
    {
        $armadillo = Slim::getInstance();
        $pageDetails = Armadillo_Content::getSingleItem('page', $itemID);

        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            //Check if the page has already been added to the navigation db table
            $query = "SELECT * FROM armadillo_nav WHERE pageid='$itemID'";
            $dbLink->query($query);
            $navItem = $dbLink->affected_rows;

            $result = '';

            //Check if the page is published -- NOTE TO SELF: We can't use "===" here when comparing the "publish" value, as they won't match
            if ($pageDetails['publish'] == TRUE and $navItem === 0) {
                $result = Armadillo_Content::addNavItem($itemID); //Page is "published" and not already in nav db table
            } elseif ($pageDetails['publish'] == TRUE and $navItem === 1) {
                $result = TRUE; //Page is "published" and already in nav db table, so return true
            } elseif ($pageDetails['publish'] == FALSE and $navItem === 1) {
                $result = Armadillo_Content::deleteNavItem($itemID); //Page is "draft" and already in nav db table, so remove it
            } elseif ($pageDetails['publish'] == FALSE and $navItem === 0) {
                $result = TRUE; //Page is "draft" and NOT in nav db table, so return true
            }

            return $result;
        }
    }

    public static function addNavItem($itemID)
    {
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            //Get the total number of items in the navigation db table
            $countQuery = "SELECT COUNT(*) FROM armadillo_nav";
            $countResult = $dbLink->query($countQuery);
            $countResult = $countResult->fetch_row();
            $totalNavItems = $countResult[0];

            $query = "INSERT INTO armadillo_nav SET
                            parentid='0',
                            pageid='$itemID',
                            position='$totalNavItems'";
            $result = $dbLink->query($query);

            if (!$result) {	return FALSE; } else { return TRUE; }
        }
    }

    public static function deleteNavItem($itemID)
    {
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            //Check if page is in the navigation db table
            $countQuery = "SELECT COUNT(*) FROM armadillo_nav WHERE pageid='$itemID'";
            $countResult = $dbLink->query($countQuery);
            $countResult = $countResult->fetch_row();
            $navItem = $countResult[0];

            if ($navItem > 0) {
                $query = "DELETE FROM armadillo_nav WHERE pageid='$itemID'";
                $result = $dbLink->query($query);
                if (!$result) {	return FALSE; } else { return TRUE; }
            } else { return TRUE; }
        }
    }

    public static function htmlToMarkdown($content)
    {
        //After (too) many hours looking for a simple solution, will probably just use to-markdown.js :-P
    }

    public static function markdownToHtml($content)
    {
        //Parsedown!
    }

}

class Armadillo_Page extends Armadillo_Content
{
    // Assigns all posts associated with a certain blog ($old_blog), to a new blog ($new_blog)
    public static function reassignBlogPosts($old_blog, $new_blog)
    {
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            if ( is_numeric($old_blog) and is_numeric($new_blog) ) {

                $query = "UPDATE armadillo_post SET blog_id='$new_blog' WHERE type='post' AND blog_id='$old_blog'";

                $result = $dbLink->query($query);

                if (!$result) {
                    $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_POST_DISPLAY_SUMMARY_DB_ERROR') . $dbLink->error); 
                }

            } else {
                $armadillo->flashNow('notification', Armadillo_Language::msg('ARM_CONTENT_ID_ERROR'));
            }

        }
    }
}

class Armadillo_Solo_Content extends Armadillo_Content
{
}

class Armadillo_Post extends Armadillo_Page
{
    private $categories = array();
    private $tags = array();
    private $displayComments;

    public function __construct( $id = '', $title = '', $content = '', $date = '', $author = '', $status = '', $categories = array(), $tags = array(), $displayComments = '', $displaySummary = '' )
    {
        parent::__construct( $id, $title, $content, $date, $author, $status );
        $this->categories = $categories;
        $this->tags = $tags;
        $this->displayComments = $displayComments;
        $this->displaySummary = $displaySummary;

    }

    public function getCategories()
    {
        return $this->categories;
    }

    public function setCategories( $newCategories )
    {
        //do some checking to make sure the value passed is an array
        $this->categories = $newCategories;
    }

    public function getTags()
    {
        return $this->tags;
    }

    public function setTags( $newTags )
    {
        //do some checking to make sure the value passed is an array
        $this->tags = $newTags;
    }

    public function getDisplayComments()
    {
        return $this->displayComments;
    }

    public function setDisplayComments( $displayOption )
    {
        $this->displayComments = $displayOption;
    }

    public function getDisplaySummary()
    {
        return $this->displaySummary;
    }

    public function setDisplaySummary( $displayOption )
    {
        $this->displaySummary = $displayOption;
    }

    public static function getAllBlogSettings()
    {
        if ( file_exists('core/config.php') ) {
            include 'core/config.php';
            include 'core/connectDB.php';

            $query = "SELECT * FROM armadillo_post WHERE type='blog'";

            $result = $dbLink->query($query);

            $blogSettings = array();

            if ($result) {
                while ( $row = $result->fetch_array() ) {
                    $blogSettings[] = $row;
                }
            }

            return $blogSettings;
        }
    }
}
