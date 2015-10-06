<?php
if($totalNotes > 0){
	echo '<p class="pull-right"><strong><a href="?clear=1" class="btn">Clear Notifications</a></strong></p>';
}
?>
<h2>Notifications</h2>
<?php

if($totalNotes == 0){
	echo '<p>No notifications</p>';
}
else{
	echo '<ul class="notification-list">';
	foreach($notes as $note){
		echo '<li>
				<div class="note-text">'.$note['message'].'</div>
				<div class="note-date">'.formatDate($note['noteDate']).'</div>
			   </li>';
	}
	
	echo '</ul>';
	
	if($numPages > 1){
		echo '<div class="note-pages paging">
				<strong>Pages:</strong> ';
	for($i = 1; $i <= $numPages; $i++){
		$active = '';
		if((isset($_GET['page']) AND $_GET['page'] == $i) OR (!isset($_GET['page']) AND $i == 1)){
			$active = 'active';
		}
		echo '<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/?page='.$i.'" class="'.$active.'">'.$i.'</a> ';
	}
		echo '</div>';
	}
	
}

?>
