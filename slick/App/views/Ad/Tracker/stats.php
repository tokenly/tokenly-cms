<h2>Tracking URL Stats (#<?= $tracking_url['urlId'] ?>)</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<hr>
<ul>
	<li><strong>Tracking URL:</strong> <a href="<?= SITE_URL ?>/ad/link/<?= $tracking_url['urlId'] ?>" target="_blank"><?= SITE_URL ?>/ad/link/<?= $tracking_url['urlId'] ?></a></li>
	<li><strong>Destination URL:</strong> <a href="<?= $tracking_url['url'] ?>" target="_blank"><?= $tracking_url['url'] ?></a></li>
	<li><strong>Created By:</strong> <a href="<?= SITE_URL ?>/profile/user/<?= $tracking_url['user']['slug'] ?>" target="_blank"><?= $tracking_url['user']['username'] ?></a></li>
	<li><strong>Created At:</strong> <?= formatDate($tracking_url['created_at']) ?></li>
	<li><strong>Active?:</strong> <?= boolToText($tracking_url['active']) ?></li>
	<li><strong>Impressions:</strong> <?= number_format($tracking_url['impressions']) ?></li>
	<li><strong>Clicks:</strong> <?= number_format($tracking_url['clicks']) ?></li>
	<li><strong>Unique Clicks:</strong> <?= number_format($tracking_url['unique_clicks']) ?></li>
	<li><strong>Last Click:</strong>
	<?php
	if($tracking_url['last_click'] != NULL AND $tracking_url['last_click'] != '0000-00-00 00:00:00'){
		echo formatDate($tracking_url['last_click']);
	}
	else{
		echo 'N/A';
	}
	?>
	</li>
</ul>
<h3>Unique Click Stats</h3>
<?php
if(count($clicks) == 0){
	echo '<p>No clicks yet!</p>';
}
else{
	echo '<table class="admin-table data-table submissions-table tracking-stats-table">
			<thead>
				<tr>
					<th>IP/User</th>
					<th>Reference URL</th>
					<th>Click Time</th>
				</tr>
			</thead>
			<tbody>';
			
	foreach($clicks as $click){
		$andUser = '';
		if($click['user']){
			$andUser = ' (<a href="'.SITE_URL.'/profile/user/'.$click['user']['slug'].'" target="_blank">'.$click['user']['username'].'</a>)';
		}
		
		echo '<tr>
				<td>'.$click['IP'].$andUser.'</td>
				<td class="url-field">'.$click['request_url'].'</td>
				<td>'.date('Y/m/d \<\b\r\> H:i', strtotime($click['click_time'])).'</td>
			  </tr>';
	}
	
	echo '</tbody></table>';
	?>
	<link href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.css" rel="stylesheet" />
	<script type="text/javascript" src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.data-table').DataTable({
				searching: true,
				lengthChange: false,
				paging: true,
				iDisplayLength: 20,
				"order": [[ 2, "desc" ]]
			});	
		});
	</script>
	<?php
}
?>
