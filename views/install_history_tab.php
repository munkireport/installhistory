<div id="installhistory-tab"></div>
<?php if($apple):?>
<h2 data-i18n="installhistory.installed_apple_software"></h2>
<?php else:?>
<h2 data-i18n="installhistory.installed_third_party_software"></h2>
<?php endif?>
<table class="install-history-<?=$apple?> table table-striped table-bordered">
  <thead>
		<tr>
			<th data-i18n="name"></th>
			<th data-i18n="version"></th>
			<th data-i18n="installhistory.install_date"></th>
			<th data-i18n="installhistory.process_name"></th>
		</tr>
  </thead>
  <tbody></tbody>
</table>

<script>
$(document).on('appReady', function(){
	<?php if($apple):?>
	var url = appUrl + '/module/installhistory/get_apple_data/' + serialNumber;
	<?php else:?>
	var url = appUrl + '/module/installhistory/get_third_party_data/' + serialNumber;
	<?php endif?>

	$.getJSON(url, function(data){
		tbody = $('table.install-history-<?=$apple?> tbody');
		$.each(data, function(i,item){

		var row = '<tr>'+
			'<td>'+item.displayName+'</td>'+
			'<td>'+item.displayVersion+'</td>'+
			'<td>'+item.date+'</td>'+
			'<td>'+item.processName+'</td>'+
		'</tr>';

		tbody.append(row)
		});

		// Initialize datatables
		$('.install-history-<?=$apple; ?>').dataTable({
			"bServerSide": false,
			"aaSorting": [[1,'asc']],
			"fnDrawCallback": function( oSettings ) {
				$('#history-cnt-<?=$apple?>').html(oSettings.fnRecordsTotal());
			},
			"fnCreatedRow": function( nRow, aData, iDataIndex ){
				mr.listingFormatter.timestampToMoment(2, nRow)
			}
		});
	});
});
</script>
