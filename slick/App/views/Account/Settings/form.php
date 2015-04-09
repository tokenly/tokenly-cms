<?php
if($adminView){
	echo '<h2>Edit Account Settings - '.$thisUser['username'].'</h2>';
	echo '<p><a href="'.SITE_URL.'/dashboard/cms/accounts/view/'.$thisUser['userId'].'">Go Back</a></p>';
}
else{
	echo '<h2>Edit Account Settings</h2>';
}
?>

<?php
if(isset($message) AND $message != null){
	echo '<p class="error">'.$message.'</p>';
}
?>
<?= $form->display() ?>
<?php
if(!$adminView){
?>
<hr>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/delete" class="delete-account">Delete Account</a>
</p>
<script type="text/javascript">
	$(document).ready(function(){
		$('.delete-account').click(function(e){
			var check = confirm('Are you sure you really want to permanently delete your account?');
			if(check == false || check == null){
				e.preventDefault();
				return false;
			}
		});
	});
</script>
<?php
}//endif
?>
