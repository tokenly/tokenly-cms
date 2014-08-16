
<?php

?>
<h2>Store Categories</h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>">Go Back</a>
</p>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add">Add Category</a>
</p>
<?php
if(count($catList) == 0){
	echo '<p>No categories added</p>';
}
else{

function showCategory($catList, $app, $module, $class = '')
{
	$output = '<ul class="'.$class.'">';
	foreach($catList as $item){
	
		$output .= '<li>
						<div class="item-actions">';
		
			$output .= '
						<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$item['categoryId'].'">Edit</a>
						<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$item['categoryId'].'" class="delete">Delete</a>
						';
		

		$output .= '</div><div class="item-label">'.$item['name'].'</div>';
		if(isset($item['children'])){
			$output .= showCategory($item['children'], $app, $module);
		}
		$output .= '</li>';
	}
	
	$output .= '</ul>';
	
	return $output;
}

	
	echo showCategory($catList, $app, $module, 'menu-item-list');

	 
}
?>
