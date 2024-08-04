/* 
    Created on : Aug 31, 2015
    Author     : yeozkaya@gmail.com
*/
;
(function ($) {

    var options = {
        geojsonServiceAddress: "http://yourGeoJsonSearchAddress",
        placeholderMessage: "Search...",
        foundRecordsMessage: "showing results.",
        limit: 10,
        notFoundMessage: "not found.",
        notFoundHint: "Make sure your search criteria is correct and try again.",
        drawColor: "blue",
        pointGeometryZoomLevel: -1, //Set zoom level for point geometries -1 means use leaflet default.
        pagingActive: true,
        map_obj: null,
        serachboxID:null

    };

    var activeResult = -1;
    var resultCount = 0;
    var lastSearch = "";
    var searchLayer;
    var focusLayer;
    var searchLayerType; // 0 --> One geometry, 1--> Multiple
    var features = [];
    var featureCollection = [];
    var offset = 0;
    var collapseOnBlur = true;
    var searchID = null;

    $.fn.GeoJsonAutocomplete = function (userDefinedOptions) {

        var keys = Object.keys(userDefinedOptions);
        for (var i = 0; i < keys.length; i++) {
            options[keys[i]] = userDefinedOptions[keys[i]];
        }

        map = options.map_obj.map;

        var searchBox = options.serachboxID;
         searchID = options.serachboxID;

        $(this).each(function () {
            var element = $(this);
            $(element).parent().addClass("searchContainer");

            $(this)[0].value = "";
            $(this).delayKeyup(function (event) {
                var target = $(event.target);

                switch (event.keyCode) {
                    case 13: // enter
                        searchButtonClick(target);
                        break;
                    case 38: // up arrow
                        prevResult(target);
                        break;
                    case 40: // down arrow
                        nextResult(target);
                        break;
                    case 37: //left arrow, Do Nothing
                    case 39: //right arrow, Do Nothing
                        break;
                    default:

                        if ($(searchBox)[0].value.length > 0) {
                            offset = 0;
                            getValuesAsGeoJson(false,target);
                        }
                        else {
                            clearButtonClick(target);
                        }
                        break;
                }
            }, 300);

            $(searchID).focus(function () {
                if ($("#resultsDiv")[0] !== undefined) {
                    $("#resultsDiv")[0].style.visibility = "visible";
                }
            });

            $(searchID).blur(function (event) {
                if ($("#resultsDiv")[0] !== undefined) {
                    if (collapseOnBlur) {
                        $("#resultsDiv")[0].style.visibility = "collapse";
                    }
                    else {
                        collapseOnBlur = true;

                        window.setTimeout(function ()
                        {
                            $(event.target).focus();
                        }, 0);
                    }
                }

            });

            $(document).on('click','#searchButton',function(){
                searchButtonClick();
            });

            $(document).on('click','#clearButton',function(){
                clearButtonClick();
            });
        });
    };

    $.fn.delayKeyup = function (callback, ms) {
        var timer = 0;
        $(this).keyup(function (event) {

            if (event.keyCode !== 13 && event.keyCode !== 38 && event.keyCode !== 40) {
                clearTimeout(timer);
                timer = setTimeout(function () {
                    callback(event);
                }, ms);
            }
            else {
                callback(event);
            }
        });
        return $(this);
    };

    function getValuesAsGeoJson(withPaging,target) {
        activeResult = -1;
        features = [];
        featureCollection = [];
        var limitToSend = options.limit;
        if (withPaging) {
            limitToSend++;
        }
        lastSearch = target[0].value;

        if (lastSearch === "") {
            return;
        }

        var data = {
            q: lastSearch,
            limit: limitToSend,
            format: 'json',
            addressdetails: true    
        };

        if(options.pagingActive){
            data.offset = offset;
        }
        
        $.ajax({
            url: options.geojsonServiceAddress,
            type: 'GET',
            data: data,
            dataType: 'json',
            success: function (json) {
            resultCount = json.length;
            features = json;

            if (limitToSend === resultCount)
                featureCollection = json.slice(0, json.length - 1);
            else
                featureCollection = json;
                
                createDropDown(withPaging,target);
                searchLayerType = (withPaging ? 1 : 0);
            },
            error: function () {
                processNoRecordsFoundOrError(target);
            }
        });

    }

    function createDropDown(withPaging,target) {
        var parent = target.parent();

        $("#resultsDiv").remove();
        parent.append("<div id='resultsDiv' class='result'><ul id='resultList' class='list'></ul><div>");

        $("#resultsDiv")[0].style.position = target[0].style.position;
        $("#resultsDiv")[0].style.left = (parseInt(target[0].style.left) - 10) + "px";
        $("#resultsDiv")[0].style.bottom = target[0].style.bottom;
        $("#resultsDiv")[0].style.right = target[0].style.right;
        $("#resultsDiv")[0].style.top = (parseInt(target[0].style.top) + 25) + "px";
        $("#resultsDiv")[0].style.zIndex = target[0].style.zIndex;

        var loopCount = features.length;
        var hasMorePages = false;
        if (withPaging && features.length === options.limit + 1) { //Has more pages
            loopCount--;
            hasMorePages = true;
            resultCount--;
        }

        for (var i = 0; i < loopCount; i++) {

            var html = "<li id='listElement" + i + "' class='listResult'>";
            html += "<span id='listElementContent" + i + "' class='content'>";
            html += "<font size='2' color='#333' class='title'>" + features[i].display_name + "</font><font size='1' color='#8c8c8c'> " + features[i].lat + " -- " + features[i].lon + "<font></span></li>";

            $("#resultList").append(html);

            $("#listElement" + i).mouseenter(function () {
                listElementMouseEnter(this,target);
            });

            $("#listElement" + i).mouseleave(function () {
                listElementMouseLeave(this,target);
            });

            $("#listElement" + i).mousedown(function () {
                listElementMouseDown(this,target);
            });
        }

        if (withPaging) {
            var prevPic = "prev.png";
            var nextPic = "next.png";
            var prevDisabled = "";
            var nextDisabled = "";

            if (offset === 0) {
                prevPic = "prev_dis.png";
                prevDisabled = "disabled";
            }

            if (!hasMorePages) {
                nextPic = "next_dis.png";
                nextDisabled = "disabled";
            }
            var image_url = options.map_obj.settings.images_url;
            var htmlPaging = "<div align='right' class='pagingDiv'>" + (offset + 1) + " - " + (offset + loopCount) + " " + options.foundRecordsMessage + " ";
            htmlPaging += "<input id='pagingPrev' type='image' src='"+image_url + prevPic + "' width='16' height='16' class='pagingArrow' " + prevDisabled + ">";
            htmlPaging += "<input id='pagingNext' type='image' src='"+image_url + nextPic + "' width='16' height='16' class='pagingArrow' " + nextDisabled + "></div>";
            $("#resultsDiv").append(htmlPaging);

            $("#pagingPrev").mousedown(function () {
                prevPaging(target);
            });

            $("#pagingNext").mousedown(function () {
                nextPaging(target);
            });

            drawGeoJsonList(target);
        }
    }

    function listElementMouseEnter(listElement,target) {

        var index = parseInt(listElement.id.substr(11));
        if (index !== activeResult) {
            $('#listElement' + index).toggleClass('mouseover');
        }
    }

    function listElementMouseLeave(listElement,target) {
        var index = parseInt(listElement.id.substr(11));

        if (index !== activeResult) {
            $('#listElement' + index).removeClass('mouseover');
        }
    }

    function listElementMouseDown(listElement,target) {
        var index = parseInt(listElement.id.substr(11));

        if (index !== activeResult) {
            if (activeResult !== -1) {
                $('#listElement' + activeResult).removeClass('active');
            }

            $('#listElement' + index).removeClass('mouseover');
            $('#listElement' + index).addClass('active');

            activeResult = index;
            fillSearchBox(target);

            if (searchLayerType === 0) {
                drawGeoJson(activeResult,target);
            }
            else {
                focusGeoJson(activeResult,target);
            }
        }
    }


    function drawGeoJsonList(target) {
        if (searchLayer !== undefined) {
            map.removeLayer(searchLayer);
            searchLayer = undefined;
        }

        searchLayer = L.geoJson(featureCollection, {
            style: function (feature) {
                return {color: "#D0473B"};
            },
            pointToLayer: function (feature, latlng) {
                return new L.CircleMarker(latlng, {radius: 5, fillOpacity: 0.85});
            },
            onEachFeature: function (feature, layer) {
                layer.bindPopup(feature.properties.popupContent);
            }
        });

        map.addLayer(searchLayer);

    }

    function focusGeoJson(index,target) {

        drawGeoJsonOnFocusLayer(index,target);
    }

    function getBoundsOfGeoJsonObject(geometry) {

        var geojsonObject = L.geoJson(geometry, {
            onEachFeature: function (feature, layer) {
            }
        });

        return geojsonObject.getBounds();
    }

    function drawGeoJson(index,target) {

        if (searchLayer !== undefined) {
            searchLayer = undefined;
        }

        if (index === -1)
            return;

        var drawStyle = {
            color: options.drawColor,
            weight: 5,
            opacity: 0.65,
            fill: false
        };

        searchLayer = L.geoJson(features[index].geometry, {
            style: drawStyle,
            onEachFeature: function (feature, layer) {
                layer.bindPopup(features[index].properties.popupContent);
            }
        });

        var center = new L.LatLng(features[index].lat, features[index].lon);

       if(!($(target).hasClass('wpomp_search_input'))){
           
            var marker = new L.Marker(center,{
                icon: L.icon({ 
                    iconUrl: options.map_obj.settings.marker_default_icon,
                     iconAnchor: [15, 55], 
                     popupAnchor: [0, -55],

                 }),
            });
            marker.addTo(map);
            var content = features[index].display_name;
            marker.bindPopup('<div class="wpomp_infowindow"><div class="wpomp_iw_content">'+content+'</div></div>');
           
       }

        if($(target).hasClass('wpomp_search_input')){
            if( typeof options.map_obj.map_data.listing !='undefined' && typeof options.map_obj.map_data.listing.display_radius_filter !='undefined' && options.map_obj.map_data.listing.display_radius_filter === true ) {
                options.map_obj.search_area = center;
            }
            options.map_obj.update_filters();
        }
        map.panTo(center);

    }

    function drawGeoJsonOnFocusLayer(index,target) {

        if (focusLayer !== undefined) {
            map.removeLayer(focusLayer);
            focusLayer = undefined;
        }

        if (index === -1)
            return;

        var drawStyle = {
            color: options.color,
            weight: 5,
            opacity: 0.65,
            fill: false
        };

        focusLayer = L.geoJson(features[index].geometry, {
            style: drawStyle,
            onEachFeature: function (feature, layer) {
                layer.bindPopup(features[index].properties.popupContent);
            }
        });

        map.addLayer(focusLayer);

    }


    function fillSearchBox(target) {
        
        
            try {

            if (activeResult === -1) {
                target[0].value = lastSearch;
            }
            else {
                target[0].value = features[activeResult].display_name;
            }

                var locationform = target.closest('form');

                if (activeResult === -1) {
                target[0].value = lastSearch;
            }
            else {
                target[0].value = features[activeResult].display_name;
            }            

            var locationform = target.closest('form');
            $(locationform).find('.google_latitude').val(features[activeResult].lat);
            
            $(locationform).find('.wpomp_metabox_location_hidden').val(features[activeResult].display_name);

            $(locationform).find('.google_longitude').val(features[activeResult].lon);

            $(locationform).find('.google_city').val(features[activeResult].address.county);

            $(locationform).find('.google_state').val(features[activeResult].address.state);

             $(locationform).find('.google_country').val(features[activeResult].address.country);


            } catch(e) {

            }
            
    }

    function nextResult(target) {

        if (resultCount > 0) {
            if (activeResult !== -1) {
                $('#listElement' + activeResult).toggleClass('active');
            }

            if (activeResult < resultCount - 1) {
                $('#listElement' + (activeResult + 1)).toggleClass('active');
                activeResult++;
            }
            else {
                activeResult = -1;
            }

            fillSearchBox(target);

            if (activeResult !== -1) {
                if (searchLayerType === 0) {
                    drawGeoJson(activeResult,target);
                }
                else {
                    focusGeoJson(activeResult,target);
                }
            }

        }
    }

    function prevResult(target) {
        if (resultCount > 0) {
            if (activeResult !== -1) {
                $('#listElement' + activeResult).toggleClass('active');
            }

            if (activeResult === -1) {
                $('#listElement' + (resultCount - 1)).toggleClass('active');
                activeResult = resultCount - 1;
            }
            else if (activeResult === 0) {
                activeResult--;
            }
            else {
                $('#listElement' + (activeResult - 1)).toggleClass('active');
                activeResult--;
            }

            fillSearchBox(target);

            if (activeResult !== -1) {
                if (searchLayerType === 0) {
                    drawGeoJson(activeResult,target);
                }
                else {
                    focusGeoJson(activeResult,target);
                }
            }
        }
    }

    function clearButtonClick(target) {
        target[0].value = "";
        lastSearch = "";
        resultCount = 0;
        features = [];
        activeResult = -1;
        $("#resultsDiv").remove();
        if (searchLayer !== undefined) {
            map.removeLayer(searchLayer);
            searchLayer = undefined;
        }
        if (focusLayer !== undefined) {
            map.removeLayer(focusLayer);
            focusLayer = undefined;
        }
    }

    function searchButtonClick(target) {
        getValuesAsGeoJson(options.pagingActive,target);

    }

    function processNoRecordsFoundOrError(target) {
        resultCount = 0;
        features = [];
        activeResult = -1;
        $("#resultsDiv").remove();
        if (searchLayer !== undefined) {
            map.removeLayer(searchLayer);
            searchLayer = undefined;
        }

        var parent = target.parent();
        $("#resultsDiv").remove();
        parent.append("<div id='resultsDiv' class='result'><i>" + lastSearch + " " + options.notFoundMessage + " <p><small>" + options.notFoundHint + "</small></i><div>");
    }

    function prevPaging(target) {
        target[0].value = lastSearch;
        offset = offset - options.limit;
        getValuesAsGeoJson(true,target);
        collapseOnBlur = false;
        activeResult = -1;
    }

    function nextPaging(target) {
        target[0].value = lastSearch;
        offset = offset + options.limit;
        getValuesAsGeoJson(true,target);
        collapseOnBlur = false;
        activeResult = -1;
    }
})(jQuery);
