(function ($, W, D, T, F, N)
{

function getPopup ( e )
{
	e.preventDefault();

	var link = this;

	$.ajax({
		data : { aspopup : 1 },
		url : link.href
	})
	.done(showPopup)
	.fail(function ()
	{
		window.location.href = link.href;
	})
}

function showPopup ( r )
{
	var popup = $('<div class="window-overlay">')
		.html(r)
		.hide()
		.appendTo('.page')
		.fadeIn(500)
		.on('click', closePopup)
		.children()
		.on('click',function(e){e.stopPropagation()});

	centerPopup(popup);

	popup
		.find('.close')
		.on('click', closePopup);

}

function centerPopup ( popup )
{
	popup.css('margin-top', Math.max(popup.position().top, ($(W).height()-popup.outerHeight())/2) + 'px');
}

function closePopup ( e )
{
	$(this)
		.closest('.window-overlay')
		.fadeOut(500)
		.promise()
		.done(function(){$(this).remove()});
}

$(D).on('ready', function ()
{
	$('.aspopup').on('click', getPopup);
});

})(jQuery, window, document, true, false, null)