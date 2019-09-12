function jnshzdrgfjbhcsdrj(editor_id, elm, command, user_interface, value) {
    var linkElm, imageElm, inst;

    console.log( command );

    switch (command) {
        case "wp_link_apply":
        	console.log( 'foo' );
            /*inst = tinyMCE.getInstanceById(editor_id);
            linkElm = tinyMCE.getParentElement(inst.selection.getFocusElement(), "a");

            if (linkElm)
                alert("Link dialog has been overriden. Found link href: " + tinyMCE.getAttrib(linkElm, "href"));
            else
                alert("Link dialog has been overriden.");*/

            return true;
    }

    return false; // Pass to next handler in chain
}

/*tinyMCE.init({
  execcommand_callback: 'jnshzdrgfjbhcsdrj'
});*/

tinyMCE.PluginManager.add( 'noasdaet', function( editor, url ) {
	editor.on( 'ExecCommand', function( e ) {
		if ( e.command === 'wp_link_apply' ) {
            linkElm = tinymce.activeEditor.selection.getNode();
            console.log( linkElm.select('a') );
			// linkElm.setAttribute('href', 'dupa');
		}
	} );
} );
