<?php
namespace App\Blog;
use App\Tokenly;
class Controller extends \App\AppControl
{
    function __construct()
    {
        parent::__construct();
        $this->catModel = new Category_Model;
        $this->blogModel = new Multiblog_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		
		if(!$output['module']){
			if(isset($this->args[1]) AND $this->args[1] != ''){
				//attempt to find a valid blog
				$getBlog = $this->catModel->get('blogs', $this->args[1], array(), 'slug');
				if($getBlog AND $getBlog['active'] == 1){
					
					$tca = new Tokenly\TCA_Model;
					$cat_module = get_app('blog.blog-category');
					$checkTCA = $tca->checkItemAccess($output['user']['userId'], $cat_module['moduleId'], $getBlog['blogId'], 'multiblog');
					if(!$checkTCA){
						$output['view'] = '403';
						return $output;
					}
					
					$getBlog['settings'] = $this->blogModel->getSingleBlogSettings($getBlog);
					//show blog home page
					$output['view'] = 'list';
					$output['title'] = $getBlog['name'];
					$output['blog'] = $getBlog;
					$postLimit = intval($getBlog['settings']['postsPerPage']);
					$output['commentsEnabled'] = intval($getBlog['settings']['enableComments']);;
					
					$output['posts'] = $this->catModel->getBlogHomePosts($getBlog['blogId'], $postLimit);
					$output['numPages'] = $this->catModel->getBlogHomePages($getBlog['blogId'], $postLimit);	

					if($getBlog['themeId'] != 0){
						$getTheme = $this->catModel->get('themes', $getBlog['themeId']);
						if($getTheme){
							$output['theme'] = $getTheme['location'];
						}
					}
                    if(isset($getBlog['settings']['domain']) AND trim($getBlog['settings']['domain']) != ''){
                        define('SITE_URL', $getBlog['settings']['domain']);
                        static_cache('ALT_DOMAIN', true);
                        $parse_blog_url = parse_url($getBlog['settings']['domain']);
                        if(isset($parse_blog_url['host'])){
                            if($_SERVER['HTTP_HOST'] != $parse_blog_url['host']){
                                redirect($getBlog['settings']['domain']);
                            }
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
    
    protected function __install($appId)
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
		$meta->updateAppMeta($appId, 'category-image-width', 1, 'Category Image Width', 1);
		$meta->updateAppMeta($appId, 'category-image-height', 1, 'Category Image Height', 1);
		$meta->updateAppMeta($appId, 'submission-fee', 1, 'Article Submission Fee', 1);
		$meta->updateAppMeta($appId, 'submission-fee-token', 1, 'Submission Fee Token', 1);
		$meta->updateAppMeta($appId, 'header_html', 1, 'Header Custom HTML', 1, 'textarea');
		$meta->updateAppMeta($appId, 'footer_html', 1, 'Footer Custom HTML', 1, 'textarea');
		$meta->updateAppMeta($appId, 'blog_tagline', 1, 'Blog Tagline/Slogan', 1);
		$meta->updateAppMeta($appId, 'meta_description', 1, 'Meta Tag Description', 1, 'textarea');

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
		$meta->addAppPerm($appId, 'canUseMagicWords');
		$meta->addAppPerm($appId, 'canSetEditStatus');
		$meta->addAppPerm($appId, 'canBypassSubmitFee');
		$meta->addAppPerm($appId, 'canDeleteSelfPostVersion');
		$meta->addAppPerm($appId, 'canDeleteOtherPostVersion');
		$meta->addAppPerm($appId, 'canEditAfterPublished');
		$meta->addAppPerm($appId, 'canManageAllBlogs');
		$meta->addAppPerm($appId, 'canChangeBlogOwner');
		$meta->addAppPerm($appId, 'canCreateBlogs');

	}
}
