<?php
/*
 * @name = Blog Posts TCA
 * 
 * 
 * */
 
//requires "access-token" and "token-req" fields to be added to custom blog post meta fields

function get_blogpost_token_fields()
{
	$model = new \Core\Model;
	$fields = $model->getAll('blog_postMetaTypes', array('active' => 1));
	if(!$fields OR count($fields) == 0){
		return false;
	}
	$output = array();
	foreach($fields as $field){
		if($field['slug'] == 'access-token' OR $field['slug'] == 'token-req'){
			$output[$field['slug']] = $field['metaTypeId'];
		}
	}
	if(!isset($output['access-token']) OR !isset($output['token-req'])){
		return false;
	}
	return $output;
	
}
 
 
//edit blog post
\Util\Filter::addFilter('App\Blog\Submissions_Model', 'editPost',
			function($id, $data, $appData){
				
				$model = new \App\Blog\Submissions_Model;
				$app = get_app('blog');
				$post_module = get_app('blog.blog-post');
				$meta_fields = get_blogpost_token_fields();
				if(!$meta_fields){
					throw new \Exception('access-token and token-req custom blog fields must be set up');
				}
				
				if(!isset($data['meta_'.$meta_fields['access-token']]) OR !isset($data['meta_'.$meta_fields['token-req']])){
					return array($id, $data);
				}				
				
				$access_token = trim(strtoupper($data['meta_'.$meta_fields['access-token']]));
				$req_amounts = trim($data['meta_'.$meta_fields['token-req']]);
			
				$model->updatePostMeta($id, 'access-token', $access_token);
				$model->updatePostMeta($id, 'token-req', $req_amounts);
				
				$model = new \Core\Model;
				$post_module = get_app('blog.blog-post');
				
				$user = user();
				
				$remove_locks = remove_tca_locks($post_module['moduleId'], $id, 'blog-post');

				if(trim($access_token) != ''){
					$parse_input = parse_tca_token($access_token);
					$parse_amount = parse_tca_amount($req_amounts);
					$add_locks = add_tca_locks($user, $post_module['moduleId'], $id, 'blog-post', $parse_input, $parse_amount);
				}		

				return array($id, $data, $appData);
				
			}, true);
 
 
 //new blog post
 \Util\Filter::addFilter('App\Blog\Submissions_Model', 'addPost',
			function($id, $args){
				$data = $args[0];
				
				$model = new \App\Blog\Submissions_Model;
				$app = get_app('blog');
				$post_module = get_app('blog.blog-post');
				$meta_fields = get_blogpost_token_fields();
				if(!$meta_fields){
					throw new \Exception('access-token and token-req custom blog fields must be set up');
				}
				
				if(!isset($data['meta_'.$meta_fields['access-token']]) OR !isset($data['meta_'.$meta_fields['token-req']])){
					return array($id, $data);
				}				
				
				$access_token = trim(strtoupper($data['meta_'.$meta_fields['access-token']]));
				$req_amounts = trim($data['meta_'.$meta_fields['token-req']]);
			
				$model->updatePostMeta($id, 'access-token', $access_token);
				$model->updatePostMeta($id, 'token-req', $req_amounts);
				
				$model = new \Core\Model;
				$post_module = get_app('blog.blog-post');
				
				$user = user();

				if(trim($access_token) != ''){
					$parse_input = parse_tca_token($access_token);
					$parse_amount = parse_tca_amount($req_amounts);
					$add_locks = add_tca_locks($user, $post_module['moduleId'], $id, 'blog-post', $parse_input, $parse_amount);
				}						
				
				return $id;
				
			});


//delete post
\Util\Filter::addFilter('App\Blog\Submissions_Controller', 'deletePost',
			function()
			{
				$args = $this->args;
				if(isset($args[4])){
					$id = intval($args[4]);
					$app = get_app('blog');
					$post_module = get_app('blog.blog-post');
					$model = new \Core\Model;
					$model->sendQuery('DELETE FROM token_access
									 WHERE moduleId = :moduleId
									 AND itemId = :id
									 AND itemType = "blog-post"',
									 array(':moduleId' => $post_module['moduleId'],
										   ':id' => $id));
					
				}
				
				
			}, true);
