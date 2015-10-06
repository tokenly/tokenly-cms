<?php
namespace UI;
class Date extends FormObject
{
	
	protected $minYear = 1969;
	protected $maxYear = 2030;
	protected $year = 1969;
	protected $month = 1;
	protected $day = 1;
	
	function __construct($name, $id = '')
	{
		parent::__construct();
		$this->name = $name;
		$this->id = $id;
		
	}
	
	public function display($elemWrap = '')
	{
		$dSelect = new Select($this->name.'-day', $this->name.'-day');
		$mSelect = new Select($this->name.'-month', $this->name.'-month');
		$ySelect = new Select($this->name.'-year', $this->name.'-year');
		
		for($i = 1; $i <= 31; $i++){
			$dSelect->addOption($i, $i);
		}
		
		$months = array(1 => 'January', 2 => 'February',  3 => 'March', 4 => 'April', 5  => 'May', 6 => 'June',
						7 => 'July', 8 => 'August', 9 => 'September', 10 => 'October', 11 => 'November', 12 => 'December');
		foreach($months as $mKey => $mVal){
			$mSelect->addOption($mKey, $mVal);
		}
		
		for($i = $this->minYear; $i <= $this->maxYear; $i++){
			$ySelect->addOption($i, $i);
		}
		
		$dSelect->setSelected($this->day);
		$mSelect->setSelected($this->month);
		$ySelect->setSelected($this->year);

		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();		
		
		$output = $this->label.'<div class="'.$this->name.'" '.$idText.' '.$classText.' '.$attributeText.'>'.$dSelect->display().$mSelect->display().$ySelect->display().'</div>';
		
		if($elemWrap != ''){
			$misc = new Misc;
			$output = $misc->wrap($elemWrap, $output, $this->wrap_class);
		}
		
		return $output;

	}
	
	public function setMinYear($year)
	{
		$this->minYear = $year;
	}
	
	public function setMaxYear($year)
	{
		$this->maxYear = $year;
	}
	
	public function getMinYear()
	{
		return $this->minYear;
	}
	
	public function getMaxYear()
	{
		return $this->maxYear();
	}
	
	public function setYear($year)
	{
		$this->year = $year;
	}
	
	public function getYear()
	{
		return $this->year;
	}
	
	public function setMonth($month)
	{
		$this->month = $month;
	}
	
	public function getMonth()
	{
		return $this->month;
	}
	
	public function setDay($day)
	{
		$this->day = $day;
	}
	
	public function getDay()
	{
		return $this->day;
	}
	
	public function setValue($value)
	{
		parent::setValue($value);
		
		$explode = explode('-', $value);
		$this->setYear($explode[0]);
		$this->setMonth($explode[1]);
		$this->setDay($explode[2]);
	}
	
	public function getPostValue()
	{
		if(!posted()){
			return false;
		}
		
		$year = $_POST[$this->name.'-year'];
		$month = $_POST[$this->name.'-month'];
		$day = $_POST[$this->name.'-day'];
		
		return $year.'-'.$month.'-'.$day;
	}

}
