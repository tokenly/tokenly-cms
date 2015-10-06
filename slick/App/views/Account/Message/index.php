<p class="pull-right">
	<strong><a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/send" class="btn">Send New Message</a></strong>
</p>

<h2><i class="fa fa-envelope"></i> Private Messages</h2>
<?php
$tca = new \App\Tokenly\TCA_Model;
$profileModule = $tca->get('modules', 'user-profile', array(), 'slug');
echo '<h3>'.ucfirst($pmbox).'</h3>';
if($pmbox == 'inbox'){
	echo '<p><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/sent">View Sent Messages (Outbox)</a></p>';
}
elseif($pmbox == 'outbox'){
	echo '<p><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'">View Received Messages (Inbox)</a></p>';
}
?>
<?php
if(count($messages) == 0){
	echo '<p><em>No messages found</em></p>';
}
else{
	echo '<table class="admin-table mobile-table">
			<thead>
				<tr>
					<th>Subject</th>
					<th>From</th>
					<th>To</th>
					<th>Date/Time</th>
				</tr>
			</thead>
			<tbody>';
			
	foreach($messages as $message){
		if(trim($message['subject']) == ''){
			$message['subject'] = '(no subject)';
		}
		
		$toTCA = true;
		if($message['to']['userId'] != $user['userId']){
			$toTCA = $tca->checkItemAccess($user['userId'], $profileModule['moduleId'], $message['to']['userId'], 'user-profile');
		}
		$fromTCA = true;
		if($message['from']['userId'] != $user['userId']){
			$fromTCA = $tca->checkItemAccess($user['userId'], $profileModule['moduleId'], $message['from']['userId'], 'user-profile');
		}
				
		$avImage = $message['from']['avatar'];
		if(trim($message['from']['real_avatar']) == ''){
			$avImage = SITE_URL.'/files/avatars/default.jpg';
		}
		else{
			if(!isExternalLink($message['from']['avatar'])){
				$avImage = SITE_URL.'/files/avatars/'.$message['from']['avatar'];
			}
		}
		$avImage = '<img src="'.$avImage.'" alt="" />';
		if($fromTCA){
			$avImage = '<a href="'.SITE_URL.'/profile/user/'.$message['from']['slug'].'" target="_blank">'.$avImage.'</a>';
		}
		$avatar = '<span class="mini-avatar">'.$avImage.'</span>';
				
		$avImage2 = $message['to']['avatar'];
		if(trim($message['to']['real_avatar']) == ''){
			$avImage2 = SITE_URL.'/files/avatars/default.jpg';
		}
		else{		
			if(!isExternalLink($message['to']['avatar'])){
				$avImage2 = SITE_URL.'/files/avatars/'.$message['to']['avatar'];
			}
		}
		$avImage2 = '<img src="'.$avImage2.'" alt="" />';
		if($toTCA){
			$avImage2 = '<a href="'.SITE_URL.'/profile/user/'.$message['to']['slug'].'" target="_blank">'.$avImage2.'</a>';
		}
		$avatar2 = '<span class="mini-avatar">'.$avImage2.'</span>';
		
		if($message['isRead'] == 0 AND $message['userId'] != $user['userId']){
			$message['subject'] = '<strong class="unread">'.$message['subject'].'</strong>';
		}
		
		if($message['hasReplied']){
			$message['subject'] .= ' <i class="fa fa-mail-reply" title="replied"></i>';
		}
		
		$fromDisplayName = $message['from']['username'];
		$toDisplayName = $message['to']['username'];
		if($fromTCA){
			$fromDisplayName = '<a href="'.SITE_URL.'/profile/user/'.$message['from']['slug'].'" target="_blank">'.$fromDisplayName.'</a>';
		}
		if($toTCA){
			$toDisplayName = '<a href="'.SITE_URL.'/profile/user/'.$message['to']['slug'].'" target="_blank">'.$toDisplayName.'</a>';
		}
		echo '<tr>
				<td>
					<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/view/'.$message['messageId'].'#message-'.$message['messageId'].'" title="'.str_replace('"', "'", shortenMsg($message['message'], 50)).'">'.$message['subject'].'</a>
				</td>
				<td>'.$avatar.'
					'.$fromDisplayName.'
				</td>
				<td>'.$avatar2.'
					'.$toDisplayName.'
				</td>
				<td>
					'.formatDate($message['sendDate']).'
				</td>
			  </tr>';
	}
	
	echo '</tbody></table>';
	
	if($numPages > 1){
		echo '<div class="pm-paging paging">
				<strong>Pages:</strong>';
		$thisBox = '';
		if($pmbox == 'outbox'){
			$thisBox = 'sent';
		}
		
		for($i = 1; $i <= $numPages; $i++){
			$active = '';
			if((isset($_GET['page']) AND $_GET['page'] == $i) OR (!isset($_GET['page']) AND $i == 1)){
				$active = 'active';
			}
			
			echo '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$thisBox.'?page='.$i.'" class="'.$active.'">'.$i.'</a> ';
		}
		echo '</div>';
	}
}
?>
