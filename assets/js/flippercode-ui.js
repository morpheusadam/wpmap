(function($) {
    "use strict";
    $.fn.fcModal = function(options) {

        var fcmodal = $.extend({
            onOpen: function() {},
            register_fc_modal_handing_events: function() {

                $('.fc-modal').each(function(i, obj) {

                    var initiator = $(this).data('initiator');
                    if (typeof initiator != typeof undefined) {
                        if ($(initiator.length)) {

                            $(initiator).data('target', $(this).attr('id'));
                            var releatedModal = $(this).attr('id');
                            $('body').on('click', initiator, function() {

                                if ($('#' + releatedModal).length > 0) {
                                    fcmodal.onOpen();
                                    $('#' + releatedModal).css('display', 'block');
                                }

                            });


                        }
                    }


                });

                $('body').on('click', '.fc-modal-close', function() {
                    var releatedModal = $(this).closest('div.fc-modal');
                    $(releatedModal).css('display', 'none');
                });

                window.onclick = function(event) {

                    if ($(event.toElement).hasClass('fc-modal'))
                        $('.fc-modal').hide();

                }
            }
        }, options);

        return this.each(function() {
            fcmodal.register_fc_modal_handing_events();
        });
    };

	jQuery(document).ready(function($) {

		// New JS Code
		 
		var allPanels = $('.custom-accordion > dd').hide();

        $('.custom-accordion > dd:first-of-type').show();
        $('.custom-accordion > dt:first-of-type').addClass('accordion-active');
        $('.fc-help-right .custom-accordion > dd:first-of-type').hide();
        $('.fc-help-right .custom-accordion > dt:first-of-type').removeClass('accordion-active');

        $('.custom-accordion > dt').on('click', function() {
            var $this = $(this);
            var $target = $this.next(); 
            if(!$this.hasClass('accordion-active')){
                $this.parent().children('dd').slideUp();
                jQuery('.custom-accordion > dt').removeClass('accordion-active');
                $this.addClass('accordion-active');
                $target.addClass('active').slideDown();
            }else{
                
                 $this.next('dd').slideUp();
                 $this.addClass('accordion-active');
                 jQuery('.custom-accordion > dt').removeClass('accordion-active');
            }
            return false;
        });

        $('.row-actions .edit a').append('<i class="fa fa-pencil"></i>');

        $('.row-actions .delete a').append('<i class="fa fa-trash"></i>');

        $('.row-actions .copy a').append('<i class="fa fa-copy "></i>');

        $('.wp-list-table .check-column input[type="checkbox"]').wrap('<span class="checkbox"></span>'); 

        $('.wp-list-table .check-column input[type="checkbox"]').after('<label></label>');

        var table = $('.wp-list-table #the-list');    

        table.on('click', 'tr', function (e) {

            if ( $(this).hasClass('active') ) {

                $(this).removeClass('active');

            }else{
                $(this).addClass('active');
            }
        });
        
         $('#delete_fc_record').on('hidden.bs.modal', function (e) {

		  $('.wp-list-table tr').removeClass('active');

		});
		
		// New UI
			
        $(document).on("change",".fc-file_input",function() {
	        //submit the form here
	        var file_name = e.target.files[0].name;
	        $(this).parent().find(".fc-file-details").text(file_name);
	    });

	    $('.fc-modal').fcModal({});

            $('.fc-forground-control .fc_tool_fgc').wpColorPicker({
                change: function(event, ui) {                    
                    $('.active_element').css('color', $(this).val());
                }
            });

            $('.fc-bg-control .fc_tool_bgc').wpColorPicker({
                change: function(event, ui) {
                    $('.active_element').css('background-color', $(this).val());
                }
            });

	});

    jQuery(document).ready(function($) {

        var currentDeletedTemplate = '';
        $('.yes-remove-current-template').on("click", function() {

            var product = $(this).data('product');
            var templatetype = $(this).data('templatetype');
            var templateName = $(this).data('templatename');
            var data = {
                action: 'core_backend_ajax_calls',
                product: product,
                templateName: templateName,
                templatetype: templatetype,
                selector: '.default-custom-template',
                operation: 'delete_custom_template'
            }

            currentDeletedTemplate = templateName;
            perform_ajax_events(data);
            $('#remove-current-template').modal('hide');

        });
        $('.default-custom-template').on("click", function() {

            $('#remove-current-template').modal('show');
            $('.yes-remove-current-template').data('product', $(this).data('product'));
            $('.yes-remove-current-template').data('templatetype', $(this).data('templatetype'));
            $('.yes-remove-current-template').data('templatename', $(this).data('templatename'));
            return false;

        });




        // Sortable JS
        $("body").find(".sortable_child").closest('.fc-form-group').addClass('sortable-item');
        $("body").find('.sortable-item').wrapAll("<div class='fc-12 sortable' />");
        $("body").find('.sortable').sortable({
            placeholder: "ui-sortable-placeholder"
        });

        var wpomp_image_id = '';
        var remove_ids = "";
        $("body").on('click', ".repeat_button", function() {

            //find out which container we need to copy.
            var target = $(this).parent().parent();
            var new_element = $(target).clone();
            //incrase index here
            var inputs = $(new_element).find("input[type='text']");
            for (var i = 0; i < inputs.length; i++) {
                var element_name = $(inputs[i]).attr("name");
                var patt = new RegExp(/\[([0-9]+)\]/i);
                var res = patt.exec(element_name);
                var new_index = parseInt(res[1]) + 1;
                var name = element_name.replace(/\[([0-9]+)\]/i, "[" + new_index + "]");
                $(inputs[i]).attr("name", name);
            }

            var inputs = $(new_element).find("input[type='number']");
            for (var i = 0; i < inputs.length; i++) {
                var element_name = $(inputs[i]).attr("name");
                var patt = new RegExp(/\[([0-9]+)\]/i);
                var res = patt.exec(element_name);
                var new_index = parseInt(res[1]) + 1;
                var name = element_name.replace(/\[([0-9]+)\]/i, "[" + new_index + "]");
                $(inputs[i]).attr("name", name);
            }

            $(new_element).find("input[type='text']").val("");
            $(new_element).find("input[type='number']").val("");

            $(target).find(".repeat_button").val("Remove");
            $(target).find(".repeat_button").removeClass("repeat_button").addClass("repeat_remove_button");
            $(target).after($(new_element));

            $("body").find('.sortable').sortable("refresh");


        });
        //Delete add more...
        $("body").on('click', ".repeat_remove_button", function() {

            //find out which container we need to copy.
            var target = $(this).parent().parent();
            var temp = $(target).clone();
            $(target).remove();
            //reindexing
            var inputs = $(temp).find("input[type='text']");
            $.each(inputs, function(index, element) {
                var current_name = $(this).attr("name");
                var name = current_name.replace(/\[([0-9]+)\]/i, "");
                $.each($("*[name^='" + name + "']"), function(index, element) {
                    current_name = $(this).attr('name');
                    var name = current_name.replace(/\[([0-9]+)\]/i, "[" + index + "]");
                    $(this).attr("name", name);
                });
            });
            var entiy_id = $(this).data("id");
            if (entiy_id) {
                remove_ids = remove_ids + $(this).data("id") + "|";
                $('#fc_remove_entities').val(remove_ids);
            }

            $("body").find('.sortable').sortable("refresh");

        });


        $("body").on('click', "span.delete a", function() {

            if (confirm(wpp_js_lang.confirm))
                return true;

            return false;

        });

        window.send_to_editor_default = window.send_to_editor;

        $("body").on('click', ".fa-picture-o", function() {
            window.send_to_editor = function(html) {

                $('body').append('<div id="temp_image">' + html + '</div>');
                var img = $('#temp_image').find('img');
                var imgurl = img.attr('src');
                $('.active_element').css('background-image', 'url(' + imgurl + ')');
                try {
                    tb_remove();
                } catch (e) {}
                $('#temp_image').remove();
                window.send_to_editor = window.send_to_editor_default;
            };
            tb_show('', 'media-upload.php?post_ID=0&type=image&TB_iframe=true');
            return false;

        });


        var wpp_image_id = '';
        var currentClickedID = '';
        $("body").on('click', ".choose_image", function() {
            currentClickedID = $(this).data('ref');
            window.send_to_editor = window.attach_image;
            tb_show('', 'media-upload.php?post_ID=0&target=' + currentClickedID + '&type=image&TB_iframe=true');
            return false;
        });

        window.attach_image = function(html) {
            $('body').append('<div id="temp_image' + currentClickedID + '">' + html + '</div>');
            var img = $('#temp_image' + currentClickedID).find('img');
            var imgurl = img.attr('src');
            var imgclass = img.attr('class');
            var imgid = parseInt(imgclass.replace(/\D/g, ''), 10);
            $('#remove_image' + currentClickedID).show();
            $('#image_' + currentClickedID).attr('src', imgurl).show();
            $('#input_' + currentClickedID).val(imgurl);
            try {
                tb_remove();
            } catch (e) {};
            $('#temp_image' + currentClickedID).remove();
            window.send_to_editor = window.send_to_editor_default;
        }
        $("body").on('click', ".remove_image", function() {

            var wpp_image_id = $(this).parent().parent();
            $(wpp_image_id).find('.selected_image').attr('src', '');
            $(wpp_image_id).find('input[name="' + $(this).data('target') + '"]').val('');
            $(this).hide();
            return false;
        });

        $("body").on('change', ".switch_onoff", function() {

            var target = $(this).data('target');
            if ($(this).attr('type') == 'radio') {
                $(target).closest('.fc-form-group').hide();
                target += '_' + $(this).val();
            }
            if ($(this).is(":checked")) {
                $(target).closest('.fc-form-group').show();
            } else {
                $(target).closest('.fc-form-group').hide();
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

        $("input[name='wpp_refresh']").trigger('change');

        function ajax_success_handler(data, selector) {

            switch (selector) {

                case '.set-default-template':
                    $('.fc_tools').css('display', 'none');
                    $('.fc_name').css('display', 'none');
                    $('.current_selected').parent().parent().find('.fc_name').css('display', 'block');
                    $('.current_selected').closest('.fc_tools').css('display', 'block');
                    $('.current-temp-in-use').removeClass('current-temp-in-use');
                    $('.current_selected').addClass('current-temp-in-use');
                    break;
                case '.default-custom-template':
                    $(".default-custom-template[data-templatename=" + currentDeletedTemplate + "]").parent().parent().parent().remove();
                    break;
                default:

            }

        }

        function perform_ajax_events(data) {

            $inputs = data;
            jQuery.ajax({
                type: "POST",
                url: settings_obj.ajax_url,
                dataType: "json",
                data: data,
                beforeSend: function() {

                    jQuery(".se-pre-con").fadeIn("slow");
                },
                success: function(data) {
                    jQuery(".se-pre-con").fadeOut("slow");
                    ajax_success_handler(data, $inputs.selector);

                }

            });

        }

        // Customizer JS

        $('body').on('click', ".fc-accordion-tab", function() {
            if ($(this).hasClass('active')) {
                $(this).removeClass('active');
                var acc_child = $(this).next().removeClass('active');
            } else {
                $(".fc-accordion-tab").removeClass('active');
                $(".fc-acc-child").removeClass('active');
                $(this).addClass('active');
                var acc_child = $(this).next().addClass('active');
            }

        });

        if (jQuery(".fc_templates").length > 0) {


            $("body").on('click', ".fc_load_original", function() {
                if (confirm('Are you Sure?')) {
                    var templates_div = $(this).closest('.fc_customizer').parent();
                    $(templates_div).find('.current-temp-in-use').trigger('click');
                }
            });

            $("body").on('click', ".fc_apply_changes", function() {

                var parent_div = $(this).closest('.fc_customizer');
                var templates_div = $(this).closest('.fc_customizer').parent();
                $(parent_div).find('.fc_preview').append('<div class="fc_loading"></div>');
                var template_source_code = $(parent_div).find('.fc_view_source').val();
                var template = $(templates_div).find('.current-temp-in-use').data("templatename");
                var template_type = $(templates_div).find('.current-temp-in-use').data("templatetype");

                jQuery.ajax({
                    type: "POST",
                    url: settings_obj.ajax_url,
                    dataType: 'json',
                    data: {
                        action: 'core_templates',
                        operation: 'fc_load_template',
                        nonce: settings_obj.nonce,
                        template_name: template,
                        template_type: template_type,
                        template_source: template_source_code,
                        columns: $(parent_div).find('.fc-grid-active').data('col')

                    },
                    beforeSend: function() {},
                    success: function(data) {
                        $(parent_div).parent().find('.fc-bottom-bar').show();
                        var editable_elements = settings_obj.text_editable;
                        var new_html = new DOMParser().parseFromString(data.html, "text/html").body.innerHTML;
                        //First apply the design changes to the new html.
                        var prev_html_with_designs = $(parent_div).find('.fc_preview').clone();
                        $(parent_div).find('.fc_preview').html(new_html);

                        $(prev_html_with_designs).find(".editable").each(function(index) {
                            var custom_style = {};
                            var temp_obj = $(this);
                            var type = $(this).data('temp-type');
                            custom_style[type] = {};
                            $(temp_obj).removeClass('editable');
                            $(temp_obj).removeClass('active_element');
                            var class_name = $(temp_obj).attr('class').split(' ');

                            if (class_name && class_name.length) {

                                $.each(class_name, function(k, c_name) {
                                    if (c_name.indexOf('{') >= 0) {
                                        delete class_name[k];
                                    }

                                    class_name[k] = '.' + c_name;
                                });

                                class_name = class_name.join('');

                                var styles = $(temp_obj).attr('style');
                                $(parent_div).find('.fc_preview').find(class_name).attr('style', styles);

                            }

                        });


                        if (editable_elements.length > 0) {
                            $.each(editable_elements, function(index, ele) {
                                $(parent_div).find(ele).addClass('editable').data('editor', 'text');
                                $(parent_div).find(ele).data('temp-type', template_type + '-' + template);

                            });
                        }
                        editable_elements = settings_obj.bg_editable;
                        if (editable_elements.length > 0) {
                            $.each(editable_elements, function(index, ele) {
                                $(parent_div).find(ele).addClass('editable').data('editor', 'bg');
                                $(parent_div).find(ele).data('temp-type', template_type + '-' + template);

                            });
                        }
                        editable_elements = settings_obj.margin_editable;
                        if (editable_elements.length > 0) {
                            $.each(editable_elements, function(index, ele) {
                                $(parent_div).find(ele).addClass('editable').data('editor', 'margin');
                                $(parent_div).find(ele).data('temp-type', template_type + '-' + template);

                            });
                        }
                        editable_elements = settings_obj.full_editable;
                        if (editable_elements.length > 0) {
                            $.each(editable_elements, function(index, ele) {
                                $(parent_div).find(ele).addClass('editable').data('editor', 'full');
                                $(parent_div).find(ele).data('temp-type', template_type + '-' + template);

                            });
                        }

                        var new_sourcecode = new DOMParser().parseFromString(data.sourcecode, "text/html").body.innerHTML;

                        $(parent_div).find('.fc_view_source').val(new_sourcecode);
                        //Apply Styles here back

                    }
                });

            });

            $("body").on('click', ".set-default-template", function(e) {

                var parent_div = $(this).closest('.fc_templates');

                $(".fc_customizer .fc_apply_style").removeClass('show_customizer');
                $('.fc_tool_fgc').iris('hide');
                $('.fc_tool_bgc').iris('hide');
                $('.active_element').removeClass('active_element');

                $(parent_div).find('.current_selected').removeClass('current_selected');
                $(this).addClass('current_selected');
                e.preventDefault();

                var template = $(this).data("templatename");
                var template_type = $(this).data("templatetype");
                var input = $(this).data("input");
                $('input[name="' + input + '[name]"]').val(template);
                $('input[name="' + input + '[type]"]').val(template_type);
                $(parent_div).find('.fc_tools').css('display', 'none');
                $(parent_div).find('.fc_name').css('display', 'none');
                $(parent_div).find('.current_selected').parent().parent().find('.fc_name').css('display', 'block');
                $(parent_div).find('.current_selected').closest('.fc_tools').css('display', 'block');
                $(parent_div).find('.current-temp-in-use').removeClass('current-temp-in-use');
                $(parent_div).find('.current_selected').addClass('current-temp-in-use');
                // load preview here.
                $(parent_div).find('.fc_preview').html('<div class="fc_loading"></div>');
                var ajax_data = {
                    action: 'core_templates',
                    operation: 'fc_load_template',
                    nonce: settings_obj.nonce,
                    template_name: template,
                    template_type: template_type,
                    columns: $(parent_div).parent().find('.fc_customizer').find('.fc-grid-active').data('col')
                };

                if ($(this).hasClass('current-saved')) {
                    ajax_data.template_source = $('input[name="' + input + '[sourcecode]"]').val();
                }
                
                jQuery.ajax({
                    type: "POST",
                    url: settings_obj.ajax_url,
                    dataType: 'json',
                    data: ajax_data,
                    beforeSend: function() {
                        $(parent_div).append('<div class="fc_loading"></div>');
                        $('.fc-btn-submit').attr("disabled","disabled");
                    },
                    complete: function() {
                       
                       $('.fc_loading').remove();
                       $('.fc-btn-submit').removeAttr("disabled");
                    },

                    success: function(data) {
                        $(parent_div).parent().find('.fc_customizer').show();
                        $(parent_div).parent().find('.fc_source_code_container').show();
                        $(parent_div).parent().find('.fc_preview').html(data.html);
                        var editable_elements = settings_obj.text_editable;
                        if (editable_elements.length > 0) {
                            $.each(editable_elements, function(index, ele) {
                                $(parent_div).parent().find(ele).addClass('editable').data('editor', 'text');
                                $(parent_div).parent().find(ele).data('temp-type', template_type + '-' + template);

                            });
                        }
                        editable_elements = settings_obj.bg_editable;
                        if (editable_elements.length > 0) {
                            $.each(editable_elements, function(index, ele) {
                                $(parent_div).parent().find(ele).addClass('editable').data('editor', 'bg');
                                $(parent_div).parent().find(ele).data('temp-type', template_type + '-' + template);

                            });
                        }
                        editable_elements = settings_obj.margin_editable;
                        if (editable_elements.length > 0) {
                            $.each(editable_elements, function(index, ele) {
                                $(parent_div).parent().find(ele).addClass('editable').data('editor', 'margin');
                                $(parent_div).parent().find(ele).data('temp-type', template_type + '-' + template);

                            });
                        }
                        editable_elements = settings_obj.full_editable;
                        if (editable_elements.length > 0) {
                            $.each(editable_elements, function(index, ele) {
                                $(parent_div).parent().find(ele).addClass('editable').data('editor', 'full');
                                $(parent_div).parent().find(ele).data('temp-type', template_type + '-' + template);

                            });
                        }
                        $(parent_div).parent().find('.fc_view_source').val(data.sourcecode);
                        //$(parent_div).parent().find(".custom_sourcecode").val(data.sourcecode);
                    }
                });

            });

            if ($('.current-temp-in-use').length > 0) {
                $('.fc_apply_changes').trigger('click');
            }

            if ($('.current-temp-in-use').length) {
                $('.current-temp-in-use').parent().parent().find('.fc_name').css('display', 'block');
                $('.current-temp-in-use').closest('.fc_tools').css('display', 'block');
            }

            if ($('.current-saved').length) {
                $(" .current-saved.set-default-template").trigger('click');
            }

            $(".fc_templates").slick({
                slidesToShow: 1,
                infinite: true,
                speed: 300,
                centerMode: true,
                variableWidth: true
            });

            $('.fc_preview').on('click', 'a', function(event) {
                event.preventDefault();
            });

            $('.fc_preview').on('hover', '.editable', function(e) {

                if (e.target !== e.currentTarget) return;

                var class_name = $(this).attr('class');
                if (class_name != 'editable') {
                    $(this).addClass('fc-show-editable');
                } else {
                    $(this).addClass('fc-show-editable');
                }


            });

            $('.fc_preview').on('mouseout', '.editable', function(e) {
                if (e.target !== e.currentTarget) return;

                var class_name = $(this).attr('class');
                $(this).removeClass('fc-show-editable');
            });


            $('.fc_preview').on('click', '.editable', function(e) {
                var preview_parent = $(this).closest('.fc_preview').parent().parent().parent();

                $(preview_parent).find('.fc_tool_fgc').iris('hide');
                $(preview_parent).find('.fc_tool_bgc').iris('hide');

                if (e.target !== e.currentTarget) return;

                var editor_type = $(this).data('editor');

                $('.fc-bg-control').show();
                $('.fc-forground-control').show();
                $('.fc-margin-control').show();

                $(preview_parent).find('.active_element').removeClass('active_element');
                $(this).addClass('fc_inline');
                var preview_pos = $(preview_parent).position();
                var preview_outer = $(preview_parent).outerWidth();

                var pos = $(this).position();
                var width = $(this).outerWidth();
                var preview_outer_width = preview_pos.left + preview_outer;
                $(this).removeClass('fc_inline');

                // Get properties of selected element. 
                var font_weight = $(this).css('font-weight');
                var font_size = $(this).css('font-size').replace(/[^-\d\.]/g, '');
                var font_color = $(this).css('color').replace(')', ', 0)').replace('rgb', 'rgba');
                var line_height = $(this).css('line-height').replace(/[^-\d\.]/g, '');
                var background_color = $(this).css('background-color');
                var background = $(this).css('background');
                var font_style = $(this).css('font-style');
                var text_decoration = $(this).css('text-decoration-line');
                var font_family = $(this).css('font-family');
                if (font_family.indexOf(',') >= 0) {
                    font_family = font_family.split(",");
                    font_family = font_family[0];
                }
                if (font_family.indexOf('"') >= 0) {
                    font_family = font_family.replace('"', '');
                    font_family = font_family.replace('"', '');
                }
                WebFont.load({
                    google: {
                        families: [font_family]
                    }
                });
                $('.fc-forground-control .fc-font-family select').val(font_family);
                var text_align = $(this).css('text-align');
                var margin_top = $(this).css('margin-top').replace(/[^-\d\.]/g, '');
                var margin_bottom = $(this).css('margin-bottom').replace(/[^-\d\.]/g, '');
                var margin_left = $(this).css('margin-left').replace(/[^-\d\.]/g, '');
                var margin_right = $(this).css('margin-right').replace(/[^-\d\.]/g, '');
                var padding_top = $(this).css('padding-top').replace(/[^-\d\.]/g, '');
                var padding_bottom = $(this).css('padding-bottom').replace(/[^-\d\.]/g, '');
                var padding_left = $(this).css('padding-left').replace(/[^-\d\.]/g, '');
                var padding_right = $(this).css('padding-right').replace(/[^-\d\.]/g, '');

                $('.fc-forground-control .fc_tool_text_align_' + text_align).addClass('fc-tool-active');
                $(preview_parent).find('.fc_margin_top').val(margin_top);
                $(preview_parent).find('.fc_margin_bottom').val(margin_bottom);
                $(preview_parent).find('.fc_margin_left').val(margin_left);
                $(preview_parent).find('.fc_margin_right').val(margin_right);
                $(preview_parent).find('.fc_padding_top').val(padding_top);
                $(preview_parent).find('.fc_padding_bottom').val(padding_bottom);
                $(preview_parent).find('.fc_padding_left').val(padding_left);
                $(preview_parent).find('.fc_padding_right').val(padding_right);

                $('.fc-bg-control .fc_tool_bgc').val(background_color);
                $('.fc-bg-control .fc_tool_bgc').trigger('change');

                $('.fc-forground-control .fc_tool_fgc').val(font_color);
                $('.fc-forground-control .fc_tool_fgc').trigger('change');

                if (font_weight == 'bold' || font_weight == '700') {
                    $(preview_parent).find('.fc_tool_text_bold').addClass('fc-tool-active');
                } else {
                    $(preview_parent).find('.fc_tool_text_bold').removeClass('fc-tool-active');
                }
                if (text_decoration == 'underline') {
                    $(preview_parent).find('.fc_tool_text_underline').addClass('fc-tool-active');
                } else {
                    $(preview_parent).find('.fc_tool_text_underline').removeClass('fc-tool-active');
                }

                if (font_style == 'italic') {
                    $(preview_parent).find('.fc_tool_text_italic').addClass('fc-tool-active');
                } else {
                    $(preview_parent).find('.fc_tool_text_italic').removeClass('fc-tool-active');
                }

                if (background == 'none') {
                    $(preview_parent).find('.fc_tool_bg_transparent').attr('checked', "checked");
                }


                $(preview_parent).find('.fc_tool_text_lineheight').val(line_height);
                $(preview_parent).find('.fc_tool_font_size').val(font_size);

                var tool_right_position = pos.left + width + 60;
                if (tool_right_position < preview_outer_width) {
                    //$(preview_parent).parent().find(".fc_customizer .fc_apply_style").show();
                    $(preview_parent).parent().find(".fc_customizer .fc_apply_style").addClass('show_customizer');
                } else {
                    //$(preview_parent).parent().find(".fc_customizer .fc_apply_style").show();
                    $(preview_parent).parent().find(".fc_customizer .fc_apply_style").addClass('show_customizer');
                }

                var class_name = $(this).attr('class');
                if (class_name != 'editable') {
                    $('*[class*="' + class_name + '"]').addClass('active_element');
                } else {
                    $(this).addClass('active_element');
                }

            });

            $("body").on('click', ".fc_tool_text_bold", function() {

                if (!$(this).hasClass('fc-tool-active')) {
                    $(this).addClass('fc-tool-active');
                    $('.active_element').css('font-weight', 'bold');
                } else {
                    $(this).removeClass('fc-tool-active');
                    $('.active_element').css('font-weight', 'inherit');
                }
            });

            $("body").on('click', ".fc_tool_text_align_left", function() {
                if (!$(this).hasClass('fc-tool-active')) {
                    $(this).parent().find('a').removeClass('fc-tool-active');
                    $(this).addClass('fc-tool-active');
                    $('.active_element').css('text-align', 'left');
                } else {
                    $(this).removeClass('fc-tool-active');
                    $('.active_element').css('text-align', 'inherit');
                }
            });


            $("body").on('click', ".fc_tool_text_align_right", function() {
                if (!$(this).hasClass('fc-tool-active')) {
                    $(this).parent().find('a').removeClass('fc-tool-active');
                    $(this).addClass('fc-tool-active');
                    $('.active_element').css('text-align', 'right');
                } else {
                    $(this).removeClass('fc-tool-active');
                    $('.active_element').css('text-align', 'inherit');
                }
            });

            $("body").on('click', ".fc_tool_text_align_center", function() {

                if (!$(this).hasClass('fc-tool-active')) {
                    $(this).parent().find('a').removeClass('fc-tool-active');
                    $(this).addClass('fc-tool-active');
                    $('.active_element').css('text-align', 'center');
                } else {
                    $(this).removeClass('fc-tool-active');
                    $('.active_element').css('text-align', 'inherit');
                }
            });

            $("body").on('click', ".fc_tool_text_align_justify", function() {
                if (!$(this).hasClass('fc-tool-active')) {
                    $(this).parent().find('a').removeClass('fc-tool-active');
                    $(this).addClass('fc-tool-active');
                    $('.active_element').css('text-align', 'justify');
                } else {
                    $(this).removeClass('fc-tool-active');
                    $('.active_element').css('text-align', 'inherit');
                }
            });

          $("body").on('change', ".fc-font-family select", function() {
                var font = $(this).val();
                WebFont.load({
                    google: {
                        families: [font]
                    }
                });
                $('.active_element').css('font-family', font);
            });

            $("body").on('click', ".fc_apply_style", function(e) {
                if (e.target !== e.currentTarget) return;
                $('.fc_tool_fgc').iris('hide');
                $('.fc_tool_bgc').iris('hide');
            });

            $('.fc-forground-control .fa-undo').click(function() {
                $('.active_element').css('color', '');
                $('.active_element').css('line-height', '');
                $('.active_element').css('font-size', '');
                $('.active_element').css('font-style', '');
                $('.active_element').css('font-weight', '');
                $('.active_element').css('text-decoration', '');
                $('.active_element').css('font-family', '');
                $('.active_element').parent().css('text-align', '');
            });

            $('.fc-margin-control .fa-undo').click(function() {
                $('.active_element').css('margin-top', '');
                $('.active_element').css('margin-bottom', '');
                $('.active_element').css('margin-left', '');
                $('.active_element').css('margin-right', '');
                $('.active_element').css('padding-top', '');
                $('.active_element').css('padding-bottom', '');
                $('.active_element').css('padding-left', '');
                $('.active_element').css('padding-right', '');
            });

            $('.fc-bg-control .fa-undo').click(function() {
                $('.active_element').css('background', '');
            });
            $('.fc_tool_text_italic').click(function() {
                if (!$(this).hasClass('fc-tool-active')) {
                    $(this).addClass('fc-tool-active');
                    $('.active_element').css('font-style', 'italic');
                } else {
                    $(this).removeClass('fc-tool-active');
                    $('.active_element').css('font-style', 'normal');
                }

            });

         
            $('.fc_tool_text_underline').click(function() {

                if (!$(this).hasClass('fc-tool-active')) {
                    $(this).addClass('fc-tool-active');
                    $('.active_element').css('text-decoration', 'underline');
                } else {
                    $(this).removeClass('fc-tool-active');
                    $('.active_element').css('text-decoration', 'none');
                }

            });

            jQuery('.fc-show-placeholder').on('click', function(event) {
                var parent_div = $(this).closest('.fc_supported_placeholder').parent().parent().parent();
                $(parent_div).find('.fc-hidden-placeholder').toggle('show');
                if ($.trim($(this).val()) === 'Show Placeholder') {
                    $(this).val('Hide Placeholder');
                } else {
                    $(this).val('Show Placeholder');
                }
            });

            $('.fc-show-source').toggle(function() {
                var parent_div = $(this).closest('.fc-bottom-bar').parent();
                $(parent_div).find('.fc_source_code_container').show();
                $(this).val('Hide Source');
            }, function() {
                var parent_div = $(this).closest('.fc-bottom-bar').parent();
                $(parent_div).find('.fc_source_code_container').hide();
                $(this).val('View Source');
            });



            $('.fc_tool_text_lineheight').on('keyup mouseup', function() {
                $('.active_element').css('line-height', $(this).val() + 'px');
            });

            $('body').on('change', '.fc_tool_bg_repeat', function(e) {        
                $('.active_element').css('background-repeat', $(this).val());
            });

            $('body').on('change', '.fc_tool_bg_transparent', function(e) {
                if ($(this).attr('checked')) {
                    $('.active_element').css('background', 'none');
                } else {
                    $('.active_element').css('background-color', $('.fc-bg-control .fc_tool_bgc').val());
                }

            });


            $('body').on('click', '.fc-texture', function(e) {
                e.preventDefault();
                $('.active_element').css({
                    'background-image': "url('" + settings_obj.image_path + "/back-skin/" + $(this).data('image') + ".png')",
                    'background-repeat': 'repeat'
                });
            });

            $('.fc_tool_font_size').on('keyup mouseup', function() {
                $('.active_element').css('font-size', $(this).val() + 'px');
            });

            $('.fc_margin_top').on('keyup mouseup', function() {
                $('.active_element').css('margin-top', $(this).val() + 'px');
            });
            $('.fc_margin_bottom').on('keyup mouseup', function() {
                $('.active_element').css('margin-bottom', $(this).val() + 'px');
            });
            $('.fc_margin_left').on('keyup mouseup', function() {
                $('.active_element').css('margin-left', $(this).val() + 'px');
            });
            $('.fc_margin_right').on('keyup mouseup', function() {
                $('.active_element').css('margin-right', $(this).val() + 'px');
            });
            $('.fc_padding_top').on('keyup mouseup', function() {
                $('.active_element').css('padding-top', $(this).val() + 'px');
            });
            $('.fc_padding_bottom').on('keyup mouseup', function() {
                $('.active_element').css('padding-bottom', $(this).val() + 'px');
            });
            $('.fc_padding_left').on('keyup mouseup', function() {
                $('.active_element').css('padding-left', $(this).val() + 'px');
            });
            $('.fc_padding_right').on('keyup mouseup', function() {
                $('.active_element').css('padding-right', $(this).val() + 'px');
            });

            $('body').on('click', '.fc-show-grid', function(e) {
                var parent_div = $(this).closest('.fc-bottom-bar').parent().parent();
                $(parent_div).find('.fc_preview .fc-component-block .fc-component-content').not(':first').remove();
                $(parent_div).find('.fc-grid-active').removeClass('fc-grid-active');
                var column = $(this).data('col');
                var box_copy = $(parent_div).find('.fc_preview .fc-component-block .fc-component-content').clone(true).off();
                for (c = 1; c < column; c++) {
                    $(parent_div).find('.fc_preview .fc-component-block').append(box_copy.clone());
                }
                $(this).addClass('fc-grid-active');
            });

            $('.fc-reset-design').toggle(function() {
                var parent_div = $(this).closest('.fc_customizer').parent().find('.fc_templates');
                var template = $(parent_div).find('.current-temp-in-use').data("templatename");
                var template_type = $(parent_div).find('.current-temp-in-use').data("templatetype");
                var undo = 'fc-' + template_type + '-' + template;
                $('.' + undo).addClass('fc-temp-undo').removeClass(undo);
                $(this).removeClass('fa-arrow-circle-left').addClass('fa-arrow-circle-right');
                $(this).parent().find('.fc-tooltiptext').text('View Customized');
            }, function() {
                var parent_div = $(this).closest('.fc_customizer').parent().find('.fc_templates');
                var template = $(parent_div).find('.current-temp-in-use').data("templatename");
                var template_type = $(parent_div).find('.current-temp-in-use').data("templatetype");
                var undo = 'fc-' + template_type + '-' + template;
                $('.fc-temp-undo').addClass(undo).removeClass('fc-temp-undo');
                $(this).removeClass('fa-arrow-circle-right').addClass('fa-arrow-circle-left');
                $(this).parent().find('.fc-tooltiptext').text('View Original');

            });
            $('body').on('click', '.fc-check-responsive .fa-mobile', function(e) {
                var parent_div = $(this).closest('.fc-bottom-bar').parent();
                $(parent_div).find('.fc_preview .fc-component-block').addClass('fc-mobile-view');
            });


            $('body').on('click', '.fc-check-responsive .fa-desktop', function(e) {
                var parent_div = $(this).closest('.fc-bottom-bar').parent();
                $(parent_div).find('.fc_preview .fc-component-block').removeClass('fc-mobile-view');
            });

            $('body').on('click', '.fc_view_source', function(e) {
                $(".fc_customizer .fc_apply_style").removeClass('show_customizer');
                $('.fc_tool_fgc').iris('hide');
                $('.fc_tool_bgc').iris('hide');
                $('.active_element').removeClass('active_element');
            });

            $(document).keyup(function(e) {
                if (e.keyCode == 27) {
                    $(".fc_customizer .fc_apply_style").removeClass('show_customizer');
                    $('.fc_tool_fgc').iris('hide');
                    $('.fc_tool_bgc').iris('hide');
                    $('.active_element').removeClass('active_element');
                }
            });

            $(document).on('submit','.wpomp-overview form',function(){

                var all_custom_style = {};
                var count = 0;
                $(".editable").each(function(index) {
                    var custom_style = {};
                    var temp_obj = $(this);
                    var type = $(this).data('temp-type');
                    custom_style[type] = {};
                    $(temp_obj).removeClass('editable');
                    $(temp_obj).removeClass('active_element');
                    var class_name = $(temp_obj).attr('class').split(' ');

                    if (class_name && class_name.length) {

                        $.each(class_name, function(k, c_name) {
                            if (c_name.indexOf('{') >= 0) {
                                delete class_name[k];
                            }
                        });
                        class_name = class_name.join('.');

                        var font_weight = $(temp_obj).css('font-weight');
                        var font_size = $(temp_obj).css('font-size');
                        var font_color = $(temp_obj).css('color');
                        var font_family = $(temp_obj).css('font-family');
                        var text_align = $(temp_obj).css('text-align');
                        var line_height = $(temp_obj).css('line-height');
                        var background = $(temp_obj).css('background-color');
                        var font_style = $(temp_obj).css('font-style');
                        var text_decoration = $(temp_obj).css('text-decoration');
                        var bg_image = $(temp_obj).css('background-image');
                        var margin_top = $(temp_obj).css('margin-top');
                        var margin_bottom = $(temp_obj).css('margin-bottom');
                        var margin_left = $(temp_obj).css('margin-left');
                        var margin_right = $(temp_obj).css('margin-right');
                        var padding_top = $(temp_obj).css('padding-top');
                        var padding_bottom = $(temp_obj).css('padding-bottom');
                        var padding_left = $(temp_obj).css('padding-left');
                        var padding_right = $(temp_obj).css('padding-right');

                        var style = 'background-image:' + bg_image + ';font-family:' + font_family + ';font-weight:' + font_weight + ';font-size:' + font_size + ';color:' + font_color + ';line-height:' + line_height + ';background-color:' + background + ';font-style:' + font_style + ';text-align:' + text_align + ';text-decoration:' + text_decoration + ';margin-top:' + margin_top + ';margin-bottom:' + margin_bottom + ';margin-left:' + margin_left + ';margin-right:' + margin_right + ';padding-top:' + padding_top + ';padding-bottom:' + padding_bottom + ';padding-left:' + padding_left + ';padding-right:' + padding_right + ';';
                        custom_style[type][class_name] = style;
                        all_custom_style[count] = custom_style;
                        count++;
                    }

                });

                $('#fc_custom_styles').val(JSON.stringify(all_custom_style));
                $(".fc_view_source").each(function(index, elem) {
                    $(elem).closest('.fc_customizer').parent().find('.custom_sourcecode').val($(elem).val());
                });
                return true;
            });
        }
        // Sticky Footer
        if ($('.fc-footer').length > 0) {

            $(window).scroll(function() {

                if ($('.flippercode-ui-height').height() > 800) {

                    if ($('.fc-no-sticky').length > 0) {
                        return;
                    }

                    var scroll = $(window).scrollTop();
                    var scrollBottom = $(window).height() - scroll;

                    if (scroll >= 0) {
                        $(".fc-footer").addClass("fc-fixed-footer");

                    }
                    if ($(window).scrollTop() + $(window).height() > ($(document).height() - 30)) {
                        $(".fc-fixed-footer").removeClass("fc-fixed-footer");
                    }
                }

            });

        }

        $('body').on('click', '.fc-main [data-toggle="tab"]', function(e) {
            e.preventDefault();
            var tab_id = $(this).attr('href');
            $('.fc-tabs-container .fc-tabs-content').hide();
            $(tab_id).show();
            $('.fc-tabs .active').removeClass('active');
            $(this).parent().addClass('active');
        });

    });

}(jQuery));
