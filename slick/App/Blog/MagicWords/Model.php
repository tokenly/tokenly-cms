<?php
class Slick_App_Blog_MagicWords_Model extends Slick_Core_Model
{
	public function getWordForm()
	{
		$form = new Slick_UI_Form;
		
		$word = new Slick_UI_Textbox('word');
		$word->addAttribute('required');
		$word->setLabel('Enter Magic Word:');
		$form->add($word);
		
		$form->setSubmitText('Verify');
		
		return $form;
	}

	public function checkMagicWord($word, $userId, $type = 'blog')
	{
		$coinApp = $this->get('apps', 'tokenly', array(), 'slug');
		if(!$coinApp){
			throw new Exception('Token app not installed');
		}
		
		$meta = new Slick_App_Meta_Model;
		$settings = $meta->appMeta($coinApp['appId']);

		if(!isset($settings['pol-word-expire'])){
			throw new Exception('No magic word expiration setting set');
		}

		$expire = intval($settings['pol-word-expire']) * 60 * 60;
		$expireLimit = time() - $expire;
		
		switch($type){
			case 'blog':
			default:
				$module = $this->get('modules', 'blog-post', array(), 'slug');
				if(!$module){
					throw new Exception('Blog post module not installed');
				}
				$candidates = $this->getBlogCandidates($word, $expireLimit);
			break;
		}

		$usedSubmits = array();
		foreach($candidates as $cand){
			$check = $this->checkWordSubmitted($word, $userId, $cand, $module['moduleId']);
			if(!$check){
				//insert into db
				$useData = array('word' => $word, 'userId' => $userId, 'itemId' => $cand, 'moduleId' => $module['moduleId'], 'submitDate' => timestamp());
				$insert = $this->insert('pop_words', $useData);
				if(!$insert){
					throw new Exception('Error submitting magic word');
				}
				return true;
			}
			else{
				$usedSubmits[] = $cand;
			}
		}
		if(count($usedSubmits) > 0){
			throw new Exception('You have already submitted that word!');
		}
		
		throw new Exception('Invalid or expired magic word');
	}
	
	public function checkWordSubmitted($word, $userId, $itemId, $moduleId)
	{
		$get = $this->getAll('pop_words', array('word' => $word, 'userId' => $userId, 'itemId' => $itemId, 'moduleId' => $moduleId));
		if($get AND count($get) > 0){
			return true;
		}
		return false;
		
	}
	
	public function getBlogCandidates($word, $expire)
	{
		$sql = 'SELECT postId FROM blog_posts WHERE published = 1 AND publishDate >= :limit';
		$getPosts = $this->fetchAll($sql, array(':limit' => date('Y-m-d H:i:s', $expire)));
		
		$postModel = new Slick_App_Blog_Post_Model;
		$candidates = array();
		foreach($getPosts as $post){
			$postMeta = $postModel->getPostMeta($post['postId'], false, true);
			if(isset($postMeta['magic-word']) AND trim($postMeta['magic-word']) != ''){
				if(trim(strtolower($postMeta['magic-word'])) == trim(strtolower($word))){
					$candidates[] = $post['postId'];
				}
			}
		}
		
		return $candidates;
		
	}
	
	public function submitMagicWord($data)
	{
		if(!isset($data['word']) OR trim($data['word']) == ''){
			throw new Exception('Please enter a valid word');
		}
		if(!isset($data['userId']) OR trim($data['userId']) == ''){
			throw new Exception('User not set');
		}
		if(!isset($data['type'])){
			$data['type'] = 'blog';
		}
		
		if(!isset($_SESSION['magicWordTries']) OR (time() - $_SESSION['lastMagicWordTry']) > 1600){
			$_SESSION['magicWordTries'] = 0;
		}
		elseif($_SESSION['magicWordTries'] >= 10){
			throw new Exception('Too many wrong tries! Please come back in a while and try again.');
		}

		try{
			$_SESSION['lastMagicWordTry'] = time();
			$submit = $this->checkMagicWord($data['word'], $data['userId'], $data['type']);
		}
		catch(Exception $e){
			$_SESSION['magicWordTries']++;
			throw new Exception($e->getMessage());
		}
		
		if($submit){
			$_SESSION['magicWordTries'] = 0;
		}
		
		return $submit;
	}
	
	public function getUserWordSubmissions($userId)
	{
		$get = $this->getAll('pop_words', array('userId' => $userId), array(), 'submitId');
		$modules = array();
		$apps = array();
		
		foreach($get as &$row){
			if(!isset($modules[$row['moduleId']])){
				$modules[$row['moduleId']] = $this->get('modules', $row['moduleId']);
			}
			$module = $modules[$row['moduleId']];
			if(!isset($apps[$module['appId']])){
				$apps[$module['appId']] = $this->get('apps', $module['appId']);
			}
			$app = $apps[$module['appId']];
			$row['itemName'] = '';
			$row['itemType'] = '';
			$row['itemUrl'] = '';
			switch($module['slug']){
				case 'blog-post':
					$row['itemType'] = 'Blog Post';
					$getItem = $this->get('blog_posts', $row['itemId']);
					if($getItem){
						$row['itemName'] = $getItem['title'];
						$row['itemUrl'] = $app['url'].'/'.$module['url'].'/'.$getItem['url'];
					}
					break;
			}
		}
		
		return $get;
	}

}
