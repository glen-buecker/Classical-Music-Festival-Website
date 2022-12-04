<?php

if ( isset($dbHostname, $dbName, $dbUsername, $dbPassword) ) {
    $dbLink = new mysqli($dbHostname, $dbUsername, $dbPassword, $dbName);

    if ($dbLink->connect_errno) {
        $notification .= "Connection to the database failed: " . mysqli_connect_error();
        echo $notification;
    }

    if (!$dbLink->set_charset("utf8")) {
        $notification .= "Oops. Unable to set the database connection encoding: " . $dbLink->error;
        echo $notification;
    }

    if (!$dbLink->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'STRICT_TRANS_TABLES',''))") || !$dbLink->query("SET sql_mode=(SELECT REPLACE(@@sql_mode,'ONLY_FULL_GROUP_BY',''))")) {
    	$notification .= "Oops. Unable to configure the database SQL mode.";
    	echo $notification;
    }
}
