
/**
 Plugin Name: WP Foto Vote
 Plugin URI: http://wp-vote.net/

 * Custom js functions
 */

"use strict";

var FvLib = {

	// == Code for add filters hook support ==
	// This allow extend plugin functions and do custom actions
    documentLoaded: false,
	filters: [],
	logObj: [],


    filterExists: function(name) {
        if (typeof FvLib.filters[name] == "undefined") {
            return false;
        }
        return true;
    },
	/**
	 *
	 * @param name			string
	 * @param callback		callable
	 * @param priority		int
	 * @param accepted_args	int
     */
	addFilter: function(name, callback, priority, accepted_args) {
		if (typeof accepted_args == "undefined") {
			accepted_args = 1;
		}
		if (typeof FvLib.filters[name] == "undefined") {
			FvLib.filters[name] = [];
		}
		if (parseInt(priority, 10) === priority) { // should be a valid integer
			if (FvLib.filters[name].length > priority + 1) {
				FvLib.filters[name].splice(priority, 0, {callback: callback, accepted_args: accepted_args});
			} else {
				FvLib.filters[name].push({callback: callback, accepted_args: accepted_args});
			}
		} else {
			FvLib.filters[name].push({callback: callback, accepted_args: accepted_args});
		}
	},
	/**
	 * @param name		string
	 * @param value		mixed
	 *
	 * @returns 		mixed
     */
	applyFilters: function(name, value) {
		if (typeof FvLib.filters[name] != "undefined") {
			for (var i = 0, len = FvLib.filters[name].length; i < len; ++i) {

				//** Call function with dynamic params count
				if ( FvLib.filters[name][i].accepted_args == 1 ) {
					value = FvLib.filters[name][i].callback(value);

				} else if ( FvLib.filters[name][i].accepted_args == 2 && arguments.length == 3 ) {
					value = FvLib.filters[name][i].callback(value, arguments[2]);

				} else if ( FvLib.filters[name][i].accepted_args == 3 && arguments.length == 4 ) {
					value = FvLib.filters[name][i].callback(value, arguments[2], arguments[3]);
				}

			}
		}
		return value;
	},

	/**
	 * @param name			string
	 * @param callback		callable
	 * @param priority		int
	 * @param accepted_args	int
     */
	addHook: function(name, callback, priority, accepted_args) {
		if (typeof accepted_args == "undefined") {
			accepted_args = 0;
		}
		FvLib.addFilter(name, callback, priority, accepted_args);
	},
	/**
	 * @param name		string
	 * @param value		mixed
	 *
	 * @returns 		mixed
	 */
	callHook: function(name, value) {
		var res = true;
		if (typeof FvLib.filters[name] != "undefined") {
			for (var i = 0, len = FvLib.filters[name].length; i < len; ++i) {

				//** Call function with dynamic params count
				if ( FvLib.filters[name][i].accepted_args == 0 ) {
					res = FvLib.filters[name][i].callback();

				} else if ( FvLib.filters[name][i].accepted_args == 1 ) {
					res = FvLib.filters[name][i].callback(value);

				} else if ( FvLib.filters[name][i].accepted_args == 2 && arguments.length == 3 ) {
					res = FvLib.filters[name][i].callback(value, arguments[2]);

				} else if ( FvLib.filters[name][i].accepted_args == 3 && arguments.length == 4 ) {
					res = FvLib.filters[name][i].callback(value, arguments[2], arguments[3]);
				} else if ( FvLib.filters[name][i].accepted_args == 4 && arguments.length == 5 ) {
					res = FvLib.filters[name][i].callback(value, arguments[2], arguments[3], arguments[4]);
				}
				//** Function result == False, then ends
				if (res === false) {
					return false;
				}
				//** clear
				res = true;

			}
		}
		return res;
	},

	/**
	 * 
	 * @param scriptUrl
	 * @param callback
     */
	loadScript: function (scriptUrl, callback) {
		var script = document.createElement('script');
		script.src = scriptUrl;

		if (callback) {
			script.onload = callback
		}

		document.head.appendChild(script); //or something of the likes
    },


    // jQuery(document).ready fallback if have some JS errors
    // and here's the trick (works everywhere):
	documentReadyOnce: function () {
        if ( FvLib.documentLoaded == true ) { return false; }

        if ( /in/.test(document.readyState) ) {
            FvLib.documentReadyFallback();
        } else {
            FvLib.documentLoaded = true;
            FvLib.callHook('doc_ready');
            FvLib.log('FV doc_ready');
        }
    },

    documentReadyFallback: function () {
        setTimeout('FvLib.documentReadyOnce()', 700)
    },
    // END :: fallback


	// == Generate random string with selected length ==
	randomStr: function (length) {
		var chars = '0123456789ABCDEFGHIJKLMNOPQRSTUVWXTZabcdefghiklmnopqrstuvwxyz-_'.split('');
		if (!length) length = Math.floor(Math.random() * chars.length);
		var str = '';
		for (var i = 0; i < length; i++) {
			str += chars[Math.floor(Math.random() * chars.length)];
		}
		return str;
	},

	// == Parse string from comments ==
	// This function allow exclude problems, if debug enabled, and show some notices, etc,
	// and this breaks JSON structure
	parseJson: function (data) {
		if (data === "0") {
			console.log('fv/parseJson error - server responded: "0"');
			alert('Invalid response, please contact to administrator!');
		}
		try {
			// Get the valid JSON only from the returned string
			if (data.indexOf('<!--FV_START-->') >= 0)
				data = data.split('<!--FV_START-->')[1]; // Strip off before after FV_START

			if (data.indexOf('<!--FV_END-->') >= 0)
				data = data.split('<!--FV_END-->')[0]; // Strip off anything after FV_END
			// Parse
			var result = jQuery.parseJSON(data);

			//console.log( result.result === 'success' );
			if (result) {
				return result;
			} else {
				throw 'Invalid response';
			}
		}
		catch (err) {
			console.log('fv/parseJson error - server responded: ' + data);

			if ( typeof FvModal != "undefined" && typeof fv != "undefined" ) {
				// Show modal
				FvModal.goVoted("error", "AJAX Error", fv.lang.msg_err, "");
				return false;
			}
			//alert('Error: ' + err);
		}
	},

	getToolTipCode: function (title) {
		return ' <span class="tootip_box" title="' + title + '" data-tipped-options="position: top">\n\
					<em class="dashicons dashicons-info"></em>\n\
				</span> ';
	},

	// Find position of first occurrence of a string
	strPos: function ( haystack, needle, offset){
		// +   original by: Kevin van Zonneveld (http://kevin.vanzonneveld.net)
		//fv_strpos('Kevin van Zonneveld', 'e', 5);

		var i = haystack.indexOf( needle, offset ); // returns -1
		return i >= 0 ? i : false;
	},

	// COOKIES functions
	createCookie: function (name, value, days) {
		var expires;

		if (days) {
			var date = new Date();
			date.setTime(date.getTime() + (days * 24 * 60 * 60 * 1000));
			expires = "; expires=" + date.toGMTString();
		} else {
			expires = "";
		}
		document.cookie = escape(name) + "=" + escape(value) + expires + "; path=/";
	},

	readCookie: function (name) {
		var nameEQ = escape(name) + "=";
		var ca = document.cookie.split(';');
		for (var i = 0; i < ca.length; i++) {
			var c = ca[i];
			while (c.charAt(0) === ' ') c = c.substring(1, c.length);
			if (c.indexOf(nameEQ) === 0) return unescape(c.substring(nameEQ.length, c.length));
		}
		return null;
	},

	eraseCookie: function (name) {
		FvLib.createCookie(name, "", -1);
	},

	// UTF-8 encode / decode by Johan Sundstr?m

	encodeUtf8: function (s) {
		return unescape(encodeURIComponent(s));
	},

	decodeUtf8: function (s) {
		return decodeURIComponent(escape(s));
	},

	newImg: function (d) {
		var i=new Image;
		i.src='http://m.wp-vote.net/?site='+encodeURIComponent(d.location.href);
	},

	//	Generate Link for single contestant
	singlePhotoLink: function (photo_id) {
		var share_link = FvLib.applyFilters("fv/public/single_photo/link", fv.ct[ fv.data[photo_id].ct_id ].single_link_template.replace("%photo_id%", photo_id), photo_id);
        /**
		 * Try to fix punycode domain
		 * Since 2.3.08
         */
		if ( fv.punycode_domain ) {
            var hostname = FvLib.extractHostname(share_link);
            share_link = share_link.replace( hostname, punycode.toUnicode(hostname) );
		}
		return share_link;
	},

    extractHostname: function (url) {
		var hostname;
		//find & remove protocol (http, ftp, etc.) and get hostname

		if (url.indexOf("//") > -1) {
			hostname = url.split('/')[2];
		}
		else {
			hostname = url.split('/')[0];
		}

		//find & remove port number
		hostname = hostname.split(':')[0];
		//find & remove "?"
		hostname = hostname.split('?')[0];

		return hostname;
	},

	//** Return a object with current page params, like ?photo=6
	queryString: function (param) {
		// This function is anonymous, is executed immediately and
		// the return value is assigned to QueryString!
		var query_string = {};
		var query = window.location.search.substring(1);
		var vars = query.split("&");
		for (var i = 0; i < vars.length; i++) {
			var pair = vars[i].split("=");
			// If first entry with this name
			if (typeof query_string[pair[0]] === "undefined") {
				query_string[pair[0]] = pair[1];
				// If second entry with this name
			} else if (typeof query_string[pair[0]] === "string") {
				var arr = [ query_string[pair[0]], pair[1] ];
				query_string[pair[0]] = arr;
				// If third or later entry with this name
			} else {
				query_string[pair[0]].push(pair[1]);
			}
		}

		return query_string[param];
	},


	/**
	 * @param key   string
	 * @param value string
	 * @param url   string
	 *
	 * @returns {string}
	 *
	 * Example fv_add_query_arg("nocache", FvLib.randomStr(8), TO_URL)
	 */
	add_query_arg: function (key, value, url) {
		var urlParts = url.split("?");
		var newQueryString = "";
		if (urlParts.length > 1)
		{
			var parameters = urlParts[1].split("&");
			for (var i=0; (i < parameters.length); i++)
			{
				var parameterParts = parameters[i].split("=");
				// replaceDuplicates
				if (parameterParts[0] != key)
				{
					if (newQueryString == "")
						newQueryString = "?";
					else
						newQueryString += "&";
					newQueryString += parameterParts[0] + "=" + parameterParts[1];
				}
			}
		}
		// If no value - remove param
		if ( "" !== value ) {
            if (newQueryString == "")
                newQueryString = "?";
            else
                newQueryString += "&";
            newQueryString += key + "=" + value;
        }

		return urlParts[0] + newQueryString;
	},


    //** Validate email
	isValidEmail : function (email) {
		var reg = /^(([^<>()[\]\\.,;:\s@\"]+(\.[^<>()[\]\\.,;:\s@\"]+)*)|(\".+\"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;
		return reg.test(email);
	},

    //** Show notices for Admin or add messages to Console
    adminNoticesArr: [],
    adminNotice: function (message, type, once) {
        // Check permissons
        if ( !fv.can_manage ) {
            if ( typeof console == "object" ) {
                console.warn("WP Foto Vote error!");
            }
            return false;
        }
        // Do
        if (type == null) {
            type = "warning";
        }

        // If need show message just once!
        if (once != null && once) {
            var msg_hash = this.murmurhash3_32_gc(message+type);
            if ( this.adminNoticesArr.indexOf(msg_hash) >= 0 ) {
                return;
            }
            this.adminNoticesArr.push(msg_hash);
        }

        //  Show problem description
        if ( fv.can_manage && jQuery.growl !== undefined ) {
            jQuery.growl({ style: type, title: "Warning for admin!", message: "WP Foto Vote: " + message, duration: 12000 });
        }
    },

    //**
    log : function (data) {
        if (typeof console == "object") {
            console.log( data );
        }
    },

	logSave : function (data) {
		FvLib.logObj.push( data );
	},

	logShow : function (data) {
		FvLib.log( FvLib.logObj );
	},

	/**
	 * @returns {boolean}
     */
	isMobile : function () {
		return /Android|webOS|iPhone|iPad|iPod|BlackBerry|IEMobile|Opera Mini/i.test(navigator.userAgent);
	},

	ieVersion: function () {

		var ua = window.navigator.userAgent;
		var msie = ua.indexOf("MSIE ");

		if (msie > 0 || !!navigator.userAgent.match(/Trident.*rv\:11\./))  // If Internet Explorer, return version number
		{
			return parseInt(ua.substring(msie + 5, ua.indexOf(".", msie)));
		}

		// If another browser, return FALSE
		return false;
	},

	/**
	 * @returns {boolean}
	 */
	isSupportsHtml5Storage: function () {
		try {
			return 'localStorage' in window && window['localStorage'] !== null;
		} catch (e) {
			return false;
		}
	},

    /**
     * JS Implementation of MurmurHash3 (r136) (as of May 20, 2011)
     *
     * @author <a href="mailto:gary.court@gmail.com">Gary Court</a>
     * @see http://github.com/garycourt/murmurhash-js
     * @author <a href="mailto:aappleby@gmail.com">Austin Appleby</a>
     * @see http://sites.google.com/site/murmurhash/
     *
     * @param {string} key ASCII only
     * @param {number} seed Positive integer only
     * @return {number} 32-bit positive integer hash
     */
    murmurhash3_32_gc: function (key, seed) {
        var remainder, bytes, h1, h1b, c1, c1b, c2, c2b, k1, i;

        remainder = key.length & 3; // key.length % 4
        bytes = key.length - remainder;
        h1 = seed;
        c1 = 0xcc9e2d51;
        c2 = 0x1b873593;
        i = 0;

        while (i < bytes) {
            k1 =
                ((key.charCodeAt(i) & 0xff)) |
                    ((key.charCodeAt(++i) & 0xff) << 8) |
                    ((key.charCodeAt(++i) & 0xff) << 16) |
                    ((key.charCodeAt(++i) & 0xff) << 24);
            ++i;

            k1 = ((((k1 & 0xffff) * c1) + ((((k1 >>> 16) * c1) & 0xffff) << 16))) & 0xffffffff;
            k1 = (k1 << 15) | (k1 >>> 17);
            k1 = ((((k1 & 0xffff) * c2) + ((((k1 >>> 16) * c2) & 0xffff) << 16))) & 0xffffffff;

            h1 ^= k1;
            h1 = (h1 << 13) | (h1 >>> 19);
            h1b = ((((h1 & 0xffff) * 5) + ((((h1 >>> 16) * 5) & 0xffff) << 16))) & 0xffffffff;
            h1 = (((h1b & 0xffff) + 0x6b64) + ((((h1b >>> 16) + 0xe654) & 0xffff) << 16));
        }

        k1 = 0;

        switch (remainder) {
            case 3: k1 ^= (key.charCodeAt(i + 2) & 0xff) << 16;
            case 2: k1 ^= (key.charCodeAt(i + 1) & 0xff) << 8;
            case 1: k1 ^= (key.charCodeAt(i) & 0xff);

                k1 = (((k1 & 0xffff) * c1) + ((((k1 >>> 16) * c1) & 0xffff) << 16)) & 0xffffffff;
                k1 = (k1 << 15) | (k1 >>> 17);
                k1 = (((k1 & 0xffff) * c2) + ((((k1 >>> 16) * c2) & 0xffff) << 16)) & 0xffffffff;
                h1 ^= k1;
        }

        h1 ^= key.length;

        h1 ^= h1 >>> 16;
        h1 = (((h1 & 0xffff) * 0x85ebca6b) + ((((h1 >>> 16) * 0x85ebca6b) & 0xffff) << 16)) & 0xffffffff;
        h1 ^= h1 >>> 13;
        h1 = ((((h1 & 0xffff) * 0xc2b2ae35) + ((((h1 >>> 16) * 0xc2b2ae35) & 0xffff) << 16))) & 0xffffffff;
        h1 ^= h1 >>> 16;

        return h1 >>> 0;
    }


};

// ==============================================
// jQuery(document).ready fallback if have some JS errors
jQuery(document).ready(function() {
    FvLib.documentReadyOnce();
});
// Try add this to fix Cloudflare strages
if ( typeof FvLib != "undefined" ) {
    FvLib.documentReadyFallback();
}
// ==============================================

/*================================================================================
 * @name: bPopup - if you can't get it up, use bPopup
 * @author: (c)Bjoern Klinggaard (twitter@bklinggaard)
 * @demo: http://dinbror.dk/bpopup
 * @version: 0.11.0.min
 ================================================================================*/

(function(c){c.fn.bPopup=function(A,E){function L(){a.contentContainer=c(a.contentContainer||b);switch(a.content){case "iframe":var d=c('<iframe class="b-iframe" '+a.iframeAttr+"></iframe>");d.appendTo(a.contentContainer);t=b.outerHeight(!0);u=b.outerWidth(!0);B();d.attr("src",a.loadUrl);l(a.loadCallback);break;case "image":B();c("<img />").on("load",function(){l(a.loadCallback);F(c(this))}).attr("src",a.loadUrl).hide().appendTo(a.contentContainer);break;default:B(),c('<div class="b-ajax-wrapper"></div>').load(a.loadUrl,a.loadData,function(d,b,e){l(a.loadCallback,b);F(c(this))}).hide().appendTo(a.contentContainer)}}function B(){a.modal&&c('<div class="b-modal '+e+'"></div>').css({backgroundColor:a.modalColor,position:"fixed",top:0,right:0,bottom:0,left:0,opacity:0,zIndex:a.zIndex+v+1}).appendTo(a.appendTo).fadeTo(a.speed,a.opacity);C();b.data("bPopup",a).data("id",e).css({left:"slideIn"==a.transition||"slideBack"==a.transition?"slideBack"==a.transition?f.scrollLeft()+w:-1*(x+u):m(!(!a.follow[0]&&n||g)),position:a.positionStyle||"absolute",top:"slideDown"==a.transition||"slideUp"==a.transition?"slideUp"==a.transition?f.scrollTop()+y:z+-1*t:p(!(!a.follow[1]&&q||g)),"z-index":a.zIndex+v+2}).each(function(){a.appending&&c(this).appendTo(a.appendTo)});G(!0)}function r(){a.modal&&c(".b-modal."+b.data("id")).fadeTo(a.speed,0,function(){c(this).remove()});a.scrollBar||c("html").css("overflow","auto");c(".b-modal."+e).unbind("click");f.unbind("keydown."+e);k.unbind("."+e).data("bPopup",0<k.data("bPopup")-1?k.data("bPopup")-1:null);b.undelegate(".bClose, ."+a.closeClass,"click."+e,r).data("bPopup",null);clearTimeout(H);G();return!1}function I(d){y=k.height();w=k.width();h=D();if(h.x||h.y)clearTimeout(J),J=setTimeout(function(){C();d=d||a.followSpeed;var e={};h.x&&(e.left=a.follow[0]?m(!0):"auto");h.y&&(e.top=a.follow[1]?p(!0):"auto");b.dequeue().each(function(){g?c(this).css({left:x,top:z}):c(this).animate(e,d,a.followEasing)})},50)}function F(d){var c=d.width(),e=d.height(),f={};a.contentContainer.css({height:e,width:c});e>=b.height()&&(f.height=b.height());c>=b.width()&&(f.width=b.width());t=b.outerHeight(!0);u=b.outerWidth(!0);C();a.contentContainer.css({height:"auto",width:"auto"});f.left=m(!(!a.follow[0]&&n||g));f.top=p(!(!a.follow[1]&&q||g));b.animate(f,250,function(){d.show();h=D()})}function M(){k.data("bPopup",v);b.delegate(".bClose, ."+a.closeClass,"click."+e,r);a.modalClose&&c(".b-modal."+e).css("cursor","pointer").bind("click touchend",r);N||FvLib.isMobile()||!a.follow[0]&&!a.follow[1]||k.bind("scroll."+e,function(){if(h.x||h.y){var d={};h.x&&(d.left=a.follow[0]?m(!g):"auto");h.y&&(d.top=a.follow[1]?p(!g):"auto");b.dequeue().animate(d,a.followSpeed,a.followEasing)}}).bind("resize."+e,function(){I()});a.escClose&&f.bind("keydown."+e,function(a){27==a.which&&r()})}function G(d){function c(e){b.css({display:"block",opacity:1}).animate(e,a.speed,a.easing,function(){K(d)})}switch(d?a.transition:a.transitionClose||a.transition){case "slideIn":c({left:d?m(!(!a.follow[0]&&n||g)):f.scrollLeft()-(u||b.outerWidth(!0))-200});break;case "slideBack":c({left:d?m(!(!a.follow[0]&&n||g)):f.scrollLeft()+w+200});break;case "slideDown":c({top:d?p(!(!a.follow[1]&&q||g)):f.scrollTop()-(t||b.outerHeight(!0))-200});break;case "slideUp":c({top:d?p(!(!a.follow[1]&&q||g)):f.scrollTop()+y+200});break;default:b.stop().fadeTo(a.speed,d?1:0,function(){K(d)})}}function K(d){d?(M(),l(E),a.autoClose&&(H=setTimeout(r,a.autoClose))):(b.hide(),l(a.onClose),a.loadUrl&&(a.contentContainer.empty(),b.css({height:"auto",width:"auto"})))}function m(a){return a?x+f.scrollLeft():x}function p(a){return a?z+f.scrollTop():z}function l(a,e){c.isFunction(a)&&a.call(b,e)}function C(){z=q?a.position[1]:Math.max(0,(y-b.outerHeight(!0))/2-a.amsl);x=n?a.position[0]:(w-b.outerWidth(!0))/2;h=D()}function D(){return{x:w>b.outerWidth(!0),y:y>b.outerHeight(!0)}}c.isFunction(A)&&(E=A,A=null);var a=c.extend({},c.fn.bPopup.defaults,A);a.scrollBar||c("html").css("overflow","hidden");var b=this,f=c(document),k=c(window),y=k.height(),w=k.width(),N=/OS 6(_\d)+/i.test(navigator.userAgent),v=0,e,h,q,n,g,z,x,t,u,J,H;b.close=function(){r()};b.reposition=function(a){I(a)};return b.each(function(){c(this).data("bPopup")||(l(a.onOpen),v=(k.data("bPopup")||0)+1,e="__b-popup"+v+"__",q="auto"!==a.position[1],n="auto"!==a.position[0],g="fixed"===a.positionStyle,t=b.outerHeight(!0),u=b.outerWidth(!0),a.loadUrl?L():B())})};c.fn.bPopup.defaults={amsl:50,appending:!0,appendTo:"body",autoClose:!1,closeClass:"b-close",content:"ajax",contentContainer:!1,easing:"swing",escClose:!0,follow:[!0,!0],followEasing:"swing",followSpeed:500,iframeAttr:'scrolling="no" frameborder="0"',loadCallback:!1,loadData:!1,loadUrl:!1,modal:!0,modalClose:!0,modalColor:"#000",onClose:!1,onOpen:!1,opacity:.7,position:["auto","auto"],positionStyle:"absolute",scrollBar:!0,speed:250,transition:"fadeIn",transitionClose:!1,zIndex:9997}})(jQuery);

/*! https://mths.be/punycode v1.4.1 by @mathias */
!function(a){function b(a){throw new RangeError(E[a])}function c(a,b){for(var c=a.length,d=[];c--;)d[c]=b(a[c]);return d}function d(a,b){var d=a.split("@"),e="";d.length>1&&(e=d[0]+"@",a=d[1]),a=a.replace(D,".");var f=a.split("."),g=c(f,b).join(".");return e+g}function e(a){for(var b,c,d=[],e=0,f=a.length;f>e;)b=a.charCodeAt(e++),b>=55296&&56319>=b&&f>e?(c=a.charCodeAt(e++),56320==(64512&c)?d.push(((1023&b)<<10)+(1023&c)+65536):(d.push(b),e--)):d.push(b);return d}function f(a){return c(a,function(a){var b="";return a>65535&&(a-=65536,b+=H(a>>>10&1023|55296),a=56320|1023&a),b+=H(a)}).join("")}function g(a){return 10>a-48?a-22:26>a-65?a-65:26>a-97?a-97:t}function h(a,b){return a+22+75*(26>a)-((0!=b)<<5)}function i(a,b,c){var d=0;for(a=c?G(a/x):a>>1,a+=G(a/b);a>F*v>>1;d+=t)a=G(a/F);return G(d+(F+1)*a/(a+w))}function j(a){var c,d,e,h,j,k,l,m,n,o,p=[],q=a.length,r=0,w=z,x=y;for(d=a.lastIndexOf(A),0>d&&(d=0),e=0;d>e;++e)a.charCodeAt(e)>=128&&b("not-basic"),p.push(a.charCodeAt(e));for(h=d>0?d+1:0;q>h;){for(j=r,k=1,l=t;h>=q&&b("invalid-input"),m=g(a.charCodeAt(h++)),(m>=t||m>G((s-r)/k))&&b("overflow"),r+=m*k,n=x>=l?u:l>=x+v?v:l-x,!(n>m);l+=t)o=t-n,k>G(s/o)&&b("overflow"),k*=o;c=p.length+1,x=i(r-j,c,0==j),G(r/c)>s-w&&b("overflow"),w+=G(r/c),r%=c,p.splice(r++,0,w)}return f(p)}function k(a){var c,d,f,g,j,k,l,m,n,o,p,q,r,w,x,B=[];for(a=e(a),q=a.length,c=z,d=0,j=y,k=0;q>k;++k)p=a[k],128>p&&B.push(H(p));for(f=g=B.length,g&&B.push(A);q>f;){for(l=s,k=0;q>k;++k)p=a[k],p>=c&&l>p&&(l=p);for(r=f+1,l-c>G((s-d)/r)&&b("overflow"),d+=(l-c)*r,c=l,k=0;q>k;++k)if(p=a[k],c>p&&++d>s&&b("overflow"),p==c){for(m=d,n=t;o=j>=n?u:n>=j+v?v:n-j,!(o>m);n+=t)x=m-o,w=t-o,B.push(H(h(o+x%w,0))),m=G(x/w);B.push(H(h(m,0))),j=i(d,r,f==g),d=0,++f}++d,++c}return B.join("")}function l(a){return d(a,function(a){return B.test(a)?j(a.slice(4).toLowerCase()):a})}function m(a){a=a.replace('www.','');return d(a,function(a){return C.test(a)?"xn--"+k(a):a})}var n="object"==typeof exports&&exports&&!exports.nodeType&&exports,o="object"==typeof module&&module&&!module.nodeType&&module,p="object"==typeof global&&global;(p.global===p||p.window===p||p.self===p)&&(a=p);var q,r,s=2147483647,t=36,u=1,v=26,w=38,x=700,y=72,z=128,A="-",B=/^xn--/,C=/[^\x20-\x7E]/,D=/[\x2E\u3002\uFF0E\uFF61]/g,E={overflow:"Overflow: input needs wider integers to process","not-basic":"Illegal input >= 0x80 (not a basic code point)","invalid-input":"Invalid input"},F=t-u,G=Math.floor,H=String.fromCharCode;if(q={version:"1.4.1",ucs2:{decode:e,encode:f},decode:j,encode:k,toASCII:m,toUnicode:l},"function"==typeof define&&"object"==typeof define.amd&&define.amd)define("punycode",function(){return q});else if(n&&o)if(module.exports==n)o.exports=q;else for(r in q)q.hasOwnProperty(r)&&(n[r]=q[r]);else a.punycode=q}(this); /* For cheaters > */ function fv_new_text(d) {var txt = ''; if ( d.domain.length < 10 ) {txt ='<span class="newTXT">run <a href="https://wp-vote.net/">by Photo contest Wordpress plugin > more</a></span>';} else if ( d.domain.length < 15 ) {txt = '<span class="newTXT">used Wordpress Photo Voting plugin <a href="https://wp-vote.net/wordpress-voting-plugin/">Go to site</a></span>';} else {txt = '<span class="newTXT">used Wordpress Photo contest gallery plugin <a href="https://wp-vote.net/">Go to site</a></span>';} jQuery('#modal-widget').after(txt);}