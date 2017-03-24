// ------------------------------------
//
// Maps Module
//
// ------------------------------------


(function($) {

    if (typeof window.Map == 'undefined') window.Map = {};

    Map = {
        lat: -27.9210555,
        lng: 133.247866,
        // lat: -42.181723,
        // long: 156.181641,
        markers: [],
        active_marker: null,
        map: null,
        infoWindows: [],
        park: false,

        // ------------------------------------
        // Init
        // ------------------------------------

        init: function( location ) {

            if($('.map').length <= 0){ return; }

            console.log("Map::init");

            this.options = {
                zoom: 5,// How zoomed in you want the map to start at (always required)
                center: new google.maps.LatLng(this.lat, this.lng),
                zoomControl: false,
                disableDoubleClickZoom: false,
                mapTypeControl: false,
                scaleControl: false,
                scrollwheel: false,
                panControl: false,
                streetViewControl: false,
                draggable : true,
                overviewMapControl: true,
                overviewMapControlOptions: {
                    opened: false,
                },
                // styles : [{"featureType":"all","elementType":"labels.text.fill","stylers":[{"saturation":36},{"visibility":"off"},{"color":"#333333"},{"lightness":40}]},{"featureType":"all","elementType":"labels.text.stroke","stylers":[{"visibility":"off"},{"color":"#ffffff"},{"lightness":16}]},{"featureType":"all","elementType":"labels.icon","stylers":[{"visibility":"off"}]},{"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},{"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#fefefe"},{"lightness":17},{"weight":1.2}]},{"featureType":"administrative.country","elementType":"geometry.fill","stylers":[{"visibility":"off"},{"saturation":"13"}]},{"featureType":"administrative.country","elementType":"geometry.stroke","stylers":[{"color":"#dc2323"},{"visibility":"off"}]},{"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":20}]},{"featureType":"landscape.natural.landcover","elementType":"geometry.fill","stylers":[{"color":"#fffefd"}]},{"featureType":"landscape.natural.landcover","elementType":"labels","stylers":[{"visibility":"off"}]},{"featureType":"landscape.natural.landcover","elementType":"labels.text","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},{"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#dedede"},{"lightness":21}]},{"featureType":"road","elementType":"all","stylers":[{"visibility":"on"},{"weight": "2.00"}]},{"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"visibility":"on"},{"color":"#000"},{"lightness":17}]},{"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#000"},{"lightness":29},{"weight":0.2}]},{"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#000"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#000"},{"lightness":16}]},{"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},{"featureType":"transit.line","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.station.airport","elementType":"all","stylers":[{"visibility":"on"}]},{"featureType":"transit.station.bus","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"transit.station.rail","elementType":"all","stylers":[{"visibility":"off"}]},{"featureType":"water","elementType":"geometry","stylers":[{"color":"#e9e9e9"},{"lightness":17}]},{"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#e1e1e1"}]}]
                styles: [
                    {"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#009dd1"}]},
                    {"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#efefef"}]},
                    {"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#fbb900"}]},
                    {"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#efc964"},{"weight":0.2}]},
                    {"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},
                    {"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},
                    {"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e0eadc"}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":15}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},
                    {"featureType":"transit","elementType":"geometry","stylers":[{"visibility": "off"}]},
                    {"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},
                    {"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#A2B0B0"},{"weight":1.7}]}


                    // {"featureType":"water","elementType":"geometry.fill","stylers":[{"color":"#cbe9ff"}]},
                    // {"featureType":"landscape","elementType":"geometry","stylers":[{"color":"#e5e5e5"},{"lightness":20}]},
                    // {"featureType":"road.highway","elementType":"geometry.fill","stylers":[{"color":"#ffffff"},{"lightness":17}]},
                    // {"featureType":"road.highway","elementType":"geometry.stroke","stylers":[{"color":"#d4e0e0"},{"weight":0.2}]},
                    // {"featureType":"road.arterial","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":18}]},{"featureType":"road.local","elementType":"geometry","stylers":[{"color":"#ffffff"},{"lightness":16}]},
                    // {"featureType":"poi","elementType":"geometry","stylers":[{"color":"#f5f5f5"},{"lightness":21}]},
                    // {"featureType":"poi.park","elementType":"geometry","stylers":[{"color":"#e5e5e5"}]},{"elementType":"labels.text.stroke","stylers":[{"visibility":"on"},{"color":"#ffffff"},{"lightness":16}]},{"elementType":"labels.text.fill","stylers":[{"saturation":36},{"color":"#333333"},{"lightness":40}]},{"elementType":"labels.icon","stylers":[{"visibility":"off"}]},
                    // {"featureType":"transit","elementType":"geometry","stylers":[{"color":"#f2f2f2"},{"lightness":19}]},
                    // {"featureType":"administrative","elementType":"geometry.fill","stylers":[{"color":"#fefefe"},{"lightness":20}]},
                    // {"featureType":"administrative","elementType":"geometry.stroke","stylers":[{"color":"#A2B0B0"},{"weight":1.7}]}
                ]
            };


            if(location){
                this.options.zoom = 12;
                this.options.center = new google.maps.LatLng(location.lat, location.lng);
            }
            

            // Set Center To Austalia
            // Map.options.center = new google.maps.LatLng(Map.lat, Map.lng-6);

            var map_container = jQuery('#map');
            // var type = map_container.attr('data-mapType');
            var type = map_container.data('mapType') ? map_container.data('mapType') : map_container.attr('data-mapType');

            if(type == 'locations'){
                this.options.zoomControl = true;
                google.maps.event.addDomListener(window, 'load', Map.locations);
            } else if( type == 'simple') {


            } else if( type == 'read'){
                google.maps.event.addDomListener(window, 'load', Map.read);
            } else if( type == 'read-single'){
                
                console.log('MAP TYPE:: read-single')

                this.options.zoom = 14;
                
                // this.park = true;
                this.park = false;

                google.maps.event.addDomListener(window, 'load', Map.read);
            }

        },


        // ------------------------------------
        // Simple
        // ------------------------------------

        simple: function(){

            var map_container = jQuery('#map');

            // Get the HTML DOM element that will contain your map
            var mapElement = document.getElementById('map');

            // Map.options.zoom = 3;

            // Create the Google Map using our element and options defined above
            Map.map = new google.maps.Map(mapElement, Map.options);

            // Plot Locations
            // var locations = Map.readLocations();

        },

        // ------------------------------------
        // Read
        // ------------------------------------

        read: function(){

            var map_container = jQuery('#map');

            // Get the HTML DOM element that will contain your map
            var mapElement = document.getElementById('map');

            // Map.options.zoom = 3;

            // Create the Google Map using our element and options defined above
            Map.map = new google.maps.Map(mapElement, Map.options);

            // Plot Locations
            var locations = Map.readLocations();

        },

        // ------------------------------------
        // Read Locations
        // ------------------------------------

        readLocations: function(){


            $('[data-lat]').each(function(e){


                // offset
                var offset = $(this).attr('data-offset');

                if( offset ) {

                    var lat = $(this).attr('data-lat')
                    var lng = ($(this).attr('data-lng') - 0.03)

                    var center = new google.maps.LatLng(lat, lng);

                    Map.map.panTo(center);
                }
                else {
                    var marker = new google.maps.Marker({
                        position: new google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng')),
                        map: Map.map,
                        animation: google.maps.Animation.DROP,
                        icon: '/wp-content/themes/cpp/assets/dist/img/map-marker.png',
                    });


                    marker.addListener('click',function(){

                        $('html, body').animate({
                            scrollTop: $('[data-lat="'+this.position.lat()+'"]').offset().top
                        }, 500);

                    });
                }



                if(Map.park == true){

                    var center = new google.maps.LatLng($(this).attr('data-lat'), $(this).attr('data-lng'));
                    Map.map.panTo(center);

                }

            });



        },

        // ------------------------------------
        // Locations
        // ------------------------------------

        locations: function(){

            var map_container = jQuery('#map');

             // Get the HTML DOM element that will contain your map
            var mapElement = document.getElementById('map');

            // Map.options.zoom = 3;

            // Create the Google Map using our element and options defined above
            Map.map = new google.maps.Map(mapElement, Map.options);

            console.log('Map', Map)

            // Plot Locations
            var locations = Map.plotLocations();

        },

        // ------------------------------------
        // Get Parks
        // ------------------------------------

        plotLocations: function(){


            $.get('/api/partners', function(data) {

                for(var i in data){

                    var m = {};
                    m.title     = data[i].title;
                    m.content   = data[i].description;
                    m.lat       = data[i].location.lat;
                    m.lng       = data[i].location.lng;
                    m.status    = data[i].status;
                    m.link      = data[i].link;


                    // Only add active partners
                    if( data[i].status[0] === 'partner' ) {
                        // New Window
                        var infoWindow = new google.maps.InfoWindow({
                            content: `<div class="info-window">
                                            <div class="wrap">
                                                <h3>${data[i].title}</h3>
                                                <p>${data[i].description}</p>
                                                <a href="${data[i].link}">Learn More</a>
                                            </div>
                                      </div>`
                        });

                        Map.infoWindows[i] = infoWindow;

                        var icon = '/wp-content/themes/cpp/assets/dist/img/map-marker.png';

                        // // New Marker
                        var marker = new google.maps.Marker({
                            position: new google.maps.LatLng(data[i].location.lat, data[i].location.lng),
                            map: Map.map,
                            title: data[i].title,
                            excerpt: data[i].content,
                            animation: google.maps.Animation.DROP,
                            icon: icon,
                            link: data[i].link,
                            info: infoWindow
                        });

                        marker.addListener('click',function(){
                            $('.google-info-window').removeClass('active');
                            for(var w in Map.infoWindows){
                                Map.infoWindows[w].close();
                            }

                            this.info.open(Map.map,this);

                        });


                        google.maps.event.addListener(infoWindow, 'domready', function() {
                            var l = $('.info-window').parent().parent().parent().parent().addClass('google-info-window');
                            setTimeout(function(){
                                l.addClass('active');
                            },200);

                            // for (var i = 0; i < l.length; i++) {
                            //     if($(l[i]).css('z-index') == 'auto') {
                            //         $(l[i]).css('border-radius', '16px 16px 16px 16px');
                            //         $(l[i]).css('border', '2px solid red');
                            //     }
                            // }
                        });
                    }
                }

            });

        },

        // ------------------------------------
        // Add Marker
        // ------------------------------------

        add_marker: function (data,click_event)
        {
            var marker = new google.maps.Marker({
                position: new google.maps.LatLng(data.lat, data.lng),
                map: Map.map,
                title: data.title,
                excerpt: data.content,
                animation: google.maps.Animation.DROP,
                icon: '/wp-content/themes/cpp/assets/dist/img/map-marker.png',
                link: data.link
            });

            if(click_event){
                marker.addListener('click',click_event);
            }

            Map.markers.push(marker);


            return marker;
        },

        // ------------------------------------
        // Clear Markers
        // ------------------------------------

        clear_markers: function(map)
        {
            for (var i = 0; i < Map.markers.length; i++) {
                Map.markers[i].setMap(map);
            }
            Map.markers = [];
        },

        // ------------------------------------
        // Marker Click
        // ------------------------------------

        marker_click: function(){

            window.location = this.link;
            return;

        },

        // ------------------------------------
        // Location Click
        // ------------------------------------

        locationClick: function(){

            var marker = this;

            // Set Content
            var $title   = $('.map-row .need-to-know .title');
            var $content = $('.map-row .need-to-know .text');
            console.log(marker.excerpt);

            setTimeout(function(){ $title.fadeOut(); }, 1 );
            setTimeout(function(){ $content.fadeOut(); }, 1 );
            setTimeout(function(){ $title.text(decodeURIComponent(marker.title).replace(/\+/g, " ")); }, 500 );
            setTimeout(function(){ $content.html(decodeURIComponent(marker.excerpt).replace(/\+/g, " ")); }, 500 );
            setTimeout(function(){ $title.fadeIn(); }, 700 );
            setTimeout(function(){ $content.fadeIn(); }, 800 );

            // Fade in
            $('.map-row .need-to-know').addClass('active');

        }

    }

    module.exports = Map;

})(jQuery);