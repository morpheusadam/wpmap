(function($) {
    "use strict";

    $(document).ready(function() {

        if($("#wpomp_meta_map1").length>0){
            var map = $("#wpomp_meta_map1").osm_maps(map_data).data('wpomp_maps');
        }

        $('body').on('change',"select[name='wpomp_metabox_location_redirect']",function() {
                var rval = $(this).val();
                if(rval=="custom_link")
                {
                $("#wpomp_toggle_custom_link").show("slow");
                }
                else
                {
                    $("#wpomp_toggle_custom_link").hide("slow");
                }
            });


    });

      
})(jQuery);
