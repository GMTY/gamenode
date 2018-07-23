/**
 * Created by Александр on 15.11.2017.
 */
$(document).ready(function () {

    $('.js-sidebar-trigger').click(function (e) {

        var parent = $(this).parents('.b-sidebar');

        if (!parent.hasClass('b-sidebar--opened')) {
            parent.addClass('b-sidebar--opened');
        }
        else {
            parent.removeClass('b-sidebar--opened');
        }

    });
    
    if ($('#open-popup').length > 0) {
    	$('#exampleModal').modal('show')
	} else {
	    console.log('None!')
	}
});

