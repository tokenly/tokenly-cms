<h2>Notification Pusher</h2>
<p>
	Use this form to manually send out notifications to specific user groups (or all users). Content editor is disabled
	for these messages (to avoid breaking the design), but basic HTML may be used for links etc. 
</p>

<?php
if($error != ''){
	echo '<p class="error">'.$error.'</p>';
}
elseif($success != ''){
	echo '<p><strong>'.$success.'</strong></p>';
}
?>
<?= $form->display() ?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input[name="groups[]"]').click(function(e){
			var thisVal = $(this).val();
			if(thisVal == 0 && $(this).is(':checked')){
				$('input[name="groups[]"]').each(function(){
					if($(this).val() != thisVal){
						$(this).removeAttr('checked');
					}
				});
			}
			else{
				$('input[name="groups[]"]').each(function(){
					if($(this).val() == 0){
						$(this).removeAttr('checked');
					}
				});
			}
			
		});
		
	});
</script>
