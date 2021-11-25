/**
* @since 0.9.0
*/
jQuery(document).ready(function($) {

	/**
	* Schema Field
	*/
	$('.asf-tag button').click(function(e){
		e.preventDefault();
		var target = $(this).closest('.asf-field-wrapper').find('input');
		if(!target.length) return;
		var targetVal = target.val();
		var buttonVal = $(this).data('tag');
		if(!buttonVal.length) return;
		target.val(targetVal +'%'+buttonVal +'%');

		var targetLength = target.val().length;
		target.focus();
		target[0].setSelectionRange(targetLength, targetLength);
	});

	/**
	* Clear Value
	*/
	$('.asf-field-wrapper .asf-clear').click(function(e){
		e.preventDefault();
		var target = $('#'+$(this).data('for'));
		if(!target.length) return;
		target.val('');
		target.focus();
	});

	/**
	* Info block
	*/
	$('.asf-info').accordion({
		'collapsible':true,
		'active':false,
	});

	/**
	* Repeater field
	*/
	$(document).on('click','[data-event]',function(e) {
		e.preventDefault();
		var rows = $(this).closest('.asf-repeater').find('.asf-rows');
		var lastRow = $(this).closest('.asf-repeater').find('.asf-row:last-child');
		switch($(this).data('event')) {
			case 'add-row' :
				var newRow = lastRow.clone();
				clearVals(newRow);
				rows.append(newRow);
				indexRows(rows);
				break;
			case 'remove-row':
				$(this).closest('.asf-row').remove();
				indexRows(rows);
				break;
			case 'duplicate-row':
				$(this).closest('.asf-row').after($(this).closest('.asf-row').clone());
				indexRows(rows);
				break;
		}
		return false;
	});

	function indexRows(rows) {
		if(rows.length) {
			$('.asf-row',rows).each(function(i){
				var n = i + 1;
				$(this).attr('data-id',n);

				$('label',this).each(function(){
					$(this).attr('for', $(this).attr('for').replace(/\-{3}\d+$/,'---' + n) );
				});

				$('input',this).each(function(){
					$(this).attr('id', $(this).attr('id').replace(/\-{3}\d+$/,'---' + n) );
					$(this).attr('name', $(this).attr('name').replace(/\[\d+\]/,'['+i+']') );
				});



			});
		}
	}

	function clearVals(field) {
		if(field.length) {
			$('input',field).each(function(i){
				$(this).val('');
				$(this).prop( "checked", false );
			});
		}
	}

	var repeaterRows = $('.asf-repeater .asf-rows');

	repeaterRows.sortable({
		axis: "y",
		cursor: "move",
		update: function() {
			indexRows(repeaterRows);
		},
		containment: ".asf-repeater",
	});
});