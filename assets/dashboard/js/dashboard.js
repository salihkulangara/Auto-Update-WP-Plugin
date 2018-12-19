(function($){
  $(document).ready(function(){
    if( $( '.pf_b_colorpicker input, input.pf_b_colorpicker' ).length )
      $( '.pf_b_colorpicker input, input.pf_b_colorpicker' ).wpColorPicker();
    
    if( $( '.pf_b-info-blocks' ).length )
      $( '.pf_b-info-blocks' ).pfBadgeInfoBlocks();
  });
})(jQuery);