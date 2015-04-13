<?php
$site = currentSite();
$culprit = $data['user']['username'];
$culprit = '<a href="'.$site['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';
?>

<p>
	<?= $culprit ?> posted a new editorial comment on 
	the blog article <strong><a href="<?= $site['url'] ?>/dashboard/blog/submissions/edit/<?= $data['post']['postId'] ?>#comment-<?= $data['commentId'] ?>" target="_blank"><?= $data['post']['title'] ?></a></strong>.
</p>
