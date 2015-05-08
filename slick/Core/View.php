<?php
namespace Core;
use UI;
class View
{
	public $misc;

	function __construct()
	{
		$this->misc = new UI\Misc;
	}
	
	public function generateTable($data, $params)
	{
		$table = new UI\Table;
		$table->setData($data);
		
		if(isset($params['class'])){
			$table->addClass($params['class']);
		}
		
		foreach($params['fields'] as $field => $heading){
			$table->addColumn($field, $heading);
		}
		
		if(isset($params['actions'])){
			foreach($params['actions'] as $action){
				
				if(!isset($action['class'])){
					$action['class'] = '';
				}
				if(!isset($action['heading'])){
					$action['heading'] = '';
				}
				if(!isset($action['target'])){
					$action['target'] = '';
				}
				
				$table->addAction($action['text'], $action['data'], $action['heading'], $action['url'], $action['class'], $action['target']);
			}
		}
		
		if(isset($params['options'])){
			foreach($params['options'] as $option){
				$table->setColumnOpts($option['field'], $option['params']);
			}
		}
		
		if(isset($params['page'])){
			$pageUrl = '#';
			if(isset($params['pageUrl'])){
				$pageUrl = $params['pageUrl'];
			}
			$table->pageData($params['page'], $pageUrl);
		}
		
		return $table;
	}
}
