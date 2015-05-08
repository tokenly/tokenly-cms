<?php
namespace UI;
class Pager extends Object
{
	function __construct()
	{
		parent::__construct();
		
	}
	
	public function display($numPages = 0, $url = '#', $curPage = 0, $seperator = '', $append = '')
	{
		if($numPages == 1){
			return '';
		}
		
		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();		
		
		$output = '<div '.$classText.' '.$idText.' '.$attributeText.'>
						<span>Pages </span>';
		
		for($i = 1; $i <= $numPages; $i++){
			
			$isCurr = '';
			if($i == $curPage){
				$isCurr = 'class="current"';
			}
			
			if($url == '#'){
				$output .= '<a href="#" value="'.$i.'" '.$isCurr.'>'.$i.'</a> ';
			}
			else{
			
				$output .= '<a href="'.$url.$i.$append.'"  '.$isCurr.'>'.$i.'</a> ';
			}
			
			if($i != $numPages){
				$output .= $seperator;
			}
		}
		
		$output .= '</div>';
		
		return $output;
		
	}
}
