<?php
namespace App\Blog;
class Controller extends \App\AppControl
{
    function __construct()
    {
        parent::__construct();
        $this->catModel = new Category_Model;
    }
    
    public function init()
    {
		$output = parent::init();
		
		if(!$output['module']){
			if(isset($this->args[1]) AND $this->args[1] != ''){
				//attempt to find a valid blog
				$getBlog = $this->catModel->get('blogs', $this->args[1], array(), 'slug');
				if($getBlog AND $getBlog['active'] == 1){
					//show blog home page
					$output['view'] = 'list';
					$output['title'] = $getBlog['name'];
					$output['blog'] = $getBlog;
					$postLimit = $this->app['meta']['postsPerPage'];
					$output['commentsEnabled'] = $this->app['meta']['enableComments'];
					
					$output['posts'] = $this->catModel->getBlogHomePosts($getBlog['blogId'], $postLimit);
					$output['numPages'] = $this->catModel->getBlogHomePages($getBlog['blogId'], $postLimit);					
					
					if($getBlog['themeId'] != 0){
						$getTheme = $this->catModel->get('themes', $getBlog['themeId']);
						if($getTheme){
							$output['theme'] = $getTheme['location'];
						}
					}
					$output['template'] = 'blog';
					
				}
				else{	
					$output['view'] = '404';
				}
				return $output;
			}
			$output['view'] = 'list';
			$output['title'] = 'Latest Blog Posts';
			$postLimit = $this->app['meta']['postsPerPage'];
			$output['commentsEnabled'] = $this->app['meta']['enableComments'];

			$output['posts'] = $this->catModel->getHomePosts($output['site']['siteId'], $postLimit);
			$output['numPages'] = $this->catModel->getHomePages($output['site']['siteId'], $postLimit);

		}
		$output['template'] = 'blog';
		
		return $output;
    }
    
    public function __install($appId)
    {
		$install = parent::__install($appId);
		if(!$install){
			return false;
		}

		$meta = new \App\Meta_Model;
		$meta->updateAppMeta($appId, 'postsPerPage', 20, 'Posts Per Page', 1);
		$meta->updateAppMeta($appId, 'maxExcerpt', 250, 'Max Post Excerpt Characters', 1);
		$meta->updateAppMeta($appId, 'enableComments', 1, 'Enable Comments', 1, 'bool');
		$meta->updateAppMeta($appId, 'featuredWidth', 1, 'Featured Image Width', 1);
		$meta->updateAppMeta($appId, 'featuredHeight', 1, 'Featured Image Height', 1);
		$meta->updateAppMeta($appId, 'coverWidth', 1, 'Cover Image Width', 1);
		$meta->updateAppMeta($appId, 'coverHeight', 1, 'Cover Image Height', 1);

		$meta->addAppPerm($appId, 'canPostComment');
		$meta->addAppPerm($appId, 'canEditSelfComment');
		$meta->addAppPerm($appId, 'canDeleteSelfComment');
		$meta->addAppPerm($appId, 'canEditOtherComment');
		$meta->addAppPerm($appId, 'canWritePost');
		$meta->addAppPerm($appId, 'canEditSelfPost');
		$meta->addAppPerm($appId, 'canDeleteSelfPost');
		$meta->addAppPerm($appId, 'canEditOtherPost');
		$meta->addAppPerm($appId, 'canDeleteOtherPost');
		$meta->addAppPerm($appId, 'canChangeAuthor');
	}
}
