<div class="pull-right blog-submit-actions">
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add" class="btn btn-large">New Property Type</a>
</div>
<h2>Token Item Property Types</h2>
<p>
	Here you can set up custom properties for your token items.
</p>
<?= $this->displayFlash('message') ?>
<?php
if(count($properties) == 0){
	echo '<p>No Property Types created.</p>';
}
else{
	?>

	<table class="admin-table data-table mobile-table">
		<thead>
			<tr>
				<th>Name</th>
				<th>Type</th>
				<th>Active</th>
				<th>Last Updated</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($properties as $item){
			?>
			<tr>
				<td><strong><?= $item['name'] ?></strong></td>
				<td><?= $item['type'] ?></td>
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
