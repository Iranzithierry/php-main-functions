
window.onload = function() {
  document.getElementById("fname").focus();
$(document).ready(function() {
  $.getJSON('https://ipapi.co/json/', function(data) {
      var country_code = data.country_code;
      $('#country').val(country_code);
      country_code.focus();
  });
});
}