<h2><?= $formType ?> Board <?php if(isset($getBoard)){ echo "- ".$getBoard['name']; } ?></h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<?php
if(isset($boardMeta['billed_user_board']) AND intval($boardMeta['billed_user_board']) == 1){
    $tokenly_app = get_app('tokenly');
    $forum_price = 0;
    if(isset($tokenly_app['meta']['tca-forum-credit-price'])){
        $forum_price = floatval($tokenly_app['meta']['tca-forum-credit-price']);
    }
    $bill_interval = 0;
    if(isset($tokenly_app['meta']['tca-forum-billing-interval'])){
        $bill_interval = intval($tokenly_app['meta']['tca-forum-billing-interval']);
    }
    ?>
    <ul>
        <li><strong>Cost:</strong> 
        <?= $forum_price ?> System Credits  every  <?= $bill_interval ?> <?= pluralize('day', $bill_interval, true) ?></li>
        <li><strong>Last Billing Date:</strong>
            <?= formatDate(intval($boardMeta['last_billing_time'])) ?>
        </li>
        <li><strong>Credits Spent to Date:</strong>
            <?= formatFloat($boardMeta['total_billed']) ?>
        </li>
        <li><strong>Status:</strong>
            <?= boolToText($getBoard['active'], '<span class="text-success">Active</span>', '<span class="text-error">Inactive</span>') ?>
        </li>
    </ul>
    <?php
}
?>
<?= $this->displayFlash('message') ?>
<?php
if(isset($error) AND $error != null){
	echo '<p class="error">'.$error.'</p>';
}
?>
<?= $form->display() ?>

<?php
if(isset($boardMods)){
	echo '<h3>Board Moderators</h3>';
	if(count($boardMods) == 0){
		echo '<p>No moderators added yet</p>';
	}
	else{
		$table = $this->generateTable($boardMods, array('fields' => array('username' => 'Username'),
														'actions' => array(array('data' => 'userId', 'text' => 'Remove',
														 'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/remove-mod/'.$getBoard['boardId'].'/',
														 'class' => 'delete'))));
		echo $table->display();
	}
	echo '<br>';
	echo $modForm->display();	
}
?>
