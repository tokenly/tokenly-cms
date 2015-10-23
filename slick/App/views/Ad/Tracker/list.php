<div class="pull-right blog-submit-actions">
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add" class="btn btn-large">New URL</a>
</div>
<h2>Advertisement URL Tracker</h2>
<p>
	Create tracking URLs and use them to view various stats such as clicks etc.
</p>
<?= $this->displayFlash('message') ?>
<?php
if(count($urls) == 0){
	echo '<p>No tracking URLs created.</p>';
}
else{
	$total_clicks = 0;
	$total_impressions = 0;
	$total_ctr = 0;
	foreach($urls as $k => $item){
		$total_clicks += $item['clicks'];
		$total_impressions += $item['impressions'];
		$ctr = 0;
		if($item['impressions'] > 0){
			$ctr = ($item['clicks'] / $item['impressions']);
			$total_ctr +=  $ctr * 100;
		}
		$urls[$k]['ctr'] = $ctr;
	}
	$average_ctr = 0;
	if(count($urls) > 0){
		$average_ctr = round($total_ctr / count($urls), 2);
	}
	?>
	<ul>
		<li><strong># Tracking URLs:</strong> <?= number_format(count($urls)) ?></li>
		<li><strong>Total Clicks:</strong> <?= number_format($total_clicks) ?></li>
		<li><strong>Total Ad Impressions:</strong> <?= number_format($total_impressions) ?></li>
		<li><strong>Average CTR:</strong> <?= $average_ctr ?>%</li>
	</ul>
	<table class="admin-table data-table mobile-table submissions-table tracking-stats-table">
		<thead>
			<tr>
				<th>ID</th>
				<th>URL</th>
				<th>Clicks</th>
				<th>Unique Clicks</th>
				<th>Impressions</th>
				<th title="Click-Thru Rate">CTR</th>
				<th>Created At</th>
				<th class="no-sort"></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($urls as $item){
			$start_date = strtotime($item['created_at']);
			$end_date = false;
			if($item['last_click'] != NULL AND $item['last_click'] != '0000-00-00 00:00:00'){
				$end_date = strtotime($item['last_click']);
			}
			$days = 0;
			if($end_date){
				$days = round(($end_date - $start_date) / 86400, 2);
			}
			?>
			<tr>
				<td>
					<?= $item['urlId'] ?></td>
				<td class="url-field">
					<?php
					if(trim($item['label']) != ''){
						echo '<strong>'.$item['label'].'</strong><Br>';
					}
					?>
					<small><a href="<?= $item['url'] ?>" target="_blank"><?= $item['url'] ?></a></small>
				</td>
				<td><?= number_format($item['clicks']) ?></td>
				<td><?= number_format($item['unique_clicks']) ?></td>
				<td><?= number_format($item['impressions']) ?></td>
				<td><?= number_format($item['ctr'], 2) ?>%</td>
				<td>
					<?= date('Y/m/d', $start_date) ?>
				</td>
				<td class="table-actions">
					<a href="<?= SITE_URL.'/'.$app['url'].'/'.$module['url'].'/view/'.$item['urlId'] ?>">View Stats</a>
					<a href="<?= SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$item['urlId'] ?>">Edit</a>
					<a href="<?= SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$item['urlId'] ?>" class="delete">Delete</a>
				</td>
			</tr>
		<?php
		}//endforeach
		?>
		</tbody>
	</table>
	<link href="//cdn.datatables.net/1.10.3/css/jquery.dataTables.css" rel="stylesheet" />
	<script type="text/javascript" src="//cdn.datatables.net/1.10.3/js/jquery.dataTables.min.js"></script>
	<script type="text/javascript">
		$(document).ready(function(){
			$('.data-table').DataTable({
				searching: true,
				lengthChange: false,
				paging: true,
				iDisplayLength: 20,
				"order": [[ 0, "desc" ]]
			});	
		});
	</script>
	<?php
}

?>
