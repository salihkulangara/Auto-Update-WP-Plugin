(function() {
  tinymce.PluginManager.add( pf_b_translations.tinymce.action + '_shortcodes', function( editor, url ) {
    editor.addButton( pf_b_translations.tinymce.action + '_shortcodes', {
      icon: pf_b_translations.tinymce.action,
      type: 'menubutton',
      menu: [
        {
          text: pf_b_translations.tinymce.grid,
          onclick: function() {
            editor.insertContent( '[pf-badge]' );
          }
        },
        {
          text: pf_b_translations.tinymce.featured,
          onclick: function() {
            editor.windowManager.open( {
              title: pf_b_translations.tinymce.featured,
              body: [
                {
                  type: 'listbox',
                  name: 'boxType',
                  label: pf_b_translations.tinymce.type,
                  values: JSON.parse( pf_b_translations.tinymce.types ),
                  value: pf_b_translations.tinymce.default_values.featured.type
                },
                {
                  type: 'textbox',
                  name: 'boxCount',
                  tooltip: pf_b_translations.tinymce.number_profiles_desc,
                  label: pf_b_translations.tinymce.number_profiles,
                  value: pf_b_translations.tinymce.default_values.featured.count
                },
                {
                  type: 'listbox',
                  name: 'boxGrid',
                  label: pf_b_translations.tinymce.grid_page,
                  values: JSON.parse( pf_b_translations.tinymce.grid_elements )
                },
                {
                  type: 'listbox',
                  name: 'boxOrder',
                  label: pf_b_translations.tinymce.order,
                  values: JSON.parse( pf_b_translations.tinymce.order_elements ),
                  value: pf_b_translations.tinymce.default_values.featured.order
                },
                {
                  type: 'listbox',
                  name: 'boxStyle',
                  label: pf_b_translations.tinymce.style,
                  values: JSON.parse( pf_b_translations.tinymce.styles ),
                  value: pf_b_translations.tinymce.default_values.featured.style
                }
              ],
              onsubmit: function( e ) {
                var content = '';

                content   += ' type="' + e.data.boxType + '"';
                content   += ' count="' + e.data.boxCount + '"';
                content   += ' order="' + e.data.boxOrder + '"';
                content   += ' grid="' + e.data.boxGrid + '"';
                content   += ' style="' + e.data.boxStyle + '"';
                
                editor.insertContent( '[pf-badge-featured' + content + ']');
              }
            });
          }
        },
      ]
    });
  });
})();