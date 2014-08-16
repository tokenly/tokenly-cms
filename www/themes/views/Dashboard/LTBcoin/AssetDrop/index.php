<h2>XCP Asset Dropper</h2>
<p>
	Use the tool below to quickly create a digital asset drop (distribution) to all system users (or specific groups) with registered addresses. Amounts
	are divided equally among all addresses. 
</p>
<?php
if($error != ''){
	echo '<p class="error">'.$error.'</p>';
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
