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
	<title>Home</title>

  <!-- Main Theme Stylesheet -->
	<link rel="stylesheet" type="text/css" media="all" href="rw_common/themes/Foundry/consolidated.css?rwcache=685984326" />
		

	<!-- RapidWeaver Color Picker Stylesheet -->
	

	<!-- Plugin injected code -->
			<link rel='stylesheet' type='text/css' media='all' href='rw_common/plugins/stacks/stacks.css?rwcache=685984326' />
		<link rel='stylesheet' type='text/css' media='all' href='files/stacks_page_page0.css?rwcache=685984326' />
        <script type='text/javascript' charset='utf-8' src='https://ajax.googleapis.com/ajax/libs/jquery/2.2.4/jquery.min.js'></script>
        
        <link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/font-awesome/4.7.0/css/font-awesome.min.css">
        
		
		<script type='text/javascript' charset='utf-8' src='files/stacks_page_page0.js?rwcache=685984326'></script>
        <meta name="formatter" content="Stacks v4.2.5 (6054)" >
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundryColumns" name="Columns" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundrySliderSlide" name="Slide (Image, Video, Hero)" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundryVideoEmbed" name="Video Embed" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundry" name="Foundry" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundryBanner" name="Banner" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundryScrollToTop" name="Scroll to Top" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundryButton" name="Button" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.marathia.stacks.nolink" name="NoLink" content="2.2.4">
		<meta class="stacks4 stack version" id="com.nimblehost.stack.armadilloSoloContent" name="Armadillo Solo Content" content="2.9.8">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundryHeader" name="Header" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundryContainer" name="Container" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundrySlider" name="Slider" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundryNavigationBar" name="Nav. Bar" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundryMargins" name="Margins" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.joeworkman.stacks.locker" name="Locker" content="1.2.1">
		<meta class="stacks4 stack version" id="com.elixir.stacks.foundrySiteLogo" name="Site Logo" content="2.4.4.0">
		<meta class="stacks4 stack version" id="com.nimblehost.stack.armadilloLoginLink" name="Armadillo Login Link" content="2.9.8">
		<?php
$assetPath = 'rw_common/plugins/stacks';
//Initialize Armadillo 
//include_once "files/stacks_page_page0.php";
include_once "rw_common/plugins/stacks/armadillo/core/displayContent.php";

$stylesheetVersion = '';
$armadilloPageFilename = basename($_SERVER['SCRIPT_NAME']);

if ($setupIsComplete) {
	$stylesheetVersion = $armadilloOptions['stylesheet_version'];
	//Link stylesheets and scripts
	if ( !$armadilloResourcesLoaded ) {
		echo '<link rel="stylesheet" type="text/css" href="rw_common/plugins/stacks/armadillo/core/css/armadilloStyles.css?v=' . $stylesheetVersion . '" />';
		echo '<link rel="stylesheet" type="text/css" href="rw_common/plugins/stacks/armadillo/core/css/jquery.plugin.css" />';
		echo '<script src="rw_common/plugins/stacks/armadillo/core/scripts/armadilloFunctions.js"></script>';
		$armadilloResourcesLoaded = TRUE;
	}
	// Check if someone is logged in and has sufficient permission to edit the content
	if ( 
		( isset($_COOKIE['armadillo']['bootMe']) and $_COOKIE['armadillo']['bootMe'] > time() 
			and isset($_COOKIE['armadillo']['loggedIn']) and $_COOKIE['armadillo']['loggedIn'] == 'TRUE' ) 
		and ( isset($_COOKIE['armadillo']['role']) and isset($_COOKIE['armadillo']['userID']) ) ) {
		if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ) {
			echo '<link rel="stylesheet" href="rw_common/plugins/stacks/armadillo/core/scripts/redactor/redactor.min.css" />';
			echo '<script type="text/javascript" src="rw_common/plugins/stacks/armadillo/core/scripts/redactor/redactor.min.js"></script>';
			if ( $_COOKIE['armadillo']['language'] !== 'default' ) {
				echo '<script type="text/javascript" src="rw_common/plugins/stacks/armadillo/core/scripts/redactor/' . $_COOKIE['armadillo']['language'] . '.js"></script>';
			}
			echo '<script type="text/javascript" src="rw_common/plugins/stacks/armadillo/core/scripts/redactor/plugins.min.js"></script>';
		} else {
			echo '<link rel="stylesheet" href="rw_common/plugins/stacks/armadillo/core/css/armadillo-markdown.css">' . PHP_EOL
				. '<script type="text/javascript" src="rw_common/plugins/stacks/armadillo/core/scripts/to-markdown.js"></script>' . PHP_EOL
				. '<script type="text/javascript" src="rw_common/plugins/stacks/armadillo/core/scripts/armadillo-markdown.js"></script>';
		}
	}
}
?>
<script type="text/javascript">
	var armasolo = {};
	armasolo.jQuery = jQuery;

	if (typeof checkArmadilloStyleSheet != 'function') {
		function checkArmadilloStyleSheet(url){
		   var found = false;
		   for(var i = 0; i < document.styleSheets.length; i++){ if(document.styleSheets[i].href==url){ found=true; break; } }
		   if(!found){ armasolo.jQuery('head').append( armasolo.jQuery('<link rel="stylesheet" type="text/css" href="' + url + '" />') ); }
		}
	}

	function errorCallback(obj, json) { alert(obj.error); }

	// Link stylesheets if they don't exist on page yet
	checkArmadilloStyleSheet('//maxcdn.bootstrapcdn.com/font-awesome/4.6.3/css/font-awesome.min.css');
	
    (function() {
    	<?php if ( $setupIsComplete and ( $armadilloOptions['menu_display_option'] === 'moveToBefore' or $armadilloOptions['menu_display_option'] === 'moveToAfter' ) ): ?>
    	Armadillo.attachMenuToSiteNav('<?php echo $armadilloOptions['site_main_nav_container']; ?>', '<?php echo $armadilloOptions['site_second_nav_container']; ?>', '<?php echo $armadilloOptions['site_third_nav_container']; ?>', '<?php echo $armadilloOptions['menu_display_option']; ?>');
    	<?php endif; ?>
    	armasolo.jQuery('.displayEditor').fancybox({ maxWidth: 800 });
		armasolo.jQuery(document).ready(function(){
			if ( armasolo.jQuery('.afb-enabled').length == 0 ) { Armadillo.startFancyBox(); }
			<?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and $_COOKIE['armadillo']['editorType'] == 'markdown' ) ): ?>
		    armasolo.jQuery('body').on("click", 'a[data-htmleditor-button="fullscreen"]', function() { armasolo.jQuery('body').toggleClass('armadilloEditorFullscreen'); });
		    armasolo.jQuery('body').on("click", '.uk-htmleditor-button-preview', function() { console.log('activating markdown preview'); });
		    armasolo.jQuery('.uk-htmleditor-button-preview').trigger('click');
		    <?php endif; ?>
		});
	})();
	
    var assetPath = 'rw_common/plugins/stacks';
