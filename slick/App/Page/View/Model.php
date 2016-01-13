<?php
namespace App\Page;
use Core;
class View_Model extends Core\Model
{
	protected function getPageData($pageId)
	{
		$getPage = $this->get('pages', $pageId);
		if(!$getPage OR $getPage['active'] == 0){
			return false;
		}
		
		$output = array();
		if($getPage['formatType'] == 'markdown'){
			$getPage['content'] = markdown($getPage['content']);
		}
		$output['content'] = $this->container->processPageContent($getPage['content'], $getPage['siteId']);
		$output['meta-description'] = $getPage['description'];
		$output['template'] = $getPage['template'];
		$output['url'] = $getPage['url'];
		$output['title'] = $getPage['name'];
		
		return $output;
	}
	
	protected function processPageContent($content, $siteId = 0)
	{
		$content = $this->container->parsePageTags($content);
		$content = $this->container->parseContentBlocks($content, $siteId);
		return $content;
	}
	
	protected static function parseContentBlocks($str, $siteId)
	{
		$model = new Core\Model;
		$newStr = $str;
		$get = $model->getAll('content_blocks', array('siteId' => $siteId));
		$getBlockTags = preg_match_all('/(\[BLOCK:(.*?)\])/', $newStr, $blockTags);

		if(isset($blockTags[2]) AND count($blockTags[2]) > 0){
			foreach($get as $block){
				if($block['active'] == 0){
					$newStr = str_replace('[BLOCK:'.$block['blockId'].']', '', $newStr);
					$newStr = str_replace('[BLOCK:'.$block['slug'].']', '', $newStr);
					continue;
				}
				
				foreach($blockTags[2] as $foundTag){
					if($foundTag == $block['slug']){
						$newStr = str_replace('[BLOCK:'.$foundTag.']', \App\View::displayBlock($block['slug']), $newStr);
					}
					elseif($foundTag == $block['blockId']){
						$newStr = str_replace('[BLOCK:'.$foundTag.']', \App\View::displayBlock($block['blockId']), $newStr);
					}
				}
			}
		}
		
		
		return $newStr;
	}
	
	protected static function parsePageTags($str, $strip = false)
	{
		$model = new Core\Model;
		$newStr = $str;
		$tags = $model->getAll('page_tags');

		preg_match_all('/\[(.+?)\]/',$str,$matches);

		foreach($matches[1] as $mk => $match){
			$checkPos = strpos($match, ':');
			
			if($checkPos !== false){
				$exp = array(substr($match, 0, $checkPos), substr($match, ($checkPos + 1)));
				foreach($tags as $tag){
					if($tag['tag'] == trim($exp[0])){
						if($strip){
							$newStr = str_replace('['.$match.']', '', $newStr);
							continue 2;
						}
						$paramData = array();
						if(isset($exp[1])){
							$params = explode(',', $exp[1]);
							foreach($params as $param){
								$expP = explode('=', $param);
								if(isset($expP[1])){
									$expVals = explode('|', $expP[1]);
									if(isset($expVals[1])){
										$paramData[$expP[0]] = $expVals;
									}
									else{
										$paramData[$expP[0]] = $expP[1];
									}
								}
								else{
									$paramData[] = $param;
								}
							}
						}		
					
						$tag['class'] = 'Tags\\'.$tag['class'];			
						$class = new $tag['class']($paramData);
						$class->params = $paramData;
						$newStr = str_replace('['.$match.']', $class->display(), $newStr);
					}
				}
			}
			else{
				foreach($tags as $tag){
					if($tag['tag'] == trim($match)){
						if($strip){
							$newStr = str_replace('['.$match.']', '', $newStr);
							continue 2;
						}
							
						$tag['class'] = 'Tags\\'.$tag['class'];			
						$class = new $tag['class'];
						$newStr = str_replace('['.$match.']', $class->display(), $newStr);					
					}
				}
			}
		}
		return $newStr;
	}
}
