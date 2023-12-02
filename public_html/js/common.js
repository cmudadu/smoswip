/**
 *  Common functions to use
 */

$(document).ready(function(){

  /* window.addEventListener('load', function() {
    // Fetch all the forms we want to apply custom Bootstrap validation styles to
    var forms = document.getElementsByClassName('needs-validation');
    // Loop over them and prevent submission
    var validation = Array.prototype.filter.call(forms, function(form) {
      form.addEventListener('submit', function(event) {
        if (form.checkValidity() === false) {
          event.preventDefault();
          event.stopPropagation();
        }
        form.classList.add('was-validated');
      }, false);
    });
  }, false); */

  $("body").ajaxComplete(function (e, xhr, settings) {
    console.log("AJAX complete")
    if (xhr.status == 302) {
        var redirect = null;
        try {
            redirect = xhr.getResponseHeader("Location");
            if (redirect) {
                window.location.href = redirect;
            }
        } catch (e) {
            return;
        }
    }
  });


})

function doLogin() {
  const email = $("#email").val();
  const pass = $("#pass").val();

  if (email === '' || pass === '') {
    alert('Set email and password');
  } else {
    // Prepare form data to send.
    let formData = new FormData();
    formData.append('email', email);
    formData.append('pass', pass);

    fetch('/api/login', {
      method: 'POST',
      body: formData
    }).then(response => {
      if (response.ok) {
        document.location.href = '/dashboard';
      } else {
        alert('Bad credentials');
      }
    }).catch(error => alert('Error: ', error.message));
  }
}

function doLogout() {  
    fetch('/api/logout', {
      method: 'POST',
      body: {}
    }).then(response => {
      if (response.ok) {
        document.location.href = '/';
      } else {
        alert('Erros happen');
      }
    }).catch(error => alert('Error: ', error.message));
 
}
