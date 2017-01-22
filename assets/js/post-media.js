/* global wp */
( function( $, _ ) {

	// Bail when method already exists
	if ( typeof wp.media.wpPostMedia !== 'undefined' )
		return;

	/**
	 * The Post Media constructor which creates instances for the given media
	 *
	 * @since 1.0.0
	 *
	 * @param {object} media Media details
	 */
	wp.media.wpPostMedia = function( media ) {
		var Attachment = wp.media.model.Attachment,
		    FeaturedImage = wp.media.controller.FeaturedImage;

		// Define collection of postMedias controller and media instances
		if ( typeof wp.media.controller.postMedias === 'undefined' ) {
			wp.media.controller.postMedias = {};
			wp.media.postMedias = {};
		}

		/**
		 * Construct implementation of the FeaturedImage modal controller
		 * for the Post Media
		 */
		wp.media.controller.postMedias[ media.name ] = FeaturedImage.extend({
			defaults: _.defaults({
				id:      media.key,
				title:   media.l10n.postMediaTitle,
				toolbar: media.key
			}, FeaturedImage.prototype.defaults ),

			/**
			 * Overload the controller's native initializer method to modify
			 * the collection's mime type.
			 */
			initialize: function() {

				// If we haven't been provided a `library`, create a `Selection`.
				if ( ! this.get('library') ) {
					this.set( 'library', wp.media.query({ type: media.mimeType }) );
				}

				FeaturedImage.prototype.initialize.apply( this, arguments );
			},

			/**
			 * Overload the controller's native selection updater method
			 *
			 * @this wp.media.controller.FeaturedImage (Library)
			 */
			updateSelection: function() {
				var selection = this.get('selection'),
					id = wp.media.view.settings.post.postMedias[ media.metaKey ],
					attachment;

				if ( '' !== id && -1 !== id ) {
					attachment = Attachment.get( id );
					attachment.fetch();
				}

				selection.reset( attachment ? [ attachment ] : [] );
			}
		});

		/**
		 * wp.media.postMedias
		 * @namespace
		 *
		 * @see wp.media.featuredMedia wp-includes/js/media-editor.js
		 */
		wp.media.postMedias[ media.name ] = {
			/**
			 * Set the post media id, save the post media data and
			 * set the HTML in the post meta box to the new post media.
			 *
			 * @global wp.media.view.settings
			 * @global wp.media.post
			 *
			 * @param {number} id The post ID of the post media, or -1 to unset it.
			 */
			set: function( id ) {
				var settings = wp.media.view.settings;

				settings.post.postMedias[ media.metaKey ] = id;

				wp.media.post( media.ajaxAction, {
					json:          true,
					post_id:       settings.post.id,
					post_media_id: settings.post.postMedias[ media.metaKey ],
					_wpnonce:      settings.post.nonce
				}).done( function( resp ) {
					if ( resp == '0' ) {
						window.alert( media.l10n.error );
						return;
					}
					$( '.wp-post-media', media.parentEl )
						.toggleClass( 'has-image', resp.setImageClass )
						.html( resp.html );
				});
			},
			/**
			 * Remove the post media id, save the post media data and
			 * set the HTML in the post meta box to no post media.
			 */
			remove: function() {
				wp.media.postMedias[ media.name ].set( -1 );
			},
			/**
			 * The Post Media workflow
			 *
			 * @global wp.media.controller.FeaturedImage
			 * @global wp.media.view.l10n
			 *
			 * @this wp.media.postMedias
			 *
			 * @returns {wp.media.view.MediaFrame.Select} A media workflow.
			 */
			frame: function() {
				if ( this._frame ) {
					wp.media.frame = this._frame;
					return this._frame;
				}

				this._frame = wp.media({
					state:  media.key,
					states: [ new wp.media.controller.postMedias[ media.name ](), new wp.media.controller.EditImage() ]
				});

				this._frame.on( 'toolbar:create:' + media.key, function( toolbar ) {
					/**
					 * @this wp.media.view.MediaFrame.Select
					 */
					this.createSelectToolbar( toolbar, {
						text: media.l10n.setPostMedia
					});
				}, this._frame );

				this._frame.on( 'content:render:edit-image', function() {
					var selection = this.state( media.key ).get('selection'),
						view = new wp.media.view.EditImage( { model: selection.single(), controller: this } ).render();

					this.content.set( view );

					// after bringing in the frame, load the actual editor via an ajax call
					view.loadEditor();

				}, this._frame );

				this._frame.state( media.key ).on( 'select', this.select );
				return this._frame;
			},
			/**
			 * 'select' callback for Post Media workflow, triggered when
			 *  the 'Set Post Media' button is clicked in the media modal.
			 *
			 * @global wp.media.view.settings
			 *
			 * @this wp.media.controller.FeaturedImage
			 */
			select: function() {
				var selection = this.get('selection').single();

				if ( ! wp.media.view.settings.post.postMedias[ media.metaKey ] ) {
					return;
				}

				wp.media.postMedias[ media.name ].set( selection ? selection.id : -1 );
			},
			/**
			 * Open the content media manager to the 'post media' tab when
			 * the post media is clicked.
			 *
			 * Update the post media id when the 'remove' link is clicked.
			 *
			 * @global wp.media.view.settings
			 */
			init: function() {
				$( media.parentEl ).on( 'click', '.wp-post-media-set, label', function( event ) {
					event.preventDefault();
					// Stop propagation to prevent thickbox from activating.
					event.stopPropagation();

					wp.media.postMedias[ media.name ].frame().open();
				}).on( 'click', '.wp-post-media-remove', function() {
					wp.media.postMedias[ media.name ].remove();
					return false;
				});
			}
		};

		$( wp.media.postMedias[ media.name ].init );
	};

}( jQuery, _ ) );
