<?php
/*
 * @name = Blog Category TCA
 * 
 * 
 * */
\Util\Filter::addFilter('App\Blog\Categories_Model', 'getBlogCategoryForm', 
					function($form, $args){
						
						$token = new \UI\Textbox('access-token');
						$token->setLabel('Access Token');
						$form->add($token);
						
						$token_req = new \UI\Textbox('token-req');
						$token_req->setLabel('Minimum amount of token required for access');
						$token_req->setValue(1);
						$form->add($token_req);
						
						if(isset($args[1]) AND $args[1] > 0){
							//set values if the page already exists
							$app = get_app('blog');
							$meta = new \App\Meta_Model;
							if(isset($app['meta']['category_meta_'.$args[1]])){
								$cat_meta = json_decode($app['meta']['category_meta_'.$args[1]], true);
								if(isset($cat_meta['access-token'])){
									$form->field('access-token')->setValue($cat_meta['access-token'], 'access-token');
								}
								if(isset($cat_meta['token-req'])){
									$form->field('token-req')->setValue($cat_meta['token-req'], 'token-req');
								}
							}
						}
						
						return $form;
					});
					
\Util\Filter::addFilter('App\Blog\Categories_Model', 'addBlogCategory',
			function($id, $args)
			{
				$meta = new \App\Meta_Model;
				$app = get_app('blog');
				$cat_meta = array();
				$data = $args[0];
				
				if(!isset($data['access-token']) OR !isset($data['token-req'])){
					return array($id, $data);
				}				
				
				$cat_meta['access-token'] = trim(strtoupper($data['access-token']));;
				$cat_meta['token-req'] = trim($data['token-req']);;
			
				$meta->updateAppMeta($app['appId'], 'category_meta_'.$id, json_encode($cat_meta));
				
				$model = new \Core\Model;
				$cat_module = get_app('blog.blog-category');
				
				$user = user();
				
				if(trim($data['access-token']) != ''){
					$parse_input = parse_tca_token($data['access-token']);
					$parse_amount = parse_tca_amount($data['token-req']);
					$add_locks = add_tca_locks($user, $cat_module['moduleId'], $id, 'blog-category', $parse_input, $parse_amount);
				}						
						
				return $id;
			});
			
\Util\Filter::addFilter('App\Blog\Categories_Model', 'editBlogCategory',
			function($id, $data)
			{
				$meta = new \App\Meta_Model;
				$app = get_app('blog');
				$cat_meta = array();
				
				if(!isset($data['access-token']) OR !isset($data['token-req'])){
					return array($id, $data);
				}				
				
				$cat_meta['access-token'] = trim(strtoupper($data['access-token']));;
				$cat_meta['token-req'] = trim($data['token-req']);;
			
				$meta->updateAppMeta($app['appId'], 'category_meta_'.$id, json_encode($cat_meta));
				
				$model = new \Core\Model;
				$cat_module = get_app('blog.blog-category');
				
				$user = user();
				
				$remove_locks = remove_tca_locks($cat_module['moduleId'], $id, 'blog-category');

				if(trim($data['access-token']) != ''){
					$parse_input = parse_tca_token($data['access-token']);
					$parse_amount = parse_tca_amount($data['token-req']);
					$add_locks = add_tca_locks($user, $cat_module['moduleId'], $id, 'blog-category', $parse_input, $parse_amount);
				}					
					
				return array($id, $data);
			}, true);			

\Util\Filter::addFilter('App\Blog\Categories_Controller', 'deleteBlogCategory',
			function()
			{
				$args = $this->args;
				if(isset($args[4])){
					$id = intval($args[4]);
					$app = get_app('blog');
					$cat_module = get_app('blog.blog-category');
					$meta = new \App\Meta_Model;
					$cat_meta = $meta->getAppMeta($app['appId'], 'category_meta_'.$id, 1);
					if($cat_meta){
						$meta->delete('app_meta', $cat_meta['appMetaId']);
					}
					$meta->sendQuery('DELETE FROM token_access
									 WHERE moduleId = :moduleId
									 AND itemId = :id
									 AND itemType = "blog-category"',
									 array(':moduleId' => $cat_module['moduleId'],
										   ':id' => $id));
					
				}
				
				
			}, true);
