<?php
class Slick_UI_Markdown extends Slick_UI_FormObject
{
	protected $livePreview = true;
	protected $previewTitle = 'Live Preview';
	
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
		
		$output = $this->label.'<textarea name="'.$this->name.'" '.$idText.' '.$classText.' '.$attributeText.'>'.$this->value.'</textarea>';
		
		if($this->livePreview){
			$output .= $this->displayLivePreview();
		}
		
		if($elemWrap != ''){
			$misc = new Slick_UI_Misc;
			$output = $misc->wrap($elemWrap, $output);
		}
		
		return $output;
	}
	
	public function displayLivePreview()
	{
		$sitePath = '';
		$model = new Slick_Core_Model;
		$getSite = $model->get('sites', $_SERVER['HTTP_HOST'], array(), 'domain');
		if($getSite){
			$sitePath = $getSite['url'];
		}
		
		ob_start();
		?>
			<div class="<?= $this->name ?>-preview markdown-preview">
				<h4><?=$this->previewTitle ?></h4>
				<div class="<?= $this->name ?>-preview-cont markdown-preview-cont"><?= $this->getHTMLValue() ?></div>
			</div>
			<script type="text/javascript" src="<?= $sitePath ?>/resources/Markdown.Converter.js"></script>
			<script type="text/javascript">
				$(document).ready(function(){
					$('textarea[name="<?= $this->name ?>"]').on('input', function(e){
						var thisVal = $(this).val();
						var converter = new Markdown.Converter();
						
						getMarkdown = converter.makeHtml(thisVal);
						$('.<?= $this->name ?>-preview-cont').html(getMarkdown);
					});
				});
			</script>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	}
	
	public function setLivePreview($val = true)
	{
		$this->livePreview = (bool)$val;
	}
	
	public function getLivePreview()
	{
		return $livePreview;
	}
	
	public function setPreviewTitle($title = '')
	{
		$this->previewTitle = $title;
	}
	
	public function getPreviewTitle()
	{
		return $this->previewTitle;
	}
	
	public function getHTMLValue()
	{
		if(isset($_REQUEST[$this->name])){
			return markdown($_REQUEST[$this->name]);
		}
		return markdown($this->value);
	}
	
}
