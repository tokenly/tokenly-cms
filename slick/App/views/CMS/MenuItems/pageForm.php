<h2><?= $formType ?> Menu Item: Page</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?php
if(isset($error) AND $error != null){
	echo '<p class="error">'.$error.'</p>';
}
?>
<?= $form->display() ?>
<script type="text/javascript">
	
function updateParentList()
{
	var menuId = $('select[name="menuId"]').val();
	$('select[name="parentId"]').find('option').each(function(){
		var splitId = $(this).attr('value').split('-');
		if(splitId[0] != 0 && splitId[0] != menuId){
			$(this).hide();
		}
		else{
			$(this).show();
		}
		
	});
	
	
}
	
	$(document).ready(function(){
		updateParentList();
		$('select[name="menuId"]').change(function(){
			updateParentList();
		});
		$('#pageId').change(function(e){

			$(this).find('option').each(function(){
				if($(this).is(':selected')){
					$('#label').val($(this).html());
				}
			});

			
		});
		
	});
</script>
