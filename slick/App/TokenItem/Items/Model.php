<?php
namespace App\TokenItem;
use Core, UI, Util;
class Items_Model extends Core\Model
{
	protected function getItemForm()
	{
		$form = new UI\Form;
		$form->setFileEnc();
		
		$label = new UI\Textbox('name');
		$label->setLabel('Item Name');
		$form->add($label);
		
		$slug = new UI\Textbox('slug');
		$slug->setLabel('Slug / URI');
		$slug->addAttribute('placeholder', '(leave blank to auto generate)');
		$form->add($slug);
		
		$token = new UI\Textbox('token');
		$token->setLabel('Access Token');
		$form->add($token);
		
		$min_token = new UI\Textbox('min_token');
		$min_token->setLabel('Minimum Token Amount Required');
		$form->add($min_token);						
			
		$desc = new UI\Markdown('description', 'markdown');
		$desc->setLabel('Description');
		$form->add($desc);
		
		$image = new UI\File('image');
		$image->setLabel('Image');
		$form->add($image);		 

		$rank = new UI\Textbox('rank');
		$rank->setLabel('Order Rank');
		$rank->setValue(0);
		$form->add($rank);			
		
		$propModel = new Properties_Model;
		$getProps = $propModel->getActivePropertyTypes();
		if($getProps AND count($getProps) > 0){
			foreach($getProps as $type){
				$field = false;
				switch($type['type']){
					case 'text':
						$field = new UI\Textbox('prop_'.$type['id']);
						break;
					case 'select':
						$field = new UI\Select('prop_'.$type['id']);
						$opts = explode("\n", $type['options']);
						$field->addOption('', '[none]');
						foreach($opts as $opt){
							$opt = trim($opt);
							$field->addOption($opt, $opt);
						}
						break;
					default:
						continue 2;
				}
				if(!$field){
					continue;
				}
				$field->setLabel($type['name']);
				$form->add($field);
			}
		}
		
		$active = new UI\Checkbox('active', 'active');
		$active->setLabel('Active?');
		$active->setBool(true);
		$active->setValue(1);
		$form->add($active);		
		
		return $form;
	}
	
	protected function addTokenItem($data)
	{
		if(!isset($data['name']) OR trim($data['name']) == ''){
			throw new \Exception('Name required');
		}
		
		if(!isset($data['token']) OR trim($data['token']) == '' OR !isset($data['min_token'])){
			throw new \Exception('Access token required');
		}
		
		$insertData = array();
		$insertData['name'] = trim(htmlentities($data['name']));
		
		if(!isset($data['slug']) OR trim($data['slug']) == ''){
			$data['slug'] = $data['name'];
		}
		$insertData['slug'] = genURL($data['slug']);
		$insertData['slug'] = $this->container->checkSlugExists($insertData['slug']);		
		
		$insertData['token'] = trim(strtoupper($data['token']));
		$insertData['min_token'] = round(floatval($data['min_token']) * SATOSHI_MOD);
		
		$active = 0;
		if(isset($data['active']) AND intval($data['active']) === 1){
			$active = 1;
		}
		$insertData['active'] = $active;
		
		if(isset($data['description'])){
			$insertData['description'] = trim(htmlentities($data['description']));
		}

		$insertData['created_at'] = timestamp();
		$insertData['updated_at'] = timestamp();

		if(isset($data['rank'])){
			$insertData['rank'] = intval($data['rank']);
		}

		
		$insert = $this->insert('token_items', $insertData);
		if(!$insert){
			throw new \Exception('Error adding token item');
		}
		
		$this->container->uploadImage($insert);
		$this->container->updateItemProperties($insert, $data);		
		
		$insertData['id'] = $insert;
		return $insertData;
	}
	
	protected function editTokenItem($id, $data)
	{
		if(!isset($data['name']) OR trim($data['name']) == ''){
			throw new \Exception('Name required');
		}
		
		if(!isset($data['token']) OR trim($data['token']) == '' OR !isset($data['min_token'])){
			throw new \Exception('Access token required');
		}
		
		$updateData = array();
		$updateData['name'] = trim(htmlentities($data['name']));
		
		if(!isset($data['slug']) OR trim($data['slug']) == ''){
			$data['slug'] = $data['name'];
		}
		$updateData['slug'] = genURL($data['slug']);
		$updateData['slug'] = $this->container->checkSlugExists($updateData['slug'], $id);	
		
		$updateData['token'] = trim(strtoupper($data['token']));
		$updateData['min_token'] = round(floatval($data['min_token']) * SATOSHI_MOD);					
		
		$active = 0;
		if(isset($data['active']) AND intval($data['active']) === 1){
			$active = 1;
		}		
		$updateData['active'] = $active;
		
		if(isset($data['description'])){
			$updateData['description'] = trim(htmlentities($data['description']));
		}

		if(isset($data['rank'])){
			$updateData['rank'] = intval($data['rank']);
		}	

		$updateData['updated_at'] = timestamp();

		$edit = $this->edit('token_items', $id, $updateData);
		if(!$edit){
			throw new \Exception('Error editing Token Item');
		}
		
		$this->container->uploadImage($id);		
		$this->container->updateItemProperties($id, $data);		

		return true;
	}
	