</script>

<?php
$assetPath = 'rw_common/plugins/stacks';
//Include main file for displaying Armadillo content
include_once "rw_common/plugins/stacks/armadillo/core/displayContent.php";

$stylesheetVersion = '';

if ($setupIsComplete) {
	$stylesheetVersion = $armadilloOptions['stylesheet_version'];
}
?>



</head>

<body class="antialiased">
  <div class="blur_wrapper">

  	
<div id='stacks_out_1' class='stacks_top'><div id='stacks_in_1' class=''><div id='stacks_out_180' class='stacks_out'><div id='stacks_in_180' class='stacks_in '><div id='stacks_out_180_1' class='stacks_out'><div id='stacks_in_180_1' class='stacks_in com_elixir_stacks_foundry_stack'>
  
    <!-- LEGACY -->
    <link href='https://fonts.googleapis.com/css?family=Lato:400|Lato:300|' rel='stylesheet' type='text/css'>

  





<!-- Foundry -->


</div></div><div id='stacks_out_180_9' class='stacks_out'><div id='stacks_in_180_9' class='stacks_in com_marathia_stacks_nolink_stack'><!-- Start of NoLink Stack v2.2.4 -->

<!-- End of NoLink Stack -->
</div></div><div id='stacks_out_180_22' class='stacks_out'><div id='stacks_in_180_22' class='stacks_in com_elixir_stacks_foundryContainer_stack'>


<div class="container-fluid">
	
		<div id='stacks_out_180_16' class='stacks_out'><div id='stacks_in_180_16' class='stacks_in com_elixir_stacks_foundryColumns_stack'>



<div class="row no-gutters custom-gutters equal">


	<!-- Column One -->
	<div class="foundry_column foundry_column_one col-xs-12 col-md-10   col-lg-11  "><div class="inner_column_content"><div id='stacks_out_180_4' class='stacks_out'><div id='stacks_in_180_4' class='stacks_in com_elixir_stacks_foundryBanner_stack'>


<div class="nav_pairing">
	<div class="banner" >
			<div class="banner_inner">
				<div class="banner_content">
					<div class="container">
						<div id='stacks_out_180_5' class='stacks_out'><div id='stacks_in_180_5' class='stacks_in com_elixir_stacks_foundrySiteLogo_stack'>



<div class="site_logo text-xs-left">
	
		<img src="rw_common/images/cmf_logo-ver_2.png" width="1248" height="97" alt=""/>
	

	
</div>
</div></div>
					</div>
				</div>
			</div>

			
	</div>

		
</div>




</div></div></div></div>



	<!-- Column Two -->
	<div class="foundry_column foundry_column_two col-xs-12 col-md-2   col-lg-2   "><div class="inner_column_content"><div id='stacks_out_180_21' class='stacks_out'><div id='stacks_in_180_21' class='stacks_in com_elixir_stacks_foundryButton_stack'>

<div class="button-base-margin text-xs-center">
<a role="button" href="about/links/" rel="" onclick="" target="" id="" class=" btn btn-md btn-danger  " >DONATE</a>
</div></div></div></div></div>







</div>
</div></div>
	
</div>
</div></div><div id='stacks_out_180_12' class='stacks_out'><div id='stacks_in_180_12' class='stacks_in com_elixir_stacks_foundryNavigationBar_stack'>






<div class="nav_bar_placeholder">

	<nav class="navigation_bar clearfix  f-bg" role="navigation">
		<div class="container navigation_container">

			<div id="stacks_in_180_12_mobile_navigation_toggle"><i class="fa fa-bars"></i></div>

			<div class="branding_logo">
				<a href="http://cmf.ou.edu/">
					
					
					
				</a>
			</div>

			

			<div class="logo_float_clear"></div>  <!-- Clears float on mobile devices -->

				<ul><li class="Selected"><a href="./" rel="" class="nav_item nav_active">Home</a></li><li><a href="about/" rel="" class="nav_item parent">About the Festival</a><ul><li><a href="about/overview/" rel="" class="nav_item">Overview</a></li><li><a href="about/personnel/" rel="" class="nav_item">Personnel</a></li><li><a href="about/links/" rel="" class="nav_item">Donate</a></li></ul></li><li><a href="program/" rel="" class="nav_item parent">Festival program</a><ul><li><a href="program/dates/" rel="" class="nav_item">Festival Dates and Travel</a></li><li><a href="program/repertoire/" rel="" class="nav_item">Repertoire</a></li><li><a href="program/schedule/" rel="" class="nav_item">Schedule</a></li><li><a href="program/registration/" rel="" class="nav_item">Registration</a></li><li><a href="program/cost/" rel="" class="nav_item">Cost</a></li></ul></li><li><a href="piano/" rel="" class="nav_item parent">Piano Seminar</a><ul><li><a href="piano/overview/" rel="" class="nav_item">Overview</a></li><li><a href="piano/description/" rel="" class="nav_item">Program Description</a></li><li><a href="piano/cost/" rel="" class="nav_item">Cost</a></li><li><a href="piano/registration/" rel="" class="nav_item">Registration</a></li><li><a href="piano/faculty/" rel="" class="nav_item">Piano Faculty and Presenters</a></li></ul></li><li><a href="accommodations/" rel="" class="nav_item">Accommodations</a></li><li><a href="trips/" rel="" class="nav_item">Optional Trips</a></li></ul>

		</div>
	</nav>

</div>
<div style="clear: both;"></div>

</div></div><div id='stacks_out_180_11' class='stacks_out'><div id='stacks_in_180_11' class='stacks_in com_elixir_stacks_foundryScrollToTop_stack'>



