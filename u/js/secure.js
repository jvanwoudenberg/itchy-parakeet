$(document).ready(function() {
  $('[data-toggle=offcanvas]').click(function() {
    $('.row-offcanvas').toggleClass('active');
  });
  setInterval(function() {
  	$.ajax({
       url: 'checklogin.php',
       cache: false,
    });
  }, 60000);
  $(window).focus( function() {
  	$.ajax({
       url: 'checklogin.php',
       cache: false,
    });
  })
});
