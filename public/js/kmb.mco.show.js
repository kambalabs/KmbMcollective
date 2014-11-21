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

function getResult(data,target,discovered_nodes,refreshResult)
{
    $ .ajax({
        type: 'GET',
	url: data['resultUrl'] + '?state=finished',
	dataType: 'json',
	success: function(data,status) {
            resultsReceived = Object.keys(data).length;
	    console.log("Success in resultUrl : " + resultsReceived);
            NProgress.set(resultsReceived/discovered_nodes);
            // console.log('polling... Received : ' + resultsReceived + ' - Discovered : ' + discovered_nodes);

            $legend.html('Ex&eacute;cution de la requ&ecirc;te <span class="label label-success">OK</span><br/>');

            if (resultsReceived == discovered_nodes) {
                $legend.append('<strong>Réception des données : done</strong><br/>');
                // console.log('Stopping polling... Received : ' + resultsReceived + ' - Discovered : ' + discovered_nodes);
                clearInterval(refreshResult);
                NProgress.done();
            }
            else
            {
                $legend.append('<strong>Réception des données : ' + resultsReceived + ' sur ' + discovered_nodes + ' serveurs.</strong><br/>');
            }

            target.html('');

	    $.each(data, function(hostname,result){
		$.each(result, function(index,jsondata){
		    obj = jsondata;
                    if (obj['statuscode'] != null) {
			if(obj['statuscode'] != 0)
			{
			    var tag = '<span class="label label-danger" id="serveurs_security">Error : '+ obj['result']+'</span>';
                            $legend.append('<a href="#' + hostname + '">' + hostname + '</a> <span class="label label-danger">KO</span><br/>');
			}else{
			    var tag = '<span class="label label-success" id="serveurs_security">OK</span>';
                            $legend.append('<a href="#' + hostname + '">' + hostname + '</a> <span class="label label-success">OK</span><br/>');
			}

                        var results = '<div class="tab-content">';

			$.each(JSON.parse(obj['result']),function(name,value){
                            if (name == 'status') { return true; }
                            if (value == '') { return true; }
                            results += '<div class="result-mco"><span class="label label-default result-mco">' + name + '</span></div>';
                            results += '<pre>' + value + '</pre>';
                        });

                        results += '</div>';

			target.append('<div class="panel panel-default"><div class="panel-heading"><h4 id=' + hostname + '><i class="glyphicon glyphicon-remove"></i>&nbsp;'+ hostname +'&nbsp;'+tag+'</div>'+results+'</h4>');
                    }
		});
	    });
	}
    });
}

$(document).ready(function(){
    var agents = null;
    $legend = $("#legende_mcol");

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
        NProgress.start();
        $legend.css('font-size','10.5px');
	var $form = $(this);
	var $target = $($form.attr('data-target'));
	$target.html('');
        $legend.html('Ex&eacute;cution de la requ&ecirc;te <span class="label label-primary">UP</span>');
	$.ajax({
	    type: $form.attr('method'),
	    url: $form.attr('action'),
	    data: $form.serialize(),
	    dataType: 'json',
	    success: function(data, status) {
		console.log("Success in run");
                $legend.html('Ex&eacute;cution de la requ&ecirc;te <span class="label label-success">OK</span><br/>');
                $legend.append('<strong>Réception des données : en cours...</strong><br/>');
                var discovered_nodes = data['discovered_nodes'].length;

                var refreshResult = setInterval(function() {
                                        getResult(data,$target,discovered_nodes,refreshResult);
                                    }, 10000);

                getResult(data,$target,discovered_nodes,refreshResult);

                $("#action_mcol :input").prop("disabled", false);
	    },
	    error: function(data,status) {
		$("#legend").html('<ul class="flash"><li class="alert alert-danger"><button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button><b> '+ data['statusText'] +'</b></li></ul>');
		$("#wait_img").remove();
		$("#action_mcol :input").prop("disabled", false);
                NProgress.done();
	    }
	});

	$("#action_mcol :input").prop("disabled", true);
	event.preventDefault();
    });
});