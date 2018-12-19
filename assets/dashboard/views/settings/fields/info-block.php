<div class="pf_b-info-blocks">
  <div>
    <?php _e( 'Show Info Blocks?', PF_B_Manager::$action );?><input type="checkbox" value="1" name="<?= PF_B_Manager::$action.PF_B_Settings::$options_suffix;?>[<?= $name;?>][show]" <?php checked( isset( PF_B_Manager::$options[$name]['show'] ) ? PF_B_Manager::$options[$name]['show'] : 0, 1 ); ?>/><br/>
  </div>
  <div class="pf_b-settings-wrap" style="display:none;">
    <div class="pf_b-wrap-position">
      <?= sprintf( __( 'Show block every %s profiles', PF_B_Manager::$action ), '<input type="text" value="'.( isset( PF_B_Manager::$options[$name]['position'] ) ? PF_B_Manager::$options[$name]['position'] : '' ).'" name="'.PF_B_Manager::$action.PF_B_Settings::$options_suffix.'['.$name.'][position]" />' );?>
    </div>
    <div class="pf_b-wrap pf_b-cfix">
      <input type="hidden" data-url="<?= admin_url( 'admin-ajax.php?'.http_build_query( array( 'nonce' => wp_create_nonce( PF_B_Manager::$info_block_nonce ), 'action' => PF_B_Manager::$action.PF_B_Manager::$info_block_action_suffix ) ) );?>" class="pf_b-hidden" name="<?= PF_B_Manager::$action.PF_B_Settings::$options_suffix;?>[<?= $name;?>][data]" value="<?= rawurlencode( json_encode( PF_B_Manager::$options[$name]['data'] ) ); ?>"/>
      <div class="pf_b-list pf_b-cfix"></div>
      <div class="pf_b-buttons">
        <a href="#" class="pf_b-add"></a>
      </div>
      <script id="pf_b-template" type="text/x-jsrender">
        <div style="{{if color != '' }}background-color:{{:color}};{{/if}}" class="pf_b-item-{{:size}}">
          <div class="pf_b-item-content-inner">
            <h1>{{:title}}</h1>
            {{if title != '' && text != '' }}
              <hr/>
            {{/if}}
            <p>{{:text}}</p>
            {{if link != '' && url != '' }}
              <a href="{{:url}}">{{:link}}</a>
            {{/if}}
          </div>
        </div>
      </script>
      <template class="pf_b-popup-template">
        <div class="pf_b-item">
          <div class="pf_b-item-content">
            %s
          </div>
          <div class="pf_b-item-popup">
            <div class="pf_b-item-buttons"> 
              <a href="#" class="pf_b-edit"><span class="dashicons dashicons-edit"></span></a>
              <a href="#" class="pf_b-remove"><span class="dashicons dashicons-trash"></span></a>
            </div>
            <div class="pf_b-item-editor">
              <input class="pf_b-input" type="text" data-name="title" value="" placeholder="<?php _e( 'Title', PF_B_Manager::$action );?>"/>
              <div>
                <div class="pf-b-item-editor-background">
                  <div class="pf-b-item-editor-background-label"><?php _e( 'Background color:', PF_B_Manager::$action );?></div>
                  <div><input class="pf_b-input" type="text" data-name="color" value="" placeholder="<?php _e( 'Color', PF_B_Manager::$action );?>"/></div>
                </div>
                <div class="pf-b-item-editor-background">
                  <div class="pf-b-item-editor-background-label"><?php _e( 'Background image:', PF_B_Manager::$action );?></div>
                  <div>
                    <input class="pf_b-input" type="hidden" data-name="image" value=""/>
                  </div>
                </div>
              </div>
              <textarea rows="7" class="pf_b-input" data-name="text" placeholder="<?php _e( 'Text', PF_B_Manager::$action );?>"></textarea>
              <div class="pf_b-item-editor-inner">
                <input class="pf_b-input" type="text" data-name="link" value="" placeholder="<?php _e( 'Link', PF_B_Manager::$action );?>" />
                <input class="pf_b-input" type="text" data-name="url" value="" placeholder="<?php _e( 'URL', PF_B_Manager::$action );?>" />
              </div>
              <select class="pf_b-input" data-name="size">
                <option value="large"><?php _e( 'Large', PF_B_Manager::$action );?></option>
                <option value="small"><?php _e( 'Small', PF_B_Manager::$action );?></option>
              </select>
            </div>
          </div>
        </div>
      </template>
    </div>
  </div>
</div>