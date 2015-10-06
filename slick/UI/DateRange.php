<?php
namespace UI;
class DateRange extends FormObject
{

	protected $minYear = 1969;
	protected $maxYear = 2030;
	protected $minDate = 0;
	protected $maxDate = 0;

	function __construct($name, $id = '')
	{
		parent::__construct();
		$this->name = $name;
		$this->id = $id;
		
	}
	
	public function display($elemWrap = ''){
		
		$date1 = new Date($this->name.'-start', $this->name.'-start');
		$date2 = new Date($this->name.'-end', $this->name.'-end');
		
		$date1->setMinYear($this->minYear);
		$date2->setMinYear($this->minYear);
		$date1->setMaxYear($this->maxYear);
		$date2->setMaxYear($this->maxYear);
		
		if($this->minDate != 0){
			$date1->setValue($this->minDate);
		}
		if($this->maxDate != 0){
			$date2->setValue($this->maxDate);
		}
		
		$date1->setLabel('Start Date');
		$date2->setLabel('End Date');
		
		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();				
		
		$output = $this->label.'<div class="'.$this->name.'" '.$idText.' '.$classText.' '.$attributeText.'>'.$date1->display().$date2->display().'</div>';
	
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
	
	public function setMinDate($date)
	{
		$this->minDate = $date;
	}
	
	public function setMaxDate($date)
	{
		$this->maxDate = $date;
	}
	
	public function getMinDate()
	{
		return $this->minDate;
	}
	
	public function getMaxDate()
	{
		return $this->maxDate;
	}

}
