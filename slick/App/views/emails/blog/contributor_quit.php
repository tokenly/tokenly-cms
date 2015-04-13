<?php
$site = currentSite();
$data['quitter']['username'] = '<a href="'.$site['url'].'/profile/user/'.$data['quitter']['slug'].'" target="_blank">'.$data['quitter']['username'].'</a>';
?>
<p>
	<?= $data['quitter']['username'] ?> is no longer a contributor on
	the blog article <strong><a href="<?= $site['url'] ?>/dashboard/blog/submissions/edit/<?= $data['post']['postId'] ?>" target="_blank"><?= $data['post']['title'] ?></a></strong>.
</p>
