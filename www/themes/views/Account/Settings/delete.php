<h2>Delete Account</h2>
<p>
	Account deletion is permanent. All data tied to your account will be removed from the system. 
	Are you sure you want to continue?
</p>
<?php
if(isset($message) AND $message != null){
	echo '<p class="error">'.$message.'</p>';
}
?>
<?= $form->display() ?>

