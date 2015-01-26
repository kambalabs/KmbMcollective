$(".result_detail").hide();
var $affixElement = $('[data-spy="affix"]');
$affixElement.width($affixElement.parent().width());

$(document).on('click','.result_line', function(){
    var title     = $(this).data('target').split('_');
    var titledetaildiv = $(this).data('target')+"_title";
    var detaildiv = $(this).data('target');

    var mcol      = $(this).text();
    $("#details > h2").html(title[0].replace('#','') + "." + title[1]  + "." + title[1]);
    $("#details > blockquote").html($(titledetaildiv).html());
    $('#result').html($(detaildiv).html());
});