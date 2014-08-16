<?php

$formTitle = 'Move Thread - '.$topic['title'];
$backLink = SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$topic['url'];

?>
<h1><?= $formTitle ?></h1>
<hr>
<p>
	<a href="<?= $backLink ?>">Go Back</a>
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
