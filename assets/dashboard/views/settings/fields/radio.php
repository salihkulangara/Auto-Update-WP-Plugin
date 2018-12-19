<div>
    <?php
    $radioname = PF_B_Manager::$action . PF_B_Settings::$options_suffix . '[' . $name . ']';
    $i = 1;
    foreach ($data as $key => $label) {
        echo '<label for="' . $radioname . $i . '"><input type="radio" ' . (isset($class) ? 'class="' . $class . '"' : '') . ' id="' . $radioname . $i . '" name="' . $radioname . '"  value="' . $key . '" ' . ( isset(PF_B_Manager::$options[$name]) && in_array($key, (array) PF_B_Manager::$options[$name]) ? 'checked' : '' ) . ' />' . $label . '</label>';
        echo '                  ';
        $i++;
    }
    ?>
    <?= isset($description) ? '<p class="description">' . $description . '</p>' : ''; ?>
</div>



