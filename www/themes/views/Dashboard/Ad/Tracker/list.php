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
	?>
	<table class="admin-table data-table mobile-table submissions-table tracking-table">
		<thead>
			<tr>
				<th>ID</th>
				<th>URL</th>
				<th>Clicks</th>
				<th>Unique Clicks</th>
				<th>Impressions</th>
				<th class="no-sort"></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($urls as $item){
		?>
			<tr>
				<td><?= $item['urlId'] ?></td>
				<td class="url-field"><a href="<?= $item['url'] ?>" target="_blank"><?= $item['url'] ?></a></td>
				<td><?= number_format($item['clicks']) ?></td>
				<td><?= number_format($item['unique_clicks']) ?></td>
				<td><?= number_format($item['impressions']) ?></td>
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
