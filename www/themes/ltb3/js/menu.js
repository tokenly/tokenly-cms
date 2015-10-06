$(document).ready(function(){

	var oldWidth = $(window).width(); 
	var breakpoints = [989, 767, 479];
	$("a.mobile-pull").click(function(e){
		e.preventDefault();
		var w = $(window).width(); 
		if(!$("a.mobile-pull").data('visible')){
			
			//$(".outer-wrap").css('overflow','hidden');
			$("div.wrap").animate({left:-200});
			$('.slide-menuCont').animate({right:0});
			$("a.mobile-pull").data('visible',1);
			$(this).addClass('active');
			
			var menuHeight = $('.wrap').height() + 5;
			$('.slide-menuCont > .mobile-nav').css('height', menuHeight + 'px');
		}else{
			$("div.wrap").animate({left:0});
			$('.slide-menuCont').animate({right:-200});
			$("div.wrap").queue(function(){
				//$(".outer-wrap").css('overflow','visible');
				$(this).dequeue();
			});
			$("a.mobile-pull").data('visible',false);
			$(this).removeClass('active');
		}
	});
	$(window).resize(function(){  
			var w = $(window).width();  
			hitBreakpoint = false;
			for (i in breakpoints){
				if((w >= breakpoints[i] && oldWidth < breakpoints[i]) || (w <= breakpoints[i] && oldWidth > breakpoints[i])){
					hitBreakpoint = true;
				}
			}
			if(hitBreakpoint){
				$("div.wrap").removeAttr('style');  
				$('.slide-menuCont').css('right', '-200px');
				$("a.mobile-pull").data('visible',false);
			}
			oldWidth = w;
			

		});
});
