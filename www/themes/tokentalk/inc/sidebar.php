<div class="sidebar">
	<?php
	include(THEME_PATH.'/inc/sidebar-header.php');
	?>
	<div class="sidebar-content">
		<?php
		if(file_exists(THEME_PATH.'/inc/sidebars/'.$template.'.php')){
			include(THEME_PATH.'/inc/sidebars/'.$template.'.php');
		}
		else{
			include(THEME_PATH.'/inc/sidebars/default.php');
		}
		?>
	</div><!-- sidebar-content -->
</div><!-- sidebar -->
