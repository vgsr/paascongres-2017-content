/* global wp */
( function( $, _ ) {

	// Bail when method already exists
	if ( typeof wp.media.wpPostImages !== 'undefined' )
		return;

	/**
	 * The Post Image constructor which creates instances for the given image
	 *
	 * @since 1.0.0
	 *
	 * @param {object} image Image details
	 */
	wp.media.wpPostImages = function( image ) {
		var Attachment = wp.media.model.Attachment,
		    FeaturedImage = wp.media.controller.FeaturedImage;

		// Define collection of postImages controller and media instances
		if ( typeof wp.media.controller.postImages === 'undefined' ) {
			wp.media.controller.postImages = {};
			wp.media.postImages = {};
		}

		/**
		 * Construct implementation of the FeaturedImage modal controller
		 * for the Post Image
		 *
		 * @since 1.0.0
		 */
		wp.media.controller.postImages[ image.name ] = FeaturedImage.extend({
			defaults: _.defaults({
				id:      image.key,
				title:   image.labels.postImageTitle,
				toolbar: image.key
			}, FeaturedImage.prototype.defaults ),

			/**
			 * Overload the controller's native selection updater method
			 *
			 * @since 1.0.0
			 */
			updateSelection: function() {
				var selection = this.get('selection'),
					id = wp.media.view.settings.post.postImages[ image.metaKey ],
					attachment;

				if ( '' !== id && -1 !== id ) {
					attachment = Attachment.get( id );
					attachment.fetch();
				}

				selection.reset( attachment ? [ attachment ] : [] );
			}
		});

		/**
		 * wp.media.postImages
		 * @namespace
		 *
		 * @see wp.media.featuredImage wp-includes/js/media-editor.js
		 */
		wp.media.postImages[ image.name ] = {
			/**
			 * Get the post image post ID
			 *
			 * @global wp.media.view.settings
			 *
			 * @returns {wp.media.view.settings.post.postImages[ image.metaKey ]|number}
			 */
			get: function() {
				return wp.media.view.settings.post.postImages[ image.metaKey ];
			},
			/**
			 * Set the post image id, save the post image data and
			 * set the HTML in the post meta box to the new post image.
			 *
			 * @global wp.media.view.settings
			 * @global wp.media.post
			 *
			 * @param {number} id The post ID of the post image, or -1 to unset it.
			 */
			set: function( id ) {
				var settings = wp.media.view.settings;

				settings.post.postImages[ image.metaKey ] = id;

				wp.media.post( image.ajaxAction, {
					json:          true,
					post_id:       settings.post.id,
					post_image_id: settings.post.postImages[ image.metaKey ],
					_wpnonce:      settings.post.nonce
				}).done( function( html ) {
					$( '.wp-post-image', image.parentEl ).html( html );
				});
			},
			/**
			 * The Post Image workflow
			 *
			 * @global wp.media.controller.FeaturedImage
			 * @global wp.media.view.l10n
			 *
			 * @this wp.media.postImages
			 *
			 * @returns {wp.media.view.MediaFrame.Select} A media workflow.
			 */
			frame: function() {
				if ( this._frame ) {
					wp.media.frame = this._frame;
					return this._frame;
				}

				this._frame = wp.media({
					state:  image.key,
					states: [ new wp.media.controller.postImages[ image.name ]() , new wp.media.controller.EditImage() ]
				});

				this._frame.on( 'toolbar:create:' + image.key, function( toolbar ) {
					/**
					 * @this wp.media.view.MediaFrame.Select
					 */
					this.createSelectToolbar( toolbar, {
						text: image.labels.setPostImage
					});
				}, this._frame );

				this._frame.on( 'content:render:edit-image', function() {
					var selection = this.state( image.key ).get('selection'),
						view = new wp.media.view.EditImage( { model: selection.single(), controller: this } ).render();

					this.content.set( view );

					// after bringing in the frame, load the actual editor via an ajax call
					view.loadEditor();

				}, this._frame );

				this._frame.state( image.key ).on( 'select', this.select );
				return this._frame;
			},
			/**
			 * 'select' callback for Post Image workflow, triggered when
			 *  the 'Set Post Image' button is clicked in the media modal.
			 *
			 * @global wp.media.view.settings
			 *
			 * @this wp.media.controller.FeaturedImage
			 */
			select: function() {
				var selection = this.get('selection').single();

				if ( ! wp.media.view.settings.post.postImages[ image.metaKey ] ) {
					return;
				}

				wp.media.postImages[ image.name ].set( selection ? selection.id : -1 );
			},
			/**
			 * Open the content media manager to the 'post image' tab when
			 * the post image is clicked.
			 *
			 * Update the post image id when the 'remove' link is clicked.
			 *
			 * @global wp.media.view.settings
			 */
			init: function() {
				$( image.parentEl ).on( 'click', '.wp-post-image-set, label', function( event ) {
					event.preventDefault();

					wp.media.postImages[ image.name ].frame().open();
				}).on( 'click', '.wp-post-image-remove', function() {
					event.preventDefault();

					wp.media.postImages[ image.name ].set( -1 );
				});
			}
		};

		$( wp.media.postImages[ image.name ].init );
	};

}( jQuery, _ ) );
