<h2>Manage Adspace - <?= $adspace['label'] ?></h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>"><i class="fa fa-mail-reply"></i> Go back</a>
</p>
<?= $this->displayFlash('message') ?>
<a name="manage-ads"></a>
<h3>Manage Ads</h3>
<?php
$total_adspace_clicks = 0;
$total_adspace_impressions = 0;
foreach($adspace['orig_items'] as $item){
	if(isset($item['stats'])){
		$total_adspace_clicks += $item['stats']['clicks'];
		$total_adspace_impressions += $item['stats']['impressions'];
	}
}
$adspace_ctr = 0;
if($total_adspace_impressions > 0){
	$adspace_ctr = ($total_adspace_clicks / $total_adspace_impressions) * 100;
}
?>
<div class="pull-right">
	<ul>
		<li><strong>Total # Ads:</strong> <?= number_format($total_ads) ?></li>
		<li><strong>Active Ads:</strong> <?= number_format($active_ads) ?></li>
		<li><strong>Archived Ads:</strong> <?= number_format($archived_ads) ?></li>
		<li><strong>Total Clicks:</strong> <?= number_format($total_adspace_clicks) ?></li>
		<li><strong>Total Impressions:</strong> <?= number_format($total_adspace_impressions) ?></li>
		<li><strong title="Click-thru-rate">Average CTR:</strong> <?= number_format($adspace_ctr, 2) ?>%</li>
		<li><strong>Created:</strong> <?= formatDate($adspace['created_at']) ?></li>
		<li><strong>Last Updated:</strong> <?= formatDate($adspace['updated_at']) ?></li>
	</ul>
</div>
<div class="adspace-ad-form">
	<h4>Add Advertisement</h4>	
	<?= $ad_form->display('div') ?>
</div>
<small><br>
	<strong><?php
	if($show_archived){
		echo '<a href="?archive=0#manage-ads"><i class="fa fa-rocket"></i> Show unarchived ads</a>';
	}
	else{
		echo '<a href="?archive=1#manage-ads"><i class="fa fa-folder-open-o"></i> Show archived ads ('.number_format($archived_ads).')</a>';
	}
	?></strong>
</small>
<?php
if(is_array($adspace['items']) AND count($adspace['items']) > 0){
	$model = new \Core\Model;
	$time = time();
	?>
	<form action="" method="post">
		<table class="admin-table mobile-table">
			<thead>
				<tr>
					<th>Advertisement</th>
					<th>Start Date</th>
					<th>End Date</th>
					<th>Schedule Active*</th>
					<th>Clicks</th>
					<th>Impressions</th>
					<th title="Click-thru-rate">CTR</th>
					<th></th>
				</tr>
			</thead>
			<tbody class="sortable ui-sortable">
				<?php
				foreach($adspace['items'] as $k => $item){
					$getUrl = $model->get('tracking_urls', $item['urlId']);
					if(!$getUrl){
						continue;
					}
					$display_url = '[#'.$getUrl['urlId'].'] <a href="'.SITE_URL.'/'.$app['url'].'/ad-tracker/view/'.$item['urlId'].'" target="_blank">'.$getUrl['url'].'</a>';
					if(trim($getUrl['label']) != ''){
						$display_url = '<strong>'.$getUrl['label'].'</strong><br>'.$display_url;
					}
					?>
					<tr>
						<td><?= $display_url ?></td>
						<td><strong class="text-success"><?= date('F jS, Y H:i', $item['start_date']) ?></strong></td>
						<td><strong class="text-error"><?= date('F jS, Y H:i', $item['end_date']) ?></strong></td>
						<td>
						<?php
						if(isset($item['archived']) AND $item['archived'] == 1){
							echo '[archived]';
						}
						else{
							if($time >= $item['start_date'] AND $time <= $item['end_date']){
								echo '<strong class="text-success">Yes</strong>';
							}
							else{
								echo '<strong class="text-error">No</strong>';
							}
						}
						?>
						</td>
						<td>
							<?php
							if(isset($item['stats'])){
								echo number_format($item['stats']['clicks']);
							}
							else{
								echo '0';
							}
							?>
						</td>
						<td>
							<?php
							if(isset($item['stats'])){
								echo number_format($item['stats']['impressions']);
							}
							else{
								echo '0';
							}							
							?>						
						</td>
						<td>
							<?php
							if(isset($item['stats'])){
								$ctr = 0;
								if($item['stats']['impressions'] > 0){
									$ctr = ($item['stats']['clicks'] / $item['stats']['impressions']) * 100;
								}
								echo number_format($ctr, 2).'%';
							}
							else{
								echo '0%';
							}							
							?>						
						</td>
						<td>
							<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $adspace['adspaceId'] ?>/edit-ad/<?= $k ?>" class="btn btn-small btn-blue"><i class="fa fa-pencil"></i> Edit</a>
							<?php
							if(isset($item['archived']) AND $item['archived'] == 1){
								?>
							<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $adspace['adspaceId'] ?>/unarchive-ad/<?= $k ?>" class="btn btn-small btn-blue"><i class="fa fa-rocket"></i> Unarchive</a>
								<?php
							}
							else{
								?>
							<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $adspace['adspaceId'] ?>/archive-ad/<?= $k ?>" class="btn btn-small btn-blue"><i class="fa fa-folder-open-o"></i> Archive</a>
							<?php
							}
							?>
							<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/edit/<?= $adspace['adspaceId'] ?>/delete-ad/<?= $k ?>" class="btn btn-small btn-blue delete"><i class="fa fa-close"></i> Delete</a>
							<a href="#" class="btn btn-small btn-blue drag-handler ui-sortable-handle" title="Click and drag to re-order"><i class="fa fa-paw"></i> Re-order</a>
							<input type="hidden" name="ad_list[]" value="<?= $k ?>">
						</td>
					</tr>
					<?php
				}
				?>
			</tbody>
		</table>
		<div class="pull-right">
			<input type="hidden" name="save-ad-order" value="1" />
			<button class="btn"><i class="fa fa-refresh"></i> Save Ad List Order</button>
		</div>
		<div class="clear"></div>
		<small>
			* Ads are "active" if current time is between the start date and end date.<br>
			Ads are automatically archived once they have passed their end expiry date.
		</small>
	</form>
	<?php
}
else{
	echo '<p>No ads to show</p>';
}
?>
<div class="clear"></div>	
<br>
<hr>

<h3>Adspace Settings</h3>
<?= $main_form->display() ?>

<link rel="stylesheet" type="text/css" href="<?= THEME_URL ?>/css/jquery.datetimepicker.css"/ >
<script src="<?= THEME_URL ?>/js/jquery.datetimepicker.js"></script>
<script type="text/javascript">

	$(document).ready(function(){
		$('.sortable').sortable({handle:'.drag-handler'});
		$('.datetimepicker').datetimepicker();
		
	});
</script>
