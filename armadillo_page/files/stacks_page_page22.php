<?php

if ( !file_exists(dirname(__FILE__) . '/../../rw_common/plugins/stacks/armadillo/core/config.php') ) {
	// Check that the provided db info is correct, only then create the config file
	$dbLink = @new mysqli('129.15.77.24', 'techsupport', '12BsSupp0rT', 'cmf_website');
    if ($dbLink->connect_errno) { 
    	// Do something here if connection fails
    } else {
		$armadilloConfigFilePath = dirname(__FILE__) . '/../../rw_common/plugins/stacks/armadillo/core/config.php';
		$armadilloConfigFileContents = '<?php' . PHP_EOL . PHP_EOL
			. '//Armadillo config settings' . PHP_EOL
			. '$dbHostname = \'129.15.77.24\';' . PHP_EOL
	        . '$dbName = \'cmf_website\';' . PHP_EOL
	        . '$dbUsername = \'techsupport\';' . PHP_EOL
	        . '$dbPassword = \'12BsSupp0rT\';' . PHP_EOL . PHP_EOL
			. '?>';
		$armadilloConfigFile = NULL;

		try {
			if ( $armadilloConfigFile = fopen($armadilloConfigFilePath, 'w') ) {
			    if ( fwrite($armadilloConfigFile, $armadilloConfigFileContents) !== FALSE ) {
			        fclose($armadilloConfigFile);
			    } else { throw new Exception('Could not edit the contents of the configuration file for Armadillo.'); }
			} else { throw new Exception('Could not generate the configuration file for Armadillo.'); }
	    } catch (Exception $result) {
	        chmod(dirname(__FILE__) . '/../../rw_common/plugins/stacks/armadillo/core', 0777);
	        $armadilloConfigFile = fopen($armadilloConfigFilePath, 'w');
	        fwrite($armadilloConfigFile, $armadilloConfigFileContents);
	        fclose($armadilloConfigFile);
	        chmod(dirname(__FILE__) . '/../../rw_common/plugins/stacks/armadillo/core', 0755);
	    }
	}
}

?>