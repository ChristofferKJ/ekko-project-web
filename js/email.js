
$(document).on('submit', 'form', function (e) {
    e.preventDefault();
    let signup = $('.signupform');

    $.ajax({
        type: 'POST',
        url: 'controller.php',
        data: { 
            email: $('#mail').val(), 
            wallet: $('#wallet').val() 
        },
        success : function(res) {
            console.log(res);
            signup.empty();
            signup.append('<h1>Thank you for signing up!</h1>');
            signup.append('<p style="margin-top: 1rem" >Please check your inbox for a confirmation email</p>');
        }
    });
    
})
