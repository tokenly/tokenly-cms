<?php
namespace Tags;
class GoogleSearch
{
	public function display()
	{
		ob_start();
		?>
		<div id="googlesearch">
			<script>
			  (function() {
				var cx = '013081327282799973673:_1rrhu36oek';
				var gcse = document.createElement('script');
				gcse.type = 'text/javascript';
				gcse.async = true;
				gcse.src = (document.location.protocol == 'https:' ? 'https:' : 'http:') +
					'//www.google.com/cse/cse.js?cx=' + cx;
				var s = document.getElementsByTagName('script')[0];
				s.parentNode.insertBefore(gcse, s);
			  })();
			</script>
			<gcse:search></gcse:search>
		</div>
		<?php
		$output = ob_get_contents();
		ob_end_clean();
		
		return $output;
	
	}

}
