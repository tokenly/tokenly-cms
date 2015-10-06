<?php
$bodyClass = 'full-template';

include(THEME_PATH.'/inc/header.php');

?>
	</div><!-- main -->
	<?php
	include(THEME_PATH.'/inc/sidebar.php');
	?>
	<div class="full-content">
		<div class="content">
			<?php include($viewPath); ?>
		</div><!-- content -->
	</div><!-- full-content -->
	<div class="mobile-sidebar">
		<?php
		include(THEME_PATH.'/inc/sidebar.php');
		?>		
	</div>
<?php
include(THEME_PATH.'/inc/footer-full.php');
?>
