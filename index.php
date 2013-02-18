<?php

//include our functions so we can call them
include_once('functions.php');

?>
<!DOCTYPE html>
<html>
    <head>
        <title>A State of Cornell Trance Episode <?php echo current_episode(); ?></title>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, maximum-scale=1">
        <meta name="author" content="Will Najar">
        <meta http-equiv="X-UA-Compatible" content="IE=edge,chrome=1">
        <link href="http://fonts.googleapis.com/css?family=Roboto:400,700" rel="stylesheet" type="text/css"/>
        <link href="style/style.css" rel="stylesheet" type="text/css"/>
        <script type="text/javascript" src="//ajax.googleapis.com/ajax/libs/jquery/1.9.1/jquery.min.js"></script>
        <script type="text/javascript" src="lib/mousetrap.min.js"></script>
        <script type="text/javascript" src="lib/jquery.serialize.js"></script>
        <script type="text/javascript" src="script/script.js"></script>
    </head>
    <body>
    	<header>
        	<img class="logo" src="static/asotlogo.png" alt="a state of trance logo" width="100" height="70">
        	<h1>A State of Cornell Trance Episode <span class="episodenumber"><?php echo current_episode(); ?></span></h1>
            <p class="subheader">Inspired by Dutch DJ Armin Van Buuren's <a target="_blank" href="http://en.wikipedia.org/wiki/A_State_of_Trance">A State of Trance weekly radio show and podcast</a>.</p>
        </header>
        <section id="nowplaying">
        	<h2>Now playing:</h2>
            <div id="musicplayer">
            </div>
            <div id="tipsandtricks">
            	<p>Use the <span class="keyboard previous">&larr;</span> and <span class="keyboard next">&rarr;</span> arrow keys (or click these ones) to switch between songs.</p>
            </div>
        </section>
        <section id="search">
        	<h2 class="searchheader">What would you like to listen to?</h2>
            <div id="primers">
            	<ul>
                	<li class="random">Pick something for me</li>
                    <li class="search">Search</li>
                    <li class="browse">Just browse the tracklist</li>
                    <li class="separator">OR</li>
                    <li class="addtrack">Add a new track</li>
                </ul>
            </div>
            <div id="searchbar">
            <!-- no form action because this will be submitted via ajax -->
            <!-- we don't want the page to reload or else everything else is unset and the entire point of the instant search is useless -->
            <form action="#" id="searchform">
				Search for an artist, track title or genre: <input type="text" id="searchbar" name="search"> <span class="resultsupdate">(results update live)</span>
			</form>
            </div>
            <div id="addtrack">
            	<h2>Add a track:</h2>
                <!-- no form action because this will be submitted via ajax -->
                <!-- we don't want the page to reload or else everything else is unset and the user will have to click through again -->
                <form action="#" id="addtrackform">
                	<div class="formitem artist">
                    	Artist: <input type="text" name="artist" id="artist" />
                    </div>
                    <div class="formitem title">
                    	Title: <input type="text" name="title" id="title" />
                    </div>
                    <div class="formitem soundcloudurl">
                    	SoundCloud ID: <input type="text" name="soundcloudurl" id="soundcloudurl" /> <span class="whatisthis">(?)</span>
                    </div>
                    <div class="formitem genre">
                    	Genre: <input type="text" name="genre" id="genre" />
                    </div>
                    <div class="formitem length">
                    	Length: <input type="text" name="length" id="length" />
                    </div>
                    <input type="submit" name="submit" id="addtracksubmit" value="Add track" />
				</form>
                <div id="addresponse"></div>
            </div>
        </section>
        <section id="tracklist" class="blurred">
        	<h2>Select any track to start listening:</h2>
            	<div class="artist tracklistlabel">Artist</div>
                <div class="title tracklistlabel">Track Title</div>
                <div class="genre tracklistlabel">Genre</div>
                <div class="length tracklistlabel">Length</div>
            <ol id="tracklist">
            </ol>
        </section>
        <footer>
        	<p>Copyright 2013 <a target="_blank" href="http://willnajar.com">Will Najar</a>. Shh no biology... only websites now. <a target="_blank" href="http://i.imgur.com/U0KwHfJ.jpg">U mirin?</a></p>
        </footer>
    </body>
</html>