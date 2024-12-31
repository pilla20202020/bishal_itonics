(function (Drupal) {
  Drupal.editors.tinymce = {
    attach: function attach(element, format) {
      format.editorSettings.json.selector = '#' + element.id;
      tinymce.init(format.editorSettings.json);
    },
    detach: function detach(element, format, trigger) {
      if (trigger !== 'serialize') {
        tinymce.get(element.id).remove();
      }
    },
    onChange: function onChange(element, callback) {
      tinymce.get(element.id).on('change', callback);
    }
  };

})(Drupal);
