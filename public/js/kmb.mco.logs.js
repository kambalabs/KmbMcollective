$(window).load(function(){
    $('#mcollective_logs').dataTable($.extend({}, DATATABLES_NPROGRESS_DEFAULT_SETTINGS, {
        "serverSide": true,
        "ajax": {
            "url": window.location,
            "complete": function() {
                NProgress.done();
            },
            "error": function (cause) {
                console.log('Could not get log list : ' + cause.statusText);
                NProgress.done();
                $('#mcollective_logs_processing').hide();
            }
        }
    }));

    //new table
    $('#action_logs').dataTable($.extend({}, DATATABLES_NPROGRESS_DEFAULT_SETTINGS, {
        "serverSide": true,
        "ajax": {
            "url": window.location,
            "complete": function() {
                NProgress.done();
            },
            "error": function (cause) {
                console.log('Could not get log list : ' + cause.statusText);
                NProgress.done();
                $('#mcollective_logs_processing').hide();
            }
        }
    }));
});
