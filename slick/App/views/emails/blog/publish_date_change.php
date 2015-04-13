<?php
$site = currentSite();
$data['culprit']['username'] = '<a href="'.$site['url'].'/profile/user/'.$data['culprit']['slug'].'" target="_blank">'.$data['culprit']['username'].'</a>';
$date = formatDate($data['new_date']);
?>
<p>
	<?= $data['culprit']['username'] ?> changed the publishing date
	on the blog article <strong><a href="<?= $site['url'] ?>/dashboard/blog/submissions/edit/<?= $data['post']['postId'] ?>" target="_blank"><?= $data['post']['title'] ?></a></strong>
	to <strong><?= $date ?></strong>
</p>
