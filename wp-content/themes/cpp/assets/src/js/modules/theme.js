// ------------------------------------
//
// Theme
//
// ------------------------------------

const util = require('./util.js');
require('./../plugins/count-to.js');

(function($) {

  if (typeof window.Theme == 'undefined') window.Theme = {};

  Theme = {

    $nav: $('.nav-main'),
    $navToggle: $('#nav-toggle'),
    $jsFooter: $('.js-footer-cols'),

    settings: {},

    
    // ----------------------------
    // Theme Init
    // ----------------------------
    init: function() {

      // Share Links
      this.shareLinks();

      // Add Placeholder Support
      this.placeholders();

      // Format Number
      this.formatNumbers();

      // Equalize height
      this.equalizeHeight();


      $(window).smartresize(() => {
        this.equalizeHeight();
      });

      // Mobile menu
      this.mobileMenu();
      
      // Footer wrap
      this.footerWrap();

      // Councils
      this.partners();

      // Layout
      this.layout();
      
      // Forms
      this.forms();

      // Donate
      this.donate();


      // Load desktop and mobile only scripts
      if ( isMobile() ) {
        
      } else {

        this.scrollMagic();

      }

    },  


    // ----------------------------
    // js layout
    // ----------------------------
    layout() {
      if( $('.bg-extend').length > 0 ) {

        var elem  = $('.bg-extend'),
            padd  = 20,
            maxW  = 1200

        setBlockWidth(elem, padd, maxW);


        $(window).on('resize', function () {

          setBlockWidth(elem, padd, maxW);

        });

      }
    },


    // ----------------------------
    // Forms
    // ----------------------------
    forms() {

      $(document).delegate('.submit-wrap', 'click', function(e) {
        e.preventDefault();

        console.log('delegate -- clicked on submit wrap')

        $(this).find('input[type=button]').click();
      })

      $('.submit-wrap').on('click', function(e){

        e.preventDefault();

        console.log('clikced on submit wrap')

      });


    },


    // ----------------------------
    // Mobile Menu
    // ----------------------------
    mobileMenu() {

      $('#nav-toggle').on('click', function(e){

        e.preventDefault();

        $(this).toggleClass('-x');
        Theme.$nav.toggleClass('-open')

      });


    },



    // ----------------------------
    // 
    // ----------------------------
    footerWrap() {

      if( !Theme.$jsFooter.length > 0 ) return;

      if( !$('.js-wrap-left').length > 0 ) return; 
        $('.js-wrap-left').css('opacity','0')
        $('.js-wrap-left').wrapAll('<div class="col -left"></div>')
        $('.js-wrap-left').css('opacity','1')
      
      if( !$('.js-wrap-mid').length > 0 ) return; 
        $('.js-wrap-mid').css('opacity','0')
        $('.js-wrap-mid').wrapAll('<div class="col -mid"></div>')
        $('.js-wrap-mid').css('opacity','1')
      
      if( !$('.js-wrap-right').length > 0 ) return;
        $('.js-wrap-right').css('opacity','0')
        $('.js-wrap-right').wrapAll('<div class="col -right"></div>')
        $('.js-wrap-right').css('opacity','1')

    },


    // ----------------------------
    // Equalize Height
    // ----------------------------
    equalizeHeight() {
      // if (util.isMobile()) return;
      if( $(window).width() < 736 ) return;

      if ($('[data-equal-heights]').length < 1) return;

        function setEqualHeights() {
          $('[data-equal-heights]').each(function () {

            // _this.setHeights();

            var eqSelectors = $(this).data('equal-heights'),
                $eqSelectors = $(this).find(eqSelectors),
                mobile = $(this).data('equal-mobile') ? true : false;

            // Cache the highest
            var highestBox = 0;

            if ($(window).width() < 768 && mobile != true) {

              $($eqSelectors, this).each(function () {
                $(this).css('height', '');
              });
            } else {

              // Select and loop the elements you want to equalise
              $($eqSelectors, this).each(function () {

                // Remove height if set from before to account for resize
                $(this).css('height', '');

                // If this box is higher than the cached highest then store it
                if ($(this).height() > highestBox) {
                  highestBox = $(this).height();
                }
              });

              // Set the height of all those children to whichever was highest 
              $($eqSelectors, this).height(highestBox);
            }
          });
        }

        setEqualHeights();

        $(window).on('resize', function () {

          setEqualHeights();
        });

        // $(window).resize($.throttle(1000, setEqualHeights));
    },

    // ----------------------------
    // Share Links
    // ----------------------------
    shareLinks: function(){

      $("a[data-share]").share({
        counts:false
      });

      $("[data-shareCount]").each(function() {
        $(this).shareCount();
      }); //shareCount();

    },


    // ----------------------------
    // Placeholders
    // ----------------------------
    placeholders: function(){

      // $('input, textarea').placeholder();

    },


    // ----------------------------
    // Councils
    // ----------------------------
    partners: function() {

      // Hero width
      // ----------------------------
      if( $('.js-bg-solid').length > 0 ) {

        var elem = $('.js-bg-solid'),
            padd  = 20,
            maxW = 960

        setBlockWidth(elem, padd, maxW);


        $(window).on('resize', function () {

          setBlockWidth(elem, padd, maxW);

        });

      }

      // Postcodes
      // ----------------------------
      $('#postcode-redirect').on('click', function(e) {
        e.preventDefault();

        var $form     = $(this).parent().parent('form'),
            $results  = $form.find('.form-results'),
            $redirect = $form.find('#postcode-redirect-url'),
            postcode  = $('input#postcode').val(),
            loading   = $('<div class="loading-dots"><span></span><span></span><span></span></div>'),
            text      = $(this).data('button-text') ? $(this).data('button-text') : 'Submit'

            

        $(this).addClass('-disabled')
        $(this).html(loading)

        if( postcode && postcode.length === 4) { 

          $.get('/api/partners/search?postcode=' + postcode, function(data) {
            
            if( data && (data[0]['status'] === 'partner' )) {

              var partner = data[0]['link']

              if( partner ) {
                $form.attr('action', data[0]['link'])
               
              }
              else {
                $form.attr('action', '/nominate')
              }

              $form.submit()
            }
            else {
              console.log('Postcode not found')
              
              $form.attr('action', '/pending')
              $form.submit()
            }
          })


        }
        else {

          setTimeout(function(){
            
            $error.html('Please enter a valid postcode.')

            $('#postcode-submit').removeClass('-disabled')
            $('#postcode-submit').html(text)

          }, 500);
        }

        
      })

      // NOMINATE PAGE
      $('#postcode-lookup').on('click', function(e) {
        e.preventDefault();

        var $form     = $(this).parent().parent('form'),
            $container= $form.parent('.aside-nominate'),
            $results  = $form.find('.form-results-wrapper'),
            $redirect = $form.find('#postcode-redirect-url'),
            postcode  = $('input#postcode').val(),
            loading   = $('<div class="loading-dots"><span></span><span></span><span></span></div>');

        // Hidden form fields
        var $hidden_postcode = $('input#nf-field-9'),
            $hidden_community= $('input#nf-field-14')

        console.log('POSTCODE', postcode)


        $(this).addClass('-disabled')
        $results.find('.loading-dots-wrapper').html(loading)
        $container.removeClassPrefix('-response')
        $hidden_postcode.val(postcode)

        if( postcode && postcode.length === 4) { 

          $.get('/api/partners/search?postcode=' + postcode, function(data) {
            
            if( data && (data[0]['status'] === 'partner' )) {

              var partner = data[0]['link']

              if( partner ) {
                $redirect.attr('href',partner)
              }

              setTimeout(function(){
                
                $container.addClass('-response-partner-exists')
                $results.find('.loading-dots-wrapper').html('')

              }, 500);
            }
            else {


              if( data ) {

                var community = data[0]['title'];

                $('input#nf-field-14').val(community)
              }
              
              setTimeout(function(){
                

                $container.addClass('-response-partner-pending')
                $results.find('.loading-dots-wrapper').html('')

              }, 500);
            }
          })


        }
        else {
          setTimeout(function(){
            $results.find('.loading-dots-wrapper').html('')
            $container.addClass('-response-error')
            $('#postcode-lookup').removeClass('-disabled')

          }, 500);
          
        }

        
      })


      $('input#postcode').on('focus', function(e) {

        var $form    = $(this).parent('form'),
            $button  = $form.find('.postcode-submit'),
            text     = $button.data('button-text') ? $button.data('button-text') : 'Submit',
            $error   = $form.find('.form-errors')

        if( $button.hasClass('-disabled')) {
          if( $button.hasClass('-animate-loading')) {
            $button.removeClass('-disabled')
            $button.html(text)
            $error.html('')
          }
          else {
            $button.removeClass('-disabled')
          }
        }

      })


      // If postcode exists on nominate page
      // ----------------------------
      if( $('.aside-nominate').length > 0  ) {

        if ($.urlParam('postcode') != 0) {

          console.log('postcode param exists')

          $('#postcode-lookup').click();

        }
        else {

        }
      }

        
        
    },

    // ------------------------------------
    // Format numbers with comma
    // ------------------------------------

    formatNumbers: function() {

      $('.-format-number').each( function() {
        
        $(this).text( formatNumber( $(this).text() ) );

      });

      if (isMobile()) {

        $('.-scroll-counter').each( function() {
        
          $(this).text( formatNumber( $(this).text() ) );

        });
        
      }

    },



    // ----------------------------
    // Scroll Magic
    // ----------------------------
    scrollMagic: function() {
      var controller = new ScrollMagic.Controller();
      
      // ------------------------------------
      // animate active section
      // ------------------------------------
      $('section').each( function() {

        var section_id = $(this).attr('id');
        var section_height = $(this).height();

        new $.ScrollMagic.Scene({
          duration: section_height,
          triggerElement: $(this)
        })
          .setClassToggle('#nav-' + section_id, '-active')
          .addTo(controller);

      });

      $('.subsection-parent').each( function() {

        var subsection_id = $(this).attr('id');
        var subsection_height = $(this).height();

        new $.ScrollMagic.Scene({
          duration: subsection_height,
          triggerElement: $(this)
        })
          .setClassToggle('#nav-' + subsection_id, '-active')
          .addTo(controller);

      });

      $('.-bg-change').each( function() {

        var bg_id = $(this).attr('id');
        var bg_height = $(this).height();

        new $.ScrollMagic.Scene({
          duration: 0,
          triggerElement: $(this).data('change'),
          triggerHook: 'onLeave'
        })
          .setClassToggle('body', '-' + bg_id)
          .addTo(controller);

      });

      // ------------------------------------
      // animate zoomIn
      // ------------------------------------
      $('.-scroll-zoomIn').each( function() {

        $(this).addClass('-inactive');

        new $.ScrollMagic.Scene({
          duration: '100%',
          triggerElement: $(this).parents('.subsection')

        })
          .setClassToggle( $(this), '-active animated zoomIn')
          .addTo(controller);

      });


      // ------------------------------------
      // animate fadeOut
      // ------------------------------------
      $('.-scroll-fadeOut').each( function() {

        new $.ScrollMagic.Scene({
          duration: '100%',
          triggerElement: $(this),
          triggerHook: 'onLeave'
        })
          .setClassToggle( $(this), 'animated fadeOut')
          .addTo(controller);

      });


      // ------------------------------------
      // animate fadeInUp
      // ------------------------------------
      $('.-scroll-fadeInUp').each( function() {

        $(this).addClass('-inactive');

        new $.ScrollMagic.Scene({
          duration: 0,
          triggerElement: $(this).parents('[data-scroll-trigger]')
        })
          .setClassToggle( $(this), '-active animated fadeInUp')
          .addTo(controller);

      });


      // ------------------------------------
      // animate fadeOutUp
      // ------------------------------------
      $('.-scroll-fadeOutUp').each( function() {

        new $.ScrollMagic.Scene({
            duration: '100%',
            triggerElement: $(this).parents('.subsection'),
            triggerHook: 'onLeave'
        })
          .setClassToggle( $(this), 'animated fadeOutUp')
          .addTo(controller);

      });


      // ------------------------------------
      // animate parallax
      // ------------------------------------
      $('.-scroll-parallax').each( function() {

        var $fast_layers = $(this).find('.layer-fast');
        var $slow_layers = $(this).find('.layer-slow');
        var $slower_layers = $(this).find('.layer-slower');

        // build neonate
        var neonate = new TimelineMax ()
          .add([
            TweenMax.fromTo( $fast_layers, 1, {top: 250}, {top: -150, ease: Linear.easeNone}),
            TweenMax.fromTo( $slow_layers, 1, {top: 150}, {top: -50, ease: Linear.easeNone}),
            TweenMax.fromTo( $slower_layers, 1, {top: 50}, {top: 0, ease: Linear.easeNone})
          ]);

        // create scene
        new $.ScrollMagic.Scene({
          duration: '100%',
          triggerElement: $(this)
        })
          .setTween(neonate)
          .addTo(controller);

      });


      // ------------------------------------
      // animate counter
      // ------------------------------------
      $('.-scroll-counter').each( function() {

        new $.ScrollMagic.Scene({
          duration: 0,
          triggerElement: $(this),
          triggerHook: 'onEnter'
        })
          .on('start', function() {

            var element = this.triggerElement();

            var $counter = $('#' + element.id);

            var total = $counter.data('total');
            var duration = $counter.data('duration');
            var multiplier = ( total > 1000000 ? 1000 : 1 );

    
            // animate value from x to y:
            $({someValue: 0}).animate({someValue: total}, {
              duration: duration,
              easing:'swing', 
              step: function() {
                // update text with rounded-up value
                $counter.text( formatNumber( Math.ceil(this.someValue / multiplier) * multiplier ) );
              }
            });

          })
          .addTo(controller);

      });



    },

    // ----------------------------
    // Donate
    // ----------------------------
    donate() {
      Payments.form.on('PAYMENTS_FORM_SCREEN_NEXT',function(element){
        var $form   = $('.payments-form'),
            active  = $form.find('.screen.active').index()

        $form.removeClassPrefix('-step-')
        $form.addClass('-step-'+active)
      })

      Payments.form.on('PAYMENTS_FORM_SCREEN_PREV',function(element){
        var $form   = $('.payments-form'),
            active  = $form.find('.screen.active').index()

        $form.removeClassPrefix('-step-')
        $form.addClass('-step-'+active)
      })
    },



    // ----------------------------
    // 
    // ----------------------------
    


  };

  module.exports = Theme;


  /**
   * Remove Class Prefix
   * 
   * @param  {String} prefix 
   */
  $.fn.removeClassPrefix = function(prefix) {
    this.each(function(i, el) {
      var classes = el.className.split(" ").filter(function(c) {
          return c.lastIndexOf(prefix, 0) !== 0;
      });
      el.className = $.trim(classes.join(" "));
    });
    return this;
  };


  function setBlockWidth(elem, padd, maxW) {
    var winW  = $(window).width(),
        diffW = (winW - maxW) / 2 + 10;



    if( winW > maxW ) {
      elem.css('width',diffW)

      if( elem.hasClass('-pos-left'))
        elem.css('left','-'+diffW+'px')  

    }
    else {

      // if( winW < 736 ) {
      //   elem.css('width','')
      // }
      // else {

      if( elem.hasClass('-mobile-full') && winW < 736 ) {
        elem.css('width','100%')
      }
      else {
        elem.css('width',padd)

        if( elem.hasClass('-pos-left'))
          elem.css('left','-'+padd+'px')  
      }


      // }
    }
  }


  function formatNumber( val ) {
    while (/(\d+)(\d{3})/.test(val.toString())){
      val = val.toString().replace(/(\d)(?=(\d\d\d)+(?!\d))/g, "$1,");
    }
    return val;
  }

  function isMobile() {
    return ( $(window).width() < 769 ? true : false );
  }



  $.urlParam = function(name){
    var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
    return results[1] || 0;
  }



})(jQuery);