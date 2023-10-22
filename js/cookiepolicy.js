$(document).on('ready', function(){
  cookiesPolicyBar()
});

function cookiesPolicyBar(){
    // Check cookie 
    if ($.cookie('yourCookieName') != "active") $('#cookieAcceptBar').show(); 
    //Assign cookie on click
    $('#cookieAcceptBarConfirm').on('click',function(){
        $.cookie('yourCookieName', 'active', { expires: 1 }); // cookie will expire in one day
        $('#cookieAcceptBar').fadeOut();
    });
}