<?php
$site = currentSite();
$data['culprit']['username'] = '<a href="'.$site['url'].'/profile/user/'.$data['culprit']['slug'].'" target="_blank">'.$data['culprit']['username'].'</a>';
$status = $data['new_status'];
switch($status){
	case 'published':
		$status = 'Finished';
		break;
	case 'ready':
		$status = 'Ready for Review';
		break;
	case 'draft':
		$status = 'Draft';
		break;
}
?>
<p>
	<?= $data['culprit']['username'] ?> changed the status of 
	the blog article <strong><a href="<?= $site['url'] ?>/dashboard/blog/submissions/edit/<?= $data['post']['postId'] ?>" target="_blank"><?= $data['post']['title'] ?></a></strong>
	to <strong><?= $status ?></strong>
</p>
