(function($, Drupal) {
  /**
   * Sticky header
   */
  Drupal.behaviors.heightFitContent = {
    attach: function(context, settings) {
      const iframe = $('.link-iframe-formatter');
      $(window, context).on("load resize", function() {
        if (drupalSettings.linkIframeFormatter.scripts.responsive) {
          let maxW = iframe.scrollWidth;
          let minW = maxW;
          let iframeHeight = iframe.height(); //IFrame starting height

          // while (minW == maxW) {
          //     iframeHeight += 100;    // increment
          //     iframe.height(iframeHeight);
          //     minW = iframe.scrollWidth;
          //     console.log(`iFrame Height: ${iframe.height()}`);
          //     console.log(`maxW: ${maxW}`);
          //     console.log(`minW: ${minW}`);
          // }
        }
      });
    }
  };
})(jQuery, Drupal);