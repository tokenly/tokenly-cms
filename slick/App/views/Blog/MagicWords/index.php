<h2>Proof of Listening - Magic Words</h2>
<p>
	Did you hear the latest episode of your favorite LTB Network show?  Listen closely, when they give you the magic word make
	sure to remember it and enter it right here!  Magic Words are the simple method to help us know you're listening, 
	and reward you for your time! 
</p>
<p>
	Each word can only be redeemed one time per listener, and they're only good for seven days from the release of the 
	episode it was obtained from.
</p>
</p>
<?php
if(isset($message) AND trim($message) != ''){
	echo '<p><strong class="'.$message_class.'">'.$message.'</strong></p>';
}
?>
<?= $form->display() ?>
<?php
if(count($words) > 0){
	echo '<h3>Word Submissions</h3>';
	
	$table = $this->generateTable($words, array('class' => 'admin-table mobile-table',
												'fields' => array('word' => 'Magic Word', 'itemName' => 'Post Name', 'itemType' => 'Post Type',
																  'submitDate' => 'Date Submitted'),
												'actions' => array(array('text' => 'View Post', 'data' => 'itemUrl', 'url' => SITE_URL.'/', 'heading' => '', 'target' => '_blank')),
												'options' => array(array('field' => 'submitDate', 'params' => array('functionWrap' => 'formatDate')))));
												 
	echo $table->display();
}
?>
