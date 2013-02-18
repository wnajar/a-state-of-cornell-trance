$(document).ready(function() {

//function that calls trackdata.php via AJAX and returns results in real time in ol#tracklist
function fetch_tracks(param) {
	param = (param === null ? "" : param);
	$.ajax({url: "trackdata.php", type: "POST", data: {query: param}, success: function(data){
		var json_data = jQuery.parseJSON(data);
		if (json_data.success == 1) {
			$('ol#tracklist').empty();
			if(json_data.tracks[0] !== undefined) {
				//tracks were returned as matches
				$.each(json_data.tracks, function(key, line) {
					append_track(line);	
				});
			} else {
				//no results were returned
				$('ol#tracklist').append('<li class="nomatch">No matches found.</li>');
			}
		}
		//register click handler to play a track that is clicked on by the user (must be done here because .live() is deprecated and nothing will register on $(document).ready() because the server hasn't returned data yet)
		$('ol#tracklist li').click(function() {
			var identifier = $(this).children('span.soundcloud').html();
			change_track(identifier);
			if($('section#tracklist').hasClass('blurred') === true) {
				$('section#tracklist').animate({opacity: 1}).removeClass('blurred'); 
			}
			if($('li.browse').length != 0) {
				$('li.browse').remove();
			}
		});
	}});		
}

//function to delay the real-time search results until the user stops typing
//credit: http://stackoverflow.com/questions/1909441/jquery-keyup-delay
var delay = (function(){
  var timer = 0;
  return function(callback, ms){
    clearTimeout (timer);
    timer = setTimeout(callback, ms);
  };
})();

//function to spit out a <li> element with track data inside, called form within real-time search function
function append_track(json_data) {
	$('ol#tracklist').append('<li><span class="artist">' + json_data[0] + '</span><span class="title">' + json_data[1] + '</span><span class="soundcloud hidden">' + json_data[2] + '</span><span class="genre">' + json_data[3] + '</span><span class="length">' + json_data[4] + '</span></li>');
}

//function to play a track according to parameter passed and open the now playing bar if it isn't already open
function change_track(track) {
	$('div#musicplayer').html("<iframe width=\"100%\" height=\"166\" scrolling=\"no\" frameborder=\"no\" src=\"https://w.soundcloud.com/player/?url=http%3A%2F%2Fapi.soundcloud.com%2Ftracks%2F" + track + "&amp;color=41d4e2&amp;auto_play=true&amp;show_artwork=true\"></iframe>").attr("data-currenttrack", track);
	if($('section#nowplaying').hasClass('visible') == false) {
		$('section#nowplaying').slideDown().addClass('visible');
	}
}

//function to play the next track in the tracklist
function next_track() {
	var alltracks = new Array();
	$('span.soundcloud').each(function() {
		alltracks.push($(this).html());
	})
	var currenttrack = $('div#musicplayer').attr('data-currenttrack');
	var index = $.inArray(currenttrack, alltracks)
	if(index == alltracks.length-1) {
		change_track(alltracks[0]);
	} else {
		change_track(alltracks[index + 1]);
	}
}

//function to play the previous track in the tracklist
function previous_track() {
	var alltracks = new Array();
	$('span.soundcloud').each(function() {
		alltracks.push($(this).html());
	})
	var currenttrack = $('div#musicplayer').attr('data-currenttrack');
	var index = $.inArray(currenttrack, alltracks)
	if(index == 0) {
		change_track(alltracks[alltracks.length-1]);
	} else {
		change_track(alltracks[index - 1]);
	}
}

//click handler for button to play a random song in the tracklist
$('li.random').click(function() {
	var alltracks = new Array();
	$('span.soundcloud').each(function() {
		alltracks.push($(this).html());
	})
	change_track(alltracks[Math.floor(Math.random()*alltracks.length)]);
	$('li.browse').remove();
	if($('section#tracklist').hasClass('blurred') === true) {
		$('section#tracklist').animate({opacity: 1}).removeClass('blurred'); 
	}
	$(this).html('Pick another track for me');
	$('section#search h2.searchheader').html('What would you like to do?');
});

//click handler for button to browse the tracklist (un-blur and make opacity = 1)
$('li.browse').click(function() {
	$(this).remove();
	$('h2.searchheader').html('What would you like to do?');
	$('section#tracklist').animate({opacity: 1}).removeClass('blurred'); 
});

//click handler for the button to slide down the search form to search
$('li.search').click(function() {
	$('h2.searchheader').html('What would you like to do?');
	$('li.browse').remove();
	if($('section#tracklist').hasClass('blurred') === true) {
		$('section#tracklist').animate({opacity: 1}).removeClass('blurred'); 
	}
	if($('div#addtrack').hasClass('toggled') === true) {
		$('div#addtrack').hide().removeClass('toggled'); 
	}
	$('div#searchbar').slideToggle().toggleClass('toggled');
});

//click handler for the button to add a new track (show the add new track form)
$('li.addtrack').click(function() {
	$('h2.searchheader').html('What would you like to do?');
	$('li.browse').remove();
	if($('section#tracklist').hasClass('blurred') === true) {
		$('section#tracklist').animate({opacity: 1}).removeClass('blurred'); 
	}
	if($('div#searchbar').hasClass('toggled') === true) {
		$('div#searchbar').hide().removeClass('toggled'); 
	}
	$('div#addtrack').slideToggle().toggleClass('toggled');
	
});

//keyboard button click handler (next track)
$('.keyboard.next').click(function() {
	next_track(); 
});

//keyboard button click handler (previous track)
$('.keyboard.previous').click(function() {
	previous_track(); 
});

//key binding right arrow key to next_track()
Mousetrap.bind('right', function() {
	next_track();
});

//key binding left arrow key to previous_track()
Mousetrap.bind('left', function() {
	previous_track();
});

//AJAX database searching and real-time result updating
$('input#searchbar').keyup(function() {
	delay(function() {
		fetch_tracks($('input#searchbar').val());
	}, 500);
})

//make sure pressing enter while in the search bar does not reload the page
$('form#searchform').submit(function() {
	return false;
});

//ajax submit handler to add a new track and perform necessary actions on success
$('#addtrackform').submit(function() {
	$.ajax({url: "addtrack.php", type: "POST", data: $("#addtrackform").serializeObject(), success: function(data){
		var json_data = jQuery.parseJSON(data);
		//display whatever message is returned
		$('div#addresponse').html(json_data.message).fadeIn();
		//clear all the input fields before re-evaluating them
		$('input[type="text"]').css('background', '#333');
		if(json_data.success == 0) {
			//show errors and make the fields with errors red
			var errors = json_data.fields.split(",");
			for (i = 0; i < errors.length; i++) {
				$('input#' + errors[i]).css('background', '#FF7878')
			}
		} else {
			//everything worked! clear the input form
			$(':input', '#addtrackform').not(':submit').val('');
			//increment the episode and fade out
			$('span.episodenumber').html(json_data.episode);
			$('title').html('A State of Cornell Trance' + json_data.episode);
			$('span.episodenumber').addClass("episodehighlight");	
			setTimeout(function() {
				$('div#addresponse').fadeOut();
				$('span.episodenumber').removeClass("episodehighlight");
			}, 3000);
			//display the object in the console to debug AJAX submission
			console.log(json_data);
			//append the new track to the list manually (live-update)
			$('ol#tracklist').append('<li><span class="artist">' + json_data.newartist + '</span><span class="title">' + json_data.newtitle + '</span><span class="soundcloud hidden">' + json_data.newsoundcloudurl + '</span><span class="genre">' + json_data.newgenre + '</span><span class="length">' + json_data.newlength + '</span></li>');
					
		}
	}});
	//make sure the page doesn't refresh or go to addtrack.php
	return false;
});

//"what is this" soundcloud id click handler to explain how to get an ID
$('span.whatisthis').click(function() {
	$('div#addresponse').html("<p class=\"directionheader\">How to get a track's unique SoundCloud ID:</p><ol><li>Click the \"Share\" button below an embeddable track on Soundcloud and get the code for the SoundCloud widget.</li>\n<li>Find the part of the widget code that begins with <strong>src=\"</strong> and has a bunch of stuff after it.</li>\n<li>In that \"stuff\", the SoundCloud track ID is the string of numbers AFTER <strong>tracks%2F</strong> and BEFORE <strong>\"></strong> in the widget code, and should be all numbers.</li>\n</ol>");
});

//call the fetch_tracks function with no parameters to display all the tracks on page load (equivalent to an empty search)
fetch_tracks(null);

});