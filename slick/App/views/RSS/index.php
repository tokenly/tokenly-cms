
<?= $this->displayBlock('rss-content') ?>

<div class="custom-feed">
	<?= $form->displayFields() ?>
	<input type="button" id="gen-link" value="Generate RSS Link" />
</div>
<div class="custom-result"></div>
<script type="text/javascript">
	$(document).ready(function(){
		$('.choose-sites').find('input[type="checkbox"]').click(function(e){
			var siteId = $(this).val();
			var thisCheck = $(this);
			
			$('.site-cats').each(function(){
				var thisSite = $(this).data('siteid');
				if(thisSite == siteId){
					if(!$(this).hasClass('collapse') && thisCheck.is(':checked')){
						$(this).addClass('collapse');
						$(this).slideDown();
					}
					else{
						$(this).removeClass('collapse');
						$(this).slideUp();
					}
				}
				
			});
			
		});
		
		$('#gen-link').click(function(e){
			var sites = [];
			$('.choose-sites').find('input[type="checkbox"]').each(function(){
				if($(this).is(':checked')){
					sites.push($(this).val());
				}
				
			});
			
			var cats = [];
			$('.site-cats').find('input[type="checkbox"]').each(function(){
				if($(this).is(':checked')){
					if($(this).val() > 0){
						cats.push($(this).val());
					}
				}
				
			});
			
			var numItems = $('input[name="numItems"]').val();
			var andAudio = $('select[name="audio"]').val();
			
			
			var url = '<?= SITE_URL ?>/<?= $app['url'] ?>/feed/blog?limit=' + numItems;
			if(andAudio == 1){
				url = url + '&soundcloud-id=true&audio-url=true';
			}
			else if(andAudio == 2){
				url = url + '&soundcloud-id=false&audio-url=false';
			}
			
			if(sites.length > 0){
				url = url + '&sites=' + sites.join();
			}
			if(cats.length > 0){
				url = url + '&categories=' + cats.join();
			}
			
			$('.custom-result').html('<h4>Your RSS Link: <br><a href="' + url + '" target="_blank">' + url + '</a></h4>');

		});
		
		$('input[name="numItems"]').keydown(function (e) {
			// Allow: backspace, delete, tab, escape, enter and .
			if ($.inArray(e.keyCode, [46, 8, 9, 27, 13, 110, 190]) !== -1 ||
				 // Allow: Ctrl+A
				(e.keyCode == 65 && e.ctrlKey === true) || 
				 // Allow: home, end, left, right
				(e.keyCode >= 35 && e.keyCode <= 39)) {
					 // let it happen, don't do anything
					 return;
			}
			// Ensure that it is a number and stop the keypress
			if ((e.shiftKey || (e.keyCode < 48 || e.keyCode > 57)) && (e.keyCode < 96 || e.keyCode > 105)) {
				e.preventDefault();
			}
		});
		
		$('input[name="numItems"]').change(function(e){
			if($(this).val() == ''){
				$(this).val(15);
			}
			
		});
		
		$('.site-cats').find('input[type="checkbox"]').click(function(e){
			var thisCat = $(this).val();
			
			if(thisCat == 0){
				if($(this).is(':checked')){
					$(this).parent().find('input[type="checkbox"]').attr('checked', 'checked');
				}
				else{
					$(this).parent().find('input[type="checkbox"]').removeAttr('checked', 'checked');
				}
				
			}
			else{
				$(this).parent().find('input[value="0"][type="checkbox"]').removeAttr('checked');
			}
			
		});
		
	});
</script>
