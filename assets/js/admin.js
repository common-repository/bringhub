jQuery(document).ready(function($) {

    tinymce.create('tinymce.plugins.bringhub_plugin', {
        init : function(ed, url) {
                // Register command for when button is clicked
                ed.addCommand('bringhub_insert_shortcode', function() {
                    selected = tinyMCE.activeEditor.selection.getContent();

                     content =  '[bringhub]';

                    tinymce.execCommand('mceInsertContent', false, content);
                });

            // Register buttons - trigger above command when clicked
            ed.addButton('bringhub_button', {title : 'Insert Bringhub Shortcode', cmd : 'bringhub_insert_shortcode', image: url + '/tinymce_logo.png' });
        },
    });

    tinymce.create('tinymce.plugins.disable_bringhub_plugin', {
        init : function(ed, url) {
                // Register command for when button is clicked
                ed.addCommand('disable_bringhub_shortcode', function() {
                    selected = tinyMCE.activeEditor.selection.getContent();

                     content =  '[disable_bringhub]';

                    tinymce.execCommand('mceInsertContent', false, content);
                });

            // Register buttons - trigger above command when clicked
            ed.addButton('disable_bringhub_button', {title : 'Disable Bringhub for this post', cmd : 'disable_bringhub_shortcode', image: url + '/tinymce_logo_disable.png' });
        },
    });

    tinymce.PluginManager.add('disable_bringhub_button', tinymce.plugins.disable_bringhub_plugin);
    tinymce.PluginManager.add('bringhub_button', tinymce.plugins.bringhub_plugin);
});