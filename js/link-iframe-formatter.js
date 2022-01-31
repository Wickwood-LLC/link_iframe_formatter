(function($, Drupal) {
  /**
   * Sticky header
   */
  Drupal.behaviors.heightFitContent = {
    attach: function(context, settings) {
      const iframe = $('.link-iframe-formatter');
      $(window, context).on("load resize", function() {
        if (drupalSettings.linkIframeFormatter.scripts.responsive) {
          iframe.style.height = iframe.contentWindow.document.body.scrollHeight + 'px';
        }
      });
    }
  };
})(jQuery, Drupal);