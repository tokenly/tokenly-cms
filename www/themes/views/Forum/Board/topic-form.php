<?php
$formTitle = 'New Topic';
$backLink = SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$board['slug'];
if(isset($mode) AND $mode == 'edit'){
	$formTitle = 'Edit Thread - '.$topic['title'];
	$backLink = SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$topic['url'];
}
?>
<h1><?= $formTitle ?></h1>
<h2><?= $board['name'] ?></h2>
<hr>
<p>
	<a href="<?= $backLink ?>">Go Back</a>
</p>
<?php
if($message != ''){
	echo '<p class="error">'.$message.'</p>';
}

?>
<div class="topic-form">
	<?= $form->display() ?>
	
</div>
<?php
	echo '<div class="markdown-preview">
				<h4>Live Preview</h4>
				<div class="markdown-preview-cont">
				
				</div>
			</div>';
	echo '<p><em>Use <strong>markdown</strong> formatting for post. See <a href="#" class="markdown-trigger" target="_blank">formatting guide</a>
				for more information.</em></p>
			<div style="display: none;" id="markdown-guide">
			'.$this->displayBlock('markdown-guide').'
			</div>
			';
				
?>
<script type="text/javascript" src="<?= THEME_URL ?>/js/Markdown.Converter.js"></script>
<script type="text/javascript">
	$(document).ready(function(){

		
		$('#markdown').keydown(function(e){
			var thisVal = $(this).val();
			var converter = new Markdown.Converter();
			
			getMarkdown = converter.makeHtml(thisVal);
			$('.markdown-preview-cont').html(getMarkdown);
		});
		

		

		
	});
</script>
