function addInputField(object,argname,details)
{
    console.log(details['optional']);
    if(details['optional'] == true)
    {
	var required='required';
    }else{
	var required='';
    }
    var help = details['metadesc'] ? details['metadesc'] : details['description'];
    object.append('<div class="form-group"><label class="control-label" for="'+ argname +'">'+ details['prompt'] +'</label><div class="controls row"><div class="col-lg-4"><input class="form-control" name="'+ argname +'" type="text"'+ required +'></div><p class="help-block"> '+ help +'</p></div>');
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

function setFilterField(type, options, maxselect,translation) {
    target=$("#form_filtre_mcol");
    if(type == "limited") {
	if(parseInt(maxselect) >1) {
	    multiple="multiple";
	}
	select = '<label class="control-label" for="filtre_mcol">'+translation['nameFilter']+'</label><select id="filtre_mcol" data-placeholder="---" class="form-control" name="filter[]" data-rel="chosen"'+ multiple +'> <option value="default"></option></select>'
	target.html(select);
	$.each(options, function(index,host) {
	    $("#filtre_mcol").append(
		$('<option></option>').val(host).html(host)
	    );
	});
	if(multiple) {
	    $("#filtre_mcol").chosen({max_selected_options: parseInt(maxselect)});
	} else {
	    $("#filtre_mcol").chosen();
	}
    }else{
	target.html('<label class="control-label" for="filtre_mcol">'+ translation['nameFilter']  +'</label><input type="text" class="form-control" name="filter" id="filtre_mcol" required>');
    }
}

function getResult(data,target,discovered_nodes,refreshResult,translation)
{
    $ .ajax({
        type: 'GET',
	url: data['resultUrl'] + '?state=finished',
	dataType: 'json',
	success: function(data,status) {
            resultsReceived = Object.keys(data).length;
            NProgress.set(resultsReceived/discovered_nodes);

            $legend.html(translation['requestStarted'] + '<br/>');

            if (resultsReceived == discovered_nodes) {
                $legend.append('<strong>'+ sprintf(translation['receivingDataDone'], resultsReceived) +'</strong><br/>');
                clearInterval(refreshResult);
                NProgress.done();
            }
            else
            {
                $legend.append('<strong>' + sprintf(translation['receivingDataNr'], resultsReceived ,discovered_nodes)  +'</strong><br/>');
            }
            var requests = $('<p></p>');
            $legend.append(requests);

            target.html('');

	    $.each(data, function(hostname,result){
		$.each(result, function(index,jsondata){
		    obj = jsondata;
                    if (obj['statuscode'] != null) {
			if(obj['statuscode'] != 0)
			{
			    var tag = '<span class="label label-danger" id="serveurs_security">Error : '+ obj['result']+'</span>';
                requests.prepend('<span class="label label-danger">KO</span> <a href="#' + hostname + '">' + hostname + '</a><br/>');
			}else{
			    var tag = '<span class="label label-success" id="serveurs_security">OK</span>';
                requests.prepend('<span class="label label-success">OK</span> <a href="#' + hostname + '">' + hostname + '</a><br/>');
			}

            var results = '<div class="tab-content">';

            $.each(JSON.parse(obj['result']),function(name,value){
                if (name == 'status') { return true; }
                if (value == '') { return true; }
                results += '<div class="result-mco"><span class="label label-default result-mco">' + name + '</span></div>';
                results += '<pre>' + value + '</pre>';
            });
            results += '</div>';

			target.prepend('<div class="panel panel-default"><div class="panel-heading"><h4 id=' + hostname + '><i class="glyphicon glyphicon-remove"></i>&nbsp;'+ hostname +'&nbsp;'+tag+'</div>'+results+'</h4>');
            }
		});
	    });
	}
    });
}

$(document).ready(function(){
    var agents = null;
    var translation = null;

    $.ajax({
	'async': false,
	'global': false,
	'url' : '/mcollective/translation',
	'dataType' : 'json',
	'success' : function(json){
	    translation = json;
	}
    });
    $legend = $("#legende_mcol");
    NProgress.configure({ trickle: false });

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
	if(agents[key]['description'] == null)
	{
	    var agentdesc = "";
	}else{
	    var agentdesc = agents[key]['description']
	}
	$("#selectAgent").append(
	    $('<option></option>').val(key).html(key + " - " + agentdesc)
	);
    });

    $(document).on('change', '#selectAgent', function() {
	var agent = $('#selectAgent option:selected').val();
	$(".arg_mcol").html('');
	$('#selectAction option[value!="default"]').remove();
	$.each(agents[agent]['actions'], function(key,value) {
	    var description = (value['description'] != "") && (value['description'] != null) ? value['description'] : value['summary'];
	    $("#selectAction").append(
		$('<option></option>').val(key).html(key + ' - ' + description)
	    ).trigger('chosen:updated');
	});
    });

    $(document).on('change', '.selectAction', function() {
	$(".arg_mcol").html('');
	var agent = jQuery.trim($('#selectAgent option:selected').val());
	var action = this.value;
	$('#arglist').remove();
	var arglist ='';
	if(agents[agent]['actions'][action]['limitnum'] > 0) {
	    $("#limit_mcol").val(agents[agent]['actions'][action]['limitnum']);
	    $("#limit_mcol").attr("disabled", "disabled");
	}else{
	    $("#limit_mcol").val("");
	    $("#limit_mcol").removeAttr("disabled");
	}
	if(agents[agent]['actions'][action]['limithosts'] != null && agents[agent]['actions'][action]['limithosts'].length > 0 && agents[agent]['actions'][action]['limithosts'][0] != "") {
	    setFilterField('limited', agents[agent]['actions'][action]['limithosts'],agents[agent]['actions'][action]['limitnum'],translation);
	}else{
	    setFilterField('normal',null,null,translation);
	}
	$.each(agents[agent]['actions'][action]['input'], function(inputargname, indetail){
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

    var refreshResult;
    $(document).on('submit', 'form[data-async]',function(event) {
        NProgress.start();
        $legend.css('font-size','10.5px');
	    var $form = $(this);
	    var $target = $($form.attr('data-target'));
	    $target.html('');
        $legend.html(translation['startingRequest']);
	    $.ajax({
            type: $form.attr('method'),
            url: $form.attr('action'),
            data: $form.serialize(),
            dataType: 'json',
            success: function(data, status) {
                $legend.html(translation['requestStarted']+'<br/>');
                $legend.append('<strong>'+translation['receivingDataPending']+'</strong><br/>');
                var discovered_nodes = data['discovered_nodes'].length;

                if (typeof refreshResult != 'undefined') {
                    clearInterval(refreshResult);
                }
                refreshResult = setInterval(function() {
                    getResult(data,$target,discovered_nodes,refreshResult,translation);
                }, 2000);

                getResult(data,$target,discovered_nodes,refreshResult,translation);

                $("#action_mcol :input").prop("disabled", false);
            },
            error: function(data,status) {
                $.gritter.add({
                    title: data['statusText'],
                    text: data['responseText'],
                    class_name: 'gritter-danger',
                    sticky: true
                });
                $("#wait_img").remove();
                $("#action_mcol :input").prop("disabled", false);
                NProgress.done();
            }
        });

	    $("#action_mcol :input").prop("disabled", true);
	    event.preventDefault();
    });
});
