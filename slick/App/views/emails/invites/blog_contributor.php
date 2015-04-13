<?php
$site = currentSite();
$data['send_user']['username'] = '<a href="'.$site['url'].'/profile/user/'.$data['send_user']['slug'].'" target="_blank">'.$data['send_user']['username'].'</a>';
$invite_url = $site['url'].'/account/invite/'.$data['acceptCode'];
if($data['info']['request_type'] == 'invite'){
?>
	<p>
		<?= $data['send_user']['username'] ?> has invited you to become a contributor
		("<?= $data['info']['request_role'] ?>" with <?= $data['info']['request_share'] ?>% reward share)
		on the blog article <strong><a href="<?= $site['url'] ?>/dashboard/blog/submissions/edit/<?= $data['itemId'] ?>" target="_blank"><?= $data['info']['post_title'] ?></a></strong>.
		<a href="<?= $invite_url ?>" target="_blank">Click here</a> to accept or decline this request.
	</p>
<?php
}
elseif($data['info']['request_type'] == 'request'){
?>
	<p>
		<?= $data['send_user']['username'] ?> is requesting to become a contributor
		("<?= $data['info']['request_role'] ?>" with <?= $data['info']['request_share'] ?>% reward share)
		on the blog article <strong><a href="<?= $site['url'] ?>/dashboard/blog/submissions/edit/<?= $data['itemId'] ?>" target="_blank"><?= $data['info']['post_title'] ?></a></strong>.
		<a href="<?= $invite_url ?>" target="_blank">Click here</a> to accept or decline this request.
	</p>
<?php
}

