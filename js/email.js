
$(document).on('submit', 'form', function (e) {
    e.preventDefault();
    let signup = $('.signupform');
    let errors = $('.errors');

    $.ajax({
        type: 'POST',
        url: 'controller.php',
        data: {
            email: $('#mail').val(),
            wallet: $('#wallet').val()
        },
        success: function (res) {
            console.log(res);
            errors.empty();
            try {
                obj = JSON.parse(res);
                if ("success" in obj) {
                    signup.empty();
                    signup.append('<h1>Thank you for signing up!</h1>');
                }
                else {
                    errors.append(obj['fail']);
                }
            } catch (error) {
                errors.append('Something went wrong, please try again later');
            }
        }
    });

})
