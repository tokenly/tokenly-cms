<?php
$catModel = new \App\Blog\Categories_Model;
$categories = $catModel->getCategories($site['siteId'], 0, 1);
$blogs = $catModel->getAll('blogs');
$tca = new \App\Tokenly\TCA_Model;
$catModule = $tca->get('modules', 'blog-category', array(), 'slug');
$splitCats = array();
foreach($categories as $ck => $cv){
	$checkCatTCA = $tca->checkItemAccess($user, $catModule['moduleId'], $cv['categoryId'], 'blog-category');
	if(!$checkCatTCA){
		unset($categories[$ck]);
		continue;
	}
	if(!isset($splitCats[$cv['blogId']])){
		$caretDir = 'right';
		$bClass = '';
		if(isset($blog) AND $cv['blog']['slug'] == $blog['slug']){
			$caretDir = 'down collapse';
			$bClass = 'active';
		}
		$blogImg = '';
		foreach($blogs as $blg){
			if($blg['blogId'] == $cv['blogId']){
				if(trim($blg['image']) != ''){
					$blogImg = '<span class="blog-mini-img"><img src="'.SITE_URL.'/files/blogs/'.$blg['image'].'" alt="" /></span>';
				}
			}
		}
		$splitCats[$cv['blogId']] = array('url' => '#', 'label' => '<i class="fa fa-caret-'.$caretDir.'" title="Click to expand or collapse"></i><a href="'.SITE_URL.'/blog/'.$cv['blog']['slug'].'">'.$blogImg.'<strong>'.$cv['blog']['name'].'</strong></a><div class="clear"></div>', 'children' => array(), 'no_link' => true, 'class' => $bClass);
	}
	$splitCats[$cv['blogId']]['children'][] = $cv;
}
$getCats = array_merge(array(array('url' => SITE_URL.'/blog', 'label' => 'All')), $categories);
$getArchive = $catModel->getArchiveList($site['siteId']);
$splitArchive = array();
foreach($getArchive as $ak => $av){
	$exp_key = explode('-', $ak);
	$year = $exp_key[0];
	if(!isset($splitArchive[$year])){
		$splitArchive[$year] = array('url' => '#', 'no_link' => true, 'label' => '<i class="fa fa-caret-right"></i> <a href="'.SITE_URL.'/blog/archive/'.$year.'"><strong>'.$year.'</strong></a>', 'children' => array());
	}
	$splitArchive[$year]['children'][] = $av;
}
?>
<div class="sidebar-inner-content">
	<div style="margin-bottom: 20px;">
		<?= $this->displayTag('DISPLAY_ADSPACE', array('slug' => 'networkblogs-sidebar')) ?>					
	</div>	
	<div class="search-cont pull-right">
		<a href="#" class="search-icon" title="Search website"><i class="fa fa-search"></i></a>
	</div><!-- search-cont -->				
	<div class="blog-sidebar">
		<h2>Network <span>Blogs</span></h2>
		<?= $this->displayMenu($splitCats, 1, 'side-menu', $pageRequest['params']) ?>
		<h2>Blog <span>Archives</span></h2>
		<?= $this->displayMenu($splitArchive, 1, 'side-menu', $pageRequest['params']) ?>
	</div>
</div>
