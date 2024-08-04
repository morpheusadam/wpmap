/**
 * This jQuery plugin displays map and it's components.
 * @author Flipper Code (hello *at* flippercode *dot* com)
 * @version 1.0
 */
(function($, window, document, undefined) {
    "use strict";
    
    var Map_Control = function(options) {
        this.options = options;
    }
    Map_Control.prototype.create_element = function(controlDiv, map, html_element) {
        // Set CSS for the control border
        controlDiv.className = 'wpomp-control-outer';
        var controlUI = document.createElement('div');
        controlUI.className = 'wpomp-control-inner';
        controlDiv.appendChild(controlUI);
        // Set CSS for the control interior
        var controlText = document.createElement('div');
        controlText.className = 'wpomp-control-content';
        controlText.innerHTML = html_element;
        controlUI.appendChild(controlText);

    };



    var OSMMaps = function(element, map_data) {

        
        var options;
        this.element = element;
        map_data = JSON.parse(atob(map_data));
        this.map_data = $.extend({}, {}, map_data);
        options = this.map_data.map_options;
        this.settings = $.extend({

            "min_zoom": "0",

            "max_zoom": "19",

            "zoom": "5",

            "map_type_id": "mapbox.streets",

            "scroll_wheel": true,

            "map_visual_refresh": false,

            "full_screen_control": false,

            "full_screen_control_position": "bottomright",

            "zoom_control": true,

            "zoom_control_style": "SMALL",

            "zoom_control_position": "topleft",

            "map_type_control": true,

            "map_type_control_style": "HORIZONTAL_BAR",

            "map_type_control_position": "topright",

            "scale_control": true,

            "overview_map_control": true,

            "center_lat": "40.6153983",

            "center_lng": "-74.2535216",

            "draggable": true,

            "gesture": "auto",

            "infowindow_open_event" : "click",
            'map_data_provider' :'openstreet',
            'map_tile_url':'https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png',
            'search_control':false,
            'locateme_control':false,
            'map_type_control':false

        }, {}, options);



        this.container = $("div[rel='" + $(this.element).attr("id") + "']");

        this.places = [];

        this.show_places = [];

        this.categories = {};

        this.tabs = [];

        this.per_page_value = 0;

        this.last_remove_cat_id = '';

        this.last_selected_cat_id = '';

        this.last_category_chkbox_action = '';

        this.search_area = '';

        this.url_filters = [];

        this.mbAttr  = 'Map data &copy; <a href="http://openstreetmap.org">OpenStreetMap</a> contributors, <a href="http://creativecommons.org/licenses/by-sa/2.0/">CC-BY-SA</a>, Imagery © <a href="http://mapbox.com">Mapbox</a>';

        this.mbURL  = '';

        this.isMobile = false;

        this.bingmaplayers = {};
        this.infowindow_marker = L.popup({closeOnClick: (this.settings.close_infowindow_on_map_click === true)});

        this.init();


    }



    OSMMaps.prototype = {



        init: function() {

            var map_obj = this;



            if (map_obj.map_data.map_property && map_obj.map_data.map_property.debug_mode == true) {

                console.log('*********wpomp Debug Mode Output*********');

                console.log('Map ID =' + map_obj.map_data.map_property.map_id);

                if (map_obj.map_data.places) {

                    console.log('Total Locations=' + map_obj.map_data.places.length);

                }

                console.log('wpomp Object=');

                console.log(map_obj.map_data);

                console.log('*********wpomp Debug Mode End Output*********');

            }

            var isMobile = false;



            var screen_type = 'desktop';



            var screen_size = $(window).width();

            if (screen_size <= 480) {

                screen_type = 'smartphones';

            } else if (screen_size > 480 && screen_size <= 768) {

                screen_type = 'ipads';

            } else if (screen_size >= 1824) {

                screen_type = 'large-screens';

            }



            if (screen_type != 'desktop' && map_obj.settings.mobile_specific == true) {



                isMobile = true;
                map_obj.isMobile = true;


                if (map_obj.settings.screens && map_obj.settings.screens[screen_type]) {



                    map_obj.settings.width_mobile = map_obj.settings.screens[screen_type].map_width_mobile;

                    map_obj.settings.height_mobile = map_obj.settings.screens[screen_type].map_height_mobile;

                    map_obj.settings.zoom = parseInt(map_obj.settings.screens[screen_type].map_zoom_level_mobile);

                    map_obj.settings.draggable = (map_obj.settings.screens[screen_type].map_draggable_mobile !== 'false');

                    map_obj.settings.scroll_wheel = (map_obj.settings.screens[screen_type].map_scrolling_wheel_mobile !== 'false');



                } else {

                    map_obj.settings.width_mobile  = '';

                    map_obj.settings.height_mobile = '';

                }



                if (map_obj.settings.width_mobile != '')

                    $(map_obj.element).css('width', map_obj.settings.width_mobile);



                if (map_obj.settings.height_mobile != '')

                    $(map_obj.element).css('height', map_obj.settings.height_mobile);

            }



            var center = new L.LatLng(map_obj.settings.center_lat, map_obj.settings.center_lng);

            var options = {

               center: center,   

               zoom: parseInt(map_obj.settings.zoom),  

               minZoom: parseInt(map_obj.settings.min_zoom),

               maxZoom: parseInt(map_obj.settings.max_zoom),

               scrollWheelZoom: map_obj.settings.scroll_wheel,

               doubleClickZoom: (map_obj.settings.doubleclickzoom === true),

               dragging: map_obj.settings.draggable,

               zoomControl: false,

               attributionControl: map_obj.settings.attribution_screen_control, 

               closePopupOnClick:(this.settings.close_infowindow_on_map_click === true)
            };


            map_obj.mbURL = 'https://api.mapbox.com/styles/v1/mapbox/streets-v11/tiles/{z}/{x}/{y}?access_token='+wpomp_local.accesstoken;

            if(map_obj.settings.map_data_provider=='mapbox'){
                map_obj.map = L.map(map_obj.element, options);
                
                L.tileLayer(map_obj.mbURL, {
                    attribution: map_obj.mbAttr ,
                    maxZoom: parseInt(map_obj.settings.max_zoom),
                    id: map_obj.settings.map_type_id,
                    accessToken: wpomp_local.accesstoken 
                }).addTo(map_obj.map);

            }else if(map_obj.settings.map_data_provider=='mapquest'){

                var base = {layers:[MQ.mapLayer()]};
                var newoptions = L.Util.extend({}, options, base);
                map_obj.map = L.map(map_obj.element, newoptions);
                var attControl = L.control.attribution({prefix:false}).addTo(map_obj.map);
                var attri = '<a href="http://leafletjs.com" title="A JS library for interactive maps">Leaflet</a>; \r\n© <a href="http://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors';
                attControl.addAttribution(attri);
            }else if(map_obj.settings.map_data_provider=='bingmap'){

                    map_obj.map = L.map(map_obj.element, options);
                    var apiKey = wpomp_local.wpomp_bingmap_key;
                    var defaults = {
                        key: apiKey,
                        detectRetina: true
                    };
                    var baseLayers = {};
                    [   'Aerial', 'AerialWithLabelsOnDemand',
                        'RoadOnDemand',
                        'CanvasDark', 'CanvasLight', 'CanvasGray'
                    ].forEach(function (imagerySet) {
                        baseLayers[imagerySet] = L.bingLayer(L.extend({imagerySet: imagerySet}, defaults));
                    });
                    map_obj.bingmaplayers = baseLayers;
                    baseLayers['CanvasGray'].addTo(map_obj.map);

            }else{
                 map_obj.map = L.map(map_obj.element, options);
                 
                 L.tileLayer(map_obj.settings.map_tile_url, {
                    maxZoom: parseInt(map_obj.settings.max_zoom)
                }).addTo(map_obj.map);
            }
            if(map_obj.settings.scale_control){
                L.control.scale({position:'bottomleft'}).addTo(map_obj.map);
            }
            if( map_obj.settings.zoom_control === true ) {

                L.control.zoom({
                 position: map_obj.settings.zoom_control_position,
                }).addTo(map_obj.map);

            }
            if(map_obj.map_data.page == "edit_location"){
                setTimeout(function(){ map.invalidateSize(true)}, 300);
            } 

            if(map_obj.settings.locateme_control=== true){
                   var lc = L.control.locate({
                    strings: {
                        title: "Show me where I am, yo!",
                        popup:'<div class="wpomp_infowindow"><div class="wpomp_iw_content">You are within {distance} {unit} from this point</div></div>'

                    },
                    icon:"wpomp_locateme_control",
                    drawCircle:false,
                    position:map_obj.settings.locateme_control_position
                }).addTo(map_obj.map);

            }

            if(map_obj.settings.full_screen_control=== true){

                map_obj.map.addControl(new L.Control.Fullscreen({

                    position:map_obj.settings.full_screen_control_position

                }));

            }


           if(map_obj.settings.map_type_control  ){
                var mapstyles = map_obj.settings.openstreet_styles_markup;
                if(map_obj.settings.map_data_provider=='mapbox'){
                    mapstyles = map_obj.settings.map_box_styles_markup;
                }
                if(map_obj.settings.map_data_provider=='mapquest'){
                    mapstyles = map_obj.settings.mapquest_styles_markup;
                }
                if(map_obj.settings.map_data_provider=='bingmap'){
                    mapstyles = map_obj.settings.binmaps_styles_markup;
                }
                map_obj.map.addControl(new (L.Control.extend({
                options: {
                    position: map_obj.settings.map_type_control_position
                },
                onAdd: function (map) {
                 var div = L.DomUtil.create('div', 'info legend');
                    div.innerHTML = mapstyles;
                    div.firstChild.onmousedown = div.firstChild.ondblclick = L.DomEvent.stopPropagation;
                    return div;
                }
                }))());
                $(document).on('change','select.wpomp_map_type',function(){
                    var layers_value = $(this).val();
                    var config = map_obj.settings.openstreet_styles[layers_value];
                    
                    var OpenStreetMap_Mapnik = L.tileLayer(config, {
                        maxZoom: parseInt(map_obj.settings.max_zoom)
                    });
                    OpenStreetMap_Mapnik.addTo(map_obj.map);
                });
                $(document).on('change','select.wpomp_mapbox_type',function(){
                    var layers_value = $(this).val();
                    var tile_url ='https://api.mapbox.com/styles/v1/mapbox/'+layers_value+'/tiles/{z}/{x}/{y}?access_token='+wpomp_local.accesstoken;
                    
                    var mapbox_layer = L.tileLayer(tile_url, {
                        maxZoom: parseInt(map_obj.settings.max_zoom)
                    });
                    mapbox_layer.addTo(map_obj.map);
                });

                if(map_obj.settings.map_data_provider=='mapquest'){

                var map_quest ={
                    'Map': MQ.mapLayer(),
                    'Hybrid': MQ.hybridLayer(),
                    'Satellite': MQ.satelliteLayer(),
                    'Dark': MQ.darkLayer(),
                    'Light': MQ.lightLayer()
                };

                $(document).on('change','select.wpomp_mapquest_type',function(){
                    var layers_value = $(this).val();
                    var selected_layer = map_quest[layers_value];
                    map_obj.map.addLayer(selected_layer);
                });
            }
            
            if(map_obj.settings.map_data_provider=='bingmap'){

            
                $(document).on('change','select.wpomp_bingmap_type',function(){
                    var layers_value = $(this).val();
                    var selected_layer = map_obj.bingmaplayers[layers_value];
                    map_obj.map.addLayer(selected_layer);
                });
            }


           }

        
          if(map_obj.settings.search_control){    

               map_obj. map.addControl(new (L.Control.extend({

                options: {

                    position: map_obj.settings.search_control_position

                },

                onAdd: function (map) {

                 var div = L.DomUtil.create('div', 'info legend');

                    div.innerHTML = '<div><input type="text"  name="wpomp_map_suggest" id="wpomp_map_suggest'+map_obj.map_data.map_property.map_id+'" class="wpomp_map_suggest" placeholder="' + wpomp_local.autocomplete_placeholder + '" ></div>';

                    return div;

                }

            }))());

        }


            map_obj.map_loaded();

            map_obj.responsive_map();

            map_obj.create_markers();

            map_obj.display_markers();

                        //Load google fonts
            if (typeof map_obj.settings.google_fonts !== 'undefined') {
                map_obj.load_google_fonts(map_obj.settings.google_fonts);
            }

            

            if (map_obj.settings.map_control == true) {

                if (typeof map_obj.settings.map_control_settings != 'undefined') {

                    var map_control_obj = new Map_Control();

                    $.each(map_obj.settings.map_control_settings, function(k, val) {

                        

                        L.Control.Watermark = L.Control.extend({

                            onAdd: function(map) {

                                var centerControlDiv = document.createElement('div');

                                map_control_obj.create_element(centerControlDiv, map_obj.map, val.html);

                                centerControlDiv.index = 1;

                                return centerControlDiv;

                            },



                            onRemove: function(map) {

                                // Nothing to do here

                            }

                        });



                        L.control.watermark = function(opts) {

                            return new L.Control.Watermark(opts);

                        }



                        L.control.watermark({ position: 'bottomleft' }).addTo(map_obj.map);



                    });

                }

            }




            if (map_obj.settings.search_control == true) {

                map_obj.show_search_control();

            }

    

            if (map_obj.map_data.listing) {

                if (map_obj.map_data.listing.default_sorting) {

                    var data_type = '';

                    if (map_obj.map_data.listing.default_sorting.orderby == 'listorder') {

                        data_type = 'num';

                    }

                    map_obj.sorting(map_obj.map_data.listing.default_sorting.orderby, map_obj.map_data.listing.default_sorting.inorder, data_type);

                }



            } else {



                if (map_obj.map_data.map_tabs !== undefined && map_obj.map_data.map_tabs != 'undefined') {



                    if (typeof map_obj.map_data.map_tabs.category_tab !== undefined && map_obj.map_data.map_tabs.category_tab !== 'undefined' && typeof map_obj.map_data.map_tabs.category_tab.cat_tab !== undefined && map_obj.map_data.map_tabs.category_tab.cat_tab) {

                        if (map_obj.map_data.map_tabs.category_tab.cat_post_order === undefined)

                            map_obj.map_data.map_tabs.category_tab.cat_post_order = 'asc';

                        map_obj.sorting('title', map_obj.map_data.map_tabs.category_tab.cat_post_order);



                    }

                }



            }



            if (map_obj.map_data.listing) {



                $(map_obj.container).on('click', '.categories_filter_reset_btn', function() {



                    $(map_obj.container).find('.wpomp_filter_wrappers select').each(function() {

                        $(this).find('option:first').attr('selected', 'selected');
                        $(this).find('option:first').prop('selected', 'selected');

                    });

                    $('.wpomp_search_input').val('');

                    map_obj.update_filters();

                    

                });





                $(map_obj.container).on('change', '[data-filter="dropdown"]', function() {

                    map_obj.update_filters();

                });



                $(map_obj.container).on('click', '[data-filter="checklist"]', function() {

                    map_obj.update_filters();

                });



                $(map_obj.container).on('click', '[data-filter="list"]', function() {



                    if ($(this).hasClass('fc_selected')) {

                        $(this).removeClass('fc_selected');

                    } else {

                        $(this).addClass('fc_selected');

                    }



                    map_obj.update_filters();

                });

                map_obj.display_filters_listing();
                map_obj.custom_filters();
                $.each(map_obj.map_data.listing.filters, function(key, filter) {



                    $(map_obj.container).find('select[name="' + filter + '"]').on('change', function() {

                        map_obj.update_filters();

                    });



                });



                $(map_obj.container).find('[data-filter="map-sorting"]').on('change', function() {



                    var order_data = $(this).val().split("__");

                    var data_type = '';

                    if (order_data[0] !== '' && order_data[1] !== '') {



                        if (typeof order_data[2] != 'undefined') {

                            data_type = order_data[2];

                        }

                        map_obj.sorting(order_data[0], order_data[1], data_type);

                        map_obj.update_places_listing();



                    }



                });



                $(map_obj.container).find('[data-name="radius"]').on('change', function() {



                    var search_data = $(map_obj.container).find('[data-input="wpomp-search-text"]').val();





                    if (search_data.length >= 2 && $(this).val() != '') {



                        $.get(location.protocol + '//nominatim.openstreetmap.org/search?format=json&q='+search_data, function(data){

                           if(Object.keys(data).length>0){

                            var position = new L.LatLng(data[0].lat, data[0].lon);

                            map_obj.search_area = position;

                            map_obj.update_filters();



                           }



                        });

                    } else {

                        map_obj.search_area = '';

                        map_obj.update_filters();

                    }

                });



                $(map_obj.container).find('[data-filter="map-perpage-location-sorting"]').on('change', function() {

                    map_obj.per_page_value = $(this).val();

                    map_obj.update_filters();

                });



                $(map_obj.container).find('[data-input="wpomp-search-text"]').on('keyup', function() {

                    var search_data = $(this).val();

                    $(map_obj.container).find('[data-filter="map-radius"]').val('');

                    map_obj.search_area = '';

                    // Apply default radius

                    if (search_data.length >= 2 && map_obj.map_data.listing.apply_default_radius == true) {

                        if (search_data.length >= 2) {

                            map_obj.update_filters();

                        }



                    } else {

                        map_obj.update_filters();

                    }



                });



                $(map_obj.container).find(".location_pagination" + map_obj.map_data.map_property.map_id).pagination(map_obj.show_places.length, {

                    callback: map_obj.display_places_listing,

                    map_data: map_obj,

                    items_per_page: map_obj.map_data.listing.pagination.listing_per_page,

                    prev_text: wpomp_local.prev,

                    next_text: wpomp_local.next

                });



            }



            $(this.container).on("click", ".wpomp_locateme_control", function() {



                        map_obj.get_current_location(function(user_location) {

                            map_obj.map.setCenter(user_location);

                            if (map_obj.map_center_marker) {

                                map_obj.map_center_marker.setPosition(user_location);

                            }

                            if (map_obj.set_center_circle) {

                                map_obj.set_center_circle.setCenter(user_location);

                            }



                        });



            });



            if (typeof map_obj.map_data.geojson != 'undefined') {

                map_obj.load_json(map_obj.map_data.geojson);

            }



            $("body").on("click", ".wpomp_marker_link", function() {

                $('html, body').animate({

                    scrollTop: $(map_obj.container).offset().top - 150

                }, 500);



                map_obj.open_infowindow($(this).data("marker"),$(this).data("source"));



            });



            $(map_obj.container).on("click", ".wpomp_locations a[data-marker]", function() {
                //dk
                var current_marker = this;

                $('html, body').animate({

                    scrollTop: $(map_obj.container).offset().top - 150

                }, 500);

                $.each(map_obj.map_data.places, function(key, place) {
               
                    if ((parseInt(place.id) == parseInt($(current_marker).data("marker"))) && (place.source == $(current_marker).data("source"))   ) {
                        
                        map_obj.map.panTo(place.marker.getLatLng());
                        return false;                    
                    }
                });
                

                setTimeout(function() {

                    map_obj.open_infowindow($(current_marker).data("marker"),$(current_marker).data("source"));

                }, 600);

            });



            $(map_obj.container).on("click", ".wpomp_location_container a[data-marker]", function() {
                map_obj.open_infowindow($(this).data("marker"),$(this).data("source"));

            });



            // REGISTER AUTO SUGGEST

            map_obj.google_auto_suggest($(".wpomp_auto_suggest"));


            if (map_obj.settings.fit_bounds === true) {

                map_obj.fit_bounds();

            }



            //url filters

            if (map_obj.settings.url_filters === true) {

                map_obj.apply_url_filters();

            }







            if (typeof map_obj.map_data.map_tabs != 'undefined') {

                this.map_widgets();



                $(map_obj.container).find(".wpomp_toggle_main_container").find("div[id^='wpomp_tab_']").css("display", "none");



                if (map_obj.settings.infowindow_filter_only === undefined || map_obj.settings.infowindow_filter_only === false) {

                    $(map_obj.container).find("input[data-marker-category]").attr("checked", true);
                    $(map_obj.container).find("input[data-marker-location]").attr("checked", true);

                    $(map_obj.container).find("input[data-marker-category]").prop("checked", true);
                    $(map_obj.container).find("input[data-marker-location]").prop("checked", true);
                }



                if (typeof map_obj.map_data.map_tabs != 'undefined' && this.map_data.map_tabs.category_tab && this.map_data.map_tabs.category_tab.select_all === true) {



                    $(map_obj.container).find('input[name="wpomp_select_all"]').click(function() {

                        if ($(this).is(":checked")) {

                            $(map_obj.container).find("input[data-marker-category]").attr("checked", true);
                            $(map_obj.container).find('input[data-marker-location]').attr('checked', true);

                            $(map_obj.container).find("input[data-marker-category]").prop("checked", true);
                            $(map_obj.container).find('input[data-marker-location]').prop('checked', true);

                        } else {

                            $(map_obj.container).find("input[data-marker-category]").attr("checked", false);
                            $(map_obj.container).find('input[data-marker-location]').attr('checked', false);

                            $(map_obj.container).find("input[data-marker-category]").prop("checked", false);
                            $(map_obj.container).find('input[data-marker-location]').prop('checked', false);

                        }

                        map_obj.update_filters();

                    });

                }



                $(map_obj.container).on('click',".wpomp_toggle_container",function() {
               

                    $(map_obj.container).find(".wpomp_toggle_main_container").slideToggle("slow");



                    if ($(this).text() == wpomp_local.hide) {

                        $(this).text(wpomp_local.show);

                    } else {

                        $(this).text(wpomp_local.hide);

                    }



                });



                if (typeof map_obj.map_data.map_tabs != 'undefined' && map_obj.map_data.map_tabs.hide_tabs_default === true) {

                    $(map_obj.container).find(".wpomp_toggle_container").trigger('click');

                }



                $(map_obj.container).find(".wpomp_specific_route_item").attr("checked", true);



                $(map_obj.container).find(".wpomp_toggle_main_container").find("div[id^='wpomp_tab_']").first().css("display", "block");



                $(map_obj.container).on('click', "li[class^='wpomp-tab-'] a", function() {



                    $(map_obj.container).find("li[class^='wpomp-tab-'] a").removeClass('active');



                    $(this).addClass('active');



                    $(map_obj.container).find(".wpomp_toggle_main_container").find("div[id^='wpomp_tab_']").css("display", "none");



                    $(map_obj.container).find(".wpomp_toggle_main_container").find("#wpomp_tab_" + $(this).parent().attr('rel')).css("display", "block");



                });



                $(map_obj.container).on('change', "input[data-marker-category]", function() {



                    //uncheck all locations

                    var current_marker_id = $(this).data('marker-category');

                    var that = this;

                    if ($(that).data('child-cats')) {

                        var data_child = $(that).data('child-cats').toString();

                        if (data_child.indexOf(',') !== -1) {

                            var child_cats = data_child.split(',');

                        } else {

                            var child_cats = [];

                            child_cats.push(data_child);

                        }

                    }

                    

                    if ($(this).is(":checked") === false) {

                        map_obj.last_remove_cat_id = current_marker_id;

                        map_obj.last_category_chkbox_action = 'unchecked';

                        $(that).closest('[data-container="wpomp-category-tab-item"]').find('input[data-marker-location]').attr('checked', false);

                        $(that).closest('[data-container="wpomp-category-tab-item"]').find('input[data-marker-location]').prop('checked', false);

                        if (child_cats) {

                            $.each(child_cats, function(i, cat) {

                                $(that).parent().parent().find('[data-marker-category="' + cat + '"]').attr('checked', false);
                                $(that).parent().parent().find('[data-marker-category="' + cat + '"]').parent().find('input[data-marker-location]').attr('checked', false);

                                $(that).parent().parent().find('[data-marker-category="' + cat + '"]').prop('checked', false);
                                $(that).parent().parent().find('[data-marker-category="' + cat + '"]').parent().find('input[data-marker-location]').prop('checked', false);

                            });

                        }

                    } else {

                        map_obj.last_selected_cat_id = current_marker_id;

                        map_obj.last_category_chkbox_action = 'checked';

                        $(that).closest('[data-container="wpomp-category-tab-item"]').find('input[data-marker-location]').attr('checked', true);

                        $(that).closest('[data-container="wpomp-category-tab-item"]').find('input[data-marker-location]').prop('checked', true);

                        if (child_cats) {

                            $.each(child_cats, function(i, cat) {

                                $(that).parent().parent().find('[data-marker-category="' + cat + '"]').attr('checked', true);
                                $(that).parent().parent().find('[data-marker-category="' + cat + '"]').parent().find('input[data-marker-location]').attr('checked', true);

                                $(that).parent().parent().find('[data-marker-category="' + cat + '"]').prop('checked', true);
                                $(that).parent().parent().find('[data-marker-category="' + cat + '"]').parent().find('input[data-marker-location]').prop('checked', true);

                            });

                        }

                    }

                    map_obj.update_filters();

                    

                    if (typeof map_obj.map_data.places != 'undefined') {

                        $.each(map_obj.map_data.places, function(place_key, place) {

                            map_obj.get_updated_icon_image(place);

                        });

                    }

                    

                    

                });



                $(map_obj.container).on('change', "input[data-marker-location]", function() {

                    map_obj.update_filters();

                });

            



            } //tabs ended



            $(map_obj.container).find(".wpomp-accordion").accordion({

                    speed: "slow"

            });


        },
        load_google_fonts: function(fonts) {
            if (fonts && fonts.length > 0) {
                $.each(fonts, function(k, font) {
                    if (font.indexOf(',') >= 0) {
                        font = font.split(",");
                        font = font[0];
                    }
                    if (font.indexOf('"') >= 0) {
                        font = font.replace('"', '');
                        font = font.replace('"', '');
                    }
                    WebFont.load({
                        google: {
                            families: [font]
                        }
                    });
                });
            }
        },

        get_updated_icon_image: function(place) {

        
            //Update icons only when filters are used, not on DOM load.
            var map_obj = this;

            if( ( map_obj.last_remove_cat_id != '' || map_obj.last_selected_cat_id != '' ) && ( place.categories && parseInt(place.categories.length) > 1 ) ){ 

                

                if( map_obj.last_category_chkbox_action == 'unchecked' ){

                    

                    var last_id = parseInt(map_obj.last_remove_cat_id);

                    $.each(map_obj.map_data.marker_category_icons, function(icon_key, icon_url) {

                        if(icon_key != last_id){ 

                            var cat_c =   L.icon({ iconUrl: icon_url,

                                iconSize: [32, 32],               
                                iconAnchor: [16, 32], 

                             popupAnchor: [0, -55],

                               });



                            place.marker.setIcon(cat_c);

                            return false;

                        }

                    });

                    

                }else{

                    

                    var last_id = map_obj.last_selected_cat_id;

                    $.each(map_obj.map_data.marker_category_icons, function(icon_key, icon_url) {

                        if(icon_key == last_id){ 

                            var cat_d =   L.icon({ iconUrl: icon_url,
                                            iconSize: [32, 32],               
                                            iconAnchor: [16, 32],
                                          });



                            place.marker.setIcon(cat_d);

                            return false;

                        }

                    });

                    

                }

                

            }

            

        },



        map_widgets: function() {



            var content = '';

            var map_obj = this;



           if (this.map_data.map_tabs.category_tab && this.map_data.map_tabs.category_tab.cat_tab === true)

            map_obj.widget_category();



            content += this.show_tabs();



            if (content != 'undefined'){

                $(this.container).find('.wpomp_map_parent').append(content);

            }

        },

        widget_category: function() {



            var map_obj = this;

            if (map_obj.map_data.map_tabs.category_tab.select_all === true) {

                var content = '<div class="wpomp-select-all"><input checked="checked" type="checkbox" value="true" name="wpomp_select_all">&nbsp&nbsp' + wpomp_local.select_all + '</div>';

            } else {

                var content = '';

            }


            var categories_tab_data = {};

            var child_categories_tab_data = {};



            if (typeof map_obj.map_data.places != 'undefined') {

                $.each(map_obj.map_data.places, function(index, place) {

                    if (typeof place.categories != 'undefined') {

                        $.each(place.categories, function(index, categories) {

                            var show = true;

                            var parent_cat = '';

                            if(typeof map_obj.map_data.map_tabs != 'undefined'){

                                parent_cat = map_obj.search_category(map_obj.map_data.map_tabs.category_tab.child_cats, categories.id, [], categories_tab_data, child_categories_tab_data); 

                            }

                            



                            if (parent_cat.length > 0)

                                show = false;



                            if (typeof categories.type != "undefined" && categories.type == 'category' && categories.name && show == true) {





                                if (typeof categories_tab_data[categories.id] == "undefined") {

                                    categories_tab_data[categories.id] = {};

                                    categories_tab_data[categories.id]['data'] = [];

                                }

                                categories_tab_data[categories.id]['cat_id'] = categories.id;

                                categories_tab_data[categories.id]['cat_title'] = categories.name;

                                categories_tab_data[categories.id]['cat_marker_icon'] = categories.icon;



                                if (categories.extension_fields && categories.extension_fields.cat_order) {

                                    categories_tab_data[categories.id]['cat_order'] = categories.extension_fields.cat_order;

                                }



                                var redirect_permalink = "";

                                if (place.location.redirect_permalink)

                                    redirect_permalink = place.location.redirect_permalink;



                                var redirect_custom_link = "";

                                if (place.location.redirect_custom_link)

                                    redirect_custom_link = place.location.redirect_custom_link;



                                categories_tab_data[categories.id]['data'].push({

                                    "cat_location_id": place.id,

                                    "cat_location_title": place.title,

                                    "cat_location_address": place.address,

                                    "cat_location_zoom": place.location.zoom,

                                    "onclick_action": place.location.onclick_action,

                                    "redirect_permalink": redirect_permalink,

                                    "redirect_custom_link": redirect_custom_link,

                                    "source":place.source

                                });



                            } else if (typeof categories.type != "undefined" && categories.type == 'category' && categories.name && show == false) {

                                if (typeof child_categories_tab_data[categories.id] == "undefined") {

                                    child_categories_tab_data[categories.id] = {};

                                    child_categories_tab_data[categories.id]['data'] = [];

                                    child_categories_tab_data[categories.id]['parent_cat'] = parent_cat;

                                }



                                child_categories_tab_data[categories.id]['cat_id'] = categories.id;

                                child_categories_tab_data[categories.id]['cat_title'] = categories.name;

                                child_categories_tab_data[categories.id]['cat_marker_icon'] = categories.icon;

                                if (categories.extension_fields && categories.extension_fields.cat_order) {

                                    child_categories_tab_data[categories.id]['cat_order'] = categories.extension_fields.cat_order;

                                }

                                var redirect_permalink = "";

                                if (place.location.redirect_permalink)

                                    redirect_permalink = place.location.redirect_permalink;



                                var redirect_custom_link = "";

                                if (place.location.redirect_custom_link)

                                    redirect_custom_link = place.location.redirect_custom_link;





                                child_categories_tab_data[categories.id]['data'].push({

                                    "cat_location_id": place.id,

                                    "cat_location_title": place.title,

                                    "cat_location_address": place.address,

                                    "cat_location_zoom": place.location.zoom,

                                    "onclick_action": place.location.onclick_action,

                                    "redirect_permalink": redirect_permalink,

                                    "redirect_custom_link": redirect_custom_link,

                                    "source":place.source



                                });



                                if (categories_tab_data[parent_cat] !== undefined) {

                                    if (typeof categories_tab_data[parent_cat]['child_cats'] == 'undefined') {

                                        categories_tab_data[parent_cat]['child_cats'] = [];

                                    }

                                    categories_tab_data[parent_cat]['child_cats'][categories.id] = categories.id;

                                }

                            }

                        });

                    }



                });

            }



            var category_orders = [];

            if (typeof categories_tab_data != 'undefined') {

                $.each(categories_tab_data, function(index, categories) {

                    var loc_count = categories.data.length;



                    if (typeof child_categories_tab_data != "undefined") {

                        $.each(child_categories_tab_data, function(c, ccat) {

                            if (ccat.parent_cat == categories.cat_id) {

                                loc_count = loc_count + ccat.data.length;

                                $.each(child_categories_tab_data, function(cc, cccat) {

                                    if (cccat.parent_cat == ccat.cat_id) {

                                        loc_count = loc_count + cccat.data.length;

                                    }

                                });

                            }

                        });

                    }

                    categories.loc_count = loc_count;



                    if (map_obj.map_data.map_tabs.category_tab.cat_order_by == 'count') {

                        category_orders.push(categories.loc_count);

                    } else if (map_obj.map_data.map_tabs.category_tab.cat_order_by == 'category') {

                        if (categories.cat_order) {

                            category_orders.push(categories.cat_order);

                        } else if (!categories.cat_order && map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id] && map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id].extensions_fields) {

                            categories.cat_order = map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id].extensions_fields.cat_order;

                            category_orders.push(categories.cat_order);

                        }



                    } else {

                        if (categories.cat_title) {

                            category_orders.push(categories.cat_title);

                        } else if (!categories.cat_title && map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id]) {

                            categories.cat_title = map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id].group_map_title;

                            category_orders.push(categories.cat_title);

                        }



                    }

                });

            }

                 

            if (typeof map_obj.map_data.map_tabs != 'undefined' && map_obj.map_data.map_tabs.category_tab.cat_order_by == 'category') {

                category_orders.sort(function(a, b) {

                    return a - b

                });

            } else if (typeof map_obj.map_data.map_tabs != 'undefined' && map_obj.map_data.map_tabs.category_tab.cat_order_by == 'count') {

                category_orders.sort(function(a, b) {

                    return b - a

                });

            } else {

                category_orders.sort();

            }

            var ordered_categories = [];

            var check_cats = [];

            $.each(category_orders, function(index, cat_title) {

                $.each(categories_tab_data, function(index, categories) {

                    var compare_with;

                    if (map_obj.map_data.map_tabs.category_tab.cat_order_by == 'count') {

                        compare_with = categories.loc_count;

                    } else if (map_obj.map_data.map_tabs.category_tab.cat_order_by == 'category') {

                        compare_with = categories.cat_order;

                    } else {

                        compare_with = categories.cat_title;

                    }



                    if (cat_title == compare_with && $.inArray(categories.cat_id, check_cats) == -1) {

                        ordered_categories.push(categories);

                        check_cats.push(categories.cat_id);

                    }

                });

            });



            if (typeof ordered_categories != 'undefined') {



                $.each(ordered_categories, function(index, categories) {



                    var category_image = '';



                    if (!categories.cat_title && map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id]) {

                        categories.cat_title = map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id].group_map_title;

                    }



                    if (!categories.cat_marker_icon && map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id]) {

                        categories.cat_marker_icon = map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id].group_marker;

                    }



                    if (typeof categories.cat_marker_icon != 'undefined') {

                        category_image = '<span class="arrow"><img src="' + categories.cat_marker_icon + '"></span>';

                    }



                    content += '<div class="wpomp_tab_item" data-container="wpomp-category-tab-item">';



                    if (categories.child_cats !== undefined) {

                        categories.child_cats = categories.child_cats.filter(function(v) {

                            return v !== ''

                        });

                        var child_cats_str = ' data-child-cats="' + categories.child_cats.join(",") + '"';

                    } else {

                        var child_cats_str = '';

                    }



                    content += '<input type="checkbox"' + child_cats_str + ' data-marker-category="' + categories.cat_id + '" value="' + categories.cat_id + '">';



                    var loc_count = categories.loc_count;;



                    $.each(map_obj.map_data.map_tabs.category_tab.child_cats, function(k, v) {

                        if (v == categories.cat_id && loc_count == 0)

                            loc_count = "";

                    });



                    var location_count = "";



                    if (map_obj.map_data.map_tabs.category_tab.show_count === true && loc_count != "") {

                        location_count = " (" + loc_count + ")";

                    }



                    content += '<a href="javascript:void(0);" class="wpomp_cat_title wpomp-accordion accordion-close">' + categories.cat_title + location_count + category_image + '</a>';



                    if (map_obj.map_data.map_tabs.category_tab.hide_location !== true) {



                        content += '<div class="scroll-pane" style="max-height:300px;width:100%;">';



                        content += '<ul class="wpomp_location_container">';



                        $.each(categories.data, function(name, location) {



                            if (location.onclick_action == "marker") {

                                content += '<li><input type="checkbox" data-marker-location="' + location.cat_location_id + '"  value="' + location.cat_location_id + '" /><a data-source="'+location.source+'" data-marker="' + location.cat_location_id + '" data-zoom="' + location.cat_location_zoom + '" href="javascript:void(0);">' + location.cat_location_title + '</a></li>';

                            } else if (location.onclick_action == "post") {

                                content += '<li><input type="checkbox" data-marker-location="' + location.cat_location_id + '"  value="' + location.cat_location_id + '" /><a href="' + location.redirect_permalink + '" target="_blank">' + location.cat_location_title + '</a></li>';

                            } else if (location.onclick_action == "custom_link") {

                                content += '<li><input type="checkbox" data-marker-location="' + location.cat_location_id + '"  value="' + location.cat_location_id + '" /><a href="' + location.redirect_custom_link + '" target="_blank">' + location.cat_location_title + '</a></li>';

                            }



                        });



                        content += '</ul>';



                        content += '</div>';

                    }



                    content += '</div>';



                    if (typeof child_categories_tab_data != "undefined") {

                        var padding = 20;

                        content += map_obj.display_sub_categories(child_categories_tab_data, categories.cat_id, '', padding);

                    }

                });

            }

            if(typeof map_obj.map_data.map_tabs != 'undefined')

            map_obj.add_tab(map_obj.map_data.map_tabs.category_tab.cat_tab_title, content);



        },

        add_tab: function(title, content) {



            var tab = [];



            tab.title = title;

            tab.content = content;

            this.tabs.push(tab);

        },

        search_category: function(array, cat_id, index, categories_tab_data, child_categories_tab_data) {

            var map_obj = this;

            var flag = true;

            $.each(array, function(k, i) {

                if (k == cat_id) {

                    index = i;

                    flag = false;

                    if (typeof child_categories_tab_data[cat_id] == "undefined") {

                        child_categories_tab_data[cat_id] = {};

                        child_categories_tab_data[cat_id]['data'] = [];

                        child_categories_tab_data[cat_id]['parent_cat'] = i;

                        child_categories_tab_data[cat_id]['cat_id'] = cat_id;

                        $.each(map_obj.categories, function(k, e) {

                            if (e.group_map_id == cat_id) {

                                child_categories_tab_data[cat_id]['cat_title'] = e.group_map_title;

                                child_categories_tab_data[cat_id]['cat_marker_icon'] = e.group_marker;

                            }

                        });

                    }

                    index = map_obj.search_category(map_obj.map_data.map_tabs.category_tab.child_cats, i, index, categories_tab_data, child_categories_tab_data);

                }

            });

            if (flag == true) {

                if (typeof categories_tab_data[cat_id] == "undefined") {

                    categories_tab_data[cat_id] = {};

                    categories_tab_data[cat_id]['data'] = [];

                    categories_tab_data[cat_id]['cat_id'] = cat_id;

                    $.each(map_obj.categories, function(k, e) {

                        if (e.group_map_id == cat_id) {

                            categories_tab_data[cat_id]['cat_title'] = e.group_map_title;

                            categories_tab_data[cat_id]['cat_marker_icon'] = e.group_marker;

                        }

                    });

                }

            }

            return index;

        },

        show_tabs: function() {



            if (this.tabs.length === 0 || (this.map_data.listing && this.map_data.listing.hide_map == true))

                return;



            var content = '<div class="wpomp_tabs_container cleanslate"><ul class="wpomp_tabs clearfix">';



            $.each(this.tabs, function(index, tab) {

                if (index == 0)

                    content += '<li class="wpomp-tab-' + index + '" rel="' + index + '"><a class="active" href="javascript:void(0);">' + tab.title + '</a></li>';

                else

                    content += '<li class="wpomp-tab-' + index + '" rel="' + index + '"><a href="javascript:void(0);">' + tab.title + '</a></li>';

            });



            content += '</ul>';



            content += '<div class="wpomp_toggle_main_container">';



            $.each(this.tabs, function(index, tab) {

                content += '<div id="wpomp_tab_' + index + '">';

                content += tab.content;

                content += '</div>';

            });



            content += '</div><div class="wpomp_toggle_container">' + wpomp_local.hide + '</div></div>';



            return content;

        },

        load_json: function(url) {

            this.map.data.loadGeoJson(url);

        },

        wpomp_within_radius: function(place, search_area) {
            var map_obj = this;
            var radius = $(map_obj.container).find('[data-name="radius"]').val();
            var dimension = map_obj.map_data.listing.radius_dimension;
            if (map_obj.map_data.listing.apply_default_radius == true && radius == '') {
                radius = map_obj.map_data.listing.default_radius;
                dimension = map_obj.map_data.listing.default_radius_dimension;
            }
            if (dimension == 'km') {
                radius = parseInt(radius) * 1000;
            } else {
                radius = parseInt(radius) * 1609.34;
            }
            var place_position = place.marker.getLatLng();
            var calculated_distance = search_area.distanceTo(place_position);
            if (calculated_distance < radius) {
                return true;
            } else {
                return false;
            }

        },
        marker_bind: function(marker) {

            var map_obj = this;
            map_obj.event_listener(marker, 'drag', function() {



                 var current_marker = this.getLatLng();



                $(".google_latitude").val(current_marker.lat);



                $(".google_longitude").val(current_marker.lng);



                      });

        },

        google_auto_suggest: function(obj) {

            var obj = $(".wpomp_auto_suggest");

            var map_obj = this;
            var country = '';
            var nomination_url = '';

            if (wpomp_local.wpomp_country_specific && wpomp_local.wpomp_country_specific == true && wpomp_local.wpomp_countries && wpomp_local.wpomp_countries != null) {    
                
                country = wpomp_local.wpomp_countries.join(",") ;    
                nomination_url = "https://nominatim.openstreetmap.org/search?countrycodes="+country;
                
            }else{
                nomination_url = "https://nominatim.openstreetmap.org/search";
            }

            obj.each(function() {


                var current_input = this;

                var id = $(current_input).attr('id');


                var options = {

                        geojsonServiceAddress: nomination_url,

                        map_obj: map_obj,

                        serachboxID:"#"+id
                };

                $(obj).parent().GeoJsonAutocomplete(options);

            });



        },

        display_sub_categories: function(child_categories_tab_data, cat_id, content, padding) {



            var map_obj = this;

            var category_orders = [];

            if (typeof child_categories_tab_data != 'undefined') {

                $.each(child_categories_tab_data, function(index, categories) {

                    var loc_count = categories.data.length;



                    if (typeof child_categories_tab_data != "undefined") {

                        $.each(child_categories_tab_data, function(c, ccat) {

                            if (ccat.parent_cat == categories.cat_id) {

                                loc_count = loc_count + ccat.data.length;

                                $.each(child_categories_tab_data, function(cc, cccat) {

                                    if (cccat.parent_cat == ccat.cat_id) {

                                        loc_count = loc_count + cccat.data.length;

                                    }

                                });

                            }

                        });

                    }

                    categories.loc_count = loc_count;



                    if (map_obj.map_data.map_tabs.category_tab.cat_order_by == 'count') {

                        category_orders.push(categories.loc_count);

                    } else if (map_obj.map_data.map_tabs.category_tab.cat_order_by == 'category') {

                        if (categories.cat_order) {

                            category_orders.push(categories.cat_order);

                        } else if (!categories.cat_order && map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id]) {

                            categories.cat_order = map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id].extensions_fields.cat_order;

                            category_orders.push(categories.cat_order);

                        }



                    } else {

                        if (categories.cat_title) {

                            category_orders.push(categories.cat_title);

                        } else if (!categories.cat_title && map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id]) {

                            categories.cat_title = map_obj.map_data.map_tabs.category_tab.all_cats[categories.cat_id].group_map_title;

                            category_orders.push(categories.cat_title);

                        }



                    }

                });

            }

            if (map_obj.map_data.map_tabs.category_tab.cat_order_by == 'category') {

                category_orders.sort(function(a, b) {

                    return a - b

                });

            } else if (map_obj.map_data.map_tabs.category_tab.cat_order_by == 'count') {

                category_orders.sort(function(a, b) {

                    return b - a

                });

            } else {

                category_orders.sort();

            }

            var ordered_categories = [];

            var check_cats = [];

            $.each(category_orders, function(index, cat_title) {

                $.each(child_categories_tab_data, function(index, categories) {

                    var compare_with;

                    if (map_obj.map_data.map_tabs.category_tab.cat_order_by == 'count') {

                        compare_with = categories.loc_count;

                    } else if (map_obj.map_data.map_tabs.category_tab.cat_order_by == 'category') {

                        compare_with = categories.cat_order;

                    } else {

                        compare_with = categories.cat_title;

                    }



                    if (cat_title == compare_with && $.inArray(categories.cat_id, check_cats) == -1) {

                        ordered_categories.push(categories);

                        check_cats.push(categories.cat_id);

                    }

                });

            });



            $.each(ordered_categories, function(index, child_cat) {

                if (child_cat.parent_cat == cat_id) {

                    var category_image = '';



                    if (!child_cat.cat_title && map_obj.map_data.map_tabs.category_tab.all_cats[child_cat.cat_id]) {

                        child_cat.cat_title = map_obj.map_data.map_tabs.category_tab.all_cats[child_cat.cat_id].group_map_title;

                    }



                    if (!child_cat.cat_marker_icon && map_obj.map_data.map_tabs.category_tab.all_cats[child_cat.cat_id]) {

                        child_cat.cat_marker_icon = map_obj.map_data.map_tabs.category_tab.all_cats[child_cat.cat_id].group_marker;

                    }

                    if (typeof child_cat.cat_marker_icon != 'undefined') {

                        category_image = '<span class="arrow"><img src="' + child_cat.cat_marker_icon + '"></span>';

                    }

                    content += '<div class="wpomp_tab_item" data-container="wpomp-category-tab-item" style="padding-left:' + padding + 'px;">';



                    if (map_obj.map_data.map_tabs.category_tab.parent_cats !== undefined && map_obj.map_data.map_tabs.category_tab.parent_cats[child_cat.cat_id])

                        var child_cats_str = ' data-child-cats="' + map_obj.map_data.map_tabs.category_tab.parent_cats[child_cat.cat_id].join(",") + '"';

                    else

                        var child_cats_str = '';



                    content += '<input type="checkbox"' + child_cats_str + ' data-parent-cat="' + cat_id + '" data-marker-category="' + child_cat.cat_id + '" value="' + child_cat.cat_id + '">';



                    var loc_count = child_cat.loc_count;



                    $.each(map_obj.map_data.map_tabs.category_tab.child_cats, function(k, v) {

                        if (v == child_cat.cat_id && loc_count == 0)

                            loc_count = "";

                    });



                    var location_count = "";

                    if (map_obj.map_data.map_tabs.category_tab.show_count === true && loc_count != "") {

                        location_count = " (" + loc_count + ")";

                    } else {

                        location_count = "";

                    }



                    content += '<a href="javascript:void(0);" class="wpomp_cat_title wpomp-accordion accordion-close">' + child_cat.cat_title + location_count + category_image + '</a>';



                    if (map_obj.map_data.map_tabs.category_tab.hide_location !== true) {



                        content += '<div class="scroll-pane" style="height: 97px; width:100%;">';

                        content += '<ul class="wpomp_location_container">';



                        $.each(child_cat.data, function(name, location) {



                            if (location.onclick_action == "marker") {

                                content += '<li><input type="checkbox" data-marker-location="' + location.cat_location_id + '"  value="' + location.cat_location_id + '" /><a data-marker="' + location.cat_location_id + '" data-zoom="' + location.cat_location_zoom + '" href="javascript:void(0);">' + location.cat_location_title + '</a></li>';

                            } else if (location.onclick_action == "post") {

                                content += '<li><input type="checkbox" data-marker-location="' + location.cat_location_id + '"  value="' + location.cat_location_id + '" /><a href="' + location.redirect_permalink + '" target="_blank">' + location.cat_location_title + '</a></li>';

                            } else if (location.onclick_action == "custom_link") {

                                content += '<li><input type="checkbox" data-marker-location="' + location.cat_location_id + '"  value="' + location.cat_location_id + '" /><a href="' + location.redirect_custom_link + '" target="_blank">' + location.cat_location_title + '</a></li>';

                            }



                        });



                        content += '</ul>';

                        content += '</div>';

                    }

                    content += '</div>';

                    content += map_obj.display_sub_categories(child_categories_tab_data, child_cat.cat_id, '', (padding + 20));

                } else if ((index + 1) == child_categories_tab_data.length)

                    return;

            });

            return content;

        },

        sorting:function(order_by, in_order, data_type) {



            switch (order_by) {



                case 'category':

                    this.places.sort(this.sortByCategory);

                    this.show_places.sort(this.sortByCategory);

                    if (in_order == 'desc') {

                        this.places.reverse();

                        this.show_places.reverse();

                    }

                    break;



                case 'title':



                    if (this.map_data.places !== undefined) {

                        this.map_data.places.sort(this.sortByTitle);

                    }

                    if (this.show_places !== undefined) {

                        this.show_places.sort(this.sortByTitle);

                    }

                    if (in_order == 'desc') {

                        this.map_data.places.reverse();

                        this.places.reverse();

                        this.show_places.reverse();

                    }

                    break;



                case 'address':

                    this.map_data.places.sort(this.sortByAddress);

                    this.show_places.sort(this.sortByAddress);

                    if (in_order == 'desc') {

                        this.places.reverse();

                        this.show_places.reverse();

                    }

                    break;

                default:



                    var first_place = this.map_data.places[0];

                    if (typeof first_place[order_by] != 'undefined') {


                        this.map_data.places.sort(this.sortByPlace(order_by, data_type));

                        this.show_places.sort(this.sortByPlace(order_by, data_type));


                    } else if (typeof first_place.location[order_by] != 'undefined') {

                        this.map_data.places.sort(this.sortByLocation(order_by, data_type));

                        this.show_places.sort(this.sortByLocation(order_by, data_type));

                    } else if (typeof first_place.location.extra_fields[order_by] != 'undefined') {

                        this.map_data.places.sort(this.sortByExtraFields(order_by, data_type));

                        this.show_places.sort(this.sortByExtraFields(order_by, data_type));

                    }



                    if (in_order == 'desc') {

                        this.places.reverse();

                        this.show_places.reverse();

                    }

            }

        },



        sortByExtraFields: function(order_by, data_type) {



            return function(a, b) {



                if (typeof b.location.extra_fields[order_by] != 'undefined' && typeof a.location.extra_fields[order_by] != 'undefined') {



                    if (b.location.extra_fields[order_by] == null) {

                        b.location.extra_fields[order_by] = '';

                    }



                    if (a.location.extra_fields[order_by] == null) {

                        a.location.extra_fields[order_by] = '';

                    }



                    if (data_type == 'num') {

                        var a_val = parseInt(a.location.extra_fields[order_by]);

                        var b_val = parseInt(b.location.extra_fields[order_by]);

                    } else {

                        var a_val = String(a.location.extra_fields[order_by]).toLowerCase();

                        var b_val = String(b.location.extra_fields[order_by]).toLowerCase();

                    }



                    return ((a_val < b_val) ? -1 : ((a_val > b_val) ? 1 : 0));



                }

            }



        },

        sortByLocation: function(order_by, data_type) {

            return function(a, b) {



                if (b.location[order_by] && a.location[order_by]) {



                    if (a.location[order_by] && b.location[order_by]) {

                        var a_val = String(a.location[order_by]).toLowerCase();

                        var b_val = String(b.location[order_by]).toLowerCase();

                        if (data_type == 'num') {

                            a_val = parseInt(a_val);

                            b_val = parseInt(b_val);

                        }

                        return ((a_val < b_val) ? -1 : ((a_val > b_val) ? 1 : 0));

                    }



                }

            }



        },

        sortByPlace: function(order_by, data_type) {





            return function(a, b) {



                if (b[order_by] && a[order_by]) {



                    if (a[order_by] && b[order_by]) {

                        var a_val = a[order_by];

                        var b_val = b[order_by];

                        if (data_type == 'num') {

                            a_val = parseInt(a_val);

                            b_val = parseInt(b_val);

                        }else{

                            var a_val = String(a_val).toLowerCase();

                            var b_val = String(b_val).toLowerCase();

                        }

                        return ((a_val < b_val) ? -1 : ((a_val > b_val) ? 1 : 0));

                    }



                }

            }



        },

        sortByCategory: function(a, b) {

            if (b.categories[0] && a.categories[0]) {

                if (a.categories[0].name && b.categories[0].name) {

                    var a_val = String(a.categories[0].name).toLowerCase();

                    var b_val = String(b.categories[0].name).toLowerCase();

                    return ((a_val < b_val) ? -1 : ((a_val > b_val) ? 1 : 0));

                }



            }

        },



        sortByTitle: function(a, b) {

            var a_val = String(a.title).toLowerCase();

            var b_val = String(b.title).toLowerCase();

            return ((a_val < b_val) ? -1 : ((a_val > b_val) ? 1 : 0));

        },



        sortByValue: function(a, b) {

            var a_val = String(a).toLowerCase();

            var b_val = String(b).toLowerCase();

            return ((a_val < b_val) ? -1 : ((a_val > b_val) ? 1 : 0));

        },



        sortByAddress: function(a, b) {

            var a_val = String(a.address).toLowerCase();

            var b_val = String(b.address).toLowerCase();

            return ((a_val < b_val) ? -1 : ((a_val > b_val) ? 1 : 0));

        },



        update_filters: function() {

            var map_obj = this;

            var filters = {};



            var all_dropdowns = $(map_obj.container).find('[data-filter="dropdown"]');

            var all_checkboxes = $(map_obj.container).find('[data-filter="checklist"]:checked');

            var all_list = $(map_obj.container).find('[data-filter="list"].fc_selected');



            $.each(all_dropdowns, function(index, element) {

                if ($(this).val() != '') {



                    if (typeof filters[$(this).data('name')] == 'undefined') {

                        filters[$(this).data('name')] = [];

                    }



                    filters[$(this).data('name')].push($(this).val());

                }



            });



            $.each(all_checkboxes, function(index, element) {



                if (typeof filters[$(this).data('name')] == 'undefined') {

                    filters[$(this).data('name')] = [];

                }



                filters[$(this).data('name')].push($(this).val());



            });



            $.each(all_list, function(index, element) {



                if (typeof filters[$(this).data('name')] == 'undefined') {

                    filters[$(this).data('name')] = [];

                }



                filters[$(this).data('name')].push($(this).data('value').toString());



            });

            this.apply_filters(filters);



        },



        apply_url_filters: function() {



            var map_obj = this;

            var search = location.search.substring(1);

            var url_filters = $.parseParams(search || '');

            var filters = {};

            if (!$.isEmptyObject(url_filters)) {



                map_obj.url_filters = url_filters;



                $.each(url_filters, function(index, element) {

                    if (index == 'search') {

                        $(map_obj.container).find('[data-input="wpomp-search-text"]').val(element);

                    }

                });



                map_obj.apply_filters(filters);

            }

        },



        apply_filters: function(filters) {



            var map_obj = this;

            var showAll = true;

            var show = true;

            map_obj.show_places = [];

            var enable_search_term = false;

            // Filter by search box.

            if ($(map_obj.container).find('[data-input="wpomp-search-text"]').length > 0) {

                var search_term = $(map_obj.container).find('[data-input="wpomp-search-text"]').val();

                search_term = String(search_term).toLowerCase();

                if (search_term.length > 0) {

                    enable_search_term = true;

                }

            }



            if (((map_obj.map_data.map_tabs && map_obj.map_data.map_tabs.category_tab && map_obj.map_data.map_tabs.category_tab.cat_tab === true) || $(map_obj.container).find('input[data-marker-category]').length > 0)) {

                var all_selected_category_sel = $(map_obj.container).find('input[data-marker-category]:checked');

                var all_selected_category = [];

                var all_not_selected_location = [];

                if (all_selected_category_sel.length > 0) {

                    $.each(all_selected_category_sel, function(index, selected_category) {

                        all_selected_category.push($(selected_category).data("marker-category"));

                        var all_not_selected_location_sel = $(selected_category).closest('[data-container="wpomp-category-tab-item"]').find('input[data-marker-location]:not(:checked)');

                        if (all_not_selected_location_sel.length > 0) {

                            $.each(all_not_selected_location_sel, function(index, not_selected_location) {

                                all_not_selected_location.push($(not_selected_location).data("marker-location"));

                            });

                        }

                    });

                }

                var all_selected_location_sel = $(map_obj.container).find('[data-container="wpomp-category-tab-item"]').find('input[data-marker-location]:checked');

                var all_selected_location = [];

                if (all_selected_location_sel.length > 0) {

                    $.each(all_selected_location_sel, function(index, selected_location) {

                        all_selected_location.push($(selected_location).data("marker-location"));

                    });

                }

            }

            if (typeof map_obj.map_data.places != 'undefined') {

                $.each(map_obj.map_data.places, function(place_key, place) {
                    show = true;
                    if (typeof filters != 'undefined') {
                        $.each(filters, function(filter_key, filter_values) {



                            var in_fields = false;



                            if ($.isArray(filter_values)) {



                                if (typeof place.categories != 'undefined' && filter_key == "category") {



                                    $.each(place.categories, function(cat_index, category) {

                                        if ($.inArray(category.id, filter_values) > -1) {

                                            in_fields = true;

                                        }

                                    });

                                }



                                if (typeof place.custom_filters != 'undefined') {

                                    $.each(place.custom_filters, function(k, val) {

                                        if (filter_key == k) {

                                            in_fields = false;

                                            if ($.isArray(val)) {

                                                $.each(val, function(index, value) {

                                                    if ($.inArray(value, filter_values) > -1)

                                                        in_fields = true;

                                                });

                                            } else if (val == filter_values.val)

                                                in_fields = true;

                                        }

                                    });

                                }



                                if (typeof place[filter_key] != 'undefined') {

                                    if ($.inArray(place[filter_key], filter_values) > -1) {

                                        in_fields = true;

                                    }

                                } else if (typeof place.location[filter_key] != 'undefined') {

                                    if ($.inArray(place.location[filter_key], filter_values) > -1) {

                                        in_fields = true;



                                    }

                                } else if (place.location.extra_fields && typeof place.location.extra_fields[filter_key] != 'undefined') {



                                    var dropdown_value = filter_values[0];

                                    if (place.location.extra_fields[filter_key] && place.location.extra_fields[filter_key].indexOf(dropdown_value) > -1) {

                                        in_fields = true;

                                    } else if ($.inArray(place.location.extra_fields[filter_key], filter_values) > -1) {

                                        in_fields = true;

                                    }



                                }



                                if (in_fields == false)

                                    show = false;



                            } else {

                                filter_values.val = "";

                            }

                        });

                    }
                  
                    if (enable_search_term === true && show === true) {


                        if (place.title != undefined && String(place.title).toLowerCase().indexOf(search_term) >= 0) {

                            show = true;

                        } else if (place.content != undefined && String(place.content).toLowerCase().indexOf(search_term) >= 0) {

                            show = true;



                        } else if (String(place.location.lat).toLowerCase().indexOf(search_term) >= 0) {

                            show = true;



                        } else if (String(place.location.lng).toLowerCase().indexOf(search_term) >= 0) {

                            show = true;



                        } else if (place.address && String(place.address).toLowerCase().indexOf(search_term) >= 0) {

                            show = true;

                        } else if (place.location.state && String(place.location.state).toLowerCase().indexOf(search_term) >= 0) {

                            show = true;



                        } else if (place.location.country && String(place.location.country).toLowerCase().indexOf(search_term) >= 0) {

                            show = true;



                        } else if (place.location.postal_code && String(place.location.postal_code).toLowerCase().indexOf(search_term) >= 0) {

                            show = true;



                        } else if (place.location.city && String(place.location.city).toLowerCase().indexOf(search_term) >= 0) {

                            show = true;

                        } else if (typeof map_obj.search_area != 'undefined' && map_obj.search_area != '' && map_obj.wpomp_within_radius(place, map_obj.search_area) === true) {

                            show = true;

                        } else {

                            show = false;

                        }



                        if (typeof place.location.extra_fields != 'undefined') {
                            $.each(place.location.extra_fields, function(field, value) {
                                if (value) {
                                    value = value.toString();
                                    if (value && String(value).toLowerCase().indexOf(search_term) >= 0)
                                        show = true;
                                }
                            });

                        }



                    }



                    //Exclude locations without category if location filters are choosed by user

                    if ((place.categories.length == undefined || place.categories.length == 'undefined') && all_selected_category && (all_selected_category.length > 0) && ($(map_obj.container).find('input[name="wpomp_select_all"]').is(":checked") == false) && show) {

                        show = false;

                    }



                    // if checked category

                    if (all_selected_category && show != false && place.categories.length != undefined) {



                        var in_checked_category = false;



                        if (all_selected_category.length === 0) {

                            // means no any category selected so show those location without categories.

                            if (typeof place.categories != 'undefined') {

                                $.each(place.categories, function(cat_index, category) {

                                    if (category.id === '')

                                        in_checked_category = true;

                                });

                            }

                        } else {

                            if (typeof place.categories != 'undefined') {

                                $.each(place.categories, function(cat_index, category) {

                                    if (category.id === '')

                                        in_checked_category = true;

                                    else if ($.inArray(parseInt(category.id), all_selected_category) > -1) {

                                        in_checked_category = true;

                                        

                                       var cat_c =   L.icon({ iconUrl: category.icon,

                                        iconSize: [32, 32],               
                                        iconAnchor: [16, 32], 

                                        popupAnchor: [0, -55],

                                         });

                                        place.marker.setIcon(cat_c);

                                    }



                                });

                            }

                        }



                        //Hide unchecked  locations.

                        if (all_not_selected_location.length !== 0) {

                            if ($.inArray(parseInt(place.id), all_not_selected_location) > -1) {

                                in_checked_category = false;

                            }

                        }



                        if (in_checked_category === false)

                            show = false;

                        else

                            show = true;







                        //Show Here checked location.

                        if (all_selected_location.length !== 0) {

                            if ($.inArray(parseInt(place.id), all_selected_location) > -1) {

                                show = true;

                            }

                        }



                    }





                    place.marker.visible = show;

                    if (show == false) {

                        place.marker.remove();

                    }else{

                        place.marker.addTo(map_obj.map);

                    }

                    

                    if (show === true)

                        map_obj.show_places.push(place);



                });

            }





            if (typeof map_obj.map_data.map_options.bound_map_after_filter !== typeof undefined &&

                map_obj.map_data.map_options.bound_map_after_filter === true) {

                var after_filter_bounds = new L.LatLngBounds();



                if(map_obj.show_places.length>0){



                         for (var j = 0; j < map_obj.show_places.length; j++) {

                            after_filter_bounds.extend(new L.LatLng(

                                    parseFloat(map_obj.show_places[j]['location']['lat']),

                                    parseFloat(map_obj.show_places[j]['location']['lng'])

                                ));

                        }

                        map_obj.map.fitBounds(after_filter_bounds);

                }

        



            }



            if (map_obj.map_data.listing) {



                if ($(map_obj.container).find('[data-filter="map-sorting"]').val()) {

                    var order_data = $(map_obj.container).find('[data-filter="map-sorting"]').val().split("__");

                    var data_type = '';

                    if (order_data[0] !== '' && order_data[1] !== '') {



                        if (typeof order_data[2] != 'undefined') {

                            data_type = order_data[2];

                        }

                        map_obj.sorting(order_data[0], order_data[1], data_type);

                    }

                } else {

                    if (map_obj.map_data.listing.default_sorting) {

                        var data_type = '';

                        if (map_obj.map_data.listing.default_sorting.orderby == 'listorder') {

                            data_type = 'num';

                        }

                        map_obj.sorting(map_obj.map_data.listing.default_sorting.orderby, map_obj.map_data.listing.default_sorting.inorder, data_type);

                    }

                }



                map_obj.update_places_listing();

            }





        },



        create_perpage_option: function() {



            var map_obj = this;

            var options = '';

            var content = '';



            content += '<select name="map_perpage_location_sorting" data-filter="map-perpage-location-sorting" class="choose_salutation">';

            content += '<option value="' + map_obj.map_data.listing.pagination.listing_per_page + '">' + wpomp_local.show_locations + '</option>';

            content += '<option value="25">25</option>';

            content += '<option value="50">50</option>';

            content += '<option value="100">100</option>';

            content += '<option value="200">200</option>';

            content += '<option value="500">500</option>';

            content += '<option value="' + map_obj.show_places.length + '">' + wpomp_local.all_location + '</option>';

            content += '</select>';



            return content;



        },

        create_sorting: function() {



            var options = '';



            var content = '';



            if (this.map_data.listing.display_sorting_filter === true) {

                content += '<select name="map_sorting" data-filter="map-sorting"><option value="">' + wpomp_local.sort_by + '</option>';

                $.each(this.map_data.listing.sorting_options, function(id, name) {

                    content += "<option value='" + id + "'>" + name + "</option>";

                });

                content += '</select>';

            }



            return content;

        },



        create_radius: function() {



            var options = '';



            var content = '';

            if (this.map_data.listing.display_radius_filter === true) {



                content += '<select data-name="radius" name="map_radius"><option value="">' + wpomp_local.select_radius + '</option>';

                var radius_options = this.map_data.listing.radius_options;

                var radius_dimension = this.map_data.listing.radius_dimension;



                if(radius_options!= undefined){





                    $.each(radius_options.split(','), function(id, name) {

                        if (radius_dimension == 'miles') {

                            content += "<option value='" + name + "'>" + name + ' ' + wpomp_local.miles + "</option>";

                        } else {

                            content += "<option value='" + name + "'>" + name + ' ' + wpomp_local.km + "</option>";

                        }

                    });



                }



                content += '</select>';

            }



            return content;

        },



        custom_filters: function() {

            var map_obj = this;

            var options = '';

            var places = this.map_data.places;

            var wpomp_filters = this.map_data.filters;

            if (typeof wpomp_filters == 'undefined' || typeof wpomp_filters.custom_filters == 'undefined' || wpomp_filters.custom_filters.length == 0) {

                return;

            }



            $.each(wpomp_filters.custom_filters, function(template_shortcode, filter_options) {

                var all_filters = [];

                var content = '';

                var filters = {};

                $.each(filter_options, function(filter_type, filter_parameter) {



                    $.each(filter_parameter, function(filter_name, filter_label) {

                        $.each(places, function(index, place) {

                            if (filter_name == 'category') {

                                if (typeof place.categories == 'undefined') {

                                    place.categories = {};

                                }

                                $.each(place.categories, function(cat_index, category) {



                                    if (typeof filters[category.type] == 'undefined') {

                                        filters[category.type] = {};

                                    }

                                    if (category.name) {

                                        filters[category.type][category.name] = category.id;

                                    }



                                });



                            } else {



                                if (typeof place[filter_name] != 'undefined') {

                                    if (typeof filters[filter_name] == 'undefined') {

                                        filters[filter_name] = {};

                                    }

                                    if (place[filter_name]) {

                                        filters[filter_name][place[filter_name]] = place[filter_name];

                                    }

                                }



                                if (typeof place.location.extra_fields[filter_name] != 'undefined') {

                                    if (typeof filters[filter_name] == 'undefined') {

                                        filters[filter_name] = {};

                                    }

                                    if (place.location.extra_fields[filter_name]) {

                                        filters[filter_name][place.location.extra_fields[filter_name]] = place.location.extra_fields[filter_name];

                                    }

                                }



                                if (typeof place.location[filter_name] != 'undefined') {

                                    if (typeof filters[filter_name] == 'undefined') {

                                        filters[filter_name] = {};

                                    }

                                    if (place.location[filter_name]) {

                                        filters[filter_name][place.location[filter_name]] = place.location[filter_name];

                                    }

                                }



                                if (typeof place.custom_filters != 'undefined' && typeof place.custom_filters[filter_name] != 'undefined') {

                                    if (typeof filters[filter_name] == 'undefined') {

                                        filters[filter_name] = {};

                                    }

                                    if (place.custom_filters[filter_name]) {

                                        var options = place.custom_filters[filter_name];

                                        if ($.isArray(options)) {

                                            $.each(options, function(index, value) {

                                                filters[filter_name][value] = value;

                                            });

                                        } else {

                                            filters[filter_name][options] = options;

                                        }

                                    }

                                }



                                // It could be radius filter. 

                                if (filter_name == 'radius') {

                                    if (typeof filters[filter_name] == 'undefined') {

                                        filters[filter_name] = {};

                                    }



                                    var radius_options = wpomp_filters.radius_options;

                                    var radius_dimension = wpomp_filters.radius_dimension;

                                    $.each(radius_options.split(','), function(id, name) {

                                        if (radius_dimension == 'miles') {

                                            filters[filter_name][name + ' ' + wpomp_local.miles] = name;

                                        } else {

                                            filters[filter_name][name + ' ' + wpomp_local.km] = name;

                                        }

                                    });



                                }

                            }



                        });



                    });







                    if (filter_type == 'dropdown') {

                        if (typeof filters != 'undefined') {

                            $.each(filters, function(index, options) {

                                options = map_obj.sort_object_by_value(options);

                                options = map_obj.sort_object_by_unique_values(options);

                                content += '<select data-filter="dropdown"  name="place_' + index + '" data-name = "' + index + '">';

                                content += '<option value="">' + ((filter_parameter[index]) ? filter_parameter[index] : 'Select ' + index) + '</option>';

                                $.each(options, function(name, value) {

                                    var optionlabel = value;

                                    value = value.replace("'", "&#39;");

                                    value = value.replace('"', '&#34;');

                                    if (value != '' && value != null)

                                        content += "<option value='" + value + "'>" + optionlabel + "</option>";

                                });

                                content += '</select>';

                            });

                        }

                    }



                    if (filter_type == 'checklist') {

                        if (typeof filters != 'undefined') {

                            $.each(filters, function(index, options) {

                                content += '<div class="wpomp_filters_checklist">';

                                content += '<label  data-filter = "place_' + index + '" >' + ((wpomp_filters.custom_filters[index]) ? wpomp_filters.custom_filters[index] : 'Select ' + index) + '</label>';

                                $.each(options, function(name, value) {

                                    if (value != '' && value != null)

                                        content += "<input data-filter='checklist' type='checkbox' data-name = '" + index + "' value='" + value + "'>" + name;

                                });

                                content += '</div>';

                            });

                        }

                    }



                    if (filter_type == 'list') {

                        if (typeof filters != 'undefined') {

                            $.each(filters, function(index, options) {

                                content += '<div class="wpomp_filters_list">';

                                content += '<label  data-filter = "place_' + index + '" >' + ((wpomp_filters.custom_filters[index]) ? wpomp_filters.custom_filters[index] : 'Select ' + index) + '</label><ul>';

                                $.each(options, function(name, value) {

                                    if (value != '' && value != null)

                                        content += "<li data-filter='list' data-name = '" + index + "' data-value='" + value + "'>" + name + "</li>";

                                });

                                content += '</ul></div>';

                            });

                        }

                    }

                });



                $('body').find(wpomp_filters.filters_container).append(content);

            });



            // now create select boxes



        },

        sort_object_by_keyvalue: function(options, by, type, in_order) {



            var sortable = [];

            for (var key in options) {

                sortable.push(options[key]);

            }

            sortable.sort(this.sortByPlace(by, type));



            if (in_order == 'desc') {

                sortable.reverse();

            }



            return sortable;

        },

        sort_object_by_unique_values: function(options) {



            var new_options = [];

            var uniqueNames = [];

            for (var key in options) {



                if (options[key].indexOf(',') > -1) {

                    options[key].split(/\s*,\s*/).forEach(function(single_option_value) {

                        new_options.push(single_option_value.trim());

                    });

                } else {

                    new_options.push(options[key].trim());

                }



            }



            uniqueNames = new_options.filter(function(item, pos) {

                return new_options.indexOf(item) == pos;

            });



            return uniqueNames.sort();

        },

        sort_object_by_value: function(options) {



            var sortable = [];

            for (var key in options) {

                sortable.push(key);

            }



            sortable.sort(this.sortByValue);

            var new_options = {}

            for (var i = 0; i < sortable.length; i++) {

                new_options[sortable[i]] = options[sortable[i]];

            }



            return new_options;

        },

        create_filters: function() {

            var map_obj = this;

            var options = '';

            var filters = {};

            var places = this.map_data.places;

            var wpomp_listing_filter = this.map_data.listing;

            var wpomp_alltfilter = wpomp_listing_filter.display_taxonomies_all_filter;



            $.each(places, function(index, place) {

                if (typeof place.categories == 'undefined') {

                    place.categories = {};

                }

                $.each(place.categories, function(cat_index, category) {



                    if (typeof filters[category.type] == 'undefined') {

                        filters[category.type] = {};

                    }



                    if (category.name) {

                        if (category.extension_fields && category.extension_fields.cat_order) {

                            filters[category.type][category.name] = {

                                'id': category.id,

                                'order': category.extension_fields.cat_order,

                                'name': category.name

                            };

                        } else {

                            filters[category.type][category.name] = {

                                'id': category.id,

                                'order': 0,

                                'name': category.name

                            };

                        }



                    }



                });

            });

            // now create select boxes



            var content = '',

                by = 'name',

                type = '',

                inorder = 'asc';



            if (map_obj.map_data.listing) {

                if (map_obj.map_data.listing.default_sorting) {

                    if (map_obj.map_data.listing.default_sorting.orderby == 'listorder') {

                        by = 'order';

                        type = 'num';

                        inorder = map_obj.map_data.listing.default_sorting.inorder;

                    }

                    inorder = map_obj.map_data.listing.default_sorting.inorder;

                }



            }



            $.each(filters, function(index, options) {

                if (wpomp_listing_filter.display_category_filter === true && index == "category") {

                    content += '<select data-filter="dropdown" data-name="category" name="place_' + index + '">';

                    content += '<option value="">' + wpomp_local.select_category + '</option>';

                    options = map_obj.sort_object_by_keyvalue(options, by, type, inorder);

                    $.each(options, function(name, value) {

                        content += "<option value='" + value.id + "'>" + value.name + "</option>";

                    });

                    content += '</select>';

                } else if (wpomp_listing_filter.display_taxonomies_filter === true) {

                    if (wpomp_alltfilter === null)

                        return false;



                    if (wpomp_alltfilter.indexOf(index) > -1) {

                        content += '<select data-filter="dropdown" data-name="category" name="place_' + index + '">';

                        content += '<option value="">Select ' + index + '</option>';

                        $.each(options, function(name, value) {

                            content += "<option value='" + value + "'>" + name + "</option>";

                        });

                        content += '</select>';

                    }

                }



            });



            return content;

        },



        update_places_listing: function() {



            var map_obj = this;



            if (map_obj.per_page_value > 0)

                map_obj.per_page_value = map_obj.per_page_value;

            else

                map_obj.per_page_value = map_obj.map_data.listing.pagination.listing_per_page;

            $(map_obj.container).find(".location_pagination" + map_obj.map_data.map_property.map_id).pagination(map_obj.show_places.length, {

                callback: map_obj.display_places_listing,

                map_data: map_obj,

                items_per_page: map_obj.per_page_value,

                prev_text: wpomp_local.prev,

                next_text: wpomp_local.next

            });



        },



        display_filters_listing: function() {



            if (this.map_data.listing) {



                var hide_locations = this.map_data.listing.hide_locations;



                var wpompgl = this.map_data.listing.list_grid;



                if (hide_locations != true) {



                    var content = '<div class="wpomp_listing_container">';





                    content += "<div class='wpomp_categories wpomp_print_listing " + wpompgl + "' data-container='wpomp-listing-" + $(this.element).attr("id") + "'></div>";





                    content += "</div>";



                    $(this.map_data.listing.listing_container).html(content);



                }



                var filter_position = this.map_data.listing.filters_position;
                
                var listing_filter_content = this.display_filters();
                if(this.map_data.layoutManager !== 'undefined' && this.map_data.layoutManager == 'true'){
                
                    if( $(this.container).find(".wpgmp_filter_wrappers").length > 0)
                    $(this.container).find(".wpgmp_filter_wrappers").html(this.listing_filter_content);
               
                }else{
                    // var filter_content = '<div class="wpomp_filter_wrappers" id>' + this.display_filters() + '</div>';

                    if (filter_position == 'top_map') {

                        $(this.container).find('.wpomp_filter_wrappers').html(listing_filter_content);
                        
                    } else if (hide_locations == true) {

                        $(this.container).find('.wpomp_filter_wrappers').html(listing_filter_content);
                        
                    } else {
                        
                        $(this.container).find('.wpomp_filter_wrappers').html(listing_filter_content);

                    }
                }




            }



        },



        display_filters: function() {



            var hide_locations = this.map_data.listing.hide_locations;



            var content = '';

            content += '<div class="wpomp_before_listing">' + this.map_data.listing.listing_header + '</div>';



            if (this.map_data.listing.display_search_form === true) {

                var autosuggest_class = '';



                if (this.map_data.listing.search_field_autosuggest === true) {

                    autosuggest_class = "wpomp_auto_suggest";

                }

                

                content += '<div class="wpomp_listing_header"><div class="wpomp_search_form"><input id="searchBox" type="text" rel="24" data-input="wpomp-search-text" name="wpomp_search_input" class="wpomp_search_input ' + autosuggest_class + '" placeholder="' + wpomp_local.search_placeholder + '"></div></div>';

                

            }



            content += '<div class="categories_filter">' + this.create_filters() + '<div data-container="wpomp-filters-container"></div>';



            if (hide_locations != true)

                content += this.create_sorting() + '';



            if (hide_locations != true && this.map_data.listing.display_location_per_page_filter === true) {

                content += ' ' + this.create_perpage_option() + ' ';

            }



            content += ' ' + this.create_radius() + ' ';



          

            if (hide_locations != true && this.map_data.listing.display_grid_option === true) {

                content += ' ' + wpomp_local.img_grid + wpomp_local.img_list;

            }



            if (typeof this.map_data.map_options.display_reset_button != "undefined" &&

                this.map_data.map_options.display_reset_button === true) {

                content += '<div class="categories_filter_reset"><input type="button" class="categories_filter_reset_btn" name="categories_filter_reset_btn" value="' + this.map_data.map_options.map_reset_button_text + '"></div>';

            }



            content += '</div>';



            return content;

        },



        display_places_listing: function(page_index, jq) {



            var content = '';



            var map_obj = this;

            var category_selector_dropdown = $('select[name = "place_category"]');

            var items_per_page = 10;

            if (map_obj.items_per_page)

                items_per_page = map_obj.items_per_page;

            else

                items_per_page = map_obj.map_data.map_data.listing.pagination.listing_per_page;



            var data_source = map_obj.map_data.show_places;



            var listing_container = map_obj.map_data.map_data.listing.listing_container;





            var listing_placeholder = map_obj.map_data.map_data.listing.listing_placeholder;



            var max_elem = Math.min((page_index + 1) * items_per_page, data_source.length);

            var link = '';

            var onclick_action = '';





            if (max_elem > 0) {

                for (var i = page_index * items_per_page; i < max_elem; i++) {

                    var place = data_source[i];

                    var temp_listing_placeholder = listing_placeholder;





                        if (place.id) {

                            if (place.location.onclick_action == "marker") { 

                                link = '<a href="javascript:void(0);" data-source="' + place.source + '"  class="place_title" data-zoom="' + place.location.zoom + '"  data-marker="' + place.id + '" >' + place.title + '</a>';

                                onclick_action = 'href="javascript:void(0);" data-source="' + place.source + '" data-zoom="' + place.location.zoom + '"  data-marker="' + place.id + '"';

                            } else if (place.location.onclick_action == "post") {

                                link = '<a href="' + place.location.redirect_permalink + '" target="_blank">' + place.title + '</a>';

                                onclick_action = 'href="' + place.location.redirect_permalink + '" target="_blank"';

                            } else if (place.location.onclick_action == "custom_link") {

                                link = '<a href="' + place.location.redirect_custom_link + '" target="_blank">' + place.title + '</a>';

                                onclick_action = 'href="' + place.location.redirect_custom_link + '" target="_blank"';

                            } else {

                                link = '<a href="javascript:void(0);" class="place_title" data-source="' + place.source + '" data-zoom="' + place.location.zoom + '"  data-marker="' + place.id + '" >' + place.title + '</a>';

                                onclick_action = 'href="javascript:void(0);"data-source="' + place.source + '" data-zoom="' + place.location.zoom + '"  data-marker="' + place.id + '"';

                            }

                        }



                        var image = [];

                        var category_name = [];

                        var wpomp_arr = {};



                        if (place.categories) {

                            for (var c = 0; c < place.categories.length; c++) {

                                if (place.categories[c].icon !== '') {

                                    image.push("<img title='" + place.categories[c].name + "' alt='" + place.categories[c].name + "' src='" + place.categories[c].icon + "' />");

                                }



                                if (place.categories[c].type == 'category' && place.categories[c].name != '') {

                                    category_name.push(place.categories[c].name);

                                }



                                if (place.categories[c].type != 'category') {

                                    if (typeof place.categories[c].name == "undefined")

                                        continue;



                                    if (place.categories[c].name)

                                        var sep = ',';



                                    if (typeof wpomp_arr[place.categories[c].type] == "undefined")

                                        wpomp_arr[place.categories[c].type] = '';



                                    wpomp_arr[place.categories[c].type] += place.categories[c].name + sep;

                                }

                            }

                        }



                        var marker_image = '';



                        if (place.source == 'post') {

                            marker_image = place.location.extra_fields.post_featured_image;

                        } else {

                            marker_image = place.location.marker_image;

                        }

                        var replaceData = {

                            "{marker_id}": place.id,

                            "{marker_title}": link,

                            "{marker_address}": place.address,

                            "{marker_latitude}": place.location.lat,

                            "{marker_longitude}": place.location.lng,

                            "{marker_city}": place.location.city,

                            "{marker_state}": place.location.state,

                            "{marker_country}": place.location.country,

                            "{marker_postal_code}": place.location.postal_code,

                            "{marker_zoom}": place.location.zoom,

                            "{marker_icon}": image,

                            "{marker_category}": category_name.join(", "),

                            "{marker_message}": place.content,

                            "{marker_image}": marker_image,

                            "{marker_featured_image}": marker_image,

                            "{wpomp_listing_html}": place.listing_hook,

                            "{onclick_action}": onclick_action



                        };



                        //Add extra fields of locations

                        if (typeof place.location.extra_fields != 'undefined') {

                            for (var extra in place.location.extra_fields) {

                                if (!place.location.extra_fields[extra]) {

                                    replaceData['{' + extra + '}'] = "<div class='wpomp_empty'>wpomp_empty</div>";



                                } else {

                                    replaceData['{' + extra + '}'] = place.location.extra_fields[extra];

                                }

                            }

                        }

        
                        if(map_obj.map_data.map_data.map_options.link_extra_field != undefined && map_obj.map_data.map_data.map_options.link_extra_field != ''){
                            var anchor_tag = map_obj.map_data.map_data.map_options.link_extra_field;
                           
                            for (var prop_an in anchor_tag) {
                                                    
                                if (replaceData[prop_an] != "<div class='wpomp_empty'>wpomp_empty</div>" && prop_an != ''){
                                    temp_listing_placeholder = temp_listing_placeholder.replace(prop_an,anchor_tag[prop_an]);
                                }
                            }
                        }


                        for (var prop in replaceData) {

                            if (replaceData[prop] == undefined || replaceData[prop] == 'undefined')

                                replaceData[prop] = '';

                        }



                        if (wpomp_arr) {

                            for (var n in wpomp_arr) {

                                replaceData["{" + n + "}"] = wpomp_remove_last_comma(wpomp_arr[n]);

                            }

                        }



                        var wpomp_remove_last_comma = function(strng) {

                            var n = strng.lastIndexOf(",");

                            var a = strng.substring(0, n)

                            return a;

                        }



                        temp_listing_placeholder = temp_listing_placeholder.replace(/{[^{}]+}/g, function(match) {

                            if (match in replaceData) {

                                return (replaceData[match]);

                            } else {

                                return ("");

                            }

                        });



                        content += temp_listing_placeholder;

                    }

            } else {

                content = "<div class='wpomp_no_locations'>" + wpomp_local.wpomp_location_no_results + "</div>";

            }





            content += '<div id="wpomp_pagination"></div>';



            content = '<div class="fc-' + map_obj.map_data.map_data.listing.list_item_skin.type + '-' + map_obj.map_data.map_data.listing.list_item_skin.name + ' fc-wait"><div data-page="2" class="fc-component-6" data-layout="' + map_obj.map_data.map_data.listing.list_item_skin.name + '" >' + content + '</div></div>';



            $(listing_container).find(".wpomp_categories").html(content);

            $(listing_container).find(".wpomp_extra_field:contains('wpomp_empty')").remove();

            $(listing_container).find(".wpomp_empty").prev().remove();

            $(listing_container).find(".wpomp_empty").remove();



            try {

                var container = $(listing_container).find('.wpomp_listing_grid');

                if (container) {



                    var msnry = $(container).data('masonry');

                    if (msnry) {

                        msnry.destroy();

                    }



                    var $grid = $(container).imagesLoaded(function() {

                        // init Masonry after all images have loaded

                        $grid.masonry({

                            itemSelector: '.wpomp_listing_grid .wpomp_locations',

                            columnWidth: '.wpomp_listing_grid .wpomp_locations',

                        });

                    });



                }



            } catch (err) {

                console.log(err);

            }



            return false;

        },

            

        open_infowindow: function(current_place,source) {

            var map_obj = this;

            $.each(this.map_data.places, function(key, place) {

                if ((parseInt(place.id) == parseInt(current_place)) && (place.source==source)   ) {

                    map_obj.openInfoWindow(place);                    



                }

            });

        },
        event_listener: function(obj, type, func) {
            obj.on(type,func);
        },
        map_loaded: function() {
            var map_obj = this;
            var gmap = map_obj.map;
            var center = gmap.getCenter();
            if (map_obj.settings.center_by_nearest === true) {

                map_obj.center_by_nearest();

            }else if (map_obj.settings.show_center_marker === true) {

                map_obj.show_center_marker();

            }

            if(map_obj.settings.center_by_nearest === false){

                 if (map_obj.settings.show_center_circle === true) {
                    map_obj.show_center_circle();
                }
            }


            $('body').on('click', ".fc-accordion-tab", function() {

            if ($(this).hasClass('active')) {

                $(this).removeClass('active');

                var acc_child = $(this).next().removeClass('active');

            } else {

                $(".fc-accordion-tab").removeClass('active');

                $(".fc-accordion dd").removeClass('active');

                $(this).addClass('active');

                var acc_child = $(this).next().addClass('active');

            }

        });







        },

        responsive_map: function() {



            var map_obj = this;

            var gmap = map_obj.map;

        },

        show_search_control: function() {

            var map_obj = this;

            var obj1 = $(".wpomp_map_suggest");

            var country = '';
            var nomination_url = '';

            if (wpomp_local.wpomp_country_specific && wpomp_local.wpomp_country_specific == true && wpomp_local.wpomp_countries && wpomp_local.wpomp_countries != null) {    
                
                country = wpomp_local.wpomp_countries.join(",") ;    
                nomination_url = "https://nominatim.openstreetmap.org/search?countrycodes="+country;
                
            }else{
                nomination_url = "https://nominatim.openstreetmap.org/search";
            }

            obj1.each(function() {



                var current_input = this;

                var id = $(current_input).attr('id');

                var options = {

                        geojsonServiceAddress: nomination_url,

                        map_obj: map_obj,

                        serachboxID:"#"+id                };



                $(obj1).parent().GeoJsonAutocomplete(options);

            });

                

        },

        fit_bounds: function() {

            var map_obj = this;

            var places = map_obj.map_data.places;

            var bounds = new L.LatLngBounds();



            if (places !== undefined) {

                places.forEach(function(place) {



                    if (place.location.lat && place.location.lng) {

                        bounds.extend(new L.LatLng(

                            parseFloat(place.location.lat),

                            parseFloat(place.location.lng)

                        ));

                    }



                });

            }

            map_obj.map.fitBounds(bounds);



        },

        create_markers: function() {



            var map_obj = this;

            var places = map_obj.map_data.places;

            var temp_listing_placeholder;

            var replaceData;

            var remove_keys = [];

            $.each(places, function(key, place) {

                if (place.location.lat && place.location.lng) {

                    if (typeof place.categories == 'undefined') {

                        place.categories = {};

                    }



                    if (typeof place.location.icon == 'undefined') {

                        place.location.icon = map_obj.settings.marker_default_icon;

                    }



                     place.marker = new L.Marker(new L.LatLng(

                            parseFloat(place.location.lat),

                            parseFloat(place.location.lng)

                        ),{



                        icon: L.icon({ 

                            iconUrl: place.location.icon,

                             iconSize: [32, 32],               
                             iconAnchor: [16, 32],

                             popupAnchor: [0, -55],



                         }),

                        draggable: place.location.draggable

                    });



                    

                    if((place.location.infowindow_disable) || (place.source !='manual')){

                        place.marker.bindPopup(place.content);

                   }

                    

                    if (map_obj.settings.infowindow_filter_only === true) {



                        place.marker.visible = false;



                        place.marker.remove();



                    }else{

                         place.marker.addTo(map_obj.map);

                    }



                    // bind event to marker



                    if (map_obj.map_data.page == 'edit_location')



                        map_obj.marker_bind(place.marker);



                    var location_categories = [];



                    if (typeof place.categories != 'undefined') {



                        for (var cat in place.categories) {



                            location_categories.push(place.categories[cat].name);



                        }



                    }



                    var content = '';



                     // replace infowindow content.



                    var marker_image = '';



                   



                     if( place.source == 'post' ) { 



                        marker_image = place.location.extra_fields.post_featured_image; 



                    } else {



                        marker_image = place.location.marker_image;



                    }







                    var temp_listing_placeholder = ''; var post_info_class = 'fc-infowindow-';



                    if( place.source == 'post' ) { 



                        temp_listing_placeholder = map_obj.settings.infowindow_geotags_setting;



                        post_info_class = 'wpomp_infowindow_post fc-item-'+map_obj.settings.infowindow_post_skin.name;



                    } else {

                        temp_listing_placeholder = map_obj.settings.infowindow_setting;

                        if (map_obj.map_data.page != 'edit_location' && map_obj.settings.infowindow_skin)

                            post_info_class = 'fc-infowindow-'+map_obj.settings.infowindow_skin.name;

                    }



                    if( typeof temp_listing_placeholder == 'undefined' ) {

                        temp_listing_placeholder = place.content;

                    }







                        replaceData = {



                            "{marker_id}": place.id,



                            "{marker_title}": place.title,



                            "{marker_address}": place.address,



                            "{marker_latitude}": place.location.lat,



                            "{marker_longitude}": place.location.lng,



                            "{marker_city}": place.location.city,



                            "{marker_state}": place.location.state,



                            "{marker_country}": place.location.country,



                            "{marker_postal_code}": place.location.postal_code,



                            "{marker_zoom}": place.location.zoom,



                            "{marker_icon}": place.location.icon,



                            "{marker_category}": location_categories.join(', '),



                            "{marker_message}": place.content,



                            "{marker_image}": marker_image,



                        };







                        //Add extra fields of locations



                        if (typeof place.location.extra_fields != 'undefined') {



                            for (var extra in place.location.extra_fields) {



                                if (!place.location.extra_fields[extra]) {



                                    replaceData['{' + extra + '}'] = "<div class='wpomp_empty'></div>";



                                } else {



                                    replaceData['{' + extra + '}'] = place.location.extra_fields[extra];



                                }



                            }



                        }



                        temp_listing_placeholder = temp_listing_placeholder.replace(/{[^{}]+}/g, function(match) {



                          

                            if (match in replaceData) {



                                if(replaceData[match] !=undefined){

                                     return (replaceData[match]);

                                }else{

                                    return ("");



                                }

                            } else {



                                return ("");



                            }



                        });





                    content = temp_listing_placeholder;

                                        



                    if (content === "") {



                        if (map_obj.settings.map_infowindow_customisations === true && map_obj.settings.show_infowindow_header === true)



                            content = '<div class="wpomp_infowindow '+post_info_class+'"><div class="wpomp_iw_head"><div class="wpomp_iw_head_content">' + place.title + '</div></div><div class="wpomp_iw_content">' + place.content + '</div></div>';



                        else



                            content = '<div class="wpomp_infowindow '+post_info_class+'"><div class="wpomp_iw_content">' + place.content + '</div></div>';



                    } else {



                        if (map_obj.settings.map_infowindow_customisations === true && map_obj.settings.show_infowindow_header === true)



                            content = '<div class="wpomp_infowindow '+post_info_class+'"><div class="wpomp_iw_head"><div class="wpomp_iw_head_content">' + place.title + '</div></div><div class="wpomp_iw_content">' + content + '</div></div>';



                        else



                            content = '<div class="wpomp_infowindow '+post_info_class+'"><div class="wpomp_iw_content">' + content + '</div></div>';





                    }



                    place.infowindow_data = content;



                    place.infowindow = map_obj.infowindow_marker;

            

                    if (place.location.infowindow_default_open === true) {

                        map_obj.openInfoWindow(place);

                    }else if (map_obj.settings.default_infowindow_open === true) {

                        map_obj.openInfoWindow(place);

                    }
                    var on_event = map_obj.settings.infowindow_open_event;
                    if(map_obj.isMobile){
                        on_event = 'click';
                    }
                    map_obj.event_listener(place.marker, on_event, function() {

                        if(place.location.infowindow_disable || (place.source !='manual')){

                            map_obj.openInfoWindow(place);

                        }                        

                    });



                    map_obj.places.push(place);

                } else {

                    remove_keys.push(key);

                }

            });

             

            $.each(remove_keys, function(index, value) {

                delete(places[remove_keys]);



            });



        },

        display_markers: function() {



            var map_obj = this;

            map_obj.show_places = [];

            map_obj.categories = [];

            var categories = {};

            for (var i = 0; i < map_obj.places.length; i++) {



                if (map_obj.settings.infowindow_filter_only === true) {

                    map_obj.places[i].marker.remove();

                }else{

                                        map_obj.places[i].marker.addTo(map_obj.map);



                }

                                    map_obj.show_places.push(this.places[i]);



                if (typeof map_obj.places[i].categories != 'undefined') {

                    $.each(map_obj.places[i].categories, function(index, category) {



                        if (typeof categories[category.name] == 'undefined') {

                            categories[category.name] = category;

                        }

                    });

                }

            }



            this.categories = categories;

        },

        show_center_circle: function() {

            var map_obj = this;



            if (map_obj.settings.center_circle_radius =='') {

                map_obj.settings.center_circle_radius = 5;

            }



            var cen = map_obj.map.getCenter();



            L.circle(cen,

                {  fillColor: map_obj.settings.center_circle_fillcolor,

                fillOpacity: map_obj.settings.center_circle_fillopacity,

                color: map_obj.settings.center_circle_strokecolor,

                opacity: map_obj.settings.center_circle_strokeopacity,

                weight: map_obj.settings.center_circle_strokeweight,

                radius: parseInt(map_obj.settings.center_circle_radius) * 1000}).addTo(map_obj.map);

        },

        show_center_marker: function() {
            var map_obj = this;
            var clickable = false;
            if (map_obj.settings.center_marker_infowindow != '') {
                clickable = true;
            }
            
            var center_pos = map_obj.map.getCenter();
            map_obj.map_center_marker = new L.Marker(center_pos,{
                        icon: L.icon({ iconUrl: map_obj.settings.center_marker_icon,
                                iconSize: [32, 32],               
                                iconAnchor: [16, 32], 
                                popupAnchor: [0, -30],}),
                                draggable: true,
             }).addTo(map_obj.map);


            if (typeof map_obj.map_center_info == 'undefined') {

                map_obj.map_center_info = map_obj.infowindow_marker;

            }

            if (map_obj.settings.center_marker_infowindow != '') {


                map_obj.event_listener(map_obj.map_center_marker, 'click', function() {

                    var center_content = '';

                    if(map_obj.settings.center_marker_infowindow !=undefined){
                        center_content = map_obj.settings.center_marker_infowindow;
                    }else{
                         center_content = wpomp_local.default_center_msg;
                    }

                    map_obj.map_center_info.setContent('<div class="wpomp_infowindow"><div class="wpomp_iw_content">'+center_content+ '</div></div>');

                    map_obj.map_center_info.setLatLng(center_pos).openOn(map_obj.map);
                });
            }

        },

        center_by_nearest: function() {

            var map_obj = this;



            this.get_current_location(function(user_position) {



                if (!map_obj.user_location_marker) {



                    map_obj.user_location_marker = new L.Marker(user_position,{



                        icon: L.icon({ iconUrl: map_obj.settings.center_marker_icon,

                             iconSize: [32, 32],               
                             iconAnchor: [16, 32], 

                             popupAnchor: [0, -30],



                        }),

                        title: wpomp_local.center_location_message,

                    }).addTo(map_obj.map);

                }



                if (typeof map_obj.map_center_info == 'undefined') {

                    map_obj.map_center_info = map_obj.infowindow_marker;

                }

                if (map_obj.settings.center_marker_infowindow != '') {

                      map_obj.event_listener(map_obj.user_location_marker, 'click', function() {

                        var center_content = '';

                        if(map_obj.settings.center_marker_infowindow !=undefined){

                            center_content = map_obj.settings.center_marker_infowindow;

                        }

                        map_obj.map_center_info.setContent('<div class="wpomp_infowindow"><div class="wpomp_iw_content">' + center_content + '</div></div>');

                         map_obj.map_center_info.setLatLng(user_position).openOn(map_obj.map);

                      

                    });

                }

                map_obj.map.setView(user_position,map_obj.settings.zoom);  



                if (map_obj.settings.show_center_circle === true) {

                    map_obj.show_center_circle();

                }



                if (map_obj.map_data.listing && map_obj.map_data.listing.apply_default_radius == true) {



                    map_obj.search_area = user_position;

                }



            },

            function(user_position){



                if (!map_obj.user_location_marker) {



                     map_obj.user_location_marker = new L.Marker(user_position,{



                        icon: L.icon({ iconUrl: map_obj.settings.center_marker_icon,

                        iconSize: [32, 32],               
                        iconAnchor: [16, 32], 

                        popupAnchor: [0, -30], }),

                        title: wpomp_local.center_location_message,

                    }).addTo(map_obj.map);



                }

                if (typeof map_obj.map_center_info == 'undefined') {

                    map_obj.map_center_info = map_obj.infowindow_marker;

                }

                if (map_obj.settings.center_marker_infowindow != '') {

                      map_obj.event_listener(map_obj.user_location_marker, 'click', function() {



                        var center_content = '';

                        if(map_obj.settings.center_marker_infowindow !=undefined){

                            center_content = map_obj.settings.center_marker_infowindow;

                        }

                        map_obj.map_center_info.setContent('<div class="wpomp_infowindow"><div class="wpomp_iw_content">' + center_content + '</div></div>');

                         map_obj.map_center_info.setLatLng(user_position).openOn(map_obj.map);

                      

                    });

                }

                map_obj.map.panTo(user_position);                

                if (map_obj.settings.show_center_circle === true) {

                    map_obj.show_center_circle();

                }



                if (map_obj.map_data.listing && map_obj.map_data.listing.apply_default_radius == true) {



                    map_obj.search_area = user_position;

                }





            });

        },



        get_current_location: function(success_func, error_func) {



            var map = this;



            if (typeof map.user_location == 'undefined') {



                navigator.geolocation.getCurrentPosition(function(position) {



                    map.user_location = new L.LatLng(position.coords.latitude, position.coords.longitude);



                    if (success_func)

                        success_func(map.user_location);



                }, function(ErrorPosition) {

                    map.user_location = map.map.getCenter();



                    if (error_func)

                        error_func(map.user_location);



                }, {

                    enableHighAccuracy: true,

                    timeout: 50000,

                    maximumAge: 0

                });

            } else {

                if (success_func)

                    success_func(map.user_location);

            }

        },



        openInfoWindow: function(place) {



            var map_obj = this;

            var skin = 'default';

            if (place.source == 'post') {

                skin = map_obj.settings.infowindow_post_skin.name;

            } else if (map_obj.map_data.page != 'edit_location' && map_obj.settings.infowindow_skin) {

                skin = map_obj.settings.infowindow_skin.name;

            }



            place.infowindow = place.marker.getPopup();

            if(place.infowindow == undefined)

            return;



            place.infowindow.setContent(place.infowindow_data);



            if (place.location.onclick_action == "post") {

                if (place.location.open_new_tab == 'yes')

                    window.open(place.location.redirect_permalink, '_blank');

                else

                    window.open(place.location.redirect_permalink, '_self');

            }

            else if (place.location.onclick_action == "custom_link") {

                if (place.location.open_new_tab == 'yes')

                    window.open(place.location.redirect_custom_link, '_blank');

                else

                    window.open(place.location.redirect_custom_link, '_self');

            } else {



                place.infowindow.setLatLng(place.marker.getLatLng()).openOn(map_obj.map);

                if (typeof map_obj.settings.infowindow_click_change_center != 'undefined' && map_obj.settings.infowindow_click_change_center == true) {

                    map_obj.map.panTo(place.marker.getLatLng());

                }

                if (typeof map_obj.settings.infowindow_click_change_zoom != 'undefined' && map_obj.settings.infowindow_click_change_zoom > 0) {

                    map_obj.map.setZoom(map_obj.settings.infowindow_click_change_zoom);

                    map_obj.map.panTo(place.marker.getLatLng());

                }

                

            }

            $(map_obj.container).find(".wpomp_empty").prev().remove();

            $(map_obj.container).find(".wpomp_empty").remove();



        },

    };



    $.fn.osm_maps = function(options, places) {



        this.each(function(r) {



            if (!$.data(this, "wpomp_maps")) {

                $.data(this, "wpomp_maps", new OSMMaps(this, options, places));

            }



        });

        // chain jQuery functions

        return this;

    };



}(jQuery, window, document));

