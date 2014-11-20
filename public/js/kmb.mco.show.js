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
    $.ajax({
	'async': false,
	'global': false,
	'url' : '/mcollective/agents',
	'dataType' : 'json',
	'success' : function(json){
	    agents = json;
	}
    });

    $.each(agents, function(key, value) {
	$("#selectAgent").append(
	    $('<option></option>').val(key).html(key) 
	);
    });

    $(document).on('change', '#selectAgent', function() {
	var agent = $('#selectAgent option:selected').text();
	$(".arg_mcol").html('');
	$('#selectAction option[value!="default"]').remove();
	$.each(agents[agent], function(key,value) {
	    $("#selectAction").append(
		$('<option></option>').val(key).html(key + ' - ' + value['summary']) 
	    ).trigger('chosen:updated');
	});
    }); 

    $(document).on('change', '.selectAction', function() {
	$(".arg_mcol").html('');
	var agent = jQuery.trim($('#selectAgent option:selected').text());
	var action = this.value;
	$('#arglist').remove();
	var arglist ='';
	$.each(agents[agent][action]['input'], function(inputargname, indetail){
	    arglist += inputargname +' ';
	    if(indetail['type'] == 'string')
	    {
		addInputField($("#form_arg_mcol"), inputargname, indetail );
	    }else if(indetail['type'] == 'list'){
		addSelectBox($("#form_arg_mcol"), inputargname, indetail);
	    }
	});
	$("#form_arg_mcol").append('<div class="form-group"><input id="arglist" type="hidden" name="args" value="'+ arglist +'"></input></div>');
    }); 
    $(document).on('submit', 'form[data-async]',function(event) {
	var $form = $(this);
	var $target = $($form.attr('data-target'));
	$target.html('');
	$("#legend").html(' \
<h2>Ex&eacute;cution de la requ&ecirc;te en cours</h2> \
\
<img alt="waiting.." id="wait_img" src="/images/wait.gif"></img>\
');
	$.ajax({
	    type: $form.attr('method'),
	    url: $form.attr('action'),
	    data: $form.serialize(),
	    dataType: 'json',
	    success: function(data, status) {
		console.log("Success in run");
		$.ajax({
		    type: 'GET',
		    url: data['resultUrl'],
		    dataType: 'json',
		    success: function(data,status) {
			console.log("Success in resultUrl");
			$.each(data, function(hostname,result){
			    $.each(result, function(index,jsondata){
				//				obj = JSON.parse(jsondata);
				obj = jsondata;
				if(obj['statuscode'] != 0)
				{
				    var tag = '<span class="label label-danger" id="serveurs_security">Error : '+ obj['result']+'</span>';
				}else{
				    var tag = '<span class="label label-success" id="serveurs_security">OK</span>';
				}
				var tablist = '<div class="panel-body"><ul class="nav tab-menu nav-tabs" data-tabs="tabs">';
				var divresult = '<div class="tab-content">';
				$.each(JSON.parse(obj['result']),function(name,value){
				    tablist += '<li><a class="output" href="#mcol_'+ hostname.replace(/\./g,'_') +'_'+ name +'" data-toggle="tab">'+name+'</a></li>';
				    divresult += '<div class="tab-pane" id="mcol_'+ hostname.replace(/\./g,'_') +'_'+ name +'"><pre>'+ value+'</pre></div>';
				});
				tablist += '</ul>';
				divresult += '</div>';
				$target.append('<div class="panel panel-default"><div class="panel-heading"><h3><i class="glyphicon glyphicon-remove"></i>&nbsp;'+ hostname +'&nbsp;'+tag+'</div>'+tablist+divresult);
				$target.find("li .output").first().addClass('active');
				$target.find(".tab-pane").first().addClass('active');
			    });
			});
		    }
		});
		$("#action_mcol :input").prop("disabled", false);
		$("#legend").html('');
	    },
	    error: function(data,status) {
		$("#legend").html('<ul class="flash"><li class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b> '+ data['statusText'] +'</b></li></ul>');
		$("#wait_img").remove();
		$("#action_mcol :input").prop("disabled", false);
	    }	    
	});
	$("#action_mcol :input").prop("disabled", true);	
	event.preventDefault();
    });
});



