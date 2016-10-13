<?php
/*
 * @name = Multi-blog TCA
 * 
 * */
\Util\Filter::addFilter('App\Blog\Multiblog_Model', 'getBlogSettingFormDataFromKeys', 
					function($settings, $args){
						$orig_settings = $args[0];
						$app = get_app('blog');
						
						$token_found = false;
						$amount_found = false;
						foreach($orig_settings as $k => $val){
							if($k == 'access-token'){
								$token_found = $val;
							}
							if($k == 'token-req'){
								$amount_found = $val;
							}
						}
						
						$token_access = array('appMetaId' => 'access-token',
											'appId' => $app['appId'],
											'metaKey' => 'access-token',
											'metaValue' => $token_found,
											'label' => 'Access Token',
											'type' => 'textbox',
											'options' => false,
											'isSetting' => 1,
											'valueBlob' => '');
											
						$token_req = array('appMetaId' => 'token-req',
											'appId' => $app['appId'],
											'metaKey' => 'token-req',
											'metaValue' => $amount_found,
											'label' => 'Minimum amount of token required for access',
											'type' => 'textbox',
											'options' => false,
											'isSetting' => 1,
											'valueBlob' => '');		
						
								
						$settings[] = $token_access;
						$settings[] = $token_req;

						
						return $settings;
					});

\Util\Filter::addFilter('App\Blog\Multiblog_Model', 'updateBlogSettings',
				function($result, $args){
					if(!$result){
						return false;
					}
					
					$model = new \Core\Model;
                    \Core\Model::$cacheMode = false;
					$meta = new \App\Meta_Model;
					$id = $args[0];
					$data = $args[1];
					$getBlog = $model->get('blogs', $id);
					$getBlog['settings'] = json_decode($getBlog['settings'], true);					
					
					
					if(!isset($data['access-token-value']) OR !isset($data['token-req-value'])){
						return array($id, $data);
					}

					$getBlog['settings']['access-token'] = trim(strtoupper($data['access-token-value']));
					$getBlog['settings']['token-req'] = trim($data['token-req-value']);
	
				
					$model = new \Core\Model;
					$cat_module = get_app('blog.blog-category');
					
					$user = user();
					
					$remove_locks = remove_tca_locks($cat_module['moduleId'], $id, 'multiblog');
					
					if(trim($data['access-token-value']) != ''){
						$parse_input = parse_tca_token($data['access-token-value']);
						$parse_amount = parse_tca_amount($data['token-req-value']);
						$add_locks = add_tca_locks($user, $cat_module['moduleId'], $id, 'multiblog', $parse_input, $parse_amount);
					}						
					
					$encode = json_encode($getBlog['settings']);
					$update = $model->edit('blogs', $id, array('settings' => $encode));
					\Core\Model::$cacheMode = true;
					return true;				
					
				});

\Util\Filter::addFilter('App\Blog\Multiblog_Controller', 'deleteBlog',
			function()
			{
				$args = $this->args;
				if(isset($args[4])){
					$id = intval($args[4]);
					$app = get_app('blog');
					$cat_module = get_app('blog.blog-category');
					$model = new \Core\Model;
					$model->sendQuery('DELETE FROM token_access
									 WHERE moduleId = :moduleId
									 AND itemId = :id
									 AND itemType = "multiblog"',
									 array(':moduleId' => $cat_module['moduleId'],
										   ':id' => $id));
					
				}
				
				
			}, true);
