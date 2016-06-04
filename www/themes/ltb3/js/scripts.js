function createCookie(name,value,days) {
    if (days) {
        var date = new Date();
        date.setTime(date.getTime()+(days*24*60*60*1000));
        var expires = "; expires="+date.toGMTString();
    }
    else var expires = "";
    document.cookie = name+"="+value+expires+"; path=/";
}

function readCookie(name) {
    var nameEQ = name + "=";
    var ca = document.cookie.split(';');
    for(var i=0;i < ca.length;i++) {
        var c = ca[i];
        while (c.charAt(0)==' ') c = c.substring(1,c.length);
        if (c.indexOf(nameEQ) == 0) return c.substring(nameEQ.length,c.length);
    }
    return null;
}
function eraseCookie(name) {
    createCookie(name,"",-1);
}

$(document).ready(function(){
    
    var client_timezone = readCookie('client_timezone');
    if(!client_timezone || client_timezone == null){
        var tz = jstz.determine();
        createCookie('client_timezone', tz.name(), 30);
    }
    
	var pageTitle = document.title;
	$('.mobile-table').mobilizeTables();
	
	
	$('body').delegate('.delete', 'click', function(e){
		var check = confirm('Are you sure you want to delete?');
		if(!check || check == null){
			e.preventDefault();
			return false;
		}
	});
	
	if(typeof CKEDITOR != 'undefined'){
		if($('#html-editor').length > 0){
			CKEDITOR.replace( 'html-editor' );
		}
		if($('#mini-editor').length > 0){	
			CKEDITOR.replace( 'mini-editor' );
		}
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
		var newWindow = window.open('', '', 'width=350, height=180,resizable=no,scrollbars=no,menubar=no,toolbar=no');
		var playerHTML = $('.top-featured').html();
		var menuHTML = $('.featured-menu-cont').html();
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
		newWindow.document.write('<link rel="stylesheet" type="text/css" href="' + window.siteURL + '/themes/ltb3/css/fonts.css">');
		newWindow.document.write('<link rel="stylesheet" type="text/css" href="' + window.siteURL + '/themes/ltb3/css/base.css">');
		newWindow.document.write('<link rel="stylesheet" type="text/css" href="' + window.siteURL + '/themes/ltb3/css/legacy.css">');
		newWindow.document.write('<link rel="stylesheet" type="text/css" href="' + window.siteURL + '/themes/ltb3/css/layout.css">');
		newWindow.document.write('<link rel="stylesheet" href="//netdna.bootstrapcdn.com/font-awesome/4.0.3/css/font-awesome.css">');
		newWindow.document.write('<script type="text/javascript" src="' + window.siteURL + '/themes/ltb3/js/jquery.js"></script>');
		newWindow.document.write('<script type="text/javascript" src="' + window.siteURL + '/themes/ltb3/js/jquery.jplayer.min.js"></script>');
		newWindow.document.write('<script type="text/javascript" src="' + window.siteURL + '/themes/ltb3/js/player.js"></script>');
		newWindow.document.write('</head><body class="pop-out-body">');
		newWindow.document.write('<div class="top-featured media-player-cont">' + playerHTML + '</div>');
		newWindow.document.write('<div class="featured-menu-cont">' + menuHTML + '</div>');
		//newWindow.document.write('<div class="logo"><a href="#"></a></div>');
		//newWindow.document.write('<div class="pop-out-player"><div class="media-player-cont"><div class="media-player-holder"></div><div class="media-player">' + playerHTML + '</div></div></div>');
		newWindow.document.write('</body></html>');
		newWindow.document.close();
		
		
	});
	
	
	$('.nav').find('li').hover(function(){
		$(this).children('.sub').show();
	
	}, function(){
		$(this).children('.sub').hide();
	});
	
	$('.side-menu').find('.children').children('i').click(function(e){
		e.preventDefault();
		if($(this).hasClass('collapse')){
			$(this).removeClass('collapse').removeClass('fa-caret-down').addClass('fa-caret-right');
			$(this).parent().find('.sub').slideUp();
		}
		else{
			$(this).addClass('collapse').removeClass('fa-caret-right').addClass('fa-caret-down');
			$(this).parent().find('.sub').slideDown();
		}
	});

	$('.list-switch').click(function(e){
		e.preventDefault();
		var type = $(this).data('switch');
		if(type == 'list'){
			$('.blog-list.grid').hide();
			$('.blog-list.list').show();
		}
		else if(type == 'grid'){
			$('.blog-list.list').hide();
			$('.blog-list.grid').show();
		}
		$.cookie('blog-list-type', type);
		$('.list-switch').removeClass('active');
		$(this).addClass('active');
	});
    
	jQuery('body').delegate(".numeric-only", "keydown", function(e) {
		// Allow: backspace, delete, tab, escape, enter and .
		if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
			 // Allow: Ctrl+A
			(e.keyCode == 65 && e.ctrlKey === true) || 
			 // Allow: home, end, left, right, down, up
			(e.keyCode >= 35 && e.keyCode <= 40)) {
				 // let it happen, don't do anything
				 return;
		}
		// Ensure that it is a number and stop the keypress
		if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
			e.preventDefault();
		}
	});	    
    
    function updateCreditPurchaseTotal(){
        var amount = $('#purchase-amount').val();
        var token = $('#payment-method').val();
        var price = $('#payment-method option[value="' + token + '"]').data('price');
        var total = (amount * price).toFixed(8).replace(/0+$/, '').replace(/\.+$/, '');
        $('#purchase-credit-total').html(total + ' ' + token);
        
    };
    
    $('#purchase-credit-form #purchase-amount,#purchase-credit-form #payment-method').change(function(e){
       updateCreditPurchaseTotal(); 
    });

    $('#purchase-credit-form #purchase-amount').keyup(function(e){
        updateCreditPurchaseTotal(); 
    });
    
	window.setTimeout(function(){
		
		var pocketsurl = $('.pockets-url').text(); //Pockets extension url
		var pocketsimage = $('.pockets-image-blue').text(); //Pockets icon
		if(pocketsurl != ''){
			createCookie('pockets-url-value', pocketsurl, 30);
			createCookie('pockets-icon-value', pocketsimage, 30);
		}
		
		$('.dynamic-payment-button').each(function(){
			var amount = $(this).data('amount');
			var address = $(this).data('address');
			var label = $(this).data('label');
			var tokens = $(this).data('tokens');
			
			if(pocketsurl == ''){
				pocketsurl = readCookie('pockets-url-value');
				pocketsimage = readCookie('pockets-icon-value');
			}
			if(!pocketsurl || pocketsurl == null || pocketsurl == ''){
				return false;
			}

			var label_encoded = encodeURIComponent(label).replace(/[!'()*]/g, escape); //URI encode label and remove special characters
			var urlattributes = "?address="+address+"&label="+label_encoded+"&tokens="+tokens+"&amount="+amount;
			$(this).html("<a href='"+pocketsurl+urlattributes+"' target='_blank'><img src='"+pocketsimage+"' width='100px'></a>");
		});
	}, 300);
    
    if($('#system-credits-payment').length > 0){
        window.setInterval(function(){
            var url = $('#check-payment-url').data('url');
            if(url){
                $.get(url, function(data){
                    if(data.complete){
                        $('#payment-status').attr('class', 'text-success').html('Payment complete! System credits have been added to your account.');
                    }
                    else{
                        if(data.receiving){
                            $('#payment-status').attr('class', 'text-info').html('Receiving transaction, waiting for confirmations');
                        }
                    }
                });
            }
            
        }, 5000);
        
    }
});
