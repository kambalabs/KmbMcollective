$(window).load(function(){
    var dataTablesDefaultSettings = {
        "sPaginationType": "bootstrap",
        "bProcessing": false,
        "bAutoWidth": false,
        "aaSorting": [],
        "oLanguage": {
            "sUrl": "/js/dataTables.french.txt"
        },
        "fnPreDrawCallback": function() {
            // gather info to compose a message
            NProgress.start();
            return true;
        },
        "fnDrawCallback": function () {
            $('a', this.fnGetNodes()).each(function () {
                $(this).tooltip({
                    "placement": $(this).attr('data-placement'),
                    delay: {
                        show: 400,
                        hide: 200
                    }
                });
                NProgress.done();
            });
        }
    };

    $('#mcollective_logs').dataTable($.extend({}, dataTablesDefaultSettings, {
        "processing": true,
        "serverSide": true,
        "ajax": {
            "url": window.location,
            "error": function (cause) {
                console.log('Could not get log list : ' + cause.statusText);
                NProgress.done();
                $('#mcollective_logs_processing').hide();
            }
        }
    }));

});
