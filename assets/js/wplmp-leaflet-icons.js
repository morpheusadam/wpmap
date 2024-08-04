(function($) {
    "use strict";
jQuery(document).ready(function($) {

				$(document).on('click', '.read_icons', function() {

               		$(".read_icons").removeClass('active');

					$(this).addClass('active');

				});

				$(document).on('keyup', 'input[name="wpomp_search_icon"]', function() {

					if($(this).val() == ''){

						$('.read_icons').show();

				    }else {

						$('.read_icons').hide();

						$('img[title^="' + $(this).val() + '"]').parent().show();

					}

				});


				$(document).on('click', '.wplmp-insert-icons', function() {

					var target = $(this).data('target');

					if(jQuery('.read_icons').hasClass('active')){

						var imgsrc = jQuery('.active').find('img').attr('src');

						var win = window.dialogArguments || opener || parent || top;

						win.send_icon_to_map(imgsrc,target);
					} else {

						alert('Choose marker icon');

					}

				});

			});
      
})(jQuery);
