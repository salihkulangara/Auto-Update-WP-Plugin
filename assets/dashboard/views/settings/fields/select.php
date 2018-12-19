<select <?= isset( $class ) ? 'class="'.$class.'"' : ''; ?> <?= isset( $multiple ) ? $multiple : '';?> name="<?= PF_B_Manager::$action.PF_B_Settings::$options_suffix;?>[<?= $name;?>]<?= isset( $multiple ) ? '[]' : '';?>">
  <?php 
    foreach( $data as $key => $label )
      echo '<option value="'.$key.'" '.( isset( PF_B_Manager::$options[$name] ) && in_array( $key, (array)PF_B_Manager::$options[$name] ) ? 'selected' : '' ).'>'.$label.'</option>';
  ?>
</select>
<?= isset( $description ) ? '<p class="description">'.$description.'</p>' : '';?>