<?php
/* 
*  TODO: Add error checks, change process to first connect to remote script, check if user is valid (domain checking?)
*  then go ahead with download and update.
*  UPDATE 2011-07-13 : Try to setup and use a smarter updating system. Perhaps add a new database table, keep track of
*  the installed version (maybe via config file, rather than database), compare to latest remote version, only updated changed files, etc.
*/
function update_core() {
	$url  = '';//'http://www.nimblehost.com/apps/rapidweaver/armadillo/latest.zip';
	$coreDirectory = dirname(__FILE__);
	$path = $coreDirectory . '/updates/latest.zip';
	
	$updateFile = fopen($path, 'w');
	echo "<p>Opening local file...</p>";
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_FILE, $updateFile);
	echo "<p>Setting connection options...</p>";
	$data = curl_exec($ch);
	echo "<p>Downloading update...</p>";
	curl_close($ch);
	echo "<p>Closing connection...</p>";
	fclose($updateFile);
	echo "<p>Success! File successfully downloaded and saved.</p>";
	
	$debug = FALSE;
	
	$zip = new ZipArchive();  
    $x = $zip->open('updates/latest.zip');  
    if($x === true) {  
        $zip->extractTo($coreDirectory);  
        $zip->close();  

        unlink('updates/latest.zip');  
    } else {
        if($debug !== true) {
            unlink('updates/latest.zip');
        }  
        die("<p>There was a problem. Please try again!</p>");  
    }
}

?>