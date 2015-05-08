<?php
namespace UI;
class CascadingCheckboxList extends CheckboxList
{
	protected $options = array();
	protected $selected = array();
	protected $labelDir = 'L';
	protected $elemWrap = '';
	protected $elemClass = '';
	protected $elemData = array();
	
	function __construct($name, $id = '')
	{
		parent::__construct($name, $id);
		$this->name = $name;
		if($id == ''){
			$id = $name;
		}
		$this->id = $id;
		$this->addClass('checkboxList');
		
	}
	
	public function display($elemWrap = ''){
		
		if(is_array($this->value)){
			$this->selected = $this->value;
		}
		
		$labelDir = $this->labelDir;
		
		$classText = '';
		if(count($this->classes) > 0){
			$classText = 'class="'.$this->getClassesText().'"';
		}
		
		$idText = '';
		if($this->id != ''){
			$idText = 'id="'.$this->id.'"';
		}
		
		$attributeText = $this->getAttributeText();		
		
		$checkArray = '';
		if(count($this->options) > 1){
			$checkArray = '[]';
		}
		
		$output = '';
		$main_label = $this->label;

		
		ob_start();
		?>
		<label><?= $main_label ?></label>
		<div class="Category_Checkboxlist">
		<?php
		foreach($this->options as $val => $opt){
			if(is_array($opt)){
				$item = $opt;
			}
			else{
				$item = array('value' => $val, 'label' => $opt, 'children' => array());
			}
			echo $this->showItem($item);
		}//endforeach
		?>			
		</div>


	
		<script type="text/javascript">
			$(document).ready(function(){
				
				function addCategoryTrail(){
						$('.category-expander').removeClass('hasChecked');
					jQuery(".Category_Checkboxlist").find('input:checked').each(function(){
						$(this).parents('.category-wrapper').find(' > .category-expander').each(function(){
							$(this).addClass('hasChecked');
						});
					});
				}


				jQuery(".Category_Checkboxlist").find("input[type='checkbox']").click(function(){
					addCategoryTrail();
				});

				jQuery(".category-expander").addClass('expand');
				jQuery(".category-children").hide();
				
				jQuery(".category-expander").click(function(){
					if(jQuery(this).is('.expand')){
						jQuery(this).removeClass('expand');
						jQuery(this).addClass('collapse');
						jQuery(this).find('i').attr('class', 'fa fa-minus-square');
					}else{
						jQuery(this).removeClass('collapse');
						jQuery(this).addClass('expand');
						jQuery(this).find('i').attr('class', 'fa fa-plus-square');
					}
					jQuery(this).parent().children('.category-children').slideToggle(200);
				});
				addCategoryTrail();
				jQuery(".category-expander.hasChecked").removeClass('expand').addClass('collapse').siblings('.category-children').slideDown();
				
			});
		
		</script>
		<?php
		$output .= ob_get_contents();
		ob_end_clean();
		return $output;
		
	}
	
	protected function showItem($item)
	{
		ob_start();
		?>
		<div class="category-wrapper">
		<?php
		if(isset($item['children']) AND count($item['children']) > 0){
		?>
				<div class="category-expander" title="Click + icon to expand/collapse"><i class="fa fa-plus-square"></i></div>
				<div class="category-label" title="Click + icon to expand/collapse"><?= $item['label'] ?></div>
				<div class="category-children" style="display: none;">
					<?php
					foreach($item['children'] as $child){
						echo $this->showItem($child);
					}
					?>
				</div>
		<?php
		}
		else{
		?>
				<div class="checkbox">
					<input type="checkbox" name="<?= $this->name ?>[]" value="<?= $item['value'] ?>" id="<?= $this->id ?>-<?= $item['value'] ?>"					
					<?php
					if(in_array($item['value'], $this->selected)){
						echo 'checked="checked" ';
					}
					echo $this->getAttributeText();	
					?>
					 />
					<label for="<?= $this->id ?>-<?= $item['value'] ?>"><?= $item['label'] ?></label>
				</div>
		<?php
		}//endif
		?>
		</div>

		
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		return $output;
	}
}
