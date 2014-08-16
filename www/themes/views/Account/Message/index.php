<h2>Private Messages</h2>
<p>
	<strong><a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/send">Send New Message</a></strong>
</p>


<?php
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
		
		
		$avImage = $message['from']['avatar'];
		if(!isExternalLink($message['from']['avatar'])){
			$avImage = SITE_URL.'/files/avatars/'.$message['from']['avatar'];
		}
		$avatar = '<span class="mini-avatar"><a href="'.SITE_URL.'/profile/user/'.$message['from']['slug'].'" target="_blank"><img src="'.$avImage.'" alt="" /></a></span>';
				
		$avImage2 = $message['to']['avatar'];
		if(!isExternalLink($message['to']['avatar'])){
			$avImage2 = SITE_URL.'/files/avatars/'.$message['to']['avatar'];
		}
		$avatar2 = '<span class="mini-avatar"><a href="'.SITE_URL.'/profile/user/'.$message['to']['slug'].'" target="_blank"><img src="'.$avImage2.'" alt="" /></a></span>';
		
		if($message['isRead'] == 0 AND $message['userId'] != $user['userId']){
			$message['subject'] = '<strong class="unread">'.$message['subject'].'</strong>';
		}
		
		if($message['hasReplied']){
			$message['subject'] .= ' <i class="fa fa-mail-reply" title="replied"></i>';
		}
		
		echo '<tr>
				<td>
					<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/view/'.$message['messageId'].'#message-'.$message['messageId'].'" title="'.str_replace('"', "'", shortenMsg($message['message'], 50)).'">'.$message['subject'].'</a>
				</td>
				<td>'.$avatar.'
					<a href="'.SITE_URL.'/profile/user/'.$message['from']['slug'].'" target="_blank">'.$message['from']['username'].'</a>
				</td>
				<td>'.$avatar2.'
					<a href="'.SITE_URL.'/profile/user/'.$message['to']['slug'].'" target="_blank">'.$message['to']['username'].'</a>
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
