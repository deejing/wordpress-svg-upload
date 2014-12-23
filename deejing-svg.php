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


    <script type="text/html" id="deejing-tmpl-attachment-details-two-column">
        <div class="attachment-media-view {{ data.orientation }}">
            <div class="thumbnail thumbnail-{{ data.type }}">

                <# if ( data.subtype === 'svg+xml') #>
                    <img src="{{ data.url }}" class="icon svg-icon" draggable="false" />
                <# else { #>
                    <# if ( data.uploading ) { #>
                        <div class="media-progress-bar"><div></div></div>
                    <# } else if ( 'image' === data.type && data.sizes && data.sizes.large ) { #>
                        <img class="details-image" src="{{ data.sizes.large.url }}" draggable="false" />
                    <# } else if ( 'image' === data.type && data.sizes && data.sizes.full ) { #>
                        <img class="details-image" src="{{ data.sizes.full.url }}" draggable="false" />
                    <# } else if ( -1 === jQuery.inArray( data.type, [ 'audio', 'video' ] ) ) { #>
                        <img class="details-image" src="{{ data.icon }}" class="icon" draggable="false" />
                    <# } #>
                <# } #>

                <# if ( 'audio' === data.type ) { #>
                <div class="wp-media-wrapper">
                    <audio style="visibility: hidden" controls class="wp-audio-shortcode" width="100%" preload="none">
                        <source type="{{ data.mime }}" src="{{ data.url }}"/>
                    </audio>
                </div>
                <# } else if ( 'video' === data.type ) {
                    var w_rule = h_rule = '';
                    if ( data.width ) {
                        w_rule = 'width: ' + data.width + 'px;';
                    } else if ( wp.media.view.settings.contentWidth ) {
                        w_rule = 'width: ' + wp.media.view.settings.contentWidth + 'px;';
                    }
                    if ( data.height ) {
                        h_rule = 'height: ' + data.height + 'px;';
                    }
                #>
                <div style="{{ w_rule }}{{ h_rule }}" class="wp-media-wrapper wp-video">
                    <video controls="controls" class="wp-video-shortcode" preload="metadata"
                        <# if ( data.width ) { #>width="{{ data.width }}"<# } #>
                        <# if ( data.height ) { #>height="{{ data.height }}"<# } #>
                        <# if ( data.image && data.image.src !== data.icon ) { #>poster="{{ data.image.src }}"<# } #>>
                        <source type="{{ data.mime }}" src="{{ data.url }}"/>
                    </video>
                </div>
                <# } #>

                <div class="attachment-actions">
                    <# if ( 'image' === data.type && ! data.uploading && data.sizes && data.can.save ) { #>
                        <a class="button edit-attachment" href="#"><?php _e( 'Edit Image' ); ?></a>
                    <# } #>
                </div>
            </div>
        </div>
        <div class="attachment-info">
            <span class="settings-save-status">
                <span class="spinner"></span>
                <span class="saved"><?php esc_html_e('Saved.'); ?></span>
            </span>
            <div class="details">
                <div class="filename"><strong><?php _e( 'File name:' ); ?></strong> {{ data.filename }}</div>
                <div class="filename"><strong><?php _e( 'File type:' ); ?></strong> {{ data.mime }}</div>
                <div class="uploaded"><strong><?php _e( 'Uploaded on:' ); ?></strong> {{ data.dateFormatted }}</div>

                <div class="file-size"><strong><?php _e( 'File size:' ); ?></strong> {{ data.filesizeHumanReadable }}</div>
                <# if ( 'image' === data.type && ! data.uploading ) { #>
                    <# if ( data.width && data.height ) { #>
                        <div class="dimensions"><strong><?php _e( 'Dimensions:' ); ?></strong> {{ data.width }} &times; {{ data.height }}</div>
                    <# } #>
                <# } #>

                <# if ( data.fileLength ) { #>
                    <div class="file-length"><strong><?php _e( 'Length:' ); ?></strong> {{ data.fileLength }}</div>
                <# } #>

                <# if ( 'audio' === data.type && data.meta.bitrate ) { #>
                    <div class="bitrate">
                        <strong><?php _e( 'Bitrate:' ); ?></strong> {{ Math.round( data.meta.bitrate / 1000 ) }}kb/s
                        <# if ( data.meta.bitrate_mode ) { #>
                        {{ ' ' + data.meta.bitrate_mode.toUpperCase() }}
                        <# } #>
                    </div>
                <# } #>

                <div class="compat-meta">
                    <# if ( data.compat && data.compat.meta ) { #>
                        {{{ data.compat.meta }}}
                    <# } #>
                </div>
            </div>

            <div class="settings">
                <label class="setting" data-setting="url">
                    <span class="name"><?php _e('URL'); ?></span>
                    <input type="text" value="{{ data.url }}" readonly />
                </label>
                <# var maybeReadOnly = data.can.save || data.allowLocalEdits ? '' : 'readonly'; #>
                <label class="setting" data-setting="title">
                    <span class="name"><?php _e('Title'); ?></span>
                    <input type="text" value="{{ data.title }}" {{ maybeReadOnly }} />
                </label>
                <# if ( 'audio' === data.type ) { #>
                <?php foreach ( array(
                    'artist' => __( 'Artist' ),
                    'album' => __( 'Album' ),
                ) as $key => $label ) : ?>
                <label class="setting" data-setting="<?php echo esc_attr( $key ) ?>">
                    <span class="name"><?php echo $label ?></span>
                    <input type="text" value="{{ data.<?php echo $key ?> || data.meta.<?php echo $key ?> || '' }}" />
                </label>
                <?php endforeach; ?>
                <# } #>
                <label class="setting" data-setting="caption">
                    <span class="name"><?php _e( 'Caption' ); ?></span>
                    <textarea {{ maybeReadOnly }}>{{ data.caption }}</textarea>
                </label>
                <# if ( 'image' === data.type ) { #>
                    <label class="setting" data-setting="alt">
                        <span class="name"><?php _e( 'Alt Text' ); ?></span>
                        <input type="text" value="{{ data.alt }}" {{ maybeReadOnly }} />
                    </label>
                <# } #>
                <label class="setting" data-setting="description">
                    <span class="name"><?php _e('Description'); ?></span>
                    <textarea {{ maybeReadOnly }}>{{ data.description }}</textarea>
                </label>
                <label class="setting">
                    <span class="name"><?php _e( 'Uploaded By' ); ?></span>
                    <span class="value">{{ data.authorName }}</span>
                </label>
                <# if ( data.uploadedToTitle ) { #>
                    <label class="setting">
                        <span class="name"><?php _e( 'Uploaded To' ); ?></span>
                        <# if ( data.uploadedToLink ) { #>
                            <span class="value"><a href="{{ data.uploadedToLink }}">{{ data.uploadedToTitle }}</a></span>
                        <# } else { #>
                            <span class="value">{{ data.uploadedToTitle }}</span>
                        <# } #>
                    </label>
                <# } #>
                <div class="attachment-compat"></div>
            </div>

            <div class="actions">
                <a class="view-attachment" href="{{ data.link }}"><?php _e( 'View attachment page' ); ?></a>
                <# if ( data.can.save ) { #> |
                    <a href="post.php?post={{ data.id }}&action=edit"><?php _e( 'Edit more details' ); ?></a>
                <# } #>
                <# if ( ! data.uploading && data.can.remove ) { #> |
                    <?php if ( MEDIA_TRASH ): ?>
                        <# if ( 'trash' === data.status ) { #>
                            <a class="untrash-attachment" href="#"><?php _e( 'Untrash' ); ?></a>
                        <# } else { #>
                            <a class="trash-attachment" href="#"><?php _ex( 'Trash', 'verb' ); ?></a>
                        <# } #>
                    <?php else: ?>
                        <a class="delete-attachment" href="#"><?php _e( 'Delete Permanently' ); ?></a>
                    <?php endif; ?>
                <# } #>
            </div>

        </div>
    </script>

    <script type="text/javascript">
    ;(function($) {
        $(function() {
            var tmp = $('#tmpl-attachment');
            var tmp_view = $('#tmpl-attachment-details-two-column');

            var new_tmp = $('#deejing-tmpl-attachment');
            var new_tmp_view = $('#deejing-tmpl-attachment-details-two-column');
            
            tmp.html( new_tmp.html() );
            tmp_view.html( new_tmp_view.html() );
        });
    })(jQuery);
    </script>
<?php
}

add_filter('admin_head', 'deejing_template_filter');