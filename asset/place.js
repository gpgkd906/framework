/**
 * @singleton
 * @aside guide place
 * @author Copyright (c) 2016 Chen Han. All rights reserved
 *
 * @description
 *
 * <script async defer src="https://maps.googleapis.com/maps/api/js?key={key}&libraries=places&language=ja"></script>
 * ## Examples
 * ###
 * @example
 */
(function (root, factory) {
    'use strict';
    if (typeof exports === 'object') {
        factory(exports);
    } else if (typeof define === 'function' && define.amd) {
        define(['exports'], factory);
    } else {
        factory(root);
    }
} (this, function (exports) {
    'use strict';
    
    return exports.Place = {
	
	radius: 50,
	
	setRadius: function (radius) {
	    this.radius = radius;
	},
	
	getRadius: function () {
	    return this.radius;
	},
	
	load: function (onSuccess) {
	    var me = this;
	    //navigatorバージョン
	    navigator.geolocation.getCurrentPosition(function(position) {
		var location =  new google.maps.LatLng(position.coords.latitude, position.coords.longitude);
		if(typeof onSuccess === "function") {
		    onSuccess.call(me, position, location);
		}
	    }, function(e) {
		alert("位置情報は取得できません\n\rブラウザの設定から位置情報をONにしてください");
	    }, {
		enableHighAccuracy: false,
		timeout: 6000,
		maximumAge: 60000
	    });
	},

	getPlaceService: function () {
	    return new google.maps.places.PlacesService(document.createElement("div"));
	},

	getCurrentPlace: function (callback) {
	    var me = this;
	    this.load(function (position, location) {
		var types = ["food"];
		var request = { location: location, radius: "" + me.getRadius() };
		me.getPlaceService().search(request, function (results, status) {
		    var placeId = null;
		    for(var key in results) {
			if(!!results[key].place_id) {
			    placeId = results[key].place_id;
			    break;
			}
		    }
		    me.getPlaceService().getDetails({ placeId: placeId }, function (place, status) {
			callback(place);
		    });
		});
	    });
	},
	
    };
}));
