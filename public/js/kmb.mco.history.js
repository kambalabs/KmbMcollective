$(".result_detail").hide();
$(document).on('click','.result_line', function(){
    var detaildiv = $(this).data('target');
    $(".result_detail").hide();
    $($(detaildiv)).appendTo($("#details"));
    $($(detaildiv)).show();
});
