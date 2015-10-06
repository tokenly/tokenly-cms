<h2>User Accounts</h2>
<hr>
<p>
	View details to change user groups and view and/or change other info.
</p>
<h3>Find User</h3>
<?php
if($message != ''){
	echo '<p><strong>'.$message.'</strong></p>';
}
?>
<?= $searchForm->display() ?>
<?php

$table = $this->generateTable($users, array('fields' => array('userId' => 'ID','username' => 'Username',
															  'email' => 'Email', 'regDate' => 'Date Registered'),
											'class' => 'admin-table mobile-table',
											'options' => array(array('field' => 'regDate', 'params' => array('functionWrap' => 'formatDate'))),
											'actions' => array(array('text' => 'View Details', 'data' => 'userId',
																			   'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/view/'),
															array('text' => 'Delete', 'data' => 'userId', 'class' => 'delete delete-user',
																			   'url' => SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'))));

echo $table->display();
$pager = new \UI\Pager;
echo $pager->display($numPages, '?page=');
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.delete-user').click(function(e){
			var check = prompt('Type in the users\' username to proceed with deletion');
			var username = $(this).parent().parent().find('td').eq(1).html();
			
			if(check == false || check == null || check != username){
				e.preventDefault();
				return false;
			}
			
		});
		
	});
</script>
