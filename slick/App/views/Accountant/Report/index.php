<h2><?= $module['name'] ?></h2>
<p>
	Enter a list of bitcoin addresses below and hit submit to receive a full report of all BTC and Counterparty
	transactions sent/received from each address, in <em>.csv</em> spreadsheet format. Please note that this may
	take some time to generate.
</p>
<p>
	Use the "asset filters" field if you want to only look at transactions dealing with specific tokens.
	Use "BTC" for bitcoin only transactions, and you can use * as a wild card (e.g "LTB*" for anything that has a LTB prefix).
	Leave blank to grab everything.
</p>
<?php
if($error != ''){
	echo '<p class="error">'.$error.'</p>';
}
?>
<?= $form->display() ?>
