$(document).ready(function(){
    var agents = null;
    $(".chosen-select").chosen({disable_search_threshold: 10});
    $(document).on('click','.action-link', function($data){
	var action = $(this).text();
	$("#help").hide();
	$(".action-detail").hide();
	$("#" + action + "_detail").show();
    });
    $legend = $("#legende_mcol");
    $(document).on('click', ".close", function() {
	$(this).parents(".action-detail").hide();
	$("#help").show();
    });
    $(document).on('change', '#selectAgent', function() {
	var url = '/mcollective/metadata/' + $(this).val(); // get selected value
	console.log(url);
	if (url) { // require a URL
	    window.location = url; // redirect
	}
	return false;
    });
    $(document).on('submit', 'form[data-async]',function(event) {
        NProgress.start();
	event.preventDefault();
    });

    $('a').on('click', 'i.glyphicon-minus-sign', function (e) {
        $(this).addClass('glyphicon-plus-sign').removeClass('glyphicon-minus-sign');
    });
    $('a').on('click', 'i.glyphicon-plus-sign', function (e) {
        $(this).addClass('glyphicon-minus-sign').removeClass('glyphicon-plus-sign');
    });

    $(document).on('change','.argtype', function(e) {
	if( $(this).val() == 'list') {
	    $($(this).data('valuefield')).prop('disabled',false);
	} else {
	    $($(this).data('valuefield')).prop('disabled',true);
	}
    });

});
