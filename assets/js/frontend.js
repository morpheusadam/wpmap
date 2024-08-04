(function($) {
    "use strict";

    $.fn.pagination = function(maxentries, opts) {
        opts = jQuery.extend({
            items_per_page: 10,
            num_display_entries: 10,
            current_page: 0,
            num_edge_entries: 0,
            link_to: "#",
            prev_text: "Prev",
            next_text: "Next",
            ellipse_text: "...",
            prev_show_always: false,
            next_show_always: false,
            data_source: '',
            listing_container: '',

            callback: function() {
                return false;
            }
        }, opts || {});

        return this.each(function() {
            /**
             * Calculate the maximum number of pages
             */
            function numPages() {
                return Math.ceil(maxentries / opts.items_per_page);
            }

            /**
             * Calculate start and end point of pagination links depending on 
             * current_page and num_display_entries.
             * @return {Array}
             */
            function getInterval() {
                var ne_half = Math.ceil(opts.num_display_entries / 2);
                var np = numPages();
                var upper_limit = np - opts.num_display_entries;
                var start = current_page > ne_half ? Math.max(Math.min(current_page - ne_half, upper_limit), 0) : 0;
                var end = current_page > ne_half ? Math.min(current_page + ne_half, np) : Math.min(opts.num_display_entries, np);
                return [start, end];
            }

            /**
             * This is the event handling function for the pagination links. 
             * @param {int} page_id The new page number
             */
            function pageSelected(page_id, evt) {
                current_page = page_id;
                drawLinks();
                var continuePropagation = opts.callback(page_id, panel);
                if (!continuePropagation) {
                    if (evt.stopPropagation) {
                        evt.stopPropagation();
                    } else {
                        evt.cancelBubble = true;
                    }
                }
                return continuePropagation;
            }

            /**
             * This function inserts the pagination links into the container element
             */
            function drawLinks() {
                panel.empty();
                var interval = getInterval();
                var np = numPages();
                // This helper function returns a handler function that calls pageSelected with the right page_id
                var getClickHandler = function(page_id) {
                    return function(evt) {
                        return pageSelected(page_id, evt);
                    }
                }
                // Helper function for generating a single link (or a span tag if it's the current page)
                var appendItem = function(page_id, appendopts) {
                    page_id = page_id < 0 ? 0 : (page_id < np ? page_id : np - 1); // Normalize page id to sane value
                    appendopts = jQuery.extend({
                        text: page_id + 1,
                        classes: ""
                    }, appendopts || {});
                    if (page_id == current_page) {
                        var lnk = jQuery("<span class='current'>" + (appendopts.text) + "</span>");
                    } else {
                        var lnk = jQuery("<a>" + (appendopts.text) + "</a>")
                            .on("click", getClickHandler(page_id))
                            .attr('href', opts.link_to.replace(/__id__/, page_id));


                    }
                    if (appendopts.classes) {
                        lnk.addClass(appendopts.classes);
                    }
                    panel.append(lnk);
                }
                // Generate "Previous"-Link
                if (opts.prev_text && (current_page > 0 || opts.prev_show_always)) {
                    appendItem(current_page - 1, {
                        text: opts.prev_text,
                        classes: "prev"
                    });
                }
                // Generate starting points

                if (np > 1) {

                    if (interval[0] > 0 && opts.num_edge_entries > 0) {
                        var end = Math.min(opts.num_edge_entries, interval[0]);
                        for (var i = 0; i < end; i++) {
                            appendItem(i);
                        }
                        if (opts.num_edge_entries < interval[0] && opts.ellipse_text) {
                            jQuery("<span>" + opts.ellipse_text + "</span>").appendTo(panel);
                        }
                    }
                    // Generate interval links
                    for (var i = interval[0]; i < interval[1]; i++) {
                        appendItem(i);
                    }
                    // Generate ending points
                    if (interval[1] < np && opts.num_edge_entries > 0) {
                        if (np - opts.num_edge_entries > interval[1] && opts.ellipse_text) {
                            jQuery("<span>" + opts.ellipse_text + "</span>").appendTo(panel);
                        }
                        var begin = Math.max(np - opts.num_edge_entries, interval[1]);
                        for (var i = begin; i < np; i++) {
                            appendItem(i);
                        }

                    }

                }


                // Generate "Next"-Link
                if (opts.next_text && (current_page < np - 1 || opts.next_show_always)) {
                    appendItem(current_page + 1, {
                        text: opts.next_text,
                        classes: "next"
                    });
                }
            }

            // Extract current_page from options
            var current_page = opts.current_page;
            // Create a sane value for maxentries and items_per_page
            maxentries = (!maxentries || maxentries < 0) ? 1 : maxentries;
            opts.items_per_page = (!opts.items_per_page || opts.items_per_page < 0) ? 1 : opts.items_per_page;
            // Store DOM element for easy access from all inner functions
            var panel = jQuery(this);
            // Attach control functions to the DOM element 
            this.selectPage = function(page_id) {
                pageSelected(page_id);
            }
            this.prevPage = function() {
                if (current_page > 0) {
                    pageSelected(current_page - 1);
                    return true;
                } else {
                    return false;
                }
            }
            this.nextPage = function() {
                if (current_page < numPages() - 1) {
                    pageSelected(current_page + 1);
                    return true;
                } else {
                    return false;
                }
            }
            // When all initialisation is done, draw the links
            drawLinks();
            // call callback function
            opts.callback(current_page, this);
        });
    }

    var masonry;
    $(document).ready(function($) {
        $('.gm-style-iw').parent().parent().addClass('wpomp_infowindow_css');
        $("div.scroll-pane").jScrollPane();

        //Social share popups
        $(".wpomp-social-share").on("click", function(e) {
            e.preventDefault();
            var url = $(this).attr("href");
            var check_url = url.split('&url=');
            if (check_url[1] == '') {
                url += window.location.href;
            }
            var width = 500,
                height = 300;
            var left = (screen.width / 2) - (width / 2),
                top = (screen.height / 2) - (height / 2);
            window.open(
                url,
                "",
                "menubar=no,toolbar=no,resizable=yes,scrollbars=yes,width=" + width + ",height=" + height + ",top=" + top + ",left=" + left
            );
        });
        var masonry_ref = '';
        $('.categories_filter span a').on('click', function(e) {
            var ref = $(this).closest(".wpomp_map_container");
            var elem = $(ref).find('div.wpomp_categories');
            if ($(this).hasClass('wpomp_grid')) {
                $(this).addClass('active');
                $(ref).find('span a.wpomp_list').removeClass('active');
                $(ref).find('span a.wpomp_print').removeClass('active');
                elem.fadeOut(500, function() {
                    elem.removeClass('wpomp_listing_list').addClass('wpomp_listing_grid');
                    elem.show();
                    try {


                        var container = $(ref).find('.wpomp_listing_grid');
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

                });
            } else if ($(this).hasClass('wpomp_list')) {

                if ($(ref).find('.wpomp_listing_grid').hasClass('masonry')) {
                    var msnry = $(ref).find('.wpomp_listing_grid').data('masonry');
                    msnry.destroy();
                }

                $(this).addClass('active');
                $(ref).find('span a.wpomp_grid').removeClass('active');
                $(ref).find('span a.wpomp_print').removeClass('active');
                elem.fadeOut(500, function() {
                    elem.removeClass('wpomp_listing_grid').addClass('wpomp_listing_list');
                    $(ref).find('.wpomp_locations').equalHeightGrid();
                    elem.fadeIn(500);
                });
            } else if ($(this).hasClass('wpomp_print')) {
                $(this).addClass('active');
                $(ref).find('span a.wpomp_grid').removeClass('active');
                $(ref).find('span a.wpomp_list').removeClass('active');
                $(ref).find('span a.wpomp_print').removeClass('active');
            }
        });

    });

    $.fn.equalHeight = function() {
        var heights = [];
        $.each(this, function(i, element) {
            $element = $(element);
            var element_height;
            // Should we include the elements padding in it's height?
            var includePadding = ($element.css('box-sizing') == 'border-box') || ($element.css('-moz-box-sizing') == 'border-box');
            if (includePadding) {
                element_height = $element.innerHeight();
            } else {
                element_height = $element.height();
            }
            heights.push(element_height);
        });
        this.css('height', Math.max.apply(window, heights) + 'px');
        return this;
    };

    $.fn.equalHeightGrid = function(columns) {
        var $tiles = this;
        $tiles.css('height', 'auto');
        for (var i = 0; i < $tiles.length; i++) {
            if (i % columns === 0) {
                var row = $($tiles[i]);
                for (var n = 1; n < columns; n++) {
                    row = row.add($tiles[i + n]);
                }
                row.equalHeight();
            }
        }
        return this;
    };

    $.fn.detectGridColumns = function() {
        var offset = 0,
            cols = 0;
        this.each(function(i, elem) {
            var elem_offset = $(elem).offset().top;
            if (offset === 0 || elem_offset == offset) {
                cols++;
                offset = elem_offset;
            } else {
                return false;
            }
        });
        return cols;
    };

    $.fn.responsiveEqualHeightGrid = function() {
        var _this = this;

        function syncHeights() {
            var cols = _this.detectGridColumns();
            _this.equalHeightGrid(cols);
        }
        $(window).on('resize load', syncHeights);
        syncHeights();
        return this;
    };

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

function wpomp_set_consent_cookies() {
    wpomp_set_cookie('wpomp_show_map', "yes", wpomp_flocal.days_to_remember);
    window.location.reload();
}

function wpomp_set_noconsent_cookies() {
    wpomp_set_cookie('wpomp_show_map', "no", wpomp_flocal.days_to_remember);
    window.location.reload();
}

function wpomp_get_cookie(cname) {
    var name = cname + "=";
    var ca = document.cookie.split(';');
    for (var i = 0; i < ca.length; i++) {
        var c = ca[i];
        while (c.charAt(0) == ' ') {
            c = c.substring(1);
        }
        if (c.indexOf(name) == 0) {
            return c.substring(name.length, c.length);
        }
    }
    return null;
}

function wpomp_set_cookie(cname, cvalue, exdays) {
    var d = new Date();
    d.setTime(d.getTime() + (exdays * 24 * 60 * 60 * 1000));
    var expires = "expires=" + d.toUTCString();
    document.cookie = cname + "=" + cvalue + "; " + expires + "; path=/";
}