<?php
namespace UI;
class DateTime extends FormObject
{
	
	protected $minYear = 1969;
	protected $maxYear = 2030;
	protected $year = 1969;
	protected $month = 1;
	protected $day = 1;
	protected $hour = 0;
	protected $minute = 0;
	protected $second = 0;
	
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
		
		$hourSelect = new Select($this->name.'-hour', $this->name.'-hour');
		$minSelect = new Select($this->name.'-minute', $this->name.'-minute');
		$secSelect = new Select($this->name.'-second', $this->name.'-second');
		
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
		
		for($i = 0; $i <= 23; $i++){
			if($i < 10){
				$hourSelect->addOption('0'.$i, '0'.$i);	
			}
			else{
				$hourSelect->addOption($i, $i);
			}
		}

		for($i = 0; $i <= 59; $i++){
			if($i < 10){
				$minSelect->addOption('0'.$i, '0'.$i);	
			}
			else{
				$minSelect->addOption($i, $i);
			}
		}

		for($i = 0; $i <= 59; $i++){
			if($i < 10){
				$secSelect->addOption('0'.$i, '0'.$i);	
			}
			else{
				$secSelect->addOption($i, $i);
			}
		}

		$dSelect->setSelected($this->day);
		$mSelect->setSelected($this->month);
		$ySelect->setSelected($this->year);
		$hourSelect->setSelected($this->hour);
		$minSelect->setSelected($this->minute);
		$secSelect->setSelected($this->second);

		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();		
		
		$output = $this->label.'<div class="'.$this->name.' DateTime" '.$idText.' '.$classText.' '.$attributeText.'>'.$mSelect->display().$dSelect->display().$ySelect->display().$hourSelect->display().$minSelect->display().$secSelect->display().'</div>';
		
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
	
	public function setHour($hour)
	{
		$this->hour = $hour;
	}
	
	public function getHour()
	{
		return $this->hour;
	}
	
	public function setMinute($min)
	{
		$this->minute = $min;
	}
	
	public function getMinute()
	{
		return $this->minute;
	}
	
	public function setSecond($sec)
	{
		$this->second = $sec;
	}
	
	public function getSecond()
	{
		return $this->second;
	}
	
	public function setValue($value)
	{
		parent::setValue($value);
		$explode = explode('-', $value);
		if(count($explode) > 1){
			$this->setYear($explode[0]);
			$this->setMonth($explode[1]);
			
			$expDay = explode(' ', trim($explode[2]));
			$this->setDay($expDay[0]);
			
			$expTime = explode(':', trim($expDay[1]));
			$this->setHour($expTime[0]);
			$this->setMinute($expTime[1]);
			$this->setSecond($expTime[2]);
		}
	}
	
	public function getPostValue()
	{
		if(!posted()){
			return false;
		}
		
		$year = $_POST[$this->name.'-year'];
		$month = $_POST[$this->name.'-month'];
		$day = $_POST[$this->name.'-day'];
		$hour = $_POST[$this->name.'-hour'];
		$min = $_POST[$this->name.'-minute'];
		$sec = $_POST[$this->name.'-second'];
		
		return $year.'-'.$month.'-'.$day.' '.$hour.':'.$min.':'.$sec;
	}

}
