<?php

$formTitle = 'Edit Post - '.$topic['title'];
$backLink = SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$topic['url'];

?>
<h1><?= $formTitle ?></h1>
<hr>
<p>
	<a href="<?= $backLink ?>">Go Back</a>
</p>
<?php
if($message != ''){
	echo '<p class="error">'.$message.'</p>';
}

?>
<div class="post-form">
	<?= $form->display() ?>
	
</div>
<?php
	echo '<p><em>Use <strong>markdown</strong> formatting for post. See <a href="#" class="markdown-trigger" target="_blank">formatting guide</a>
				for more information.</em></p>
			<div style="display: none;" id="markdown-guide">
			'.$this->displayBlock('markdown-guide').'
			</div>
			';
				
?>
