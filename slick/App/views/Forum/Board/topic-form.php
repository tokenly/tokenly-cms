<?php
$formTitle = 'New Topic';
$backLink = SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$board['slug'];
if(isset($mode) AND $mode == 'edit'){
	$formTitle = 'Edit Thread - '.$topic['title'];
	$backLink = SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$topic['url'];
}
?>
<h1 class="large"><?= $app['meta']['forum-title'] ?></h1>
<hr>
<h2><?= $formTitle ?> - <?= $board['name'] ?></h2>
<p>
	<a href="<?= $backLink ?>"><i class="fa fa-mail-reply"></i> Go Back</a>
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
	echo '<p><em>Use <strong>markdown</strong> formatting for post. See <a href="#" class="markdown-trigger" target="_blank">formatting guide</a>
				for more information.</em></p>
			<div style="display: none;" id="markdown-guide">
			'.$this->displayBlock('markdown-guide').'
			</div>
			';
				
?>
