$(".result_detail").hide();
var $affixElement = $('[data-spy="affix"]');
$affixElement.width($affixElement.parent().width());

$(document).on('click','.result_line', function(){
    var title     = $(this).data('target').split('_');
    var detaildiv = $(this).data('target');
    var mcol      = $(this).text();
    $("#details > h2").html(title[0].replace('#','') + "." + title[1]  + "." + title[1]);
    $("#details > blockquote").html("Voici le résultat de la commande " + mcol.match(/(\S+::\S+)\s+\S+/)[1]);
    $('#result').html($(detaildiv).html()).css('');
    $($(detaildiv)).show();
});