<p class="pull-right text-center" style="width: 150px; font-size: 12px;">
	<strong>Server Time:</strong><br> <span class="server-time"><?= date('Y/m/d  H:i') ?></span>
</p>
<h2>The Newsroom</h2>
<div class="newsroom-cont">
<?php
	echo '<p>Please select one of the available blogs below to enter the appropriate newsroom.</p>';
	echo '<hr>';	
	echo $this->displayFlash('blog-message');		
	$profModel = new \App\Profile\User_Model;

foreach($blogs as $blog){
	$blogImage = '';
	if(trim($blog['image']) != '' AND file_exists(SITE_PATH.'/files/blogs/'.$blog['image'])){
		$blogImage = '<span class="blog-avatar"><img src="'.SITE_URL.'/files/blogs/'.$blog['image'].'" alt="" /></span>';
	}
	echo '<h3><a href="'.SITE_URL.'/'.$app['url'].'/'.$module['url'].'/'.$blog['slug'].'">'.$blogImage.' '.$blog['name'].' <i class="fa fa-chevron-right"></i></a></h3>';
	echo '<hr>';

}//endforeach
?>
</div><!-- newsroom-cont -->
