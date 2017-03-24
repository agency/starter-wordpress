/*!
 * jQuery Share Count Plugin
 * Original author: @agencysc
 * Licensed under the MIT license
 */


// the semi-colon before the function invocation is a safety
// net against concatenated scripts and/or other plugins
// that are not closed properly.
;(function ($, window, document, undefined) {

  // undefined is used here as the undefined global
  // variable in ECMAScript 3 and is mutable (i.e. it can
  // be changed by someone else). undefined isn't really
  // being passed in so we can ensure that its value is
  // truly undefined. In ES5, undefined can no longer be
  // modified.

  // window and document are passed through as local
  // variables rather than as globals, because this (slightly)
  // quickens the resolution process and can be more
  // efficiently minified (especially when both are
  // regularly referenced in your plugin).

  // Create the defaults once
  const shareCount = 'shareCount';
  const defaults = {
    facebook: {
      baseUrl: 'https://graph.facebook.com/?id=',
    },
    twitter: {
      baseUrl: 'https://opensharecount.com/count.json?url=',
    },
    sharesText: 'shares',
  };

  class ShareCountPlugin {

    constructor($el, options) {
      this.$el = $el;

      this.options = $.extend({}, defaults, options);
      this.name = shareCount;

      this.options.url = (typeof this.$el.data('url') !== 'undefined') ? $el.data('url') : document.URL;
      this.options.network = (typeof this.$el.data('share') !== 'undefined') ? $el.data('share') : 'facebook';
      this.shares = 0;

      this.init();
    }

    init() {
      this.$el.addClass('js-shareCount');
      this.getShares();
    }

    /**
     * Get shares based on options
     */
    getShares() {
      switch (this.options.network) {

        case 'facebook':
          this.getFacebookShares()
            .then((shares) => {
              this.updateShares(shares);
            })
            .catch((e) => {
              throw e;
            });
          break;

        case 'twitter':
          this.getTwitterShares()
            .then((shares) => {
              this.updateShares(shares);
            })
            .catch((e) => {
              throw e;
            });
          break;

        case 'twitter+facebook':

          // Run Twitter and Facebook Promises at the same time
          Promise.all([this.getFacebookShares(), this.getTwitterShares()])
            .then((sharesArray) => {
              // Sum up all shares
              const shares = sharesArray.reduce((acc, val) => {
                return acc + val;
              }, 0);

              this.updateShares(shares);
            })
            .catch((e) => {
              throw e;
            });
          break;
      }
    }

    /**
     * Updates elements share count
     */
    updateShares(shares) {
      this.shares = shares;

      if (shares == 0) return;
      this.$el
        .text(`${shares} ${this.options.sharesText}`)
        .addClass('active');
    }

    /**
     * Returns Promise that returns facebook shares
     */
    getFacebookShares() {

      return new Promise((resolve, reject) => {
        $.getJSON(`${this.options.facebook.baseUrl}${this.options.url}`, function(data) {
          if (!data || !data.count)
            resolve(0);
            //reject(new Error('Can\'t get shares'));
          else resolve(data.count);
        }).complete(() => resolve(0));
      });
    }

    /**
     * Returns Promise that returns twitter shares
     */
    getTwitterShares() {

      return new Promise((resolve, reject) => {
        $.getJSON(`${this.options.twitter.baseUrl}${this.options.url}`, function(data) {
          if (!data || !data.share || !data.share.share_count) resolve(0);
          //reject(new Error('Can\'t get shares'));

          else resolve(data.share.share_count);
        }).complete(() => resolve(0));
      });
    }
  }

  // A really lightweight plugin wrapper around the constructor,
  // preventing against multiple instantiations
  $.fn[shareCount] = function (options) {

    return this.each(function () {
      if (!$.data($(this), 'plugin_' + shareCount)) {
        $.data($(this), 'plugin_' + shareCount,
        new ShareCountPlugin($(this), options));
      }
    });
  };

})(jQuery, window, document);
