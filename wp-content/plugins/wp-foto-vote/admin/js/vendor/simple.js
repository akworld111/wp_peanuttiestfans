/*
 * simple.js - client side [M]VC kept simple.
 * v0.1 - by Diego Caponera - http://www.diegocaponera.com/
 * MIT Licensed.
 * Thanks to Paul Irish for the main idea, to Jason Garber for taking it further,
 * to John Resig for the following snippet and all the useful resources out there.
 */

//================================================================================

/* Simple JavaScript Inheritance
 * By John Resig http://ejohn.org/
 * MIT Licensed.
 */
// Inspired by base2 and Prototype
(function(){
    var initializing = false, fnTest = /xyz/.test(function(){xyz;}) ? /\b_super\b/ : /.*/;
    // The base Class implementation (does nothing)
    this.Class = function(){};

    // Create a new Class that inherits from this class
    Class.extend = function(prop) {
        var _super = this.prototype;

        // Instantiate a base class (but only create the instance,
        // don't run the init constructor)
        initializing = true;
        var prototype = new this();
        initializing = false;

        // Copy the properties over onto the new prototype
        for (var name in prop) {
            // Check if we're overwriting an existing function
            prototype[name] = typeof prop[name] == "function" &&
            typeof _super[name] == "function" && fnTest.test(prop[name]) ?
                (function(name, fn){
                    return function() {
                        var tmp = this._super;

                        // Add a new ._super() method that is the same method
                        // but on the super-class
                        this._super = _super[name];

                        // The method only need to be bound temporarily, so we
                        // remove it when we're done executing
                        var ret = fn.apply(this, arguments);
                        this._super = tmp;

                        return ret;
                    };
                })(name, prop[name]) :
                prop[name];
        }

        // The dummy class constructor
        function Class() {
            // All construction is actually done in the init method
            if ( !initializing && this.init )
                this.init.apply(this, arguments);
        }

        // Populate our constructed prototype object
        Class.prototype = prototype;

        // Enforce the constructor to be what we expect
        Class.prototype.constructor = Class;

        // And make this class extendable
        Class.extend = arguments.callee;

        return Class;
    };
})();

//================================================================================

(function(){


    // Simple = root namespace.
    var Simple;

    if (typeof exports !== 'undefined') {
        Simple = exports;
    } else {
        Simple = this.Simple = {};
    }

    Simple.Controller = Class.extend({

        _element : null,
        _basePath : '',

        init : function(basePath){

            this._basePath = basePath;

        },

        startup : function(element){

            this._element = element;

        }

    });

    Simple.Router = Class.extend({

        _controllers : [],

        exec : function(controller, action, element) {

            if ( typeof( jQuery(element).attr('data-disabled') ) == 'undefined' && controller !== "" && this._controllers[controller] && typeof this._controllers[controller][action] == "function" ) {
                this._controllers[controller][action].apply(this._controllers[controller], Array.prototype.slice.call(arguments).slice(2));
            }

        },

        addController : function(slug, controller){
            this._controllers[slug] = new controller(this.basePath);
        },

        setControllers : function(controllers){

            // Initialize controllers
            for(var i in controllers){

                this._controllers[i] = new controllers[i](this.basePath);

            }

            // Closure of my dreams
            var _self = this;

            // Run startup actions [if any]. If no action is given, 'startup' action is called by default
            jQuery.each(jQuery('[data-startup]'), function(){
                var callArr = jQuery(this).attr('data-call').split(".");

                if ( callArr ) {
                    _self.exec(
                        callArr[0],
                        callArr[1],
                        this
                    );
                }
            });

        },

        init : function(basePath){

            // Assign basePath
            this.basePath = basePath;

            // Closure of my dreams
            var _self = this;

            // Bind clickable elements to controllers
            jQuery(document).on('click', 'a[data-call], button[data-call], input[type="button"][data-call], .clickable[data-call]', function(event){

                event.preventDefault();

                var callArr = jQuery(this).attr('data-call').split(".");

                if ( callArr ) {
                    _self.exec(
                        callArr[0],
                        callArr[1],
                        this
                    );
                }
            });

            // Bind forms to controllers
            jQuery(document).on('submit', 'form[data-call]', function(event){

                event.preventDefault();

                var callArr = jQuery(this).attr('data-call').split(".");

                if ( callArr ) {
                    _self.exec(
                        callArr[0],
                        callArr[1],
                        this
                    );
                }

            });

        }

    });

})();