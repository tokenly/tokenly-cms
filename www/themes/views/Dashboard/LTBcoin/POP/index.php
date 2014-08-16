<h1>Proof of Participation Reports</h1>
<p>
	Create a Proof of Participation report for the weekly LTBcoin distributions. Just choose a start and end date and hit "generate report", or choose custom metrics.
	Reports may take several minutes to build (due to external API requests), please be patient. Reports can be directly uploaded into the asset distributor.
</p>
<?php
if(isset($message) AND trim($message) != ''){
	echo '<p class="error">'.$message.'</p>';
}
?>
<div class="pop-report-form">
<?= $form->display() ?>
</div>

<?php

if(isset($reports) AND count($reports) > 0){
	echo '<hr>';
	
	$table = $this->generateTable($reports, array('fields' => array('label' => 'Label', 'totalPoints' => 'Total PoP Points', 'startDate' => 'Start Date',
																		'endDate' => 'End Date', 'reportDate' => 'Date Generated'),
												  'actions' => array(array('data' => 'reportId', 'text' => 'View Report', 'heading' => '',
																		'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/view/'),
																	  array('data' => 'reportId', 'text' => 'Delete', 'heading' => '',
																			 'class' => 'delete', 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'))));
	echo $table->display();
	
}

?>
