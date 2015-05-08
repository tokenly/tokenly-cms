<?php
namespace UI;
class Misc
{
		public function link($url, $text = '', $class = '', $id = '')
		{
			$link = new Link;
			if(is_array($class)){
				$link->setClasses($class);
			}
			else{
				$link->addClass($class);
			}
			
			$link->setID($id);
			
			return $link->display($url, $text);
			
		}
		
		public function img($src, $alt = '', $class = '', $id = '')
		{
			$img = new Img;
			if(is_array($class)){
				$img->setClasses($class);
			}
			else{
				$img->addClass($class);
			}
			
			$img->setID($id);
			
			return $img->display($src, $alt);			
			
		}
		
		public function wrap($elem, $contents = '', $class = '', $id = '')
		{
			$classText = '';
			if($class != ''){
				$classText = 'class="'.$class.'"';
			}
			
			$idText = '';
			if($id != ''){
				$idText = 'id="'.$id.'"';
			}
			
			$output = '<'.$elem.' '.$classText.' '.$idText.'>'.$contents.'</'.$elem.'>';
			
			return $output;
			
		}
}
