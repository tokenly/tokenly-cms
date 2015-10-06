<?php

?>
<div class="pull-right blog-submit-actions">
	<p>
		<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>/add" class="btn">Create Category</a>
	</p>
</div>
<h2>Blog Categories</h2>
<hr>
<?=  $this->displayFlash('blog-message') ?>
<div class="clear"></div>
<?php
if(count($catList) == 0){
	echo '<p>No categories added</p>';
}
else{
	
	$blogCats = array();
	$blogInfo = array();
	foreach($catList as $cat){
		if(!isset($blogCats[$cat['blogId']])){
			$blogCats[$cat['blogId']] = array();
			$blogInfo[$cat['blogId']] = $cat['blog'];
		}
		$blogCats[$cat['blogId']][] = $cat;
	}
	

function showCategory($catList, $app, $module, $class = '', $blogSlug = '')
{
	if($blogSlug != ''){
		$blogSlug = 'categories-'.$blogSlug;
	}
	$output = '<ul class="'.$class.' '.$blogSlug.'">';
	foreach($catList as $item){
	
		$output .= '<li>
						<div class="item-actions">';
		
			$output .= '
						<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$item['categoryId'].'">Edit</a>
						<a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/'.$item['categoryId'].'" class="delete">Delete</a>
						';
		

		$output .= '</div><div class="item-label"><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/edit/'.$item['categoryId'].'">'.$item['name'].'</a></div>';
		if(isset($item['children'])){
			$output .= showCategory($item['children'], $app, $module);
		}
		$output .= '</li>';
	}
	
	$output .= '</ul>';
	
	return $output;
}

	foreach($blogCats as $blogId => $blogCatList){
		echo '<h3>'.$blogInfo[$blogId]['name'].' <a href="#" class="expand-cats collapse" data-blog="'.$blogInfo[$blogId]['slug'].'"><i class="fa fa-minus-circle"></i></a></h3>';
		echo showCategory($blogCatList, $app, $module, 'menu-item-list', $blogInfo[$blogId]['slug']);
	}
}
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('.expand-cats').click(function(e){
			e.preventDefault();
			var blog = $(this).data('blog');
			if($(this).hasClass('collapse')){
				$('.categories-' + blog).slideUp();
				$(this).html('<i class="fa fa-plus-circle"></i>');
				$(this).removeClass('collapse');
			}
			else{
				$('.categories-' + blog).slideDown();
				$(this).html('<i class="fa fa-minus-circle"></i>');
				$(this).addClass('collapse');
			}
		});
		
	});
</script>
