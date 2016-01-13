<?php
namespace App\RSS;
use Core, App\API\V1, UI, Util, App\Blog;
class Model extends Core\Model
{
	protected function getBlogFeed($data)
	{
		$startTime = microtime(true);
		
		include(SITE_PATH.'/resources/rss/FeedWriter.php');

		$model = new \App\API\V1\Blog_Model;
		$data['isRSS'] = true;
		if(isset($data['audio'])){
			switch($data['audio']){
				case '1':
					$andAudio = true;
					break;
				case '2':
					$andAudio = false;
					break;
			}
			unset($data['audio']);
		}
		
		if(isset($andAudio)){
			$data['soundcloud-id'] = $andAudio;
			//$data['audio-url'] = $andAudio;
		}
		
		$getPosts = $model->getAllPosts($data);
		
		$blogApp = $this->get('apps', 'blog', array('appId', 'url'), 'slug');
		$postModule = $this->get('modules', 'blog-post', array('moduleId', 'url'), 'slug');
		
		$meta = new \App\Meta_Model;
		$rssApp = $this->get('apps', 'rss', array('appId', 'url'), 'slug');
		$rssMeta = $meta->appMeta($rssApp['appId']);
		$feedModule = $this->get('modules', 'rss-feed', array('moduleId', 'url'), 'slug');
		
		$feed = new \FeedWriter(RSS2);
		$feed->setTitle($rssMeta['blog-feed-title']);
		
		if(isset($_SERVER['HTTP_FROMLINK'])){
			//$feed->setLink($_SERVER['HTTP_FROMLINK']);
		}
		else{
			$newGet = $_GET;
			unset($newGet['params']);
			//$feed->setLink($data['site']['url'].'/'.$rssApp['url'].'/'.$feedModule['url'].'/blog?'.http_build_query($newGet));
		}
		$feed->setLink('https://letstalkbitcoin.com/resources/files/images/LTBNETWORK-LOGO3.jpg');
		$feed->setDescription($rssMeta['blog-feed-description']);
		
		//hardcode image for now..
		$site = currentSite();
		$feed->setImage('The Let\'s Talk Bitcoin Network', 'https://letstalkbitcoin.com/resources/files/images/LTBNETWORK-LOGO3.jpg', 'https://letstalkbitcoin.com/resources/files/images/LTBNETWORK-LOGO3.jpg');
		
	
		foreach($getPosts as $post){
			
			$post['content'] = strip_tags(replaceNonSGML($post['content']), '<a><p><br><strong><em><ul><ol><li><hr><h1><h2><h3><h4><h5><h6>');
			$post['excerpt'] = strip_tags(replaceNonSGML($post['excerpt']), '<a><p><br><strong><em><ul><ol><li><hr><h1><h2><h3><h4><h5><h6>');
			$post['title'] = replaceNonSGML($post['title']);
			$post['title'] = preg_replace('/&[^;]+;/','',$post['title']);
			
			$post['content'] = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $post['content']);
			$post['excerpt'] = preg_replace('/(<[^>]+) style=".*?"/i', '$1', $post['excerpt']);
			
			//temp
			$post['content'] = str_replace(array('href="../../../', 'href="%20', 'href="../../'), array('href="'.$data['site']['url'].'/', 'href="', 'href="'.$data['site']['url'].'/'), $post['content']);
			$post['excerpt'] = str_replace(array('href="../../../', 'href="%20', 'href="../../'), array('href="'.$data['site']['url'].'/', 'href="', 'href="'.$data['site']['url'].'/'), $post['excerpt']);
			
			$getPostSite = $this->get('sites', $post['siteId']);
			$item = $feed->createNewItem();
			$item->setTitle($post['title']);
			$item->setLink($getPostSite['url'].'/'.$blogApp['url'].'/'.$postModule['url'].'/'.$post['url']);
			$item->setDate($post['publishDate']);
			$item->setDescription($post['content']);
			$authorName = $post['author']['username'];
			if(isset($post['author']['profile']['real-name']) AND trim($post['author']['profile']['real-name']['value']) != ''){
				$authorName = $post['author']['profile']['real-name']['value'];
			}
			$item->setAuthor($authorName);
			
			
			
			if((isset($data['audio-url']) AND ($data['audio-url'] == 'true' OR $data['audio-url'] === true))
				OR (isset($data['soundcloud-id']) AND ($data['soundcloud-id'] == 'true' OR $data['soundcloud-id'] === true))){
				
				$audio = '';
				if(isset($post['audio-url']) AND trim($post['audio-url']) != ''){
					$audio = $post['audio-url'];
					if(isset($post['soundcloud-id']) AND trim($post['soundcloud-id']) != '' AND !strpos($audio, 'amazonaws')){
						//$audio .= '&ltb=/ltb.mp3';
					}
				}
				elseif(isset($post['soundcloud-id']) AND trim($post['soundcloud-id']) != ''){
					$audio = 'http://api.soundcloud.com/tracks/'.$post['soundcloud-id'].'/stream?client_id='.SOUNDCLOUD_ID.'&ltb.mp3';
				}
				
				$audio = str_replace('https://', 'http://', $audio);

				
				
				if(trim($audio) != ''){
					//use proxy link instead..
					$audio = $getPostSite['url'].'/rss/pod-proxy/'.$post['postId'].'.mp3';
					if(isset($post['audio-length']) AND trim($post['audio-length']) != ''){
						$audioLength = intval($post['audio-length']);
					}
					else{
						$getAudio = get_headers($audio, 1);
					
						if($getAudio AND isset($getAudio['Content-Length'])){
							if(is_array($getAudio['Content-Length'])){
								$getAudio['Content-Length'] = $getAudio['Content-Length'][1];
							}
							$audioLength = intval($getAudio['Content-Length']);
							$getMetaField = $this->get('blog_postMetaTypes', 'audio-length', array(), 'slug');
							if($getMetaField){
								$getMetaVal = $this->getAll('blog_postMeta', array('postId' => $post['postId'], 'metaTypeId' => $getMetaField['metaTypeId']));
								if($getMetaVal AND count($getMetaVal) > 0){
									$this->edit('blog_postMeta', $getMetaVal[0]['metaId'], array('value' => $audioLength));
								}
								else{
									$this->insert('blog_postMeta', array('postId' => $post['postId'], 'metaTypeId' => $getMetaField['metaTypeId'], 'value' => $audioLength));
								}
							}
						}						
					}
					
					if(isset($audioLength)){
						$audio = str_replace('https://', 'http://', $audio);
						$item->setEncloser($audio, $audioLength, 'audio/mpeg');
					}
				}
			}
			$item->addElement('guid', $getPostSite['url'].'/'.$blogApp['url'].'/'.$postModule['url'].'/'.$post['url'], array('isPermaLink' => 'true'));
			$feed->addItem($item);
		}
		$feed->generateFeed();
	}
	
	protected function getCustomizeForm($data)
	{
		$form = new UI\Form;
		
		$sites = new UI\CheckboxList('sites');
		$sites->setElemClass('choose-sites');
		$sites->setElemWrap('div');
		$sites->setLabel('Network Sites');
		$sites->setLabelDir('R');
		$getSites = $this->getAll('sites');
		foreach($getSites as $site){
			if($site['siteId'] > 1){
				continue;//temp skip other sites
			}			
			$sites->addOption($site['siteId'], $site['name']);
		}
		$form->add($sites);
		
		$catModel = new Blog\Categories_Model;
		
		foreach($getSites as $site){
			if($site['siteId'] > 1){
				continue;//temp skip other sites
			}
			$getCats = $catModel->getCategoryFormList($site['siteId']);
			if(count($getCats) <= 0){
				continue;
			}
			$siteCats = new UI\CheckboxList('cats-'.$site['siteId']);
			$siteCats->setLabel($site['name'].' Categories');
			$siteCats->setElemWrap('div');
			$siteCats->setElemClass('site-cats');
			$siteCats->setElemData('siteId', $site['siteId']);
			$siteCats->addOption(0, 'All');
			foreach($getCats as $cat){
				$siteCats->addOption($cat['categoryId'], $cat['name']);
			}
			$siteCats->setLabelDir('R');
			
			$form->add($siteCats);
		}
		
		$audio = new UI\Select('audio');
		$audio->setLabel('Contains Audio (podcasts)?');
		$audio->addOption(0, 'Either');
		$audio->addOption(1, 'Audio Only');
		$audio->addOption(2, 'No Audio');
		$form->add($audio);
		
		
		$numItems = new UI\Textbox('numItems');
		$numItems->setLabel('Max # of Feed Items');
		$numItems->setValue(15);
		$numItems->addAttribute('required');
		$form->add($numItems);
		
		return $form;
	}
}


