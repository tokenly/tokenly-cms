<h2>Distribute CounterParty Shares</h2>
<?php
if($perms['canDistribute']){
?>
	<p>
		Use this tool to mass distribute Counterparty shares. Enter in an asset name (BTC, XCP or other) to pay with and either upload a <strong>.csv</strong> file
		or manually enter in addresses in the following format:
	</p>
	<blockquote>&lt;Bitcoin Address&gt;, &lt;Amount&gt;
	<br>(one per line)
	</blockquote>
	<p>
		An address and total amount + fee will be presented to you. Amounts sent to that address will automatically be dispersed among all addresses.
	</p>
	<?php
	if(isset($message) AND trim($message) != ''){
		echo '<p class="error">'.$message.'</p>';
	}

	echo $form->display();
}

if(count($distributeList) > 0){
	foreach($distributeList as &$row){
		$row['total'] = 0;
		$row['addressList'] = json_decode($row['addressList'], true);
		foreach($row['addressList'] as $val){
			if($row['divisible'] == 1){
				$val = $val / SATOSHI_MOD;
			}
			$row['total'] += $val;
		}
		if(trim($row['name']) != ''){
			$row['asset'] = '<em>"'.$row['name'].'"</em><br>'.$row['asset'];
		}
	}

	$table = new \UI\Table;
	$table->addClass('admin-table mobile-table');
	$table->setData($distributeList);
	$table->addColumn('distributeId', 'ID');
	$table->addColumn('asset', 'Asset');
	$table->addColumn('total', 'Total');
	$table->addColumn('address', 'Address');
	$table->addColumn('status', 'Status');
	$table->addColumn('initDate', 'Date/Time');
	$table->setColumnOpts('initDate', array('functionWrap' => 'formatDate'));
	$table->addAction('View Details', 'address', '', SITE_URL.'/'.$app['url'].'/'.$module['url'].'/tx/');
	if($perms['canDeleteDistribution']){
		$table->addAction('Delete', 'address', '', SITE_URL.'/'.$app['url'].'/'.$module['url'].'/delete/', 'delete');
	}
	$table->pageData(50, SITE_URL.'/'.$app['url'].'/'.$module['url'].'?page=');


	echo '<h3>Distribution History</h3>';
	echo $table->display();
}

		
?>
<script type="text/javascript">
	$(document).ready(function(){
		$('input[name="amount"],label[for="amount"]').hide();
		$('select[name="valueType"]').change(function(){
			var newVal = $(this).val();
			if(newVal == 'fixed'){
				$('input[name="amount"],label[for="amount"]').hide();
			}
			else{
				$('input[name="amount"],label[for="amount"]').show();
			}
			
		});
		
	});
</script>
