<?php


?>
<h2>Menu Items</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add-page">Add Page to Menu</a><br>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add-link">Add Link to Menu</a>
</p>


<?php
//debug($menus);
if(count($menus) == 0){
	echo '<p>No menus added</p>';
}
else{

function showMenu($items, $app, $module, $class = '')
{
	$output = '';
	if(count($items) == 0){
		return $output;
	}
	
	$output .= '<ul class="'.$class.'">';
	foreach($items as $item){
	
		$output .= '<li>
						<div class="item-actions">';
		if(isset($item['actionUrl'])){
			$output .= '
						<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit-'.$item['actionUrl'].'">Edit Item</a>
						<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete-'.$item['actionUrl'].'" class="delete">Delete</a>
						';
		}

		$output .= '</div><div class="item-label"><a href="'.$item['url'].'" target="_blank">'.$item['label'].'</a></div>';
		if(isset($item['children'])){
			$output .= showMenu($item['children'], $app, $module);
		}
		$output .= '</li>';
	}
	
	$output .= '</ul>';
	
	return $output;
	
}

	foreach($menus as $menu){
		echo '<h3>'.$menu['name'].'</h3>';
		
		if(count($menu['items']) == 0){
			echo '<p>No items added</p>';
			continue;
		}
		
		echo showMenu($menu['items'], $app, $module, 'menu-item-list');

		echo '<br>';
	}
	
	 
	 
}
?>

