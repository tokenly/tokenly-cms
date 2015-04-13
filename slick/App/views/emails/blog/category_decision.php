<?php
$site = currentSite();
$culprit = $data['user']['username'];
$culprit = '<a href="'.$site['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';
?>

<p>
	<?= $culprit ?> updated the decisions for requested categories on
	the blog article <strong><a href="<?= $site['url'] ?>/dashboard/blog/submissions/edit/<?= $data['post']['postId'] ?>" target="_blank"><?= $data['post']['title'] ?></a></strong>.
	<?= join(', ', $data['cat_results']) ?>
</p>
