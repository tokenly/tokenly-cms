<?php
namespace App\Blog;
use Core, API;
class Model extends Core\Model
{
	protected function downloadBlogAudio($categories, $path)
	{
		$output = '';
		$sc = new API\Soundcloud;
		$model = new Post_Model;
		
		$andCats = '';
		if(!in_array(0, $categories)){
			$andCats = 'WHERE c.categoryId IN('.join(',',$categories).')';
		}
		
		$sql = ' SELECT p.* 
				FROM blog_posts p
				LEFT JOIN blog_postCategories c ON c.postId = p.postId
				'.$andCats;
		$getPosts = $this->fetchAll($sql);
		$usePosts = array();
		foreach($getPosts as $post){
			$post['meta'] = $model->getPostMeta($post['postId']);
			if(isset($post['meta']['soundcloud-id']) AND trim($post['meta']['soundcloud-id'])){
				if(!isset($post['meta']['audio-url']) OR trim($post['meta']['audio-url'] == '')){
					$usePosts[] = $post;
				}
			}
		}
		
		foreach($usePosts as $post){
			$filename = genURL($post['title']).'.mp3';
			$fullPath = $path.'/'.$filename;
			if(!file_exists($fullPath)){
				try{
					$download = $sc->downloadTrack($post['meta']['soundcloud-id'], $fullPath);
				}
				catch(\Exception $e){
					$output .= $e->getMessage.' - #'.$post['postId'].' ['.$post['title']."]\n";
				}
				if($download){
					$output .= 'Downloaded: '.$fullPath."\n";
				}
			}
		}
		return $output;
	}
}