	protected function uploadImage($id)
	{
		if(isset($_FILES['image']['tmp_name']) AND trim($_FILES['image']['tmp_name']) != ''){
			$ext = 'jpg';
			if(isset($_FILES['image']['type'])){
				switch($_FILES['image']['type']){
					case 'image/jpg':
					case 'image/jpeg':
						$ext = 'jpg';
						break;
					case 'image/gif':
						$ext = 'gif';
						break;
					case 'image/png':
						$ext = 'png';
						break;
				}
			}
			$fileName = 'ad-'.md5($id.'-'.$_FILES['image']['name']).'.'.$ext;
			$dir = SITE_PATH.'/files/tokenitems';
			if(!is_dir($dir)){
				@mkdir($dir, 755);
			}
			$move = move_uploaded_file($_FILES['image']['tmp_name'], $dir.'/'.$fileName);
			if(!$move){
				throw new \Exception('Error uploading image');
			}
			$save = $this->edit('token_items', $id, array('image' => $fileName));
			if(!$save){
				throw new \Exception('Error saving uploaded image');
			}
			return true;
		}
		
	}	
	
	protected function checkSlugExists($slug, $ignore = 0, $count = 0)
	{
		$useurl = $slug;
		if($count > 0){
			$useurl = $slug.'-'.$count;
		}
		$get = $this->get('token_items', $useurl, array('id', 'slug'), 'slug');
		if($get AND $get['id'] != $ignore){
			//url exists already, search for next level of url
			$count++;
			return $this->container->checkSlugExists($slug, $ignore, $count);
		}
		if($count > 0){
			$slug = $slug.'-'.$count;
		}
		return $slug;
	}	
	
	protected function updateItemProperties($id, $data)
	{
		$prop_data = array();
		foreach($data as $k => $val){
			$exp_k = explode('_', $k);
			if($exp_k[0] == 'prop' AND isset($exp_k[1])){
				$prop_data[$exp_k[1]] = $val;
			}
		}
		if(count($prop_data) == 0){
			return false;
		}
		foreach($prop_data as $propId => $val){
			$getProp = $this->get('token_itemPropertyTypes', $propId);
			if(!$getProp){
				continue;
			}
			$update = $this->container->updateItemProperty($id, $propId, $val);
			if(!$update){
				throw new \Exception('Error updating item propery '.$getProp['name']);
			}
		}
		return true;
	}
	
	protected function updateItemProperty($id, $propId, $val)
	{
		$get = $this->fetchSingle('SELECT * FROM token_itemProperties WHERE itemId = :id AND propertyId = :propId',
								array(':id' => $id, ':propId' => $propId));
		
		$values = array();
		$values['value'] = trim($val);
		$values['updated_at'] = timestamp();
		if($get){
			//update
			$update = $this->edit('token_itemProperties', $get['id'], $values);
		}
		else{
			//insert
			$values['itemId'] = $id;
			$values['propertyId'] = $propId;
			$update = $this->insert('token_itemProperties', $values);
		}
		return $update;
	}
	
	protected function getItemFormProperties($id)
	{
		$output = array();
		$get = $this->getAll('token_itemProperties', array('itemId' => $id));
		if(!$get OR count($get) == 0){
			return $output;
		}
		foreach($get as $prop){
			$output['prop_'.$prop['propertyId']] = $prop['value'];
		}
		return $output;
	}
	
	protected function getItemProperties($id)
	{
		$get = $this->fetchAll('SELECT p.id, p.name, p.description, p.type, p.options, p.created_at,
										ip.updated_at, ip.value, ip.id as valueId, p.rank
								FROM token_itemProperties ip
								LEFT JOIN token_itemPropertyTypes p ON p.id = ip.propertyId
								WHERE ip.itemId = :id
								GROUP BY p.id
								ORDER BY p.rank ASC, p.name ASC', array(':id' => $id));
		return $get;
	}
	
}
