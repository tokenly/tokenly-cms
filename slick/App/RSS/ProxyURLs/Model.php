<?php
class Slick_App_RSS_ProxyURLs_Model extends Slick_Core_Model
{

	public function getProxyForm($proxyId = 0)
	{
		$form = new Slick_UI_Form;
		
		$slug = new Slick_UI_Textbox('slug');
		$slug->addAttribute('required');
		$slug->setLabel('Slug');
		$form->add($slug);	

		$url = new Slick_UI_Textbox('url');
		$url->addAttribute('required');
		$url->setLabel('Feed URL');
		$form->add($url);

		return $form;
	}
	


	public function addProxy($data)
	{
		$req = array('slug', 'url');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		$add = $this->insert('proxy_url', $useData);
		if(!$add){
			throw new Exception('Error adding proxy');
		}
		
		return $add;
		
		
	}
		
	public function editProxy($id, $data)
	{
		$req = array('slug', 'url');
		$useData = array();
		foreach($req as $key){
			if(!isset($data[$key])){
				throw new Exception(ucfirst($key).' required');
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		
		$edit = $this->edit('proxy_url', $id, $useData);
		if(!$edit){
			throw new Exception('Error editing proxy');
		}
		
		
		return true;
		
	}





}

?>
