<?php
$site = currentSite();
$data['accept_user']['username'] = '<a href="'.$site['url'].'/profile/user/'.$data['accept_user']['slug'].'" target="_blank">'.$data['accept_user']['username'].'</a>';
?>

<p>
	<?= $data['accept_user']['username'] ?> has declined your request to become a contributor on 
	the blog article <strong><a href="<?= $site['url'] ?>/dashboard/blog/submissions/edit/<?= $data['itemId'] ?>" target="_blank"><?= $data['info']['post_title'] ?></a></strong>.
</p>

