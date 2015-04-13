<?php
$site = currentSite();
$culprit = $data['user']['username'];
$culprit = '<a href="'.$site['url'].'/profile/user/'.$data['user']['slug'].'">'.$culprit.'</a>';


$message = 'A blog post by '.$culprit.' has 
			been marked as ready for review, please review: 
			<a href="'.$site['url'].'/dashboard/blog/submissions/edit/'.$data['post']['postId'].'" target="_blank">'.$data['post']['title'].'</a>';

echo $message;
