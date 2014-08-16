<?php
class Slick_App_Blog_Post_Model extends Slick_Core_Model
{

	public function getPost($url, $siteId)
	{
		$get = $this->fetchSingle('SELECT * FROM blog_posts WHERE url = :url AND siteId = :siteId',
									array(':url' => $url, ':siteId' => $siteId));
		if(!$get){
			$get = $this->fetchSingle('SELECT * FROM blog_posts WHERE postId = :id AND siteId = :siteId',
									array(':id' => $url, ':siteId' => $siteId));
			if(!$get){
				return false;
			}
		}
		
		$output = $get;
		$profModel = new Slick_App_Profile_User_Model;
		$output['author'] = $profModel->getUserProfile($get['userId'], $siteId);
		$getMeta = $this->getPostMeta($get['postId']);
		foreach($getMeta as $key => $val){
			if(!isset($output[$key])){
				$output[$key] = $val;
			}
		}
	
		return $output;
		
	}
	
	public function getCommentForm()
	{
		$form = new Slick_UI_Form;
		
		$message = new Slick_UI_Textarea('message', 'markdown');
		$message->setLabel('Message');
		$form->add($message);
		
		return $form;
	}
	
	public function getPostComments($postId, $siteId)
	{
		$getComments = $this->getAll('blog_comments', array('postId' => $postId, 'buried' => 0), array(), 'commentId', 'asc');
		$profModel = new Slick_App_Profile_User_Model;
		foreach($getComments as $key => $comment){
			if($comment['buried'] == 1){
				$getComments[$key]['author'] = 'null';
			}
			else{
				$getComments[$key]['author'] = $profModel->getUserProfile($comment['userId'], $siteId);
			}
			unset($getComments[$key]['userId']);
		}
		
		return $getComments;
		
	}
	
	public function getComment($commentId, $siteId)
	{
		$getComment = $this->get('blog_comments', $commentId);
		if(!$getComment){
			return false;
		}
		$profModel = new Slick_App_Profile_User_Model;
		if($getComment['buried'] == 1){
			$getComment['author'] = 'null';
		}
		else{
			$getComment['author'] = $profModel->getUserProfile($getComment['userId'], $siteId);
		}
		unset($getComment['userId']);
		
		return $getComment;
	}
	
	public function postComment($data, $appData)
	{
		if(!isset($data['message']) AND trim($data['message']) == ''){
			throw new Exception('Message required');
		}
		$useData = array();
		$useData['userId'] = $data['userId'];
		$useData['postId'] = $data['postId'];
		$useData['message'] = strip_tags($data['message']);
		$useData['commentDate'] = timestamp();
		$post = $this->insert('blog_comments', $useData);
		if(!$data){
			throw new Exception('Error posting comment');
		}
		
		mention($useData['message'], '%username% has mentioned you in a 
				<a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/'.$appData['post']['url'].'#comment-'.$post.'">blog comment.</a>',
				$useData['userId'], $post, 'blog-reply');
		
		if($appData['user']['userId'] != $appData['post']['userId']){
			Slick_App_Meta_Model::notifyUser($appData['post']['userId'],
			'<a href="'.$appData['site']['url'].'/profile/user/'.$appData['user']['slug'].'">'.$appData['user']['username'].'</a>
			posted a comment on your blog post: <a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/'.$appData['post']['url'].'#comment-'.$post.'">'.$appData['post']['title'].'</a>.',
											$post, $type = 'new-reply');
		}
		
		return $post;
		
	}
	
	public function getPostMeta($postId, $fullData = false, $private = false)
	{
		$andPrivate = '';
		if(!$private){
			$andPrivate = ' AND t.isPublic = 1 ';
		}
		$getMeta = $this->fetchAll('SELECT m.value, t.slug, t.metaTypeId
									FROM blog_postMeta m
									LEFT JOIN blog_postMetaTypes t ON t.metaTypeId = m.metaTypeId
									WHERE m.postId = :id AND m.value != ""
									'.$andPrivate.'
									ORDER BY t.rank ASC', array(':id' => $postId));
		if($fullData){
			return $getMeta;
		}
		
		$output = array();
		foreach($getMeta as $meta){
			$output[$meta['slug']] = $meta['value'];
		}
		
		return $output;
		
	}
}
