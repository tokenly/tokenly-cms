<h2>App Settings</h2>
<hr>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<?php
echo $this->displayFlash('message');
$form = new \UI\Form;
echo $form->open();
foreach($apps as $appSettings){
	if(count($appSettings['settings']) == 0){
		continue;
	}
	echo '<h3><a href="#" data-app="'.$appSettings['appId'].'" class="appExpand">'.$appSettings['name'].' <i class="fa fa-plus-circle"></i></a></h3>';
	echo '<div id="settings-'.$appSettings['appId'].'" style="display: none;">';
	echo $appSettings['form']->displayFields();
	echo '</div>';
	
}
echo $form->displaySubmit();
echo $form->close();
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.appExpand').click(function(e){
			e.preventDefault();
			var app = $(this).data('app');
			if($(this).hasClass('collapse')){
				$('#settings-' + app).slideUp();
				$(this).removeClass('collapse');
				$(this).find('i').removeClass('fa-minus-circle').addClass('fa-plus-circle');	
			}
			else{
				$('#settings-' + app).slideDown();
				$(this).addClass('collapse');
				$(this).find('i').removeClass('fa-plus-circle').addClass('fa-minus-circle');
			}
		});
		
	});

</script>
