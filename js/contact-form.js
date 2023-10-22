
function submitForm(){
    // Initiate Variables With Form Content
    var name    = $("#name").val();
    var email   = $("#email").val();
    var message = $("#message").val();
    var subject = $("#subject").val();

    $.ajax({
        type: "POST",
        url: "js/form-process/",
        data: "name=" + name + "&email=" + email + "&message=" + message + "&subject=" + subject,
        success : function(text){
            if (text == "success"){
                formSuccess();
            } else {
                formError();
                submitMSG(false,text);
            }
        }
    });
}



function formSignUpSuccess(){
    $("#signUpModal")[0].reset();
    submitMSG(true, "Thank You. We will contact you shortly.")
}

function formSuccess(){
    $("#contactForm")[0].reset();
    submitMSG(true, "Thank You. We will contact you shortly.")
}

function formSignUpError(){
    $("#signUpModal").removeClass().addClass('shake animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
        $(this).removeClass();
    });
}

function formError(){
    $("#contactForm").removeClass().addClass('shake animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
        $(this).removeClass();
    });
}

function formSignUpError(){
    $("#signUpModal").removeClass().addClass('shake animated').one('webkitAnimationEnd mozAnimationEnd MSAnimationEnd oanimationend animationend', function(){
        $(this).removeClass();
    });
}


function submitMSG(valid, msg){
    if(valid){
        var msgClasses = "h3 text-center tada animated text-success";
    } else {
        var msgClasses = "h3 text-center text-danger";
    }
    $("#msgSubmit").removeClass().addClass(msgClasses).text(msg);
}