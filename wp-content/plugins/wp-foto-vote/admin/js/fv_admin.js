/*
Plugin Name: WP Foto Vote
Plugin URI: http://wp-vote.net
Author: Maxim K
Author URI: http://maxim-kaminsky.com/
*/

var contestTable = false;
var FvRouter;

FvLib.addHook('doc_ready', function() {
    if ( typeof Simple !== "undefined" ) {
        FvRouter = new Simple.Router();
    }

    if( fv.wp_lang == "ru-RU" ){
        var dt_lang = '//cdn.datatables.net/plug-ins/725b2a2115b/i18n/Russian.json';
    } else if( fv.wp_lang == "de-DE" ) {
        var dt_lang = '//cdn.datatables.net/plug-ins/725b2a2115b/i18n/German.json';
    }

    if ( jQuery.fn.dataTable !== undefined && jQuery('#table_units').length > 0 ) {
        var dt_opts = {
            language: {url: dt_lang},
            fnRowCallback: function(nRow, aData, iDisplayIndex) {
                nRow.querySelector(".fv-table-thumb").setAttribute("src", nRow.querySelector(".fv-table-thumb").getAttribute('data-src') );
                return nRow;
            },
            order: [[ 1, "desc" ]],
            columnDefs: [ {
                orderable: false,
                targets:   0
            } ],
            select: {
                style:    'multi',
                selector: '.selected-checkbox'
            },
            "aLengthMenu": [[10, 25, 50, 100, -1], [10, 25, 50, 100, "All"]]
        };

        contestTable = jQuery('#table_units').DataTable(dt_opts);
    }

    if ( jQuery.fn.datetimepicker !== undefined && jQuery('.datetime').length > 0 ) {
        jQuery('.datetime').datetimepicker(
            {
                //mask:'1111-19-09 29:59:09',
                format:'Y-m-d H:i:s',
                formatDate:'Y-m-d',
                formatTime:'H:i'
            });
    }

    // Select2 JS init
    // #https://select2.github.io/
    if ( jQuery.fn.select2 !== undefined ) {
        if ( jQuery('.fv-page .select2').length > 0 ) {
            jQuery(".fv-page .select2").select2();
        }
        // Late init Select2 for not fetch all Pages & Posts on every page loading
        jQuery(".fv-init-posts-dropdown").click(function(event) {
            event.preventDefault();

            var linkEl = this;
            linkEl.innerHTML += '<span class="spinner is-active"></span>';
            jQuery.growl.notice({ message: "Start load pages/posts list" });

            jQuery.get(
                fv.ajax_url,
                {action: "fv_get_pages_and_posts", what_get: linkEl.dataset.whatGet, fv_nonce: fv.nonce },
                function(resp){
                    resp = FvLib.parseJson(resp);
                    if ( resp.success && resp.list ) {
                        // Find Select, make active, remove all OPTION's
                        var $posts_dropdown = jQuery(linkEl).parent().parent().find(".fv-posts-dropdown")
                            .attr("disabled", false)
                            .addClass("fv-posts-dropdown--inited", false)
                            .children(":not(.do-not-remove-option)").remove().end();
                        if ( resp.list ) {
                            // Add options
                            var opt;
                            var opt_group;
                            for( var N = 0; N < resp.list.length; N ++ ) {
                                opt_group = document.createElement('optgroup');
                                opt_group.label = resp.list[N].text;
                                for (var K = 0; K < resp.list[N].children.length; K++) {
                                    var opt = document.createElement('option');
                                    opt.value = resp.list[N].children[K].id;
                                    opt.innerHTML = resp.list[N].children[K].text;
                                    opt_group.appendChild(opt);
                                }
                                $posts_dropdown.append(opt_group);
                                opt_group = null;
                            }
                            opt = null;
                            $posts_dropdown.select2();
                        }

                        if ( linkEl.dataset.postId ) {
                            $posts_dropdown.val( linkEl.dataset.postId ).trigger("change");
                        }
                        // Remove Edit Link
                        jQuery(linkEl).parent().find(".fv-posts-hidden-value").remove();
                        jQuery(linkEl).remove();
                    } else {
                        alert("Can't load pages/posts list!");
                    }
                }
            );

            return false;
        });

    }

    // Process mass action
    jQuery('.fv-mass-actions-select').change(function() {
        var do_action = this.value;
        if (!do_action) {
            return;
        }
        jQuery(".dataTable").find("tr.selected").each(function (key, tr) {
            jQuery(tr).find( "a[data-action='" + do_action + "']" )
                .data("confirmation", "no").click();
            
        });

        this.value = "";
    });
    
    // Process relation to another element
    jQuery('.fv-js-relation').change(function() {
        var $this = $(this);

        var relation_el = $this.data("r-el");
        var relation_val_to_show = $this.data("r-show-on");
        if ( !relation_el || !$(relation_el).length ) {
            return;
        }

        if ( $this.val() == relation_val_to_show ) {
            $(relation_el).removeClass("hidden");
        } else {
            $(relation_el).addClass("hidden");
        }
    });

    // Send request to Dismiss Notice
    jQuery(".fv-is-dismissible .notice-dismiss").click(function() {
        var dismiss_url = jQuery(this).parent().data("dismiss-url")
        if ( dismiss_url ) {
            jQuery.get( dismiss_url );
        }
    });

    jQuery(document).on("submit", ".fv-run-export-form", function(e) {
        e.preventDefault();

        var export_url = fv_add_query_arg("do", "do&" + jQuery(this).serialize(),fv.ajax_url);
        
        var export_iframe = document.querySelector('#export_iframe');
        // if Iframe not exists
        if ( export_iframe == null ) {
            var export_iframe = document.createElement('iframe');
            export_iframe.id = 'export_iframe';
            export_iframe.style.display = "none";
            export_iframe.src = export_url;
            export_iframe.setAttribute("download", "download");
            document.body.appendChild(export_iframe);
        } else {
            export_iframe.setAttribute('src', export_url);
        }
        jQuery.growl.notice({ message: "Export data runs." });

    });
    
});

/**
 *
 * @param key   string
 * @param value string
 * @param url   string
 *
 * @returns {string}
 */
function fv_add_query_arg(key, value, url) {
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
    if (newQueryString == "")
        newQueryString = "?";
    else
        newQueryString += "&";
    newQueryString += key + "=" + value;

    return urlParts[0] + newQueryString;
}
