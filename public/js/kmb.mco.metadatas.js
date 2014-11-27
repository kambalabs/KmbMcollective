function addInputField(object,argname,details)
{
    if(details['optional'] == false)
    {
	var required='required';
    }else{
	var required='';
    }
    object.append('<div class="form-group"><label class="control-label" for="'+ argname +'">'+ details['prompt'] +'</label><div class="controls row"><div class="col-lg-4"><input class="form-control" name="'+ argname +'" type="text"'+ required +'></div><p class="help-block"> '+ details['description'] +'</p></div>');
}

function addSelectBox(object,argname, details)
{
    object.append('<div class="form-group"><label class="control-label" for="'+ argname +'">'+ details['prompt'] +'</label><div class="controls row"><div class="col-lg-12"><select id="'+ argname +'list_arg_chosen" data-placeholder="---" class="form-control" name="'+ argname +'" data-rel="chosen"> <option value="default"></option></select></div></div></div>');
    $.each(details['list'], function(item,value){
	$("#"+argname+'list_arg_chosen').append(
	    $('<option></option>').val(value).html(value)
	);
    });
    $("#"+argname+'list_arg_chosen').chosen();
}


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
});
