<?php

//include our functions so we can call them
include_once('functions.php');

//open up the flat-file database and make an array of lines in it
$tracks = file('db/db.txt');

//if we made a query and it isn't empty
if(isset($_POST['query']) && strlen($_POST['query']) > 0) {
	
	//create an array of matches that we will fill
	$match = array();
	
	//split the query into separate terms, getting rid of spaces
	//make sure to trim the query so that we still see a result if we press space before typing something new
	$searchterms = preg_split("/\s+/", trim($_POST['query']));
	foreach($tracks as $track) {
		$success = true;
		foreach($searchterms as $searchterm) {
			
			//check for matches, case insensitive with stripos
			if(stripos($track, $searchterm) === false) {
				//didn't find any matches
				$success = false;
				break;
			}
		}
		
		//we found a match! put it in the match array
		if ($success) {
			$match[] = explode("|", rtrim($track));
		}
	}
	
	//return an array with all tracks that are matched, and success = 1
	$json = array(
				"tracks" => $match,
				"success" => 1
				);
} else {

	//for the case where we are just loaded the page and haven't searched anything yet, return all the tracks
	$json = array(
				//create a 3-dimensional array where each line is exploded with the character "|"
				"tracks" => array_map(function($e) { return explode("|", rtrim($e)); }, $tracks),
				"success" => 1
				);
}

//die and return the JSON object
die(ajax_response($json));

?>