<div id="scrollToTopButton" class="stacks_in_180_11-scroll-to-top-btn btn custom animated bounceOutDown   ">
	<i class="fa fa-angle-up"></i>
</div>


</div></div></div></div>

<div id='stacks_out_178' class='stacks_out'><div id='stacks_in_178' class='stacks_in com_elixir_stacks_foundryContainer_stack'>


<div class="container">
	
		<div id='stacks_out_61' class='stacks_out'><div id='stacks_in_61' class='stacks_in com_elixir_stacks_foundryColumns_stack'>



<div class="row   equal">


	<!-- Column One -->
	<div class="foundry_column foundry_column_one col-xs-12 col-md-4   "><div class="inner_column_content"><div id='stacks_out_190' class='stacks_out'><div id='stacks_in_190' class='stacks_in com_elixir_stacks_foundryMargins_stack'>

<div class="margins">
	<div id='stacks_out_64' class='stacks_out'><div id='stacks_in_64' class='stacks_in com_nimblehost_stack_armadilloSoloContent_stack'>
<div class="armadilloSoloContentContainer">
<?php
/* Only for use with the Armadillo CMS from NimbleHost*/
$siteAssetPath = 'rw_common/plugins/stacks';
include_once  $siteAssetPath . "/armadillo/core/model/Parsedown.php";
if ( $setupIsComplete ) {
	//TODO 	- if logged in, display edit button
	//		- option to edit content "inline"
	//		- enable lightbox and other functions that make sense
	//		- insert Armadillo menu items in theme nav if specified to do so

    //Display specified content
    $soloContent = displaySoloContent( $dbLink, '22', 'true', $armadilloOptions );

    if ( $soloContent == Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_SELECTED_ITEM_MISSING') ) {
    	//specified solo content ID is missing from database
    	echo $soloContent;
    } else {
	    if ( $soloContent['publish'] ) {
	    	$padding_for_22 = $soloContent['content'] == '' ? ' armadilloPadding' : '';
	    	echo '<div id="armadilloSoloContent_22_stacks_in_64" class="armadilloContent' . $padding_for_22 . '">' . $soloContent['content'] . '</div>';
	    } else {
	    	echo '<div id="armadilloSoloContent_22_stacks_in_64" class="armadilloContent draft"></div>';
    	}
    }
} else {
	// Setup isn't complete so display a notification
    echo "<p>" . Armadillo_Language::msg('ARM_SETUP_REQUIRED_MESSAGE1')
        . "</p><p><a href='$siteAssetPath/armadillo/'>"
        . Armadillo_Language::msg('ARM_SETUP_REQUIRED_MESSAGE2') . "</a></p>";
}
?>
<?php 
	// Check if someone is logged in and has sufficient permission to edit the content
	if ( 
		( isset($_COOKIE['armadillo']['bootMe']) and $_COOKIE['armadillo']['bootMe'] > time() 
			and isset($_COOKIE['armadillo']['loggedIn']) and $_COOKIE['armadillo']['loggedIn'] == 'TRUE' ) 
		and ( isset($_COOKIE['armadillo']['role']) and isset($_COOKIE['armadillo']['userID']) ) 
		and ( $_COOKIE['armadillo']['role'] == 'admin' or $_COOKIE['armadillo']['role'] == 'editor' or $_COOKIE['armadillo']['userID'] == $soloContent['userid'] ) ):
?>
	<!--<style>#armadilloSoloContent_22_stacks_in_64.draft { min-height: 2em; }</style>-->
	<?php if ( $soloContent == Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_SELECTED_ITEM_MISSING') ): ?>
	<p style="margin: 20px 0;padding: 10px;border:solid 1px #ffcccc;color: #cc0000;"><?php echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_SOLO_CONTENT_ID_MISSING'); ?></p>
	<!-- TODO: Add ajax call to create soloContent, refresh page if successfull or display error message otherwise -->
	<?php else: ?>
	<div class="armadilloEditButton"><a href="#editSoloContentID_22_stacks_in_64" class="displayEditor"><i class="fa fa-pencil-square-o"></i>&nbsp;ID: 22</a></div>
	<?php endif; ?>
	<div id="editSoloContentID_22_stacks_in_64" class="editSoloContent" style="display:none;">
		<div id="editSoloContentID_22_stacks_in_64_toolbar"></div>
		<?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
        <!-- Rich Text Editor -->
        <textarea id="soloContentID_22_stacks_in_64" name="soloContentID_22_stacks_in_64"><?php echo htmlspecialchars($soloContent['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        <?php else: ?>
        <!-- Markdown Editor -->
        <textarea autofocus id="soloContentID_22_stacks_in_64" name="soloContentID_22_stacks_in_64" data-uk-htmleditor="{markdown:true,autofocus:true}"><?php if ( isset($soloContent['format']) and $soloContent['format'] == 'markdown' ) { echo $soloContent['rawContent']; } ?></textarea>
        <?php if ( !isset($soloContent['format']) or $soloContent['format'] == 'html' ): ?>
        <script>document.addEventListener("DOMContentLoaded", function(event) { soloContentID_22_stacks_in_64 = document.getElementById('soloContentID_22_stacks_in_64'); soloContentID_22_stacks_in_64.value = toMarkdown('<?php echo str_replace(array("\n", "\r"),'', str_replace("'","\'",$soloContent['rawContent'])); ?>'); });</script>
        <?php endif; ?>
	    <?php endif; ?>
		<p class="saveSoloContentDetails">
			<a href="#soloContentID_22_stacks_in_64" id="saveSoloContentID_22_stacks_in_64" class="saveSoloContentButton armadilloButton"><?php echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_SOLO_CONTENT_SAVE_TEXT'); ?></a>
			<span class="saveSoloContent result"><i class="fa fa-lg"></i></span>
			<span class="saveSoloContent progress"></span>
		</p>
	</div>
	<script>
		document.addEventListener("DOMContentLoaded", function(event) {
			var armadilloSoloContent_22_stacks_in_64 = {};
			armadilloSoloContent_22_stacks_in_64 = (function(){
				<?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
				$R('#soloContentID_22_stacks_in_64', {
					toolbarExternal: '#editSoloContentID_22_stacks_in_64_toolbar',
					toolbarFixed: true,
					autoresize: false,
					maxHeight: 500,
					<?php if ( $_COOKIE['armadillo']['language'] !== 'default' ): ?>
					lang: '<?php echo $_COOKIE['armadillo']['language']; ?>',
					<?php endif; ?>
					deniedTags: ['html', 'head', 'link', 'body', 'meta', 'applet'],
					replaceDivs: false,
					phpTag: true,
					imageUpload: 'rw_common/plugins/stacks/armadillo/core/model/redactorFunctions.php?armURL=<?php echo $_COOKIE['armadillo']['armURL']; ?>',
					imageUploadErrorCallback: errorCallback,
					imageManagerJson: 'rw_common/plugins/stacks/armadillo/media/images.json',
					imageResizable: true,
					imagePosition: true,
					fileUpload: 'rw_common/plugins/stacks/armadillo/core/model/redactorFunctions.php?armURL=<?php echo $_COOKIE['armadillo']['armURL']; ?>',
					fileManagerJson: 'rw_common/plugins/stacks/armadillo/media/files.json',
					<?php if ($_COOKIE['armadillo']['editorType'] == 'basictext'): ?>
					buttons: ['format', 'bold', 'italic', 'deleted', 'lists', 'link', 'line', 'image', 'file'],
					plugins: ['imagemanager', 'filemanager']
					<?php else: ?>
					buttons: ['html', 'undo', 'redo', 'format', 'bold', 'italic', 'deleted', 'underline', 'lists', 'indent', 'outdent', 'image', 'file', 'link', 'line'],
					plugins: ['imagemanager', 'filemanager', 'video', 'table', 'alignment', 'fontsize', 'fontcolor', 'textdirection', 'counter']
					<?php endif; ?>
				});
				<?php endif; ?>
				armasolo.jQuery('#saveSoloContentID_22_stacks_in_64').click(function(){
					<?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
					var updatedContent = $R('#soloContentID_22_stacks_in_64', 'source.getCode');
					var previewHTML = updatedContent;
					updatedContent = encodeURIComponent(updatedContent);
					var format = 'html';
					<?php else: ?>
					var updatedContent = encodeURIComponent(armasolo.jQuery('#soloContentID_22_stacks_in_64').val());
					var previewHTML = armasolo.jQuery('#editSoloContentID_22_stacks_in_64 .uk-htmleditor .uk-htmleditor-content .uk-htmleditor-preview').html();
					var format = 'markdown';
					<?php endif; ?>
					armasolo.jQuery.ajax({
						beforeSend: function(){ armasolo.jQuery('#editSoloContentID_22_stacks_in_64 .saveSoloContent.result i.fa').removeClass('fa-times, fa-check').parent().css('opacity',0); armasolo.jQuery('#editSoloContentID_22_stacks_in_64 .saveSoloContent.progress').fadeTo(300,1); },
						url: 'rw_common/plugins/stacks/armadillo/core/model/updateArmadilloSettings.php',
						type: 'post',
						data: 'action=updateContent&format=' + format + '&contentID=22&content=' + updatedContent,
						success: function(data, status) {
							if ( data == 'true' ) {
								armasolo.jQuery('#editSoloContentID_22_stacks_in_64 .saveSoloContent.result i.fa').addClass('fa-check').parent().fadeTo(300,1);
								armasolo.jQuery('#armadilloSoloContent_22_stacks_in_64').html(previewHTML);
							} else { console.log(data); console.log(status); armasolo.jQuery('#editSoloContentID_22_stacks_in_64 .saveSoloContent.result i.fa').addClass('fa-times').parent().fadeTo(300,1); }
						},
						error: function(jqxhr, status, error) { console.log(error); console.log(jqxhr); armasolo.jQuery('#editSoloContentID_22_stacks_in_64 .saveSoloContent.result i.fa').addClass('fa-times').parent().fadeTo(300,1); },
						complete: function(){ armasolo.jQuery('#editSoloContentID_22_stacks_in_64 .saveSoloContent.progress').fadeTo(300,0); }
					});
				});
			})(armadilloSoloContent_22_stacks_in_64);
		});
	</script>
<?php endif; ?>
</div>

</div></div>
</div>
</div></div><div id='stacks_out_203' class='stacks_out'><div id='stacks_in_203' class='stacks_in '><div id='stacks_out_203_1' class='stacks_out'><div id='stacks_in_203_1' class='stacks_in com_elixir_stacks_foundryButton_stack'>

<div class="button-base-margin text-xs-center">
<a role="button" href="http://registration.classicalmusicfestival.org" rel="external" onclick="" target="" id="" class=" btn btn-md btn-primary  " >Register and Login</a>
</div></div></div></div></div></div></div>



	<!-- Column Two -->
	<div class="foundry_column foundry_column_two col-xs-12 col-md-8    "><div class="inner_column_content"><div id='stacks_out_168' class='stacks_out'><div id='stacks_in_168' class='stacks_in com_elixir_stacks_foundryContainer_stack'>


<div class="container">
	
		<div id='stacks_out_156' class='stacks_out'><div id='stacks_in_156' class='stacks_in com_elixir_stacks_foundrySlider_stack'>


<div class="loading_indicator">
<div class='uil-reload-css' style='-webkit-transform:scale(0.37)'><div></div></div></div>



<!-- Place somewhere in the <body> of your page -->
<div class="flexslider">
  <ul class="slides">
		<li data-thumb="files/thumbnail_filler.jpg">

  
    
    
    <img class="image_slide" src="files/slide_image-157.jpg" alt=""></img>
    
    
    <div class="flex-caption">
      Esterhazy Palace, Eisenstadt, Austria 
    </div>
    
  

  

  

  

</li>
<li data-thumb="files/thumbnail_filler.jpg">

  
    
    
    <img class="image_slide" src="files/slide_image-158.jpg" alt=""></img>
    
    
    <div class="flex-caption">
      The Haydn Hall of the Esterhazy Palace.
    </div>
    
  

  

  

  

</li>
<li data-thumb="files/thumbnail_filler.jpg">

  
    
    
    <img class="image_slide" src="files/slide_image-159.jpg" alt=""></img>
    
    
    <div class="flex-caption">
      The Classical Music Festival performs in Vienna's St. Stephen's Cathedral as part of the Sunday mass.
    </div>
    
  

  

  

  

</li>
<li data-thumb="files/thumbnail_filler.jpg">

  
    
    
    <img class="image_slide" src="files/slide_image-162.jpg" alt=""></img>
    
    
    <div class="flex-caption">
      The Bergkirche where Haydn is buried and where the festival will perform within the mass on August 15th, celebrating the Ascension of Mary. 
    </div>
    
  

  

  

  

</li>
<li data-thumb="files/thumbnail_filler.jpg">

  
    
    
    <img class="image_slide" src="files/slide_image-161.jpg" alt=""></img>
    
    
    <div class="flex-caption">
      Esterhazy Palace, Empiresaal
    </div>
    
  

  

  

  

</li>
<li data-thumb="files/thumbnail_filler.jpg">

  
    
    
    <img class="image_slide" src="files/slide_image-163.jpg" alt=""></img>
    
    
    <div class="flex-caption">
      View rarely seen original Haydn manuscripts from the Esterhazy archives.
    </div>
    
  

  

  

  

</li>
<li data-thumb="files/thumbnail_filler.jpg">

  
    
    
    <img class="image_slide" src="files/slide_image-164.jpg" alt=""></img>
    
    
    <div class="flex-caption">
      The Festival - Eisenstadt, the capital of the federal province of Burgenland since 1925, is situated on the southern slopes of the Leitha Mountains. Surrounded by a great number of vineyards, Eisenstadt is the smallest federal capital of Austria, with only 13,250 inhabitants.
    </div>
    
  

  

  

  

</li>
<li data-thumb="files/thumbnail_filler.jpg">

  
    
    
    <img class="image_slide" src="files/slide_image-165.jpg" alt=""></img>
    
    
    <div class="flex-caption">
      Leopoldine Temple in Schloss Park
    </div>
    
  

  

  

  

</li>

  </ul>
</div>
</div></div>
	
</div>
</div></div></div></div>







</div>
</div></div>
	
</div>
</div></div><div id='stacks_out_76' class='stacks_out'><div id='stacks_in_76' class='stacks_in com_elixir_stacks_foundryContainer_stack'>


<div class="container">
	
		<div id='stacks_out_181' class='stacks_out'><div id='stacks_in_181' class='stacks_in com_elixir_stacks_foundryMargins_stack'>

<div class="margins">
	<div id='stacks_out_192' class='stacks_out'><div id='stacks_in_192' class='stacks_in com_elixir_stacks_foundryColumns_stack'>



<div class="row   equal">


	<!-- Column One -->
	<div class="foundry_column foundry_column_one col-xs-12 col-md-7   "><div class="inner_column_content"><div id='stacks_out_78' class='stacks_out'><div id='stacks_in_78' class='stacks_in com_nimblehost_stack_armadilloSoloContent_stack'>
<div class="armadilloSoloContentContainer">
<?php
/* Only for use with the Armadillo CMS from NimbleHost*/
$siteAssetPath = 'rw_common/plugins/stacks';
include_once  $siteAssetPath . "/armadillo/core/model/Parsedown.php";
if ( $setupIsComplete ) {
	//TODO 	- if logged in, display edit button
	//		- option to edit content "inline"
	//		- enable lightbox and other functions that make sense
	//		- insert Armadillo menu items in theme nav if specified to do so

    //Display specified content
    $soloContent = displaySoloContent( $dbLink, '5', 'false', $armadilloOptions );

    if ( $soloContent == Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_SELECTED_ITEM_MISSING') ) {
    	//specified solo content ID is missing from database
    	echo $soloContent;
    } else {
	    if ( $soloContent['publish'] ) {
	    	$padding_for_5 = $soloContent['content'] == '' ? ' armadilloPadding' : '';
	    	echo '<div id="armadilloSoloContent_5_stacks_in_78" class="armadilloContent' . $padding_for_5 . '">' . $soloContent['content'] . '</div>';
	    } else {
	    	echo '<div id="armadilloSoloContent_5_stacks_in_78" class="armadilloContent draft"></div>';
    	}
    }
} else {
	// Setup isn't complete so display a notification
    echo "<p>" . Armadillo_Language::msg('ARM_SETUP_REQUIRED_MESSAGE1')
        . "</p><p><a href='$siteAssetPath/armadillo/'>"
        . Armadillo_Language::msg('ARM_SETUP_REQUIRED_MESSAGE2') . "</a></p>";
}
?>
<?php 
	// Check if someone is logged in and has sufficient permission to edit the content
	if ( 
		( isset($_COOKIE['armadillo']['bootMe']) and $_COOKIE['armadillo']['bootMe'] > time() 
			and isset($_COOKIE['armadillo']['loggedIn']) and $_COOKIE['armadillo']['loggedIn'] == 'TRUE' ) 
		and ( isset($_COOKIE['armadillo']['role']) and isset($_COOKIE['armadillo']['userID']) ) 
		and ( $_COOKIE['armadillo']['role'] == 'admin' or $_COOKIE['armadillo']['role'] == 'editor' or $_COOKIE['armadillo']['userID'] == $soloContent['userid'] ) ):
?>
	<!--<style>#armadilloSoloContent_5_stacks_in_78.draft { min-height: 2em; }</style>-->
	<?php if ( $soloContent == Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_SELECTED_ITEM_MISSING') ): ?>
	<p style="margin: 20px 0;padding: 10px;border:solid 1px #ffcccc;color: #cc0000;"><?php echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_SOLO_CONTENT_ID_MISSING'); ?></p>
	<!-- TODO: Add ajax call to create soloContent, refresh page if successfull or display error message otherwise -->
	<?php else: ?>
	<div class="armadilloEditButton"><a href="#editSoloContentID_5_stacks_in_78" class="displayEditor"><i class="fa fa-pencil-square-o"></i>&nbsp;ID: 5</a></div>
	<?php endif; ?>
	<div id="editSoloContentID_5_stacks_in_78" class="editSoloContent" style="display:none;">
		<div id="editSoloContentID_5_stacks_in_78_toolbar"></div>
		<?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
        <!-- Rich Text Editor -->
        <textarea id="soloContentID_5_stacks_in_78" name="soloContentID_5_stacks_in_78"><?php echo htmlspecialchars($soloContent['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        <?php else: ?>
        <!-- Markdown Editor -->
        <textarea autofocus id="soloContentID_5_stacks_in_78" name="soloContentID_5_stacks_in_78" data-uk-htmleditor="{markdown:true,autofocus:true}"><?php if ( isset($soloContent['format']) and $soloContent['format'] == 'markdown' ) { echo $soloContent['rawContent']; } ?></textarea>
        <?php if ( !isset($soloContent['format']) or $soloContent['format'] == 'html' ): ?>
        <script>document.addEventListener("DOMContentLoaded", function(event) { soloContentID_5_stacks_in_78 = document.getElementById('soloContentID_5_stacks_in_78'); soloContentID_5_stacks_in_78.value = toMarkdown('<?php echo str_replace(array("\n", "\r"),'', str_replace("'","\'",$soloContent['rawContent'])); ?>'); });</script>
        <?php endif; ?>
	    <?php endif; ?>
		<p class="saveSoloContentDetails">
			<a href="#soloContentID_5_stacks_in_78" id="saveSoloContentID_5_stacks_in_78" class="saveSoloContentButton armadilloButton"><?php echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_SOLO_CONTENT_SAVE_TEXT'); ?></a>
			<span class="saveSoloContent result"><i class="fa fa-lg"></i></span>
			<span class="saveSoloContent progress"></span>
		</p>
	</div>
	<script>
		document.addEventListener("DOMContentLoaded", function(event) {
			var armadilloSoloContent_5_stacks_in_78 = {};
			armadilloSoloContent_5_stacks_in_78 = (function(){
				<?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
				$R('#soloContentID_5_stacks_in_78', {
					toolbarExternal: '#editSoloContentID_5_stacks_in_78_toolbar',
					toolbarFixed: true,
					autoresize: false,
					maxHeight: 500,
					<?php if ( $_COOKIE['armadillo']['language'] !== 'default' ): ?>
					lang: '<?php echo $_COOKIE['armadillo']['language']; ?>',
					<?php endif; ?>
					deniedTags: ['html', 'head', 'link', 'body', 'meta', 'applet'],
					replaceDivs: false,
					phpTag: true,
					imageUpload: 'rw_common/plugins/stacks/armadillo/core/model/redactorFunctions.php?armURL=<?php echo $_COOKIE['armadillo']['armURL']; ?>',
					imageUploadErrorCallback: errorCallback,
					imageManagerJson: 'rw_common/plugins/stacks/armadillo/media/images.json',
					imageResizable: true,
					imagePosition: true,
					fileUpload: 'rw_common/plugins/stacks/armadillo/core/model/redactorFunctions.php?armURL=<?php echo $_COOKIE['armadillo']['armURL']; ?>',
					fileManagerJson: 'rw_common/plugins/stacks/armadillo/media/files.json',
					<?php if ($_COOKIE['armadillo']['editorType'] == 'basictext'): ?>
					buttons: ['format', 'bold', 'italic', 'deleted', 'lists', 'link', 'line', 'image', 'file'],
					plugins: ['imagemanager', 'filemanager']
					<?php else: ?>
					buttons: ['html', 'undo', 'redo', 'format', 'bold', 'italic', 'deleted', 'underline', 'lists', 'indent', 'outdent', 'image', 'file', 'link', 'line'],
					plugins: ['imagemanager', 'filemanager', 'video', 'table', 'alignment', 'fontsize', 'fontcolor', 'textdirection', 'counter']
					<?php endif; ?>
				});
				<?php endif; ?>
				armasolo.jQuery('#saveSoloContentID_5_stacks_in_78').click(function(){
					<?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
					var updatedContent = $R('#soloContentID_5_stacks_in_78', 'source.getCode');
					var previewHTML = updatedContent;
					updatedContent = encodeURIComponent(updatedContent);
					var format = 'html';
					<?php else: ?>
					var updatedContent = encodeURIComponent(armasolo.jQuery('#soloContentID_5_stacks_in_78').val());
					var previewHTML = armasolo.jQuery('#editSoloContentID_5_stacks_in_78 .uk-htmleditor .uk-htmleditor-content .uk-htmleditor-preview').html();
					var format = 'markdown';
					<?php endif; ?>
					armasolo.jQuery.ajax({
						beforeSend: function(){ armasolo.jQuery('#editSoloContentID_5_stacks_in_78 .saveSoloContent.result i.fa').removeClass('fa-times, fa-check').parent().css('opacity',0); armasolo.jQuery('#editSoloContentID_5_stacks_in_78 .saveSoloContent.progress').fadeTo(300,1); },
						url: 'rw_common/plugins/stacks/armadillo/core/model/updateArmadilloSettings.php',
						type: 'post',
						data: 'action=updateContent&format=' + format + '&contentID=5&content=' + updatedContent,
						success: function(data, status) {
							if ( data == 'true' ) {
								armasolo.jQuery('#editSoloContentID_5_stacks_in_78 .saveSoloContent.result i.fa').addClass('fa-check').parent().fadeTo(300,1);
								armasolo.jQuery('#armadilloSoloContent_5_stacks_in_78').html(previewHTML);
							} else { console.log(data); console.log(status); armasolo.jQuery('#editSoloContentID_5_stacks_in_78 .saveSoloContent.result i.fa').addClass('fa-times').parent().fadeTo(300,1); }
						},
						error: function(jqxhr, status, error) { console.log(error); console.log(jqxhr); armasolo.jQuery('#editSoloContentID_5_stacks_in_78 .saveSoloContent.result i.fa').addClass('fa-times').parent().fadeTo(300,1); },
						complete: function(){ armasolo.jQuery('#editSoloContentID_5_stacks_in_78 .saveSoloContent.progress').fadeTo(300,0); }
					});
				});
			})(armadilloSoloContent_5_stacks_in_78);
		});
	</script>
<?php endif; ?>
</div>

</div></div></div></div>



	<!-- Column Two -->
	<div class="foundry_column foundry_column_two col-xs-12 col-md-5    "><div class="inner_column_content"><div id='stacks_out_196' class='stacks_out'><div id='stacks_in_196' class='stacks_in com_elixir_stacks_foundryHeader_stack'>

<div class="text-xs-left">
	
	<h6 class="theme_style  ">Watch the Classical Music Festival Video</h6>
	
</div>
</div></div><div id='stacks_out_195' class='stacks_out'><div id='stacks_in_195' class='stacks_in com_elixir_stacks_foundryVideoEmbed_stack'>


<div class="embed-responsive embed-responsive-16by9">
	
		
		<iframe src="https://player.vimeo.com/video/183558521" frameborder="0" webkitallowfullscreen mozallowfullscreen allowfullscreen></iframe>
		
	

	
</div>








</div></div></div></div>







</div>
</div></div><div id='stacks_out_202' class='stacks_out'><div id='stacks_in_202' class='stacks_in '><div id='stacks_out_202_933' class='stacks_out'><div id='stacks_in_202_933' class='stacks_in stack_stack'><div id='stacks_out_202_226' class='stacks_out'><div id='stacks_in_202_226' class='stacks_in html_stack'><a class="social-facebook social-import" href=http://www.facebook.com/classicalmusicfestival></a></div></div><div id='stacks_out_202_937' class='stacks_out'><div id='stacks_in_202_937' class='stacks_in com_nimblehost_stack_armadilloSoloContent_stack'>
<div class="armadilloSoloContentContainer">
<?php
/* Only for use with the Armadillo CMS from NimbleHost*/
$siteAssetPath = 'rw_common/plugins/stacks';
include_once  $siteAssetPath . "/armadillo/core/model/Parsedown.php";
if ( $setupIsComplete ) {
	//TODO 	- if logged in, display edit button
	//		- option to edit content "inline"
	//		- enable lightbox and other functions that make sense
	//		- insert Armadillo menu items in theme nav if specified to do so

    //Display specified content
    $soloContent = displaySoloContent( $dbLink, '6', 'true', $armadilloOptions );

    if ( $soloContent == Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_SELECTED_ITEM_MISSING') ) {
    	//specified solo content ID is missing from database
    	echo $soloContent;
    } else {
	    if ( $soloContent['publish'] ) {
	    	$padding_for_6 = $soloContent['content'] == '' ? ' armadilloPadding' : '';
	    	echo '<div id="armadilloSoloContent_6_stacks_in_202_937" class="armadilloContent' . $padding_for_6 . '">' . $soloContent['content'] . '</div>';
	    } else {
	    	echo '<div id="armadilloSoloContent_6_stacks_in_202_937" class="armadilloContent draft"></div>';
    	}
    }
} else {
	// Setup isn't complete so display a notification
    echo "<p>" . Armadillo_Language::msg('ARM_SETUP_REQUIRED_MESSAGE1')
        . "</p><p><a href='$siteAssetPath/armadillo/'>"
        . Armadillo_Language::msg('ARM_SETUP_REQUIRED_MESSAGE2') . "</a></p>";
}
?>
<?php 
	// Check if someone is logged in and has sufficient permission to edit the content
	if ( 
		( isset($_COOKIE['armadillo']['bootMe']) and $_COOKIE['armadillo']['bootMe'] > time() 
			and isset($_COOKIE['armadillo']['loggedIn']) and $_COOKIE['armadillo']['loggedIn'] == 'TRUE' ) 
		and ( isset($_COOKIE['armadillo']['role']) and isset($_COOKIE['armadillo']['userID']) ) 
		and ( $_COOKIE['armadillo']['role'] == 'admin' or $_COOKIE['armadillo']['role'] == 'editor' or $_COOKIE['armadillo']['userID'] == $soloContent['userid'] ) ):
?>
	<!--<style>#armadilloSoloContent_6_stacks_in_202_937.draft { min-height: 2em; }</style>-->
	<?php if ( $soloContent == Armadillo_Language::public_msg($armadilloOptions, 'ARM_CONTENT_SELECTED_ITEM_MISSING') ): ?>
	<p style="margin: 20px 0;padding: 10px;border:solid 1px #ffcccc;color: #cc0000;"><?php echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_SOLO_CONTENT_ID_MISSING'); ?></p>
	<!-- TODO: Add ajax call to create soloContent, refresh page if successfull or display error message otherwise -->
	<?php else: ?>
	<div class="armadilloEditButton"><a href="#editSoloContentID_6_stacks_in_202_937" class="displayEditor"><i class="fa fa-pencil-square-o"></i>&nbsp;ID: 6</a></div>
	<?php endif; ?>
	<div id="editSoloContentID_6_stacks_in_202_937" class="editSoloContent" style="display:none;">
		<div id="editSoloContentID_6_stacks_in_202_937_toolbar"></div>
		<?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
        <!-- Rich Text Editor -->
        <textarea id="soloContentID_6_stacks_in_202_937" name="soloContentID_6_stacks_in_202_937"><?php echo htmlspecialchars($soloContent['content'], ENT_QUOTES, 'UTF-8'); ?></textarea>
        <?php else: ?>
        <!-- Markdown Editor -->
        <textarea autofocus id="soloContentID_6_stacks_in_202_937" name="soloContentID_6_stacks_in_202_937" data-uk-htmleditor="{markdown:true,autofocus:true}"><?php if ( isset($soloContent['format']) and $soloContent['format'] == 'markdown' ) { echo $soloContent['rawContent']; } ?></textarea>
        <?php if ( !isset($soloContent['format']) or $soloContent['format'] == 'html' ): ?>
        <script>document.addEventListener("DOMContentLoaded", function(event) { soloContentID_6_stacks_in_202_937 = document.getElementById('soloContentID_6_stacks_in_202_937'); soloContentID_6_stacks_in_202_937.value = toMarkdown('<?php echo str_replace(array("\n", "\r"),'', str_replace("'","\'",$soloContent['rawContent'])); ?>'); });</script>
        <?php endif; ?>
	    <?php endif; ?>
		<p class="saveSoloContentDetails">
			<a href="#soloContentID_6_stacks_in_202_937" id="saveSoloContentID_6_stacks_in_202_937" class="saveSoloContentButton armadilloButton"><?php echo Armadillo_Language::public_msg($armadilloOptions, 'ARM_SOLO_CONTENT_SAVE_TEXT'); ?></a>
			<span class="saveSoloContent result"><i class="fa fa-lg"></i></span>
			<span class="saveSoloContent progress"></span>
		</p>
	</div>
	<script>
		document.addEventListener("DOMContentLoaded", function(event) {
			var armadilloSoloContent_6_stacks_in_202_937 = {};
			armadilloSoloContent_6_stacks_in_202_937 = (function(){
				<?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
				$R('#soloContentID_6_stacks_in_202_937', {
					toolbarExternal: '#editSoloContentID_6_stacks_in_202_937_toolbar',
					toolbarFixed: true,
					autoresize: false,
					maxHeight: 500,
					<?php if ( $_COOKIE['armadillo']['language'] !== 'default' ): ?>
					lang: '<?php echo $_COOKIE['armadillo']['language']; ?>',
					<?php endif; ?>
					deniedTags: ['html', 'head', 'link', 'body', 'meta', 'applet'],
					replaceDivs: false,
					phpTag: true,
					imageUpload: 'rw_common/plugins/stacks/armadillo/core/model/redactorFunctions.php?armURL=<?php echo $_COOKIE['armadillo']['armURL']; ?>',
					imageUploadErrorCallback: errorCallback,
					imageManagerJson: 'rw_common/plugins/stacks/armadillo/media/images.json',
					imageResizable: true,
					imagePosition: true,
					fileUpload: 'rw_common/plugins/stacks/armadillo/core/model/redactorFunctions.php?armURL=<?php echo $_COOKIE['armadillo']['armURL']; ?>',
					fileManagerJson: 'rw_common/plugins/stacks/armadillo/media/files.json',
					<?php if ($_COOKIE['armadillo']['editorType'] == 'basictext'): ?>
					buttons: ['format', 'bold', 'italic', 'deleted', 'lists', 'link', 'line', 'image', 'file'],
					plugins: ['imagemanager', 'filemanager']
					<?php else: ?>
					buttons: ['html', 'undo', 'redo', 'format', 'bold', 'italic', 'deleted', 'underline', 'lists', 'indent', 'outdent', 'image', 'file', 'link', 'line'],
					plugins: ['imagemanager', 'filemanager', 'video', 'table', 'alignment', 'fontsize', 'fontcolor', 'textdirection', 'counter']
					<?php endif; ?>
				});
				<?php endif; ?>
				armasolo.jQuery('#saveSoloContentID_6_stacks_in_202_937').click(function(){
					<?php if ( !isset($_COOKIE['armadillo']['editorType']) or ( isset($_COOKIE['armadillo']['editorType']) and ( $_COOKIE['armadillo']['editorType'] == 'richtext' or $_COOKIE['armadillo']['editorType'] == 'basictext' ) ) ): ?>
					var updatedContent = $R('#soloContentID_6_stacks_in_202_937', 'source.getCode');
					var previewHTML = updatedContent;
					updatedContent = encodeURIComponent(updatedContent);
					var format = 'html';
					<?php else: ?>
					var updatedContent = encodeURIComponent(armasolo.jQuery('#soloContentID_6_stacks_in_202_937').val());
					var previewHTML = armasolo.jQuery('#editSoloContentID_6_stacks_in_202_937 .uk-htmleditor .uk-htmleditor-content .uk-htmleditor-preview').html();
					var format = 'markdown';
					<?php endif; ?>
					armasolo.jQuery.ajax({
						beforeSend: function(){ armasolo.jQuery('#editSoloContentID_6_stacks_in_202_937 .saveSoloContent.result i.fa').removeClass('fa-times, fa-check').parent().css('opacity',0); armasolo.jQuery('#editSoloContentID_6_stacks_in_202_937 .saveSoloContent.progress').fadeTo(300,1); },
						url: 'rw_common/plugins/stacks/armadillo/core/model/updateArmadilloSettings.php',
						type: 'post',
						data: 'action=updateContent&format=' + format + '&contentID=6&content=' + updatedContent,
						success: function(data, status) {
							if ( data == 'true' ) {
								armasolo.jQuery('#editSoloContentID_6_stacks_in_202_937 .saveSoloContent.result i.fa').addClass('fa-check').parent().fadeTo(300,1);
								armasolo.jQuery('#armadilloSoloContent_6_stacks_in_202_937').html(previewHTML);
							} else { console.log(data); console.log(status); armasolo.jQuery('#editSoloContentID_6_stacks_in_202_937 .saveSoloContent.result i.fa').addClass('fa-times').parent().fadeTo(300,1); }
						},
						error: function(jqxhr, status, error) { console.log(error); console.log(jqxhr); armasolo.jQuery('#editSoloContentID_6_stacks_in_202_937 .saveSoloContent.result i.fa').addClass('fa-times').parent().fadeTo(300,1); },
						complete: function(){ armasolo.jQuery('#editSoloContentID_6_stacks_in_202_937 .saveSoloContent.progress').fadeTo(300,0); }
					});
				});
			})(armadilloSoloContent_6_stacks_in_202_937);
		});
	</script>
<?php endif; ?>
</div>

</div></div><div id='stacks_out_202_938' class='stacks_out'><div id='stacks_in_202_938' class='stacks_in com_nimblehost_stack_armadilloLoginLink_stack'>
<?php if ( $setupIsComplete && $armadilloOptions['display_admin_link'] ): ?>
<style>
	.armadilloAdminLink { display: <?php echo $armadilloOptions['display_admin_link'] == FALSE ? 'none' : 'inline'; ?>; color: #<?php echo $armadilloOptions['adminlink_color']; ?>; }
    .armadilloAdminLink:hover { color: #<?php echo $armadilloOptions['adminlink_hovercolor']; ?>; }
</style>
<p><a href="rw_common/plugins/stacks/armadillo/index.php/" class="armadilloAdminLink"><?php echo $armadilloOptions['adminlink_text']; ?></a></p>
<?php endif; ?>

</div></div></div></div></div></div>
</div>
</div></div>
	
</div>
</div></div></div></div>


  </div>

  <!-- Base RapidWeaver Javascript -->
  <script src="rw_common/themes/Foundry/javascript.js?rwcache=685984326"></script>

  <!-- Load jQuery -->
  <script src="rw_common/themes/Foundry/js/jquery.min.js?rwcache=685984326"></script>

  <!-- Tether.js || used for tooltips -->
	<script src="rw_common/themes/Foundry/js/tether.min.js?rwcache=685984326"></script>

	<!-- Latest compiled and minified JavaScript -->
	<script src="rw_common/themes/Foundry/js/bootstrap.min.js?rwcache=685984326"></script>

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
