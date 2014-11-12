<?php
class Slick_App_RSS_PodProxy_Controller extends Slick_App_ModControl
{
	function __construct()
	{
		parent::__construct();
		$this->model = new Slick_App_Blog_Post_Model;
	}
	
	public function init()
	{
		$output = parent::init();
		ob_end_clean();
			
		if(isset($this->args[2])){
			$split = explode('.', $this->args[2]);
			$postId = $split[0];
			$getPost = $this->model->get('blog_posts', $postId);
			if($getPost){
				$getMeta = $this->model->getPostMeta($getPost['postId']);
				$audio = false;
				if(isset($getMeta['audio-url']) AND trim($getMeta['audio-url']) != ''){
					$audio = $getMeta['audio-url'];
				}
				elseif(isset($getMeta['soundcloud-id']) AND trim($getMeta['soundcloud-id']) != ''){
					$audio = 'http://api.soundcloud.com/tracks/'.$getMeta['soundcloud-id'].'/stream?client_id='.SOUNDCLOUD_ID.'&ltb.mp3';
				}
				if($audio !== false){
					header('Location: '.$audio);
					die();
				}
			}
		}
		
		$output['view'] = '404';

		return $output;
	}
	

}
