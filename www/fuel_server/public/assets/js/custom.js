$('li.nsearch').on('click', function(){
    $('li.nsearch').each(function() {
        $(this).siblings().removeClass("active");
    })
    $(this).toggleClass("active");
    // console.log('asdadsad');
});
var st_ = true;
function touch(url)
{
    $('#container').html();
    $('#container').load(url);
    // console.log(url);
}

function resfreshBalance(money)
{
    $('#balance').text(money);
}

function addBalance(money)
{
    var before = $('#balance').text();
    $('#balance').text(parseFloat(before) + parseFloat(money));
}

function checkAmount(betAmount)
{
    var before = $('#balance').text();
    return (parseFloat(before) - parseFloat(betAmount)) < 0;
}

