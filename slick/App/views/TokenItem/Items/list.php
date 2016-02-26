<div class="pull-right blog-submit-actions">
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add" class="btn btn-large">New Token Item</a>
</div>
<h2>Token Items</h2>
<?= $this->displayFlash('message') ?>
<?php
if(count($token_items) == 0){
	echo '<p>No Token Items created.</p>';
}
else{
	?>

	<table class="admin-table data-table mobile-table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Token</th>
				<th>Min Token</th>
				<th>Active</th>
				<th>Last Updated</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($token_items as $item){
			?>
			<tr>
				<td><strong><?= $item['name'] ?></strong></td>
				<td><?= $item['token'] ?></td>
				<td><?= convertFloat(round($item['min_token'] / SATOSHI_MOD, 8)) ?></td>
				<td><?= boolToText($item['active']) ?></td>
				<td><?= formatDate($item['updated_at']) ?></td>
				<td>
					<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $item['id'] ?>" class="btn btn-small btn-blue">Edit</a>
					<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/delete/<?= $item['id'] ?>" class="btn btn-small delete btn-blue">Delete</a>
				</td>
			</tr>
		<?php
		}//endforeach
		?>
		</tbody>
	</table>
	<?php
}
