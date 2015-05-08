<?php
namespace UI;
use Util;
class Table extends Object
{
	protected $columns = array();
	protected $data = array();
	protected $columnOpts = array();
	protected $actions = array();
	protected $usePaging = 0;
	protected $pagedData = array();
	protected $numPages = 0;
	protected $pagerUrl = '';
	protected $rowClasses = array();
	
	
	function __construct()
	{
		parent::__construct();
		
	}
	
	public function addRowClass($key, $class)
	{
		$this->rowClasses[$key] = $class;
	}
	
	public function display()
	{
		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();
		
		$output = '<table '.$idText.' '.$classText.' '.$attributeText.'>';
		
		//create headings
		$output .= '<thead><tr>';
		
		foreach($this->columns as $key => $column){
			$output .= '<th>'.$column.'</th>';
		}
		
		foreach($this->actions as $action){
			$output .= '<th>'.$action['heading'].'</th>';
		}
		
		$useData = $this->data;
		if($this->usePaging == 1){
			if(isset($_GET['page']) AND isset($this->pagedData[$_GET['page']])){
				$useData = $this->pagedData[$_GET['page']];
			}
			else{
				$useData = $this->pagedData[1];
			}
			
		
		}
		
		$output .= '</tr></thead>
					<tbody>';
		
		foreach($useData as $rowKey => $row){
			
			$rowClass = '';
			if(isset($this->rowClasses[$rowKey])){
				$rowClass = 'class="'.$this->rowClasses[$rowKey].'"';
			}
			
			$output .= '<tr '.$rowClass.'>';
			
			foreach($this->columns as $key => $column){
				
				if(!isset($row[$key])){
					$output .= '<td></td>';
					continue;
				}
				
				$value = $row[$key];
				
				if(isset($this->columnOpts[$key])){
					
					$thisOpts = $this->columnOpts[$key];
					
					if(isset($thisOpts['functionWrap'])){
						$value = $thisOpts['functionWrap']($value);
					}
					
					if(isset($thisOpts['preText'])){
						$value = $thisOpts['preText'].$value;
					}
					
					if(isset($thisOpts['postText'])){
						$value = $value.$thisOpts['postText'];
					}
					
					
				}
				
				
				$output .= '<td>'.$value.'</td>';
			}
			
			foreach($this->actions as $action){
					
					$actionText = $action['text'];
					$actionClass = '';
					if($action['class'] != ''){
						$actionClass = 'class="'.$action['class'].'"';
					}
					$actionTarget = '';
					if($action['target'] != ''){
						$actionTarget = 'target="'.$action['target'].'"';
					}
					
					if($action['url'] != ''){
						$actionText = '<a href="'.$action['url'].$row[$action['dataKey']].'" '.$actionTarget.' '.$actionClass.'>'.$actionText.'</a>';
					}
					
					$output .= '<td>'.$actionText.'</td>';
				
			}
			
			$output .= '</tr>';
		}
		
		$output .= '</tbody></table>';
		
		if($this->usePaging == 1){
			
			$currPage = 1;
			if(isset($_GET['page'])){
				$currPage = $_GET['page'];
			}
			
		
			$pager = new Pager;
			$output .= $pager->display($this->numPages, $this->pagerUrl, $currPage);

		}
		
		return $output;
		
	}
	
	/**
	 *  add a column to table
	 *  key should be the same as the data key
	 * 
	 * */
	public function addColumn($key, $heading)
	{
		$this->columns[$key] = $heading;
		
	}
	
	public function removeColumn($key)
	{
		unset($this->columns[$key]);
		
	}
	
	public function setColumns($columns)
	{
		$this->columns = $columns;
		
	}
	
	public function getColumns()
	{
		return $this->columns;
	}
	
	public function setData($data)
	{
		$this->data = $data;
	}
	
	public function setColumnOpts($key, $options)
	{
		$this->columnOpts[$key] = $options;

	}
	
	public function addAction($text, $dataKey, $heading = '', $url = '', $class = '', $target = '')
	{
		$this->actions[] = array('text' => $text, 'dataKey' => $dataKey, 'heading' => $heading, 'url' => $url, 'class' => $class, 'target' => $target);
	}
	
	public function pageData($perPage, $pageUrl = '#')
	{
		$this->usePaging = 1;
		$this->numPages = ceil(count($this->data) / $perPage);
		$this->pagerUrl = $pageUrl;
		$util = new Util\Paging;
		$this->pagedData  = $util->pageArray($this->data, $perPage);
		
		
	}
	
	public function getData()
	{
		return $this->data;
	}
	
	
}

