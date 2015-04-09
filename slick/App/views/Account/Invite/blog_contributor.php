<h2>Blog Contributor Invitation</h2>
<?php
if($message != ''){
	echo '<p class="'.$message_class.'">'.$message.'</p>';
}
?>
<?php
$invite['send_user']['username'] = '<a href="'.$site['url'].'/profile/user/'.$invite['send_user']['slug'].'" target="_blank">'.$invite['send_user']['username'].'</a>';

if($invite['info']['request_type'] == 'invite'){
?>
	<p>
		<?= $invite['send_user']['username'] ?> has invited you to become a contributor
		("<?= $invite['info']['request_role'] ?>" with <?= $invite['info']['request_share'] ?>% reward share)
		on the blog article <strong><?= $invite['info']['post_title'] ?></strong>. Do you wish to accept or decline this request?
	</p>
<?php
}
elseif($invite['info']['request_type'] == 'request'){
?>
	<p>
		<?= $invite['send_user']['username'] ?> is requesting to become a contributor
		("<?= $invite['info']['request_role'] ?>" with <?= $invite['info']['request_share'] ?>% reward share)
		on the blog article <strong><?= $invite['info']['post_title'] ?></strong>. Do you wish to accept or decline this request?
	</p>
<?php
}

if($cancelled){
	echo '<p class="error">Request declined, you may now leave this page.</p>';
}
else{
	echo $form_html;
}
?>
