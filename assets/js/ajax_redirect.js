jQuery(function($){
	$('.redirect_link').blur(function(){
		redirect_link = $(this);
		$.ajax({
			type:'POST',
			url:ajaxurl,
			data:'action=update_redirect_url&redirect_val=' + redirect_link.val() + '&redirect_id=' + redirect_link.attr('data-id'),
			beforeSend:function(xhr){
				redirect_link.attr('readonly','readonly').next().html('Сохраняю...');
			},
			success:function(results){
				redirect_link.removeAttr('readonly').next().html('<span style="color:#0FB10F">Сохранено</span>');
			}
		});
	});
});