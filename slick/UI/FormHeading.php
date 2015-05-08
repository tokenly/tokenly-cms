<?php
namespace UI;
class FormHeading extends FormObject
{
	protected $heading = '';
	protected $level = 1;
	
	function __construct($heading = '', $level = 2)
	{
		parent::__construct();
		
		if($level <= 0 OR $level > 6){
			$level = 2;
		}
		
		$this->name = 'heading-'.genURL($heading);
		$this->heading = $heading;
		$this->level = $level;
	}
	
	public function display($elemWrap = '')
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
		
		$output = $this->label.'<h'.$this->level.' '.$idText.' '.$classText.' '.$attributeText.'>'.$this->heading.'</h'.$this->level.'>';
		
		if($elemWrap != ''){
			$misc = new Misc;
			$output = $misc->wrap($elemWrap, $output);
		}
		
		return $output;
	}
	
	public function setHeading($heading)
	{
		$this->heading = $heading;
	}
	
	public function getHeading()
	{
		return $this->heading;
	}
	
	public function setLevel($level)
	{
		$this->level = $level;
	}
	
	public function getLevel()
	{
		return $this->level;
	}

}
