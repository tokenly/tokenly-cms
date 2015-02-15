$(document).ready(function(){

	if(typeof window.currentTrack == 'undefined'){
		window.currentTrack = 0;
	}
	
	$('.media-player-holder').jPlayer({
	ready: function (event) {
		$(this).jPlayer("setMedia", {
			mp3: window.headerMedia[0].stream
		});
	},
	 swfPath: '/themes/ltb/js',
	 supplied: 'mp3',
	wmode: "window",
	smoothPlayBar: true,
	loadeddata: function(e){
		window.curTrackDuration = e.jPlayer.status.duration;
	},
	 cssSelectorAncestor: '.media-player-cont',
	 cssSelector: {
		  videoPlay: '.jp-video-play',
		  play: '.jp-play',
		  pause: '.jp-pause',
		  stop: '.jp-stop',
		  seekBar: '.jp-seek-bar',
		  playBar: '.jp-play-bar',
		  mute: '.jp-mute',
		  unmute: '.jp-unmute',
		  volumeBar: '.jp-volume-bar',
		  volumeBarValue: '.jp-volume-bar-value',
		  volumeMax: '.jp-volume-max',
		  playbackRateBar: '.jp-playback-rate-bar',
		  playbackRateBarValue: '.jp-playback-rate-bar-value',
		  currentTime: '.jp-current-time',
		  duration: '.jp-duration',
		  fullScreen: '.jp-full-screen',
		  restoreScreen: '.jp-restore-screen',
		  repeat: '.jp-repeat',
		  repeatOff: '.jp-repeat-off',
		  gui: '.jp-gui',
		  noSolution: '.jp-no-solution',
	 },
	 errorAlerts: false,
	 warningAlerts: false,
	 preload: 'none'
	});
	
	$('.jp-play').click(function(e){
		$(this).hide();
		$('.jp-pause').show();
	});
	$('.jp-pause').click(function(e){
		$(this).hide();
		$('.jp-play').show();
	});
	$('.media-player .next').click(function(e){
		e.preventDefault();
		if($(this).hasClass('disabled')){
			return false;
		}
		
		var newTrack = window.currentTrack + 1;
		var nextTrack = newTrack + 1;
		
		if(nextTrack > (window.headerMedia.length)){
			newTrack = 0;
			nextTrack = 1;
			//$(this).addClass('disabled');
		}


		if(newTrack > 0){
			$('.media-player .prev').removeClass('disabled');
		}
	
		var thisTrack = window.headerMedia[newTrack];
		$('.media-player .track').html(thisTrack.title);
		$('.media-player-holder').jPlayer('setMedia', {
		  mp3: thisTrack.stream,
		}).jPlayer("play");
		window.currentTrack = newTrack;
	});
	$('.media-player .prev').click(function(e){
		e.preventDefault();
		if($(this).hasClass('disabled')){
			return false;
		}
		
		var newTrack = window.currentTrack - 1;
		var nextTrack = newTrack - 1;

		
		if(nextTrack < -1){
			newTrack = window.headerMedia.length - 1;
			nextTrack = window.headerMedia.length - 2;
		}

		if(newTrack < window.headerMedia.length){
			$('.media-player .next').removeClass('disabled');
		}
	
		var thisTrack = window.headerMedia[newTrack]
		$('.media-player .track').html(thisTrack.title);
		$('.media-player-holder').jPlayer('setMedia', {
		  mp3: thisTrack.stream
		}).jPlayer("play");
		window.currentTrack = newTrack;
	});
	
	
	setTimeout(function(){
		if(typeof window.isPlayer != 'undefined'){
			var thisTrack = window.headerMedia[window.currentTrack];
			$('.media-player .track').html(thisTrack.title);
			$('.media-player-holder').jPlayer('setMedia', {
			  mp3: thisTrack.stream
			}).jPlayer("play").jPlayer('playHead', window.startPercent);

		}
	}, 1000);
	
	
});
