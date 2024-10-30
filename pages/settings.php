<?php
$settings = $this->getSettings(); 

?>
<script type="text/javascript" >

function addFormField() {
	var id = document.getElementById("customid").value;
	jQuery("#divTxt").append("<p id='row" + id + "'><label for='txt" + id + "'>Keyword " + id + "&nbsp;&nbsp;<input type='text' size='20' name='name[]' id='name" + id + "'>&nbsp;&nbsp<a href='#' onClick='removeFormField(\"#row" + id + "\"); return false;'>Remove</a><p>");

	id = (id - 1) + 2;
	document.getElementById("customid").value = id;
}

function addFormFieldExists(title) {
	var id = document.getElementById("customid").value;
	jQuery("#divTxt").append("<p id='row" + id + "'><label for='txt" + id + "'>Keyword " + id + "&nbsp;&nbsp;<input type='text' size='20' name='name[]' id='name" + id + "' value='" + title + "'>&nbsp;&nbsp<a href='#' onClick='removeFormField(\"#row" + id + "\"); return false;'>Remove</a><p>");
	id = (id - 1) + 2;
	document.getElementById("customid").value = id;
}

function removeFormField(id) {
	jQuery(id).remove();
}



</script>
<div class="wrap">
	<?php screen_icon(); ?><h2><?php _e( 'Digitalquills Keyword Density Monitor For Wordpress' ); ?></h2>
	<form method="post">
		<h3><?php _e( 'Keyword Settings' ); ?></h3>
		<p><?php printf( __( 'In order for this plugin to work properly, you must setup the keywords you wish to monitor on your site.  ') ); ?></p>
		<table class="form-table">
			<tbody>
				<tr>
					<th scope="row"><label for="keywords_array"><?php _e( 'Keywords to monitor' ); ?></label></th>
					<td>
                        <p><a href="#" onClick="addFormField(); return false;">Add</a></p>
                        <input type="hidden" id="customid" value="1">

                        <p class="meta-options">
                        <!-- we use the following div to display the custom fields -->
                        <div id="divTxt"></div>
                        <!-- end of custom fields layer -->
                        </p>
                        </div>


					</td>
				</tr>
			</tbody>
		</table>
	<p class="submit">
			<input type="submit" class="button-primary" name="save-settings" id="save-settings" value="<?php _e( 'Save Settings' ); ?>" />
			<?php wp_nonce_field( 'save-settings' ); ?>
		</p>
	</form>
</div>

<div style="text-align:center; font-weight:bold#">This plugin was brought to you by Digitalquill, <a href="http://www.digitalquill.co.uk">please click here see our other plugins</a></div>

<?php

    if ($settings['keywords'] != '') {
      $custom_fields = explode('|', $settings['keywords']);
      $t = 0;
      while ($t <= count($custom_fields) ) {
        if ($custom_fields[$t] != ''){
            echo "<script language=javascript>addFormFieldExists('" . $custom_fields[$t] . "')</script>";
        }
        $t++;
      }
    }


?>