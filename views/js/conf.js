$(document).ready(function() {
	$('#producttable').dataTable({
		'processing': true,
		'serverSide': true,
		'iDisplayLength': 15,
		'bLengthChange': false,
		'bFilter': false,
		'ajax': {
			'url': '../../modules/moonfriend/ajax.php' + '?action=ptable'
		},
		'columns': [
			{'data': 'id_order_one_click'},
			{'data': 'id_product'},
			{'data': 'product_name'},
			{'data': 'name'},
			{'data': 'email'},
			{'data': 'phone'},
			{'data': 'date'},
		]
	});
});