/*
 * tcr_twitter_card_uploader.js
 * Cal Evans <cal@getpantheon.com>
 * http://getpantheon.com
 * 
 * This script is part of the Replace-default-twitter-card WordPress plugin.
 * is is the code that calls the WordPress media uploader, gets the image and
 * passes it back to WordPress
 * 
 */

/*
 * When the page is fully loaded, add this code into the ready function.
 */
jQuery(document).ready(function($) {

    var custom_uploader;

    /*
     * Look to see if we already have an image. If we do, show it, if not 
     * don't show the broken image tag.
     */
    var default_graphic_image_src = $('#default_graphic_display').attr('src');
    if ( default_graphic_image_src == null || default_graphic_image_src == undefined || default_graphic_image_src.trim() == '') {
        $('#default_graphic_display').hide();
    } else {
        $('#default_graphic_display').show();
    }

    /*
     * Now attach some code to the click of the uploader button. This will 
     * create a custom uploader if necesary and then open it. Once the 
     * button has been clicked, it pulls the info back out and stores it in 
     * both the hidden form element and the Image tag.
     */
    $('#default_graphic_upload_button').click(function(e) {
        e.preventDefault();

        /*
         * If the uploader object has already been created, simply re-open the 
         * dialog instead of recreating the object again.
         */
        if (custom_uploader) {
            custom_uploader.open();
            return;
        }

        /*
         * Extend the wp.media object with a couple of extra properties.
         */
        custom_uploader = wp.media.frames.file_frame = wp.media({
            title: 'Choose an Image',
            button: {
                text: 'Select Image'
            },
            multiple: false
        });

        /* 
         * When a file is selected: 
         *   - Grab the URL 
         *   - Set it as the hidden field's value
         *   - Set the image tab's src property
         *   - Show the image
         *   - Hide the Upload button
         *   - Show the Remove button
         */
        custom_uploader.on('select', function() {
            attachment = custom_uploader.state().get('selection').first().toJSON();
            $('#default_graphic').val(attachment.url);
            $('#default_graphic_display').attr('src',attachment.url);
            $('#default_graphic_display').show();
            $('#default_graphic_upload_button').hide();
            $('#default_graphic_remove_button').show();
        });

        //Open the uploader dialog
        custom_uploader.open();

    });

    /*
     * Set code for the Remove button's click.
     * On click
     *   - Remove the URL from both the hidden field and the image tag
     *   - Hide the image
     *   - Hide the Remove button
     *   - Show the Upload button
     */
    $('#default_graphic_remove_button').click(function(e) {

        $('#default_graphic').val('');
        $('#default_graphic_display').attr('src','');
        $('#default_graphic_display').hide();
        $('#default_graphic_remove_button').hide();
        $('#default_graphic_upload_button').show();

    });
}); // jQuery(document).ready(function($)