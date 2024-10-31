<?php

    $pluginname = __("Search Box", "search-box");
    $prefix = SEARCH_BOX_PREFIX;
	
	class SearchBox{
		function create_opening_tag($value) { 
			$group_class = "";
			if (isset($value['grouping'])) {
				$group_class = "suf-grouping-rhs";
			}
			echo '<div class="suf-section fix">'."\n";
			if ($group_class != "") {
				echo "<div class='$group_class fix'>\n";
			}
			if (isset($value['name'])) {
				echo "<h3>" . $value['name'] . "</h3>\n";
			}
			if (isset($value['desc']) && !(isset($value['type']) && $value['type'] == 'checkbox')) {
				echo $value['desc']."<br />";
			}
			if (isset($value['note'])) {
				echo "<span class=\"note\">".$value['note']."</span><br />";
			}
		 }
	
		/**
		 * Creates the closing markup for each option.
		 *
		 * @param $value
		 * @return void
		 */
		function create_closing_tag($value) { 
			if (isset($value['grouping'])) {
				echo "</div>\n";
			}
			//echo "</div><!-- suf-section -->\n";
			echo "</div>\n";
		 }
			
		function create_suf_header_3($value) { echo '<h3 class="suf-header-3">'.$value['name']."</h3>\n"; }
			
		function create_section_for_text($value) { 
			self::create_opening_tag($value);
			$text = "";
			if (get_option($value['id']) === FALSE) {
				$text = $value['std'];
			}
			else {
				$text = get_option($value['id']);
			}
		 
			echo '<input type="text" id="'.$value['id'].'" placeholder="" name="'.$value['id'].'" value="'.esc_attr($text).'" />'."\n";
			self::create_closing_tag($value);
		 }
		
		function create_section_for_textarea($value) { 
			self::create_opening_tag($value);
			echo '<textarea name="'.$value['id'].'" type="textarea" cols="" rows="10">'."\n";
			if ( get_option( $value['id'] ) != "") {
				echo esc_textarea(get_option( $value['id'] ));
			}
			else {
				echo esc_textarea($value['std']);
			}
			echo '</textarea>';
			self::create_closing_tag($value);
		 }
		
		function create_section_for_color_picker($value) { 
			self::create_opening_tag($value);
			$color_value = "";
			if (get_option($value['id']) === FALSE) {
				$color_value = $value['std'];
			}
			else {
				$color_value = get_option($value['id']);
			}
		 
			echo '<div class="color-picker">'."\n";
			echo '<input type="text" id="'.$value['id'].'" name="'.$value['id'].'" value="'.esc_attr($color_value).'" class="search-box-color-field" />';
			
			echo "</div>\n";
			self::create_closing_tag($value);
		 }
		
		function create_section_for_radio($value) { 
			self::create_opening_tag($value);
			foreach ($value['options'] as $option_value => $option_text) {
				$checked = ' ';
				if (get_option($value['id']) == $option_value) {
					$checked = ' checked="checked" ';
				}
				else if (get_option($value['id']) === FALSE && $value['std'] == $option_value){
					$checked = ' checked="checked" ';
				}
				else {
					$checked = ' ';
				}
				echo '<div class="search-box-radio"><input type="radio" name="'.$value['id'].'" value="'.
					$option_value.'" '.$checked."/>".esc_attr($option_text)."</div>\n";
			}
			self::create_closing_tag($value);
		 }
	
		function create_section_for_multi_select($value) { 
			self::create_opening_tag($value);
			echo '<ul class="search-box-checklist" id="'.$value['id'].'" >'."\n";
			$i = 0;
			foreach ($value['options'] as $option_value => $option_list) {
				$checked = " ";
				if ($val = get_option($value['id'])) {
					if ($val[0]==true) $checked = " checked='checked' ";
				}
				echo "<li>\n";
				echo '<input type="checkbox" name="'.$value['id'].'[]" value="true" '.$checked.' class="depth-'.($option_list['depth']+1).'" />'.esc_attr($option_list['title'])."\n";
				echo "</li>\n";
				$i++;
			}
			echo "</ul>\n";
			self::create_closing_tag($value);
		 }
	}
	
	$options = array( 
		array("name" => __("Search Box Customization", "search-box"),
				"type" => "sub-section-3",
				"category" => "box-styles",
		),
		
		array("name" => __("Search Box Color", "search-box"),
				"desc" => __("Set the color of the search box icon. ", "search-box"),
				"id" => $prefix."_icon_color",
				"type" => "color-picker",
				"parent" => "box-styles",
				"std" => "#25ade4"),

		array("name" => __("Search Box width", "search-box"),
				"desc" => "",
				"id" => $prefix."_width",
				"type" => "text",
				"parent" => "box-styles",
				"std" => "150px"
		),

		array("name" => __("Search Box Mouseover width", "search-box"),
				"desc" => "",
				"id" => $prefix."_hover_width",
				"type" => "text",
				"parent" => "box-styles",
				"std" => "200px"
		),
		
	 );
	
	function search_box_create_form($options) {
		$search_box = new SearchBox;
		echo "<form id='options_form' method='post' name='form' >\n";
		foreach ($options as $value) {
			switch ( $value['type'] ) {
				case "sub-section-3":
					$search_box->create_suf_header_3($value);
					break;
	 
				case "text";
					$search_box->create_section_for_text($value);
					break;
	 
				case "textarea":
					$search_box->create_section_for_textarea($value);
					break;
	 
				case "multi-select":
					$search_box->create_section_for_multi_select($value);
					break;
	 
				case "radio":
					$search_box->create_section_for_radio($value);
					break;
	 
				case "color-picker":
					$search_box->create_section_for_color_picker($value);
					break;

			}
		}
		
		?>
		<div style="margin-top:50px">
			<?php _e("Shortcode", "search-box");?>: <span style="border: dashed 1px yellowgreen;padding: 5px;margin-left: 10px;">[search_box]</span>
		</div>

		<div style="margin-top:50px">
			<input name="save" type="button" value="<?php esc_attr_e("Save", "search-box");?>" class="button" onclick="submit_form(this, document.forms['form'])" />
			<input name="reset_all" type="button" value="<?php esc_attr_e("Reset to default values", "search-box");?>" class="button" onclick="submit_form(this, document.forms['form'])" />
			<input type="hidden" name="formaction" value="default" />
		</div>
     <script> function submit_form(element, form){ 
				 form['formaction'].value = element.name;
				 form.submit();
			 } </script>
    
		</form>
	<?php }  ?>

<?php
// Escaping input data
function search_box_sanitize($id, $value){
	global $prefix;
	$return = '';
	switch($id){
		case $prefix."_icon_color":
			$return = sanitize_hex_color($value);
		break;
		case $prefix."_width":
			$return = sanitize_text_field($value);
		break;
		case $prefix."_hover_width":
			$return = sanitize_text_field($value);
		break;
	}
	return $return;
}

// Save data
add_action('admin_menu', 'search_box_add_admin');   
function search_box_add_admin() { 
    global $pluginname, $prefix, $options;

    if ( isset($_GET['page']) && $_GET['page'] == basename(__FILE__) ) {

        if ( isset($_REQUEST['formaction']) && 'save' == $_REQUEST['formaction'] ) {
            foreach ($options as $value) {
				if(!isset($value['id'])) continue;

                if( isset( $_REQUEST[ $value['id'] ] ) ) {
					$option = search_box_sanitize($value['id'], $_REQUEST[ $value['id'] ]);
                    update_option( $value['id'], $option );
                }
                else {
                    delete_option( $value['id'] );
                }
            }

			header("Location: options-general.php?page=options.php&saved=true");
            die;
        }
        else if(isset($_REQUEST['formaction']) && 'reset_all' == $_REQUEST['formaction']) {
            foreach ($options as $value) {
                delete_option( $value['id'] );
            }

 			header("Location: options-general.php?page=options.php&".esc_attr($_REQUEST['formaction'])."=true");
            die;
        }
  }

add_options_page( sprintf(__("%s Options", "search-box"), $pluginname), sprintf(__("%s Options", "search-box"), $pluginname), 'edit_plugins', basename(__FILE__), 'search_box_admin'); }

function search_box_admin() { 
    global $pluginname, $prefix, $options;
 
    if (isset($_REQUEST['saved']) && $_REQUEST['saved']) {
        echo '<div id="message" class="updated fade"><p><strong>'.sprintf(__("%s settings saved for this page.", "search-box"), $pluginname).'</strong></p></div>';
    }
    if (isset($_REQUEST['reset_all']) && $_REQUEST['reset_all']) {
        echo '<div id="message" class="updated fade"><p><strong>'.sprintf(__("%s settings reset.", "search-box"), $pluginname).'</strong></p></div>';
    }
    ?>
<div class="wrap">
    <div class="search-box-options">
<?php
    search_box_create_form($options);
?>
    </div><!-- search-box-options -->
</div><!-- wrap -->
<?php } // end function search_box_admin()
?>