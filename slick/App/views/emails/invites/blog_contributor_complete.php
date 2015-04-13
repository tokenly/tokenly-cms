<?php
$site = currentSite();
$data['main_user']['username'] = '<a href="'.$site['url'].'/profile/user/'.$data['main_user']['slug'].'" target="_blank">'.$data['main_user']['username'].'</a>';
?>
<p>
	<?= $data['main_user']['username'] ?> has joined as a contributor on
	the blog article <strong><a href="<?= $site['url'] ?>/dashboard/blog/submissions/edit/<?= $data['itemId'] ?>" target="_blank"><?= $data['info']['post_title'] ?></a></strong>.
</p>
