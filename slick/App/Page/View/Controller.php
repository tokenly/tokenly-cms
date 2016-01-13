<?php
namespace App\Page;
use App\Tokenly;
class View_Controller extends \App\ModControl
{
    function __construct()
    {
        parent::__construct();
        $this->model = new View_Model;
        $this->tca = new Tokenly\TCA_Model;
    }
    
    protected function init()
    {
		$output = parent::init();
		if($this->itemId != null){
			$get = $this->model->get('page_index', $this->itemId, array(), 'itemId');
			if(!$get){
				$output['view'] = '404';
				return $output;
			}
			
			$checkTCA = $this->tca->checkItemAccess($this->data['user'], $this->data['module']['moduleId'], $this->itemId, 'page');
			if(!$checkTCA){
				$output['view'] = '403';
				return $output;
			}
			$getPage = $this->model->getPageData($this->itemId);
			if($getPage){
				$output = array_merge($getPage, $output);
				$output['view'] = 'page';
				if($this->data['user']){
					Tokenly\POP_Model::recordFirstView($this->data['user']['userId'], $this->data['module']['moduleId'], $this->itemId);
				}
			}
			else{
				$output['view'] = '404';
			}
		}
		else{
			$output['view'] = '404';
		}
		return $output;
	}
}
