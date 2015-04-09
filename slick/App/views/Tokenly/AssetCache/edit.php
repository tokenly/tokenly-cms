<?php
if(trim($thisAsset['image']) != ''){
	echo '<p style="float: right; vertical-align: top;"><img src="'.SITE_URL.'/files/tokens/'.$thisAsset['image'].'" alt="" /></p>';
}
?>
<h2>Edit Asset Details: <?= $thisAsset['asset'] ?></h2>
<p>
	<a href="<?= SITE_URL ?>/<?= $app['url'] ?>/<?= $module['url'] ?>">Go Back</a>
</p>
<div class="clear"></div>
<?php
if(isset($message) AND trim($message) != ''){
	echo '<p class="error">'.$message.'</p>';
}
?>
<?= $form->display() ?>
<div class="markdown-preview">
	<h4>Live Preview</h4>
	<div class="markdown-preview-cont">
		<?= markdown($thisAsset['description']) ?>
	</div>
</div>
<script type="text/javascript" src="<?= THEME_URL ?>/js/Markdown.Converter.js"></script>
<script type="text/javascript">
	$(document).ready(function(){
		$('#markdown').on('input', function(e){
			var thisVal = $(this).val();
			var converter = new Markdown.Converter();
			
			getMarkdown = converter.makeHtml(thisVal);
			$('.markdown-preview-cont').html(getMarkdown);
		});
	});
</script>
