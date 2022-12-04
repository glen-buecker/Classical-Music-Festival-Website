<?php

function armadilloMenuInfo()
{
    require_once dirname(dirname(__FILE__)) . '/config.php';
    require_once dirname(dirname(__FILE__)) . '/connectDB.php';

    $query = "SELECT * FROM armadillo_options LIMIT 1";
    $result = $dbLink->query($query);
    $options = array();
    if ($result) {
        $row = $result->fetch_array();
        foreach ($row as $option => $value) { $options["$option"] = $value; }
    }

    $armMenuInfo = array();
    $armMenuInfo['siteMainNav'] = $options['site_main_nav_container'];
    $armMenuInfo['position'] = $options['menu_display_option'];
    //Get URL of Armadillo page
    $protocol = strpos(strtolower($_SERVER['SERVER_PROTOCOL']),'https') === FALSE ? 'http' : 'https';
    $host     = $_SERVER['HTTP_HOST'];
    $script   = dirname(dirname(dirname(dirname(dirname($_SERVER['SCRIPT_NAME'])))));

    $currentUrl = $protocol . '://' . $host . $script;
    ob_start();
    createNestedList( $dbLink, 0, 0, '', $currentPage, 'TRUE', $currentUrl );
    $menuHTML = ob_get_contents();
    ob_end_clean();
    $armMenuInfo['menuHTML'] = $menuHTML;
    echo json_encode($armMenuInfo);
}

// creates the nested list with ul , li , ul .................. can be use for the navigation and menus.
function createNestedList( $dbLink, $parentid, $counter, $pageFilename, $currentPage, $importMenu='', $armadilloURL='' )
{
    if ( file_exists( dirname(dirname(__FILE__)) . '/config.php' ) ) {

        if ($counter == 0 )
             echo "<div id='armadilloContentMenu'><ul>";

        // retrieve all children of $parent
        $query = "SELECT pageid, title, position, type FROM armadillo_nav INNER JOIN armadillo_post ON pageid=id WHERE parentid='$parentid' ORDER BY position ASC";
        $result = $dbLink->query($query);
        while ($row = $result->fetch_array()) {
            $res 	= 	$dbLink->query ( "SELECT pageid, title, position, type FROM armadillo_nav INNER JOIN armadillo_post ON pageid=id WHERE parentid='" . $row['pageid'] . "' ORDER BY position ASC" );
            $tot 	= 	$res->num_rows;
            $ul 	=  	$tot == 0 ? "":'<ul>';
            $_ul 	= 	$tot == 0 ? "":'</ul>';
            //Check if we're importing menu to a non-Armadillo page
            $itemURL = $importMenu == TRUE ? $armadilloURL . '/' : '.';
            $titleInURL = preg_replace("/[\s]/", '-', $row['title']);
            //Determine if the page being shown is the current page
            $currentPageCSS = ($currentPage === $row['pageid'] or $currentPage === $row['type']) ? 'current' : '';
            echo "<li class='$currentPageCSS'><a href='" . $itemURL . $pageFilename . "?page_id=" . $row['pageid'] . "&/" . $titleInURL . "' class='armadilloMenuItem armadilloPage $currentPageCSS'>" . $row['title'] ."</a>";
            echo "{$ul}";
            createNestedList( $dbLink, $row['pageid'], $counter+1, $pageFilename, $currentPage );
            echo "{$_ul}";
            echo "</li>";
        }

        if ($counter == 0 )
            echo "</ul></div>" ;
    }
}
