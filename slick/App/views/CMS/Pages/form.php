<h2><?= $formType ?> Page</h2>
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
	$(document).ready(function(){
		<?php
		if(isset($thisPage) AND $thisPage['formatType'] == 'wysiwyg'){
		?>
		$('select[name="formatType"]').change(function(e){
			var thisVal = $(this).val();
			if(thisVal == 'markdown'){
				var check = confirm('Warning: Switching to the markdown editor may erase the current page content. Are you sure you want to continue? Save/Submit to complete change.');
				if(check == null || check == false){
					$(this).val('wysiwyg');
					e.preventDefault();
				}
			}
		});
		<?php
		}//endif
		?>
	});
</script>
