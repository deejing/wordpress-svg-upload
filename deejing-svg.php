<?php
/*
Plugin Name: SVG upload and preview
Plugin URI: https://github.com/deejing
Description: SVG file upload and SVG attachment preview
Author: Dechapon Bunpia
Version: 1.0
Author URI: http://www.deejing.de/
*/

/**
 * base on http://css-tricks.com/snippets/wordpress/allow-svg-through-wordpress-media-uploader/
 */
function deejing_svg ( $type ) 
{
    $type['svg'] = 'image/svg+xml';
    $type['svgz'] = 'image/svg+xml';
    return $type;
}
add_filter( 'upload_mimes', 'deejing_svg' );


/**
 * override attachment template
 */
function deejing_template_filter( $images ) 
{
?>
    <style type="text/css">
        svg, td.media-icon img[src$=".svg"], 
        img[src$=".svg"].attachment-post-thumbnail {
            width: 100% !important; 
            height: auto !important; 
        }
    </style>

    <script type="text/html" id="deejing-tmpl-attachment">
        <div class="attachment-preview deejing-custom-attachment-preview js--select-attachment type-{{ data.type }} subtype-{{ data.subtype }} {{ data.orientation }}">
            <div class="thumbnail">
                <# if ( data.uploading ) { #>
                    <div class="media-progress-bar"><div style="width: {{ data.percent }}%"></div></div>
                <# } else if ( 'image' === data.type && data.sizes ) { #>
                    <div class="centered">
                        <img src="{{ data.size.url }}" draggable="false" alt="" />
                    </div>
                <# } else { #>
                    <div class="centered">
                        <# if ( data.subtype === 'svg+xml') #>
                            <img src="{{ data.url }}" class="icon svg-icon" draggable="false" />
                        <# else { #>

                            <# if ( data.image && data.image.src && data.image.src !== data.icon ) { #>
                                <img src="{{ data.image.src }}" class="thumbnail" draggable="false" />
                            <# } else { #>
                                <img src="{{ data.icon }}" class="icon" draggable="false" />
                            <# } #>

                        <# } #>
                    </div>
                    <div class="filename">
                        <div>{{ data.filename }}</div>
                    </div>
                <# } #>
            </div>
            <# if ( data.buttons.close ) { #>
                <a class="close media-modal-icon" href="#" title="<?php esc_attr_e('Remove'); ?>"></a>
            <# } #>
        </div>
        <# if ( data.buttons.check ) { #>
            <a class="check" href="#" title="<?php esc_attr_e('Deselect'); ?>" tabindex="-1"><div class="media-modal-icon"></div></a>
        <# } #>
        <#
        var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly';
        if ( data.describe ) {
            if ( 'image' === data.type ) { #>
                <input type="text" value="{{ data.caption }}" class="describe" data-setting="caption"
                    placeholder="<?php esc_attr_e('Caption this image&hellip;'); ?>" {{ maybeReadOnly }} />
            <# } else { #>
                <input type="text" value="{{ data.title }}" class="describe" data-setting="title"
                    <# if ( 'video' === data.type ) { #>
                        placeholder="<?php esc_attr_e('Describe this video&hellip;'); ?>"
                    <# } else if ( 'audio' === data.type ) { #>
                        placeholder="<?php esc_attr_e('Describe this audio file&hellip;'); ?>"
                    <# } else { #>
                        placeholder="<?php esc_attr_e('Describe this media file&hellip;'); ?>"
                    <# } #> {{ maybeReadOnly }} />
            <# }
        } #>
    </script>

    <script type="text/javascript">
    ;(function($) {
        $(function() {
            var tmp = $('#tmpl-attachment');
            var new_tmp = $('#deejing-tmpl-attachment');
            tmp.html( new_tmp.html() );
        });
    })(jQuery);
    </script>
<?php
}

add_filter('admin_head', 'deejing_template_filter');