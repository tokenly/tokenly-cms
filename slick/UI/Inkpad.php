<?php
namespace UI;
class Inkpad extends FormObject
{
	private $inkpad = false;
	const INK_URL = 'http://www.inkpad.io';
	
	function __construct($name, $id = '')
	{
		parent::__construct();
		$this->name = $name;
		if($id == ''){
			$id = $name;
		}
		$this->id = $id;
		
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
		
		if(!$this->inkpad){
			$output = $this->label.'<textarea name="'.$this->name.'" '.$idText.' '.$classText.' '.$attributeText.'>'.$this->value.'</textarea>';
		}
		else{
			$output = $this->label.'<div class="inkpad-cont"><input type="hidden" name="'.$this->name.'_inkpad" value="'.$this->inkpad.'" /><iframe src="'.self::INK_URL.'/'.$this->inkpad.'/edit"></iframe></div>';
		}
		
		if($elemWrap != ''){
			$misc = new Misc;
			$output = $misc->wrap($elemWrap, $output);
		}
		
		return $output;
	}
	
	public function getInkpad()
	{
		return $this->inkpad;
	}
	
	public function setInkpad($url)
	{
		$this->inkpad = $url;
	}
	
	public function setValue($value)
	{
		if(!$this->inkpad){
			$this->value = $value;
		}
		else{
			//do nothing
		}
	}
	
	public function getValue()
	{
		if(!$this->inkpad OR trim($this->inkpad) == ''){
			return $this->value;
		}
		else{
			$get = file_get_contents(self::INK_URL.'/'.$this->inkpad.'.md');

			if(!$get){
				return false;
			}
			
			return $get;
			
		}
	}
	
	public static function getNewPad()
	{
		$inkUrl = self::INK_URL.'/pads';
		$curl = curl_init();
		curl_setopt($curl, CURLOPT_URL, $inkUrl);
		curl_setopt($curl, CURLOPT_POST, 1); 
		curl_setopt($curl, CURLOPT_RETURNTRANSFER, true);
		curl_setopt($curl, CURLOPT_VERBOSE, 1);
		curl_setopt($curl, CURLOPT_HEADER, 1);		
		curl_setopt($curl, CURLOPT_USERAGENT, 'Mozilla/4.0 (compatible; MSIE 6.0; Windows NT 5.1; SV1; .NET CLR 1.0.3705; .NET CLR 1.1.4322)');
		curl_setopt($curl, CURLOPT_POSTFIELDS, '');

		$exec = curl_exec($curl);
		curl_close($curl);

		if(!$exec){
			return false;
		}
		
		$url = false;
		$response = explode("\n", $exec);
		foreach($response as $row){
			$pos = strpos($row, 'Location:');
			if($pos === 0){
				$row = str_replace('Location: ', '', $row);
				$row = str_replace('/edit', '', $row);
				$row = str_replace('/', '', $row);
				if(trim($row) != ''){
					$url = trim($row);
				}
			}
		}
		
		return $url;
	}

}
