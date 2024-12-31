(function ($) {

Drupal.behaviors.datepicker = {
  attach: function() {
    for (var id in Drupal.settings.datePopup) {
      $('#'+ id).once('datepicker', function() {
        datePopup = Drupal.settings.datePopup[id];
        datePopup.settings.onSelect = function(date) {
          $(datePopup.settings.altField).trigger('change');
        };
        switch (datePopup.func) {
          case 'datepicker-inline':
            $(this).wrap('<div id="' + id + '-wrapper" />');
            if (datePopup.settings.defaultDate != null) {
              datePopup.settings.defaultDate = $('#' + id).val();
            }
            $(this).parent().datepicker(datePopup.settings);
            $(this).hide();
            
          break;
        }
      });
    }
  }
}

})(jQuery);
