(function($) {
    $.fn.pfBadgeInfoBlocks = function() {
        var $settingsWrap = this.find( '.pf_b-settings-wrap' );

        if( this.find( 'input[type="checkbox"]' )[0].checked == true )
          $settingsWrap.show();

        this.find( 'input[type="checkbox"]' ).on( 'change', function(){
          if( $(this)[0].checked )
            $settingsWrap.show();
          else
            $settingsWrap.hide();
        });

        $(document).on( 'click', '.pf_b-info-blocks .pf_b-item .pf_b-item-popup .pf_b-edit', function(e){
          e.preventDefault();
          $(this).closest( '.pf_b-item' ).toggleClass( "pf_b-edit-mode" );
        });

        $(document).on( 'mouseleave', '.pf_b-info-blocks .pf_b-item', function(e){
          e.preventDefault();
          $(this).removeClass( "pf_b-edit-mode" );
        });

        $(document).on( 'click', '.pf_b-info-blocks .pf_b-item .pf_b-item-popup .pf_b-edit', function(e){
          e.preventDefault();
          $(this).closest( '.pf_b-item' ).find( '.pf_b-input[data-name="color"]' ).wpColorPicker();
        });

        this.find( '.pf_b-wrap' ).each( function() {
            var $wrap = $(this),
                $list = $wrap.find( '.pf_b-list' ),
                $result = $wrap.find( 'input.pf_b-hidden' ),
                $addBtn = $wrap.find( '.pf_b-add' ),
                itemClass = '.pf_b-item',
                showTemplate = $.templates( '#pf_b-template' ),
                $template = $( $wrap.find( '.pf_b-popup-template' ).html() ),
                itemsJSON = JSON.parse( decodeURIComponent( $result.val() ) ),
                defaultData = {},
                file_frame,
                index_current_item;

            $template.find( '.pf_b-item-editor .pf_b-input' ).each( function(){
              defaultData[ $(this).attr( 'data-name' ) ] = $(this).val();
            });

            if( itemsJSON.length ) 
              for( var i = 0; i < itemsJSON.length; i++ ) 
                $list.append( updateItemData( i, false ) );
              
            $addBtn.on( 'click', function(e) {
              e.preventDefault();

              $list.append( updateItemData( undefined, false ) );
              
              createJSON();
            });

            $list.sortable({
              stop: function(event, ui) {
                createJSON();
              }
            });

            $wrap.delegate( '.pf_b-item.pf_b-edit-mode', 'mouseleave', reRenderItem );

            $wrap.delegate( '.pf_b-remove', 'click', function(e) {
              e.preventDefault();
              $(this).closest( itemClass ).remove();
              createJSON();
            });

            $wrap.delegate( '.pf_b-item.pf_b-edit-mode .pf_b-item-editor .pf_b-editor-image-remove', 'click', function(e) {
              e.preventDefault();

              var index = $(this).closest( '.pf_b-item' ).index(); 

              createJSON();
              itemsJSON[ index ]['image'] = '';

              $(this).closest( '.pf_b-item' ).removeClass( "pf_b-edit-mode" );

              updateItemData( index, true );

            });

            $wrap.delegate( '.pf_b-item.pf_b-edit-mode .pf_b-item-editor .pf_b-editor-image-add', 'click', function(e) {
              e.preventDefault();

              index_current_item = $(this).closest( '.pf_b-item' ).index();
              
              if( file_frame ) 
              {
                file_frame.open();
                return;
              }

              file_frame = wp.media.frames.file_frame = wp.media({
                title: pf_b_translations.info_blocks.select_image,
                button: {
                  text: pf_b_translations.info_blocks.insert_image
                },
                multiple: false
              });

              file_frame.on( 'select close', function() {
                attachment = file_frame.state().get('selection');
                if(attachment.length) {
                  attachment = attachment.first().toJSON();
                  var item = $list.find( '.pf_b-item' ).eq( index_current_item ),
                      image_field = item.find( '.pf_b-item-editor .pf_b-input[data-name="image"]' );

                  image_field.defaultValue( attachment.id );
                  image_field.val( attachment.id );
                  
                }
                createJSON();
                updateItemData( index_current_item, true );
              });

              file_frame.open();
            });

            function reRenderItem()
            {
              /*if( file_frame )
                return;*/

              createJSON();

              updateItemData( $(this).index(), true );
              
              $(this).removeClass( 'pf_b-edit-mode' );
            }

            function createJSON() 
            {
              var res = [];
              $list.find( itemClass ).each( function() {
                  var item = {};
                  $(this).find( '.pf_b-input' ).each( function() {
                    item[ $(this).attr( 'data-name' ) ] = $(this).val();
                  });
                  res.push( item );
              });

              itemsJSON = res;
              
              $result.val( encodeURIComponent( JSON.stringify( res ) ) );
            }

            function updateItemData( index, rerender )
            {
              var item = $template.clone(),
                  html = '',
                  data = {};

              if( index !== undefined )
                for( var key in itemsJSON[index] ) 
                {
                  data[ key ] = itemsJSON[index][key];
                  item.find( '.pf_b-input[data-name="' + key + '"]' ).defaultValue( data[ key ] );
                }
              else
                data = defaultData;

              
              if( index !== undefined && itemsJSON[index]['image'] != '' )
                var link = { type: 'remove', text: pf_b_translations.info_blocks.button_remove_image };
              else
                var link = { type: 'add', text: pf_b_translations.info_blocks.button_add_image };
              
              item.find( '.pf_b-item-editor .pf_b-input[data-name="image"]' ).closest('div').append( '<a href="#" class="button activate pf_b-editor-image-' + link.type + '">' + link.text + '</a>' );

              html = item.html().replace( /%s/g, showTemplate.render( data ) );
      
              if( rerender === false )
                item.html( html );
              else
                $list.find( '.pf_b-item' ).eq( index ).html( html );
                
              if( itemsJSON[index] !== undefined )
              {
                $.ajax({
                  type:         'get',
                  url:          $result.attr( 'data-url' ),
                  data:         '&image=' + itemsJSON[index].image + '&size=' + itemsJSON[index].size,
                  processData:  false, 
                  contentType:  false,
                  success:      function( data ){
                    $list.find( '.pf_b-item' ).eq( index ).find( '.pf_b-item-content > div' ).css( 'background-image', 'url(' + data + ')' ); 
                  }
                });
              }

              return item;
            }
        })
    }
})(jQuery);