(function($) {
    "use strict";

    $(document).ready(function() {

        var wpomp_timeouts = [];

        if(typeof customizer_fonts !== 'undefined'){

            var result = Object.keys(customizer_fonts).map(function(key) {
              return customizer_fonts[key];
            });

            if (result && result.length > 0) {
                for (var i in result ) {
                    var font = result[i];

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

                }
            }

        }

        $('.wpomp_datepicker').datepicker({
            dateFormat: 'dd-mm-yy'
        });

        var wpomp_image_id = '';
        //intialize add more...
        $('body').on('click', '.wpomp_check_key', function(e) {
            $('.wpomp_maps_preview').html("...");
            var wpomp_maps_key = $("input[name='wpomp_api_key']").val();
            if(wpomp_maps_key){

                $.get("https://api.mapbox.com/tokens/v2?access_token=" + wpomp_maps_key, function(data) {
                    if (data.code == 'TokenValid') {
                        $('.wpomp_maps_preview').html("Perfect!");
                    } else {
                        $('.wpomp_maps_preview').html("Key is not valid!");
                    }
                });
            }else{
                $('.wpomp_maps_preview').html("Please Enter Key!");
            }
        });


        $('body').on('click', '.wpomp_mapquest_btn', function(e) {
            $('.wpomp_mapsquest_preview').html("...");
            var wpomp_maps_key = $("input[name='wpomp_mapquest_key']").val();
            if(wpomp_maps_key){
               
                $.get("http://www.mapquestapi.com/geocoding/v1/address?key="+wpomp_maps_key+"&street=1600+Pennsylvania+Ave+NW&city=Washington&state=DC&postalCode=20500", function(data) {
                    if (data.info.statuscode == '0') {
                        $('.wpomp_mapsquest_preview').html("Perfect!");
                    } else {
                        $('.wpomp_mapsquest_preview').html("Key is not valid!");
                    }
                }).fail(function() {
                        $('.wpomp_mapsquest_preview').html("Key is not valid!");
                });
            }else{
                $('.wpomp_mapsquest_preview').html("Please Enter Key!");
            }
        });

        $('body').on('click', '.wpomp_bingmap_btn', function(e) {
            $('.wpomp_bingmap_preview').html("...");
            var wpomp_maps_key = $("input[name='wpomp_bingmap_key']").val();
            if(wpomp_maps_key){
                   var bing = new L.BingLayer(wpomp_maps_key);
                   var request = bing._makeApiUrl('Imagery/Metadata', 'Aerial', {
                    UriScheme: 'https',
                    include: 'ImageryProviders',
                    culture: '',
                    style: ''
                });

                bing.callRestService(request, function (meta) {
                    if (typeof(meta.statusCode) == 'undefined') {
                        $('.wpomp_bingmap_preview').html("Key is not valid!");
                    }
                    if (meta.statusCode == 200) {
                        $('.wpomp_bingmap_preview').html("Perfect!");
                    }else {
                        $('.wpomp_bingmap_preview').html("Key is not valid!");
                    }
                });
            }else{
                $('.wpomp_bingmap_preview').html("Please Enter Key!");
            }
        });

        $('body').on('change','.wpomp_mapdata_providers',function(){
            var map_data_provider = $(this).val();
            var target = $(this).data('target');
            if(map_data_provider=='openstreet'){
                $(target).closest('.fc-form-group ').show();
            }else{
                $(target).closest('.fc-form-group ').hide();
            }
        });
        $('.wpomp_mapdata_providers').trigger("change");

        $('body').on('click', '.cancel_import', function(e) {
            var wpomp_bid = confirm("Do you want to cancel import process?.");
            if (wpomp_bid == true) {
                $(this).closest("form").find("input[name='operation']").val("cancel_import");
                $(this).closest("form").submit();
                return true;
            } else {
                return false;
            }
        });

        $('body').on('change', "select[name='map_id']", function(e) {
            $(this).closest('form').submit();
        });

        $('body').on('change', "select[name='filter_location']", function(e) {

            event.preventDefault();

            var what_value = $(this).val();

            if (what_value > 0) {
                $("tr[class^='filter_group_cat']").hide();
                $(".filter_group_cat" + $(this).val()).show("slow");
            } else {
                $("tr[class^='filter_group_cat']").show("slow");
            }

        });

        $('body').on('keyup', ".wpomp_search_input", function(e) {


            map_id = $(this).attr("rel");
            $(".wpomp_locations_listing[rel='" + map_id + "']").addClass("wpomp_loading");
            wpomp_filter_locations(map_id, 1);
        });

        $('body').on('click', ".wpomp_toggle_container", function(e) {

            $(".wpomp_toggle_main_container").toggle("slow");
            if ($(this).text() == "Hide") {
                $(this).text("Show");
            } else {
                $(this).text("Hide");
            }
        });

        $('body').on('click', ".wpomp_mcurrent_loction", function(e) {
            wpomp_get_current_location();
        });


        $('body').on('click', ".wpomp-select-all", function(e) {

            var checkAll = $(".wpomp-select-all").prop('checked');
            if (checkAll) {
                $(this).closest('table').find(".wpomp-location-checkbox").prop("checked", true);
            } else {
                $(this).closest('table').find(".wpomp-location-checkbox").prop("checked", false);
            }
        });

        $('body').on('click', ".wpomp-location-checkbox", function(e) {

            if ($(".wpomp-location-checkbox").length == $(".wpomp-location-checkbox:checked").length) {
                $(".wpomp-select-all").prop("checked", true);
            } else {
                $(".wpomp-select-all").prop("checked", false);
            }
        });

        var maptable = $('#wpomp_google_map_data_table').dataTable({
            "lengthMenu": [
                [10, 25, 50, 100, 200, 500, -1],
                [10, 25, 50, 100, 200, 500, "All"]
            ],
            "order": [
                [1, "desc"]
            ],
            "aoColumns": [{
                sWidth: '5%',
                "bSortable": false
            }, {
                sWidth: '40%'
            }, {
                sWidth: '30%'
            }, {
                sWidth: '20%'
            }],
            "language": { "search":"", "searchPlaceholder": "Search..." }
        });

        var route_maptable = $('#wpomp_google_map_route_data_table').dataTable({
            "lengthMenu": [
                [10, 25, 50, 100, 200, 500, -1],
                [10, 25, 50, 100, 200, 500, "All"]
            ],
            "aoColumns": [{
                sWidth: '10%'
            }, {
                sWidth: '35%'
            }, {
                sWidth: '35%'
            }, {
                sWidth: '20%'
            }]
        });


        $('body').on('click', 'input[name="save_entity_data"]', function(e) {
            var data = maptable.$('input[type="checkbox"]:checked');
            var selected_val = [];
            if (data.length > 0) {
                $.each(data, function(index, chk) {
                    selected_val.push($(chk).val());
                });
                $('input[name="map_locations"]').val(selected_val);
            }

            return true;
        });

        $('body').on('change', 'select[name="select_all"]', function(e) {
            if ($(this).val() == 'select_all')
                $('input[name="map_locations[]"]').attr('checked', true);
            else
                $('input[name="map_locations[]"]').attr('checked', false);

        });

        $('body').on('change', '.switch_onoff', function(e) {
            var target = $(this).data('target');
            if ($(this).attr('type') == 'radio') {
                $(target).closest('.form-group').hide();
                target += '_' + $(this).val();
            }
            if ($(this).is(":checked")) {
                $(target).closest('.form-group').show();
            } else {
                $(target).closest('.form-group').hide();
                if ($(target).hasClass('switch_onoff')) {
                    $(target).attr('checked', false);
                    $(target).trigger("change");
                }
            }


        });

        $.each($('.switch_onoff'), function(index, element) {
            if (true == $(this).is(":checked")) {
                $(this).trigger("change");
            }

        });

        $('.wpomp-overview .color').wpColorPicker();

    });

    var re = /([^&=]+)=?([^&]*)/g;
    var decodeRE = /\+/g; // Regex for replacing addition symbol with a space
    var decode = function(str) {
        return decodeURIComponent(str.replace(decodeRE, " "));
    };
    $.parseParams = function(query) {
        var params = {},
            e;
        while (e = re.exec(query)) {
            var k = decode(e[1]),
                v = decode(e[2]);
            if (k.substring(k.length - 2) === '[]') {
                k = k.substring(0, k.length - 2);
                (params[k] || (params[k] = [])).push(v);
            } else params[k] = v;
        }
        return params;
    };

})(jQuery);

function send_icon_to_map(imagesrc, target) {
        jQuery('#remove_image_' + target).show();
        jQuery('#image_' + target).attr('src', imagesrc).show();
        jQuery('#input_' + target).val(imagesrc);
        tb_remove();
}