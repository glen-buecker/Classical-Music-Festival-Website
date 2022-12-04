<!DOCTYPE html>
<html lang="en">

<head>
  <!-- Required meta tags always come first -->
  <meta charset="utf-8">
  <meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
  <meta http-equiv="x-ua-compatible" content="ie=edge">

	<!-- Head content such as meta tags and encoding options, etc -->
	
<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="robots" content="index, follow" />
		

	<!-- User defined head content -->
	

  <!-- Browser title -->
	<title>Armadillo</title>

  <!-- Main Theme Stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="../rw_common/themes/Foundry/consolidated.css?rwcache=685984326" />
		

	<!-- RapidWeaver Color Picker Stylesheet -->
	

	<!-- Plugin injected code -->
			<link rel='stylesheet' type='text/css' media='all' href='../rw_common/plugins/stacks/stacks.css?rwcache=685984326' />
		<link rel='stylesheet' type='text/css' media='all' href='files/stacks_page_page22.css?rwcache=685984326' />
        <script type='text/javascript' charset='utf-8' src='https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js'></script>
        
        
        
		
        <meta name="formatter" content="Stacks v4.2.5 (6054)" >
		<meta class="stacks4 stack version" id="com.nimblehost.stack.armadillo" name="Armadillo" content="2.9.8">
		<?php
$assetPath = '../rw_common/plugins/stacks';
//Initialize Armadillo 
include_once "../rw_common/plugins/stacks/armadillo/core/displayContent.php";
include_once "files/stacks_page_page22.php";

$stylesheetVersion = '';
$armadilloPageFilename = basename($_SERVER['SCRIPT_NAME']);
$pageTitleAndMetaContent = array();

if ($setupIsComplete) {
	$stylesheetVersion = $armadilloOptions['stylesheet_version'];
	//Link stylesheets and scripts
	if ( !$armadilloResourcesLoaded ) {
		echo '<link rel="stylesheet" type="text/css" href="../rw_common/plugins/stacks/armadillo/core/css/armadilloStyles.css?v=' . $stylesheetVersion . '" />';
		echo '<link rel="stylesheet" type="text/css" href="../rw_common/plugins/stacks/armadillo/core/css/jquery.plugin.css" />';
		echo '<script src="../rw_common/plugins/stacks/armadillo/core/scripts/armadilloFunctions.js"></script>';
		$armadilloResourcesLoaded = TRUE;
	}

	// Retrieve relevant info from database according to content that needs to be displayed
	if ( isset($_GET['page_id']) ) {
	    $pageTitleAndMetaContent = getPageTitleAndMetaContent($dbLink, $_GET['page_id']);
	} else {
	    $pageTitleAndMetaContent = getPageTitleAndMetaContent($dbLink, 'default');
	}

	// Display page title and meta tags
	echo '<title>' . $pageTitleAndMetaContent['pageTitle'] . '</title>';
	echo $pageTitleAndMetaContent['metaContent'];
}
?>
<script type="text/javascript">
	if (typeof checkArmadilloStyleSheet != 'function') {
		function checkArmadilloStyleSheet(url){
		   var found = false;
		   for(var i = 0; i < document.styleSheets.length; i++){ if(document.styleSheets[i].href==url){ found=true; break; } }
		   if(!found){ jQuery('head').append( jQuery('<link rel="stylesheet" type="text/css" href="' + url + '" />') ); }
		}
	}

	function errorCallback(obj, json) { alert(obj.error); }

	// Link stylesheets if they don't exist on page yet
	checkArmadilloStyleSheet('//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');

    (function() {
		jQuery(document).ready(function(){
			if ( jQuery('.afb-enabled').length == 0 ) { Armadillo.startFancyBox(); }
		});
	})();
	
    var assetPath = '../rw_common/plugins/stacks';
</script>



</head>

<body class="antialiased">
  <div class="blur_wrapper">

  	
<div id='stacks_out_1' class='stacks_top'><div id='stacks_in_1' class=''><div id='stacks_out_2' class='stacks_out'><div id='stacks_in_2' class='stacks_in com_nimblehost_stack_armadillo_stack'>
<?php if ( $setupIsComplete and $armadilloOptions['armadillo_build_version'] >= 1018 ): ?>
    <?php
        if ( !isset($_GET['page_id']) and $armadilloOptions['default_content'] !== "none" and $armadilloOptions['default_content'] !== "enabled" ) { 
                $currentPage = $armadilloOptions['default_content']; 
        } elseif ( isset($_GET['page_id']) and is_numeric($_GET['page_id'] ) ) { 
            $currentPage = $_GET['page_id']; 
        } elseif ( $armadilloOptions['default_content'] === "enabled" ) {
            $currentPage = defaultContent($dbLink);
        } else { 
            $currentPage = NULL; 
        }
    ?>
    <div class="armadilloClearer"></div>
    <div id="armadilloContentContainer" class="armadilloContent">
    <?php
        //Display default content
        if ( !isset($_GET['page_id']) and $armadilloOptions['default_content'] !== "none" and $armadilloOptions['default_content'] !== "enabled" ) { 
            echo displayPage($dbLink, $armadilloOptions['default_content'], $armadilloOptions); 
        } elseif ( isset($_GET['page_id']) and is_numeric($_GET['page_id'] ) ) { 
            echo displayPage($dbLink, $_GET['page_id'], $armadilloOptions); 
        } elseif ( $armadilloOptions['default_content'] === "enabled" ) {
            echo displayPage($dbLink, $currentPage, $armadilloOptions);
        } 
    ?>
    </div>
<?php else: ?>
<?php
    if ( isset($armadilloOptions['armadillo_build_version']) and $armadilloOptions['armadillo_build_version'] < 1018 ) {
        echo "<p>" . Armadillo_Language::msg('ARM_UPDATE_AVAILABLE_GENERAL_ANNOUCEMENT') . "</p>"
        . "<p><a href='$assetPath/armadillo/index.php/'>"
        . Armadillo_Language::msg('ARM_CONTINUE_TEXT') . "</a></p>";
        // Recreate Armadillo stylesheet since core files have moved to new location
        include '../rw_common/plugins/stacks/armadillo/core/model/Armadillo_Data.php';
        Armadillo_Data::generateArmadilloStylesheet($dbLink);
    } else {
        echo "<p>" . Armadillo_Language::msg('ARM_SETUP_REQUIRED_MESSAGE1') . "</p>"
        . "<p><a href='$assetPath/armadillo/index.php/'>"
        . Armadillo_Language::msg('ARM_SETUP_REQUIRED_MESSAGE2') . "</a></p>";
    }
?>
<?php endif; ?>



</div></div></div></div>


  </div>

  <!-- Base RapidWeaver Javascript -->
  <script src="../rw_common/themes/Foundry/javascript.js?rwcache=685984326"></script>

  <!-- Load jQuery -->
  <script src="../rw_common/themes/Foundry/js/jquery.min.js?rwcache=685984326"></script>

  <!-- Tether.js || used for tooltips -->
	<script src="../rw_common/themes/Foundry/js/tether.min.js?rwcache=685984326"></script>

	<!-- Latest compiled and minified JavaScript -->
	<script src="../rw_common/themes/Foundry/js/bootstrap.min.js?rwcache=685984326"></script>

	<!-- Style variations -->
	

	<!-- User defined javascript -->
	

  <!-- User defined styles -->
	

	<script>
		// Initializes dropdowns
		$('.dropdown-toggle').dropdown();

		// Initializes popovers
		$(function () {
		  $('[data-toggle="popover"]').popover()
		});
	</script>

  <!-- Foundry theme v1.0.1 -->

</body>

</html>
