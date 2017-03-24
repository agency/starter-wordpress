let util = require('./util.js');

// ------------------------------------
//
// Video
//
// ------------------------------------

(function($) {

  class VideoManager {

    constructor($container, $playButton, Util) {
      this.player = null;
      this.$container = $container;
      this.$playButton = $playButton;
      this.$stopButton = this.$container.find('.js-video-stop');
    }

    init() {
      
       // Add scrips
      this.scripts();

      window.onYouTubeIframeAPIReady = () => {
          this.eventListener();
      }
    }

    getId() {
     return this.$playButton.data('youtube-id');
    }

    createVideo(videoId) {

      this.player = null;
      $('#youtube-frame').remove();
      let $frame = $('<div>').attr('id', 'youtube-frame');

      this.$container.find('.video-container').append($frame); 

      this.player = new YT.Player('youtube-frame', {
        height: '390',
        width: '640',
        videoId: videoId,
        playerVars: { 'autoplay': 0, 'controls': 0, 'showinfo': 0, rel: 0, fs: 0 },
        events: {
          'onReady': (e) => {
            this.onPlayerReady(e);
          },
        }
      });
    }

    eventListener() {
      this.$playButton.click((e) => {
        e.preventDefault();
        this.createVideo(this.getId());
      })

      this.$stopButton.click((e) => {
        e.preventDefault();
        this.stopVideo();
      })
    }

    scripts() {

      let tag = document.createElement('script');
      tag.src = "https://www.youtube.com/iframe_api";

      let firstScriptTag = document.getElementsByTagName('script')[0];
      firstScriptTag.parentNode.insertBefore(tag, firstScriptTag);

    }
    onPlayerReady(event) {
      this.playVideo();
    }

    playVideo() {
      $('body').addClass('-overlay');
      this.$container.fadeIn();
      this.player.playVideo();
    }
    
    stopVideo() {
      $('body').removeClass('-overlay');
      this.$container.fadeOut();
      this.player.stopVideo();
    }

  }

  module.exports = VideoManager;

})(jQuery);