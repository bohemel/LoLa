(function($) {
  $(function() {
    $.get('/authenticate', function(data) {
      var s = $('p', $(data)).eq(0).text();
      if(s === 'You are logged in to LoLa!.') {
        $('#user-menu').show();

      }
    });
  });
})(jQuery);
