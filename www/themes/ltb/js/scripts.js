$(document).ready(function(){
	var pageTitle = document.title;
	//$('.mobile-table').mobilizeTables();
	$('.mobile-table').mobilizeTables();
	
	
	$('body').delegate('.delete', 'click', function(e){
		var check = confirm('Are you sure you want to delete?');
		if(!check || check == null){
			e.preventDefault();
			return false;
		}
	});
	
	if($('#html-editor').length > 0){
		CKEDITOR.replace( 'html-editor' );
	}
	if($('#mini-editor').length > 0){	
		CKEDITOR.replace( 'mini-editor' );
	}
	
	$('.header-top .menu').find('li').hover(function(){
		$(this).find('ul').show();
	},function(){
		$(this).find('ul').hide();
	});
	
	$('.header-top .notify-pull').click(function(e){
		e.preventDefault();
		if($(this).hasClass('collapse')){
			$('.notify-list').hide();
			$(this).removeClass('collapse');
		}
		else{
			$('.notify-list').show();
			$(this).addClass('collapse');
			var url = window.siteURL + '/account/notifications/setread';
			$.get(url, function(data){
				$('.notify-pull').html(0);
				$('.notifications').removeClass('has-notes');
				document.title = pageTitle;
			});
		}
	});
	
	
	$('.fancy').fancybox();
	
	$('.markdown-trigger').click(function(e){
		if($(this).hasClass('collapse')){
			$('#markdown-guide').slideUp();
			$(this).removeClass('collapse');
		}
		else{
			$('#markdown-guide').slideDown();
			$(this).addClass('collapse');
		}
		e.preventDefault();

	});

	if(window.userLogged){
		setInterval(function(){
			var url = window.siteURL + '/account/notifications/check';
			$.get(url, function(data){
				var curNotes = parseInt($('.notify-pull').html());
				if(data.notes.length > curNotes){
					var noteTitleText = 'notification';
					if(data.notes.length > 1){
						noteTitleText = noteTitleText + 's';
					}
					document.title = '(' + data.notes.length + ') ' + noteTitleText + ' - ' + pageTitle;
					$('.notify-pull').html(data.notes.length);
					$('.notifications').addClass('has-notes');
					var noteHTML = $('.notify-list').html();
					$.each(data.notes, function(idx,val){
						noteHTML = '<li><div class="note-text">' + val.message + '</div><div class="note-date">' + val.formatDate + '</div></li>' + noteHTML;
						
					});
					$('.notify-list').html(noteHTML);
				}
				if(data.notes.length < curNotes){
					$('.notify-pull').html(0);
					$('.notifications').removeClass('has-notes');
					document.title = pageTitle;
				}
			});
		},60000);
	}
	$('.dash-pull').click(function(){
		if($(this).hasClass('collapse')){
			$('.mobile-dash-menu').slideUp();
			$(this).removeClass('collapse');
		}
		else{
			$('.mobile-dash-menu').slideDown();
			$(this).addClass('collapse');
		}
		e.preventDefault();
	});
	$('.forum-mobile-pull').click(function(){
		if($(this).hasClass('collapse')){
			$('.forum-mobile-menu').slideUp();
			$(this).removeClass('collapse');
		}
		else{
			$('.forum-mobile-menu').slideDown();
			$(this).addClass('collapse');
		}
		e.preventDefault();
	});
	$('.mobile-header .menu-pull').click(function(){
		if($(this).hasClass('collapse')){
			$('.mobile-nav').slideUp();
			$(this).removeClass('collapse');
		}
		else{
			$('.mobile-nav').slideDown();
			$(this).addClass('collapse');
		}
		e.preventDefault();
	});
	$('.blog-pull').click(function(){
		if($(this).hasClass('collapse')){
			$('.blog-mobile-nav').slideUp();
			$(this).removeClass('collapse');
		}
		else{
			$('.blog-mobile-nav').slideDown();
			$(this).addClass('collapse');
		}
		e.preventDefault();
	});



	$('.media-player').find('.pop-out').click(function(e){
		var newWindow = window.open('', '', 'width=350, height=100,resizable=no,scrollbars=no,menubar=no,toolbar=no');
		var playerHTML = $(this).parent().parent().html();
		$('.media-player-holder').jPlayer("pause");
		var curTrackTime = $('.media-player-holder').data('jPlayer').status.currentTime;
		var trackPercent = ((curTrackTime / window.curTrackDuration) * 100).toFixed(2);

		newWindow.startPercent = trackPercent;
		newWindow.isPlayer = true;
		newWindow.headerMedia = window.headerMedia;
		newWindow.currentTrack = window.currentTrack;
		newWindow.currentTrackTime = curTrackTime;
		
		newWindow.document.open();
		newWindow.document.write('<html><head><title>Lets Talk Bitcoin Media Player</title>');
		newWindow.document.write('<link rel="stylesheet" type="text/css" href="' + window.siteURL + '/themes/ltb/css/base.css"><link type="text/css" rel="stylesheet" href="' + window.siteURL + '/themes/ltb/css/layout.css">');
		newWindow.document.write('<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">');
		newWindow.document.write('<script type="text/javascript" src="' + window.siteURL + '/themes/ltb/js/jquery.js"></script>');
		newWindow.document.write('<script type="text/javascript" src="' + window.siteURL + '/themes/ltb/js/jquery.jplayer.min.js"></script>');
		newWindow.document.write('<script type="text/javascript" src="' + window.siteURL + '/themes/ltb/js/player.js"></script>');
		newWindow.document.write('</head><body class="pop-out-body">');
		newWindow.document.write('<div class="logo"><a href="#"></a></div>');
		newWindow.document.write('<div class="pop-out-player"><div class="media-player-cont"><div class="media-player-holder"></div><div class="media-player">' + playerHTML + '</div></div></div>');
		newWindow.document.write('</body></html>');
		newWindow.document.close();
	});
});
