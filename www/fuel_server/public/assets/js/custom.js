$('li.nsearch').on('click', function(){
    $('li.nsearch').each(function() {
        $(this).siblings().removeClass("active");
    })
    $(this).toggleClass("active");

    // console.log(url);
});
$(function () {
    if(url.indexOf('?') > 0) url = url.substring(0,url.indexOf('?'));
    $("li.nsearch").each(function () {
        if($(this).find('a').attr("onClick") == 'touch("'+ url +'")'){
            $(this).find('a').click();
        }
    })
})
var st_ = true;
var current_url = '';
function touch(url)
{
    if(url != '') {
        if (url.indexOf('api') == -1) current_url = url;
        $('#container').html('');
        $('#container').load(url);
    }
}

function langChange(url)
{
    url += "&url=" + current_url;
    location.href = url;
}

function resfreshBalance(money)
{
    $('#balance').text(money.toFixed(3));
}

function addBalance(money)
{
    var before = $('#balance').text();
    $('#balance').text((parseFloat(before) + parseFloat(money)).toFixed(3));
}

function checkAmount(betAmount)
{
    var before = $('#balance').text();
    return (parseFloat(before) - parseFloat(betAmount)) < 0;
}

function openModal()
{
    $("#loadingModal").modal('show');
}

function closeModal()
{
    $('#loadingModal').modal('hide');
    $('.modal-backdrop').remove();
}


