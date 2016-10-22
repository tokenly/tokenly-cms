<?php
namespace App\Forum;
use Core, UI, Util, App\Tokenly;
class Boards_Model extends Core\Model
{

	protected function getBoardForm($boardId = false)
	{
		$form = new UI\Form;
		$form->setFileEnc();
        $site = currentSite();
        $siteId = $site['siteId'];
		
		$getCats = $this->getAll('forum_categories', array('siteId' => $siteId), array(), 'rank', 'asc');
		$categoryId = new UI\Select('categoryId');
		$categoryId->setLabel('Board Category');
		$categoryId->addOption(0, '[none]');
		foreach($getCats as $cat){
			$categoryId->addOption($cat['categoryId'], $cat['name']);
		}
		$form->add($categoryId);
        
        $parent_boards = array();
        $parent_boards = $this->getBoardFormParentList($boardId);
        $user = user();
        $forum_app = get_app('forum');
        $perms = \App\Meta_Model::getUserAppPerms($user['userId'], $forum_app['appId']);
        if(!isset($perms['canChangeAnyParentBoard']) OR !$perms['canChangeAnyParentBoard']){
            foreach($parent_boards as $k => $board){
                if($board['ownerId'] != $user['userId']){
                    unset($parent_boards[$k]);
                    continue;
                }
            }
        }
        
        $parentId = new UI\Select('parentId');
        $parentId->setLabel('Parent Board');
        $parentId->addOption(0, '[none]');
        if($parent_boards AND count($parent_boards) > 0){
            foreach($parent_boards as $p_board){
                if($p_board['boardId'] == $boardId){
                    continue;
                }
                $parentId->addOption($p_board['boardId'], $p_board['name']);
            }
        }
        $form->add($parentId);        
		
		$name = new UI\Textbox('name');
		$name->addAttribute('required');
		$name->setLabel('Board Name');
		$form->add($name);
		
		$slug = new UI\Textbox('slug');
		$slug->setLabel('Slug / URL (blank to auto generate)');
		$form->add($slug);	
		
		$ownerId = new UI\Select('ownerId');
		$ownerId->setLabel('Board Owner');
		$ownerId->addOption(0, '[nobody]');
		$getUsers = $this->getAll('users');
		foreach($getUsers as $user){
			$ownerId->addOption($user['userId'], $user['username']);
		}
		$form->add($ownerId);
		
		$rank = new UI\Textbox('rank');
		$rank->setLabel('Order Rank');
		$form->add($rank);
		
		$active = new UI\Checkbox('active');
		$active->setLabel('Board Active?');
		$active->setBool(1);
		$active->setValue(1);
		$form->add($active);
		
		$description = new UI\Markdown('description', 'markdown');
		$description->setLabel('Description (use markdown)');
		$form->add($description);
		
		return $form;
	}
	
    protected function getBoardFormParentList($boardId = 0, $boards = false, $output = array(), $indent = 0)
    {
        if($boards === false){
            $boards = $this->getBoardParentTree(0, 0, false);
        }
 	foreach($boards as $board){
 		if($board['boardId'] == $boardId){
 			continue;
 		}
 		$indenter = '';
 		if($indent !== false){
 			for($i = 0; $i < $indent; $i++){
 				$indenter .= '---- ';
 			}
 			
 		}
            $row = $board;
            $row['name'] = $indenter.$board['name'];
 		$output[] = $row;
 		if(isset($board['children']) AND count($board['children']) > 0){
 			$newIndent = $indent;
 			if($indent !== false){
 				$newIndent = $indent+1;
 			}
 			$output = $this->container->getBoardFormParentList($boardId, $board['children'], $output, $newIndent);
 		}
 	}
 	
 	return $output;
 }    
    
    protected function getBoardParentTree($parentId = 0, $menuMode = 0, $use_tca = true, $categoryId = false)
    {
 	$thisUser = user();
 	$tca = new Tokenly\TCA_Model;
 	$boardModule = get_app('forum.forum-board');
 	$getSite = currentSite();
 	
        $values = array(':siteId' => $getSite['siteId'], ':parentId' => $parentId);
        
        $andCategory = '';
        if($categoryId){
            $andCategory = ' AND b.categoryId = :categoryId';
            $values[':categoryId'] = $categoryId;
        }
        
        $andActive = '';
        if($use_tca){
            $andActive = ' AND b.active = :active';
            $values[':active'] = 1;
        }
        
        $get = $this->fetchAll('SELECT b.*, c.name as category
                                 FROM forum_boards b
                                 LEFT JOIN forum_categories c ON c.categoryId = b.categoryId
                                 WHERE b.siteId = :siteId AND b.parentId = :parentId '.$andCategory.' '.$andActive.'
                                 ORDER BY c.rank ASC, b.categoryId ASC, b.rank ASC', $values);
 	foreach($get as $key => $row){
  		if($use_tca){
 			$boardTCA = $tca->checkItemAccess($thisUser, $boardModule['moduleId'], $row['boardId'], 'board');
 			$catTCA = $tca->checkItemAccess($thisUser, $boardModule['moduleId'], $row['categoryId'], 'category');
 			if(!$boardTCA OR !$catTCA){
 				unset($get[$key]);
 				continue;
 			}	
 		}
 		
 		$getChildren = $this->container->getBoardParentTree($row['boardId'], $menuMode, $use_tca, $categoryId);
 		if(count($getChildren) > 0){
 			$get[$key]['children'] = $getChildren;
 		}
 		if($menuMode == 1){
 			$get[$key]['target'] = '';
 			$get[$key]['url'] = $getSite['url'].'/blog/category/'.$row['slug'];
 			$get[$key]['label'] = $row['name'];
 			$get[$key]['value'] = $row['boardId'];
 		}
        
 	}
 	return $get;
    }
    


	protected function addBoard($data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'rank' => false, 'description' => false, 'active' => false, 'categoryId' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new \Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		if(!isset($useData['slug']) OR trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['name']);
		}
		$useData['slug'] = strip_tags($useData['slug']);
		$useData['slug'] = $this->container->checkDupeSlug($useData['slug']);
		$useData['name'] = strip_tags($useData['name']);
		$useData['description'] = strip_tags($useData['description']);
		
		if(isset($data['ownerId'])){
			$useData['ownerId'] = $data['ownerId'];
		}
        
        if(isset($data['parentId'])){
            $data['parentId'] = intval($data['parentId']);
            if($data['parentId'] > 0){
                $get_parent = $this->get('forum_boards', $data['parentId']);
                if(!$get_parent){
                    throw new \Exception('Parent board not found');
                }
                $user = user();
                $forum_app = get_app('forum');
                $perms = \App\Meta_Model::getUserAppPerms($user['userId'], $forum_app['appId']);                
                if($user['userId'] != $get_parent['ownerId']   
                    AND (!isset($perms['canChangeAnyParentBoard']) OR !$perms['canChangeAnyParentBoard'])){
                        throw new \Exception('Cannot choose parent board you do not own');
                }
                if(!isset($perms['canChangeParentBoard']) OR !$perms['canChangeParentBoard']){
                    throw new \Exception('You do not have permission to create child boards');
                }
                $useData['categoryId'] = $get_parent['categoryId']; //force set to parent boards category
                $useData['parentId'] = $get_parent['boardId'];
            }
        }        
		
		$add = $this->insert('forum_boards', $useData);
		if(!$add){
			throw new \Exception('Error adding board');
		}
		
		return $add;
		
		
	}
		
	protected function editBoard($id, $data)
	{
		$req = array('name' => true, 'slug' => false, 'siteId' => true, 'description' => false, 'active' => false);
		$useData = array();
		foreach($req as $key => $required){
			if(!isset($data[$key])){
				if($required){
					throw new \Exception(ucfirst($key).' required');
				}
				else{
					$useData[$key] = '';
				}
			}
			else{
				$useData[$key] = $data[$key];
			}
		}
		
		if(isset($data['categoryId'])){
			$useData['categoryId'] = $data['categoryId'];
		}
		
		if(isset($data['rank'])){
			$useData['rank'] = intval($data['rank']);
		}
		
		if(!isset($useData['slug']) OR trim($useData['slug']) == ''){
			$useData['slug'] = genURL($useData['name']);
		}
		$useData['slug'] = strip_tags($useData['slug']);
		$useData['slug'] = $this->container->checkDupeSlug($useData['slug'], $id);
		$useData['name'] = strip_tags($useData['name']);
		$useData['description'] = strip_tags($useData['description']);
		
		if(isset($data['ownerId'])){
			$useData['ownerId'] = $data['ownerId'];
		}
        
        $useData['active'] = 0;
        if(trim($data['active']) != '' AND intval($data['active']) == 1){
            $useData['active'] = 1;
        }
        
        $useData['parentId'] = 0;
        if(isset($data['parentId'])){
            $data['parentId'] = intval($data['parentId']);
            if($data['parentId'] > 0){
                $get_parent = $this->get('forum_boards', $data['parentId']);
                if(!$get_parent){
                    throw new \Exception('Parent board not found');
                }
                $user = user();
                $forum_app = get_app('forum');
                $perms = \App\Meta_Model::getUserAppPerms($user['userId'], $forum_app['appId']);                
                if($user['userId'] != $get_parent['ownerId']   
                    AND (!isset($perms['canChangeAnyParentBoard']) OR !$perms['canChangeAnyParentBoard'])){
                        throw new \Exception('Cannot choose parent board you do not own');
                }
                if(!isset($perms['canChangeParentBoard']) OR !$perms['canChangeParentBoard']){
                    throw new \Exception('You do not have permission to create child boards');
                }
                $useData['categoryId'] = $get_parent['categoryId']; //force set to parent boards category
                $useData['parentId'] = $get_parent['boardId'];
            }
        }           
		
		$edit = $this->edit('forum_boards', $id, $useData);
		if(!$edit){
			throw new \Exception('Error editing board');
		}
        
        if(isset($useData['categoryId'])){
            $this->updateChildCategories(intval($id), $useData['categoryId']);
        }        
			
		return true;
		
	}
    
    protected function updateChildCategories($parentId, $categoryId)
    {
        $get = $this->getAll('forum_boards', array('parentId' => $parentId));
        $update = $this->sendQuery('UPDATE forum_boards SET categoryId = :categoryId WHERE parentId = :parentId',
                        array(':categoryId' => $categoryId, ':parentId' => $parentId));
        if($update AND $get AND count($get) > 0){
            foreach($get as $row){
                $this->container->updateChildCategories($row['boardId'], $categoryId);
            }
        }
    }    


	protected function getBoardMods($boardId)
	{
		$sql = 'SELECT u.userId, u.username, u.email, u.slug
				FROM forum_mods m
				LEFT JOIN users u ON m.userId = u.userId
				WHERE m.boardId = :boardId
				ORDER BY u.username ASC';
		$get = $this->fetchAll($sql, array(':boardId' => $boardId));
		return $get;
	}

	protected function getModForm()
	{
		$form = new UI\Form;
		
		$id = new UI\Textbox('userId');
		$id->setLabel('Add New Moderator');
		$id->addAttribute('placeholder', 'Username or User ID');
		$form->add($id);
		
		$form->setSubmitText('Add Mod');
		
		return $form;
	}
	
	protected function addMod($boardId, $userId)
	{
		
		$userId = trim($userId);
		$get = $this->get('users', $userId, array(), 'username');
		if(!$get){
			$get = $this->get('users', intval($userId));
			if(!$get){
				throw new \Exception('User not found');
			}
		}
		
		$getMod = $this->getAll('forum_mods', array('userId' => $userId, 'boardId' => $boardId));
		if(count($getMod) > 0){
			throw new \Exception('User already a moderator');
		}
		
		return $this->insert('forum_mods', array('userId' => $get['userId'], 'boardId' => $boardId));
	}
	
	protected function checkDupeSlug($slug, $boardId = 0)
	{
		$sql = 'SELECT count(*) as total FROM forum_boards WHERE slug LIKE :slug';
		$values = array(':slug' => $slug.'%');
		if($boardId != 0){
			$sql .= ' AND boardId != :boardId';
			$values[':boardId'] = $boardId;
		}
		
		$get = $this->fetchSingle($sql,$values);
		if(!$get OR $get['total'] == 0){
			return $slug;
		}
		return $slug.'-'.($get['total']+1);
	}
}
