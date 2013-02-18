<?php

//include functions so we can call them
include_once('functions.php');

//if we've submitted the form
if (isset($_POST)) {
	
	//sanitize inputs and store them in a $fields array
	$fields = array(
		"artist" => htmlspecialchars($_POST['artist']),
		"title" => htmlspecialchars($_POST['title']),
		"soundcloudurl" => htmlspecialchars($_POST['soundcloudurl']),
		"genre" => htmlspecialchars($_POST['genre']),
		"length" => htmlspecialchars($_POST['length'])
	);
	
	//validate all fields to make sure each field is at least 3 characters
	validate_all_fields($fields, function($field) { 
									return strlen($field) >= 3;
								 }, 
						"Please... stay safe.  Each entry must be at least 3 characters.");
	
	//validate all fields to make sure each field contains only ASCII-US characters				
	validate_all_fields($fields, function($field) {
									return preg_match('/^[\x00-\x7f]+$/', $field);
								 }, 
						"Shh no tears... only ASCII-US characters now.");
	
	//validate all fields to make sure the delimiter | is not in the input		
	validate_all_fields($fields, function($field) {
									return !preg_match("/\|+/", $field);
								 }, 
						"Please... stay safe. | is a very dangerous character.");
	
	//validate only the soundcloudurl field to make sure it is only numbers
	//notice how here and in the next example we create an array with one entry to simulate the usual input format of $fields
	validate_all_fields(array("soundcloudurl" => $fields["soundcloudurl"]), function($field) {
																				return preg_match("/^\d+$/", $field);
																			},
						"Your SoundCloud ID should be numbers only.");
	
	//validate only the length field to make sure it is only numbers and colons
	validate_all_fields(array("length" => $fields["length"]), function($field) {
																return preg_match("/^[\d:]+$/", $field);
															  },
						"That doesn't look like a time... try again.");
	
	//open the flat-file database to append
	$fp = fopen("db/db.txt", "a+");
	
	//if we couldn't open the database file
	if(!$fp) {
		$json = array(
			"message" => "Database file could not be opened.",
			"success" => 0
		);
		die(ajax_response($json));
	}
	
	//check for exact duplicates (even with different cases)
	//could also be done more creatively with stripos() see: trackdata.php
	$compare = strtolower(implode("|", $fields) . "\n");
	foreach(file('db/db.txt') as $entry => $value){
		$value = strtolower($value);
		if($value === $compare) {
			$json = array(
				"message" => "You've already added that track!",
				"fields" => "artist,title,soundcloudurl,genre,length",
				"success" => 0 
			);	
			die(ajax_response($json));
		}
	}
	
	//store these fields in convenient variables to access later, fix strange bug
	$newartist = $fields["artist"];
	$newtitle = $fields["title"];
	$newsoundcloudurl = $fields["soundcloudurl"];
	$newgenre = $fields["genre"];
	$newlength = $fields["length"];
	
	//write the new line in the flat-file database
	fputs($fp, implode("|", $fields) . "\n");
	
	//increment the episode number, lol
	$current_episode = (int)file_get_contents('db/episode.txt');
	$current_episode++;
	file_put_contents('db/episode.txt', $current_episode);
	
	//return all the stuff we will need to update the page via a JSON object
	$json = array(
		"message" => "Track successfully added.",
		"newartist" => $newartist,
		"newtitle" => $newtitle,
		"newsoundcloudurl" => $newsoundcloudurl,
		"newgenre" => $newgenre,
		"newlength" => $newlength,
		"episode" => $current_episode,
		"success" => 1
	);
	
	//close the file
	fclose($fp);
	
	//no matter what happened, echo the JSON object with whatever it holds
	echo ajax_response($json);
}

?>