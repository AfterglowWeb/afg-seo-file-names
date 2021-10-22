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
});