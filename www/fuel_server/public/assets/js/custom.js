$('li.nsearch').on('click', function(){
    $('li.nsearch').each(function() {
        $(this).siblings().removeClass("active");
    })
    $(this).toggleClass("active");
    // console.log('asdadsad');
});

function touch(url)
{
    $('#container').html();
    $('#container').load(url);
    console.log(url);
}

function resfreshBalance(money)
{
    $('#balance').text(money);
}

