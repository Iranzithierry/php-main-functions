window.onload = function () {
  document.getElementById("fname").focus();
  $(document).ready(function () {
    $.getJSON("https://ipapi.co/json/", function (data) {
      var country_code = data.country_code;
      $("#country").val(country_code);
      country_code.focus();
    });
  });
};
const login_btn = document.querySelector(".login-button"),
     error_text = document.querySelector(".error-text");

     login_btn.addEventListener("click", function(e){
      let error_text = document.querySelector('.error-text');
      if (error_text) {
        window.scrollTo({
          top: error_text.offsetTop,
          behavior: "smooth",
        });
      }
    });
    