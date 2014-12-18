<?php
class Slick_App_Blog_Post_Model extends Slick_Core_Model
{
	public static $postMetaTypes = false;
	
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
		
		$get['modifiedDate'] = $get['editTime'];
		unset($get['editTime']);
		
		
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
		
		$message = new Slick_UI_Markdown('message', 'markdown');
		$message->setLabel('Message');
		$form->add($message);
		
		return $form;
	}
	
	public function getPostComments($postId, $editorial = 0)
	{
		$getSite = currentSite();
		$siteId = $getSite['siteId'];
		
		$getComments = $this->getAll('blog_comments', array('postId' => $postId, 'buried' => 0, 'editorial' => $editorial), array(), 'commentId', 'asc');
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
	
	public function getComment($commentId)
	{
		$getSite = currentSite();
		$siteId = $getSite['siteId'];
		
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
	
	public function postComment($data, $appData, $editorial = 0)
	{
		if(!isset($data['message']) AND trim($data['message']) == ''){
			throw new Exception('Message required');
		}
		$useData = array();
		$useData['userId'] = $data['userId'];
		$useData['postId'] = $data['postId'];
		$useData['editorial'] = $editorial;
		$useData['message'] = strip_tags($data['message']);
		$useData['commentDate'] = timestamp();
		$post = $this->insert('blog_comments', $useData);
		if(!$data){
			throw new Exception('Error posting comment');
		}
		
		//only use @mentions when not private editorial comments
		if($editorial == 0){
			mention($useData['message'], '%username% has mentioned you in a 
					<a href="'.$appData['site']['url'].'/'.$appData['app']['url'].'/'.$appData['module']['url'].'/'.$appData['post']['url'].'#comment-'.$post.'">blog comment.</a>',
					$useData['userId'], $post, 'blog-reply');
		}
		
		$notifyData = $appData;
		$notifyData['postId'] = $post;
		if($appData['user']['userId'] != $appData['post']['userId']){
			if($editorial == 1){
				$meta = new Slick_App_Meta_Model;
				$getDiscussions = $meta->getUserMeta($appData['user']['userId'], 'editorial_discussions');
				if($getDiscussions){
					$discussList = explode(',', $getDiscussions);
				}
				else{
					$discussList = array();
				}
				if(!in_array($appData['post']['postId'], $discussList)){
					$discussList[] = $appData['post']['postId'];
					$meta->updateUserMeta($appData['user']['userId'], 'editorial_discussions', join(',', $discussList));
				}
				
				Slick_App_Meta_Model::notifyUser($appData['post']['userId'], 'emails.blogPrivateCommentNotice', $post, 'new-editor-reply', false, $notifyData);
			}
			else{
				Slick_App_Meta_Model::notifyUser($appData['post']['userId'], 'emails.blogCommentNotice', $post, 'new-reply', false, $notifyData);
			}
		}
		
		$noticeList = $this->getEditorialDiscussionUsers($appData['post']['postId']);
		foreach($noticeList as $extraNotice){
			if($extraNotice != $appData['user']['userId'] AND $extraNotice != $appData['post']['userId']){
				Slick_App_Meta_Model::notifyUser($extraNotice, 'emails.blogOtherPrivateCommentNotice', $post, 'new-editor-reply', false, $notifyData);
			}
		}		
		
		$useData['commentId'] = $post;
		
		return $useData;
		
	}
	
	public function getEditorialDiscussionUsers($postId)
	{
		$getRows = $this->fetchAll('SELECT userId, metaValue as value FROM user_meta WHERE metaKey = "editorial_discussions"');
		$output = array();
		foreach($getRows as $user){
			$exp = explode(',', $user['value']);
			if(in_array($postId, $exp)){
				$output[] = $user['userId'];
			}
		}
		return $output;
	}
	
	public function getPostMeta($postId, $fullData = false, $private = false)
	{
		if(!self::$postMetaTypes){
			$site = currentSite();
			self::$postMetaTypes = $this->getAll('blog_postMetaTypes', array('siteId' => $site['siteId']), array(), 'rank' ,'asc');
		}
		$types = self::$postMetaTypes;

		//return array();
		$getMeta = $this->getAll('blog_postMeta', array('postId' => $postId), array('value', 'metaTypeId'));

		$metaList = array();
		foreach($getMeta as $meta){
			foreach($types as $type){
				if($type['metaTypeId'] == $meta['metaTypeId']){
					if(!$private AND $type['isPublic'] == 0){
						continue;
					}
					$meta['rank'] = $type['rank'];
					$meta['slug'] = $type['slug'];
					$metaList[] = $meta;
					continue;
				}
			}
		}
		aasort($metaList, 'rank');
		if($fullData){
			return $metaList;
		}
		
		$output = array();
		foreach($metaList as $meta){
			if(trim($meta['value']) == ''){
				continue;
			}
			$output[$meta['slug']] = $meta['value'];
		}
		
		return $output;
		
	}
}
