<h2>Manage Adspaces</h2>
<p class="pull-right">
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add" class="btn">New Adspace</a>
</p>
<div class="clear"></div>
<?= $this->displayFlash('message') ?>
<?php
if(!$adspaces OR count($adspaces) == 0){
	echo '<p>No adspaces created</p>';
}
else{
	?>
	<table class="admin-table mobile-table">
		<thead>
			<tr>
				<th>ID</th>
				<th>Label</th>
				<th>Slug</th>
				<th>Dimensions</th>
				<th>Active</th>
				<th>Last Updated</th>
				<th></th>
			</tr>
		</thead>
		<tbody>
		<?php
		foreach($adspaces as $adspace){
			?>
			<tr>
				<td><?= $adspace['adspaceId'] ?></td>
				<td><?= $adspace['label'] ?></td>
				<td><?= $adspace['slug'] ?></td>
				<td><?= $adspace['width'] ?>x<?= $adspace['height'] ?></td>
				<td><?= boolToText($adspace['active']) ?></td>
				<td><?= formatDate($adspace['updated_at']) ?></td>
				<td>
					<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $adspace['adspaceId'] ?>" class="btn btn-small btn-blue"><i class="fa fa-pencil"></i> Manage</a>
					<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/delete/<?= $adspace['adspaceId'] ?>" class="btn btn-small btn-blue delete"><i class="fa fa-close"></i> Delete</a>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
	</table>
	<?php
}
?>
