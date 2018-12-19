<div class="<?= isset( $class ) ? $class : ''; ?> pf_b_badge_wrapper">
  <input type="text" name="<?= PF_B_Manager::$action.PF_B_Settings::$options_suffix;?>[<?= $name;?>][badge]" value="<?= isset( PF_B_Manager::$options[$name]['badge'] ) ? PF_B_Manager::$options[$name]['badge'] : ''; ?>" >
  <input type="text" class="pf_b_colorpicker" name="<?= PF_B_Manager::$action.PF_B_Settings::$options_suffix;?>[<?= $name;?>][color]" value="<?= isset( PF_B_Manager::$options[$name]['color'] ) ? PF_B_Manager::$options[$name]['color'] : ''; ?>" >
</div>
<?= isset( $description ) ? '<p class="description">'.$description.'</p>' : '';?>