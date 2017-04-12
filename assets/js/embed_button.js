jQuery(function ($) {

    tinymce.create('tinymce.plugins.msb_embed_plugin', {

        init: function (ed, url) {

            // button options
            ed.addButton('msb_button', {
                title: 'Add Embed', // localize this if necessary
                cmd: 'msb_insert_shortcode',
                image : url+'example.png' // optionally use custom image for the button
            });

            ed.addCommand('msb_insert_shortcode', function () {

                var selected = tinyMCE.activeEditor.selection.getContent();
                var content;

                // if text is selected use that as the video source
                // remove this if it doesn't apply
                if (selected) {
                    content = '[msb_video video="' + selected + '"]';
                    tinymce.execCommand('mceInsertContent', false, content);
                } else {
                    ed.windowManager.open({
                        title: 'Insert video url', // localize this if necessary
                        body: [{
                            type: 'textbox',
                            name: 'video',
                            label: 'Url'
                        }, {
                            type: 'listbox',
                            name: 'autoplay',
                            label: 'Autoplay?',
                            values: [
                                {
                                    text: 'No',
                                    value: 0
                                },
                                {
                                    text: 'Yes',
                                    value: 1
                                }
                            ]
                        },
                            {
                                type: 'textbox',
                                name: 'width',
                                label: 'Width'
                            },
                            {
                                type: 'textbox',
                                name: 'height',
                                label: 'Height'
                            }
                        ],
                        onsubmit: function (e) {

                            // generate shortcode to be inserted
                            var shortcode = '[msb_video video="' + e.data.video + '" ';
                            if (e.data.autoplay == 1) {
                                shortcode += ' autoplay="1" ';
                            }
                            if (e.data.height !== '') {
                                shortcode += ' height="' + e.data.height + '" ';
                            }
                            if (e.data.width !== '') {
                                shortcode += ' width="' + e.data.width + '" ';
                            }
                            shortcode += ' ]';

                            ed.insertContent(shortcode);

                        }
                    });
                }

            });
        }
    });

    // Add button
    tinymce.PluginManager.add('msb_button', tinymce.plugins.msb_embed_plugin);

});
