<?php

//function that displays the episode number, stored in a lolzy flat-file database
function current_episode() {
	return file_get_contents('db/episode.txt');
}

//function to generate a well-formed JSON object with whatever $fields you give it
//JSON syntax: http://en.wikipedia.org/wiki/JSON#Data_types.2C_syntax_and_example
function ajax_response($fields) {
	
	$ajax = "{";
	foreach ($fields as $field => $value) {
		$ajax .= "\"" . $field . "\" : ";
		if (is_array($value)) {
			$ajax .= ajax_response($value);
		} elseif (is_numeric($value)) {
			$ajax .= $value;
		} else {
			$ajax .= "\"$value\"";
		}
		$ajax .= ", ";
	}
	//get rid of the last comma
	$ajax = rtrim($ajax, ", ") . "}";
	
	return $ajax;
}

//function to validate all fields (or potentially one field) with whatever test you give it, and return the specific message if there is an error
function validate_all_fields($fields, $callback, $message) {
	$invalid = array();
	
	foreach($fields as $field => $value) {
		if(!$callback($value)) {
			//collect fields with errors in $invalid array
			$invalid[] = $field;
		}
	}
	
	//if there were errors
	if(count($invalid) > 0) {
		$json = array(
			"message" => $message,
			"fields" => implode(",", $invalid),
			"success" => 0 
		);
		//return the JSON object with the right error fields and message	
		die(ajax_response($json));
	}
}

?>