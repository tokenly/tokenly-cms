<?php
namespace App\RSS;
use Core, UI, Util;
class ProxyURLs_Model extends Core\Model
{

	protected function getProxyForm($proxyId = 0)
	{
		$form = new UI\Form;
		
		$slug = new UI\Textbox('slug');
		$slug->addAttribute('required');
		$slug->setLabel('Slug');
		$form->add($slug);	

		$url = new UI\Textbox('url');
		$url->addAttribute('required');
		$url->setLabel('Feed URL');
		$form->add($url);

		return $form;
	}
	
	protected function addProxy($data)
	{
		$req = array('slug', 'url');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		$add = $this->insert('proxy_url', $useData);
		if(!$add){
			throw new \Exception('Error adding proxy');
		}
		return $add;
	}
		
	protected function editProxy($id, $data)
	{
		$req = array('slug', 'url');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new \Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		$edit = $this->edit('proxy_url', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing proxy');
		}
		return true;
	}
}
