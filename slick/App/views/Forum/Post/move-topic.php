<?php

$formTitle = 'Move Thread - '.$topic['title'];
$backLink = SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$topic['url'];

?>
<h1 class="large"><?= $app['meta']['forum-title'] ?></h1>
<hr>
<h2><?= $formTitle ?></h2>
<p>
	<a href="<?= $backLink ?>"><i class="fa fa-mail-reply"></i> Go Back</a>
</p>
<p>
	Choose a board to move this thread to.
</p>
<?php
if($message != ''){
	echo '<p class="error">'.$message.'</p>';
}
?>
<div class="post-form">
	<?= $form->display() ?>
</div>
