<?php
namespace UI;
class Markdown extends FormObject
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
			$misc = new Misc;
			$output = $misc->wrap($elemWrap, $output, $this->wrap_class);
		}
		
		return $output;
	}
	
	public function displayLivePreview()
	{
		$sitePath = '';
		$getSite = currentSite();
		$sitePath = $getSite['url'];
		
		ob_start();
		?>
			<div class="<?= $this->name ?>-preview markdown-preview">
				<button type="button" id="toggle-markdown-preview-<?= $this->name ?>" class="btn btn-info pull-right">Disable Preview</button>
				<?php
				if($this->previewTitle != ''){
					echo '<h4 class="text-progress">'.$this->previewTitle.' - '.$this->label_raw.'</h4>';
				}
				?>
				<div class="<?= $this->name ?>-preview-cont markdown-preview-cont"><?= $this->getHTMLValue() ?></div>
			</div>
			<script type="text/javascript" src="<?= $sitePath ?>/resources/Markdown.Converter.js"></script>
			<script type="text/javascript">
				$(document).ready(function(){
					window.<?= $this->name ?>_markdown_preview_enable = true;
					var converter = new Markdown.Converter();
					$('textarea[name="<?= $this->name ?>"]').on('input', function(e){
						if(!window.<?= $this->name ?>_markdown_preview_enable){
							return false;
						}
						var thisVal = $(this).val();
						
						getMarkdown = converter.makeHtml(thisVal);
						$('.<?= $this->name ?>-preview-cont').html(getMarkdown);
					});
					
					$('#toggle-markdown-preview-<?= $this->name ?>').click(function(e){
						e.preventDefault();
						if($(this).hasClass('enable')){
							$(this).removeClass('enable');
							window.<?= $this->name ?>_markdown_preview_enable = true;
							$(this).html('Disable Preview');
							var get = $('textarea[name="<?= $this->name ?>"]').val();
							getMarkdown = converter.makeHtml(get);
							$('.<?= $this->name ?>-preview-cont').html(getMarkdown);
						}
						else{
							$(this).addClass('enable');
							window.<?= $this->name ?>_markdown_preview_enable = false;
							$(this).html('Enable Preview');
							$('.markdown-preview-cont').html('');
						}
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
