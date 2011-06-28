<?php
//auth_redirect();
if ( !current_user_can('manage_easymail_options') ) 	wp_die(__('Cheatin&#8217; uh?'));


// Base link
$link_base = "edit.php?post_type=newsletter&page=alo-easymail/alo-easymail_options.php";

	
global $wp_version, $wpdb, $user_ID, $wp_roles;

// delete welcome setting alert
if ( isset($_REQUEST['timeout_alert']) && $_REQUEST['timeout_alert'] == "stop" ) {
	update_option( 'alo_em_timeout_alert', "hide" ); 
}

// If updating languages list
if ( isset($_POST['langs_list']) && current_user_can('manage_options') ) {
	$new_langs = explode ( ",", stripslashes( trim($_POST['langs_list'])) );
	for ( $i=0; $i < count($new_langs); $i++ ) {
		if ( strlen( trim($new_langs[$i]) ) < 2 ) unset( $new_langs[$i] );
		$new_langs[$i] = alo_em_short_langcode ( trim($new_langs[$i]) );
	}
	$str_langs = implode ( ',', $new_langs );
	$str_langs = rtrim ( $str_langs, "," );
	update_option('alo_em_langs_list', $str_langs );
}

// All available languages
$languages = alo_em_get_all_languages( false );
// Text fields for multilangual customization
$text_fields = array ( "optin_msg", "optout_msg", "lists_msg", "preform_msg", "disclaimer_msg" );


if ( isset($_REQUEST['submit']) ) {
	flush_rewrite_rules( false ); // reset for newsletter permalink 
	
	// -------- Options permitted to all ('manage_easymail_options')
	// Tab TEXTS
	if ( isset($_REQUEST['task']) && $_REQUEST['task'] == "tab_texts" ) {
		$activamail_subj = array();
		$activamail_mail = array();
		$optin_msg	= array();
		$optout_msg	= array();	
		$lists_msg	= array();
		$disclaimer_msg	= array();
		$unsub_footer = array();
		$preform_msg = array();
		$viewonline_msg = array();
		foreach ( $languages as $key => $lang ) {
			if (isset($_POST['activamail_subj_'.$lang]) && trim( $_POST['activamail_subj_'.$lang] ) != "" ) $activamail_subj[$lang] = stripslashes(trim($_POST['activamail_subj_'.$lang]));
			if (isset($_POST['activamail_mail_'.$lang]) && trim( $_POST['activamail_mail_'.$lang] ) != "" ) $activamail_mail[$lang] = stripslashes(trim($_POST['activamail_mail_'.$lang]));
			if (isset($_POST['optin_msg_'.$lang]) )		$optin_msg[$lang] = stripslashes(trim($_POST['optin_msg_'.$lang]));
			if (isset($_POST['optout_msg_'.$lang]) )	$optout_msg[$lang] = stripslashes(trim($_POST['optout_msg_'.$lang]));
			if (isset($_POST['lists_msg_'.$lang]) )		$lists_msg[$lang] = stripslashes(trim($_POST['lists_msg_'.$lang]));
			if (isset($_POST['disclaimer_msg_'.$lang]) ) $disclaimer_msg[$lang] = stripslashes(trim($_POST['disclaimer_msg_'.$lang]));
			if (isset($_POST['unsub_footer_'.$lang]) )	$unsub_footer[$lang] = stripslashes(trim($_POST['unsub_footer_'.$lang]));
			if (isset($_POST['preform_msg_'.$lang]) )	$preform_msg[$lang] = stripslashes(trim($_POST['preform_msg_'.$lang]));
			if (isset($_POST['viewonline_msg_'.$lang]) ) $viewonline_msg[$lang] = stripslashes(trim($_POST['viewonline_msg_'.$lang]));
		}
		if ( count ($activamail_subj) ) update_option('alo_em_txtpre_activationmail_subj', $activamail_subj );
		if ( count ($activamail_mail) ) update_option('alo_em_txtpre_activationmail_mail', $activamail_mail );
		if ( count ($optin_msg) ) 		update_option('alo_em_custom_optin_msg', $optin_msg );
		if ( count ($optout_msg) ) 		update_option('alo_em_custom_optout_msg', $optout_msg );
		if ( count ($lists_msg) ) 		update_option('alo_em_custom_lists_msg', $lists_msg );		
		if ( count ($disclaimer_msg) ) 		update_option('alo_em_custom_disclaimer_msg', $disclaimer_msg );		
		if ( count ($unsub_footer) ) 	update_option('alo_em_custom_unsub_footer', $unsub_footer );
		if ( count ($preform_msg) ) 	update_option('alo_em_custom_preform_msg', $preform_msg );
		if ( count ($viewonline_msg) ) 	update_option('alo_em_custom_viewonline_msg', $viewonline_msg );
	
	}
	// --------
	
	// -------- Options permitted ONLY to ADMIN ('manage_options')
	if ( current_user_can('manage_options') ) {
		// Tab GENERAL
		if ( isset($_REQUEST['task']) && $_REQUEST['task'] == "tab_general" ) {
		
			if(isset($_POST['sender_email'])) update_option('alo_em_sender_email', trim($_POST['sender_email']));
			if(isset($_POST['sender_name'])) update_option('alo_em_sender_name', stripslashes( trim($_POST['sender_name'])) );
			if(isset($_POST['lastposts']) && (int)$_POST['lastposts'] > 0) update_option('alo_em_lastposts', trim($_POST['lastposts']));	
		
			if(isset($_POST['subsc_page']) && (int)$_POST['subsc_page'] ) update_option('alo_em_subsc_page', trim($_POST['subsc_page']));
			if(isset($_POST['debug_newsletters']) && in_array( $_POST['debug_newsletters'], array("","to_author","to_file") ) ) update_option('alo_em_debug_newsletters', $_POST['debug_newsletters']);
		
			if ( isset($_POST['show_subscripage']) ) {
				update_option('alo_em_show_subscripage', "yes");
			} else {
				update_option('alo_em_show_subscripage', "no") ;
			}
			if ( isset($_POST['embed_css']) ) {
				update_option('alo_em_embed_css', "yes");
			} else {
				update_option('alo_em_embed_css', "no") ;
			}
			if ( isset($_POST['credit_banners']) ) {
				update_option('alo_em_show_credit_banners', "yes");
			} else {
				update_option('alo_em_show_credit_banners', "no") ;
			}
			if ( isset($_POST['no_activation_mail']) ) {
				update_option('alo_em_no_activation_mail', "yes");
			} else {
				update_option('alo_em_no_activation_mail', "no") ;
			}				
			/*
			// maybe useless in v.2...
			if ( isset($_POST['filter_br']) ) {
				update_option('alo_em_filter_br', "yes");
			} else {
				update_option('alo_em_filter_br', "no") ;
			}
			*/
			if ( isset($_POST['filter_the_content']) ) {
				update_option('alo_em_filter_the_content', "yes");
			} else {
				update_option('alo_em_filter_the_content', "no") ;
			}
			if ( isset($_POST['js_rec_list']) ) {
				update_option('alo_em_js_rec_list', "yes");
			} else {
				update_option('alo_em_js_rec_list', "no") ;
			}					
			if ( isset($_POST['delete_on_uninstall']) && isset($_POST['delete_on_uninstall_2']) ) {
				update_option('alo_em_delete_on_uninstall', "yes");
			} else {
				update_option('alo_em_delete_on_uninstall', "no") ;
			}
			
		} // end Tab GENERAL

		// Tab BATCH SENDING
		if ( isset($_REQUEST['task']) && $_REQUEST['task'] == "tab_batch" ) {
			if(isset($_POST['dayrate']) && (int)$_POST['dayrate'] >= 300 && (int)$_POST['dayrate'] <= 10000 ) update_option('alo_em_dayrate', trim((int)$_POST['dayrate']));
			if(isset($_POST['batchrate']) && (int)$_POST['batchrate'] >= 10 && (int)$_POST['batchrate'] <= 300 ) update_option('alo_em_batchrate', trim((int)$_POST['batchrate']));
			if(isset($_POST['sleepvalue']) && (int)$_POST['sleepvalue'] <= 5000 ) update_option('alo_em_sleepvalue', trim((int)$_POST['sleepvalue']));
		} // end Tab BATCH SENDING

		// Tab PERMISSIONS
		if ( isset($_REQUEST['task']) && $_REQUEST['task'] == "tab_permissions" ) {				
			// get roles to update cap
			$role_author = get_role( 'author' );
			$role_editor = get_role( 'editor' );
		
			if ( isset($_POST['can_manage_newsletters']) ) {
				switch ( $_POST['can_manage_newsletters'] ) {
					case "editor":
						$role_editor->add_cap( 'manage_easymail_newsletters' );
						$role_editor->add_cap( 'send_easymail_newsletters' );
						break;
					case "administrator":
					default:
						$role_editor->remove_cap( 'manage_easymail_newsletters' );
				}
			}		
			if ( isset($_POST['can_send_newsletters']) ) {	
				switch ( $_POST['can_send_newsletters'] ) {
					case "author":
						$role_author->add_cap( 'send_easymail_newsletters' );
						$role_editor->add_cap( 'send_easymail_newsletters' );				
						break;
					case "editor":
						$role_editor->add_cap( 'send_easymail_newsletters' );
						$role_author->remove_cap( 'send_easymail_newsletters' );	
						break;
					case "administrator":
					default:
						$role_author->remove_cap( 'send_easymail_newsletters' );
						$role_editor->remove_cap( 'send_easymail_newsletters' );
						$role_editor->remove_cap( 'manage_easymail_newsletters' );
				}
			}
			if ( isset($_POST['can_manage_subscribers']) ) {
				switch ( $_POST['can_manage_subscribers'] ) {
					case "editor":
						$role_editor->add_cap( 'manage_easymail_subscribers' );
						break;
					case "administrator":
					default:
						$role_editor->remove_cap( 'manage_easymail_subscribers' );
				}
			}
			if ( isset($_POST['can_manage_options']) ) {
				switch ( $_POST['can_manage_options'] ) {
					case "editor":
						$role_editor->add_cap( 'manage_easymail_options' );
						break;
					case "administrator":
					default:
						$role_editor->remove_cap( 'manage_easymail_options' );
				}
			}
			//echo "<pre style='font-size:80%'>";print_r($wp_roles);echo "</pre>";	
		} // end Tab PERMISSIONS
		
	} // end if Submit
	// --------
    echo '<div id="message" class="updated fade"><p>'. __("Updated", "alo-easymail") .'</p></div>';
}?>

<script type="text/javascript">
	var $em = jQuery.noConflict();
	$em(document).ready(function(){
		$em('#easymail_slider').tabs({ fx: { opacity: 'toggle', duration:'fast' }  });
		$em('#activamail_container').tabs();
		<?php 
		foreach ( $text_fields as $text_field ) {
			echo '$em(\'#'.$text_field.'_container\').tabs();'."\n";
		} ?>
		$em('#listname_container').tabs();
		$em('#unsub_footer_container').tabs();	
		$em('#viewonline_msg_container').tabs();	
	});
</script>

<style type="text/css">
	.text-alert { background-color:#FFFFE0;	-moz-border-radius:3px 3px 3px 3px;	border: 1px solid #E6DB55; padding:0 0.6em;	}
	.text-alert p {	padding:0; margin:0.5em 0; }	
</style>
		
<!--<div class="wrap">-->


<div id="easymail_slider" class="wrap">
<div class="icon32" id="icon-options-general"><br></div>
<h2>Alo EasyMail Newsletter Options</h2>

<ul id="tabs">
	<?php if ( current_user_can('manage_options') ) echo '<li><a href="#general">' . __("General", "alo-easymail") .'</a></li>'; ?>
	<li><a href="#texts"><?php _e("Texts", "alo-easymail") ?></a></li>
	<?php if ( current_user_can('manage_options') ) echo '<li><a href="#batchsending">' . __("Batch sending", "alo-easymail") .'</a></li>'; ?>
	<?php if ( current_user_can('manage_options') ) echo '<li><a href="#permissions">' . __("Permissions", "alo-easymail") .'</a></li>'; ?>
	<li><a href="#mailinglists"><?php _e("Mailing Lists", "alo-easymail") ?></a></li>
</ul>


<!-- --------------------------------------------
GENERAL
--------------------------------------------  -->

<?php if ( current_user_can('manage_options') ) : /* only admin can */ ?>

<div id="general">

<form action="#general" method="post">
<h2><?php _e("General", "alo-easymail") ?></h2>

<table class="form-table"><tbody>
<tr valign="top">
<th scope="row"><label for="lastposts"><?php _e("Number of last posts to display", "alo-easymail") ?>:</label></th>
<td><input type="text" name="lastposts" value="<?php echo get_option('alo_em_lastposts') ?>" id="lastposts" size="2" maxlength="2" />
<span class="description"><?php _e("Number of recent posts to show in the dropdown list of the newsletter sending form", "alo-easymail");?></span></td>
</tr>

<tr valign="top">
<th scope="row"><label for="sender_email"><?php _e("Sender's email address", "alo-easymail") ?>:</label></th>
<td><input type="text" name="sender_email" value="<?php echo get_option('alo_em_sender_email') ?>" id="sender_email" size="30" maxlength="100" /></td>
</tr>

<tr valign="top">
<th scope="row"><label for="sender_name"><?php _e("Sender's name", "alo-easymail") ?>:</label></th>
<td><input type="text" name="sender_name" value="<?php esc_attr_e( get_option('alo_em_sender_name') ) ?>" id="sender_name" size="30" maxlength="100" /></td>
</tr>

<?php 
if ( get_option('alo_em_subsc_page') ) {
	$selected_subscripage = get_option('alo_em_subsc_page');
} else {
	$selected_subscripage = "";
}
?>
<tr valign="top">
<th scope="row"><?php _e("Subscription page", "alo-easymail") ?>:</th>
<td>
<?php
$args = array(
	'numberposts' => -1,
	'post_type' => 'page',
	'order' => 'ASC',
	'orderby' => 'title'
); 
$get_pages = get_posts($args);
if ( count($get_pages) ) {
	echo "<select name='subsc_page' id='subsc_page'>";
	echo "<option value=''> </option>";
	foreach($get_pages as $page) :
		echo "<option value='".$page->ID."' ". ( ($page->ID == $selected_subscripage)? " selected='selected'": "") .">#". $page->ID ." ". get_the_title ($page->ID) ." </option>";
	endforeach;
	echo "</select>\n";
}
?>
<br /><span class="description"><?php _e("This should be the page that includes the [ALO-EASYMAIL-PAGE] shortcode. By default, this page is titled &#39;Newsletter&#39;", "alo-easymail") ?>.</span></td>
</tr>


<?php 
if ( get_option('alo_em_show_subscripage') == "yes" ) {
	$checked_show_subscripage = 'checked="checked"';
} else {
	$checked_show_subscripage = "";
}
//$subcripage_link = "<a href='" . get_permalink(get_option('alo_em_subsc_page')) . "'>" . get_the_title (get_option('alo_em_subsc_page')) . "</a>";
?>
<tr valign="top">
<th scope="row"><?php _e("Show subscription page", "alo-easymail") ?>:</th>
<td><input type="checkbox" name="show_subscripage" id="show_subscripage" value="yes" <?php echo $checked_show_subscripage ?> /> <span class="description"><?php _e("If yes, the subscription page appears in menu or widget that list all blog pages", "alo-easymail") ?>.</span></td>
</tr>

<?php 
if ( get_option('alo_em_embed_css') == "yes" ) {
	$checked_embed_css = 'checked="checked"';
} else {
	$checked_embed_css = "";
}
?>
<tr valign="top">
<th scope="row"><?php _e("Embed CSS file", "alo-easymail") ?>:</th>
<td><input type="checkbox" name="embed_css" id="embed_css" value="yes" <?php echo $checked_embed_css ?> /> <span class="description"><?php _e("If yes, the plugin loads the CSS styles from a file in its directory", "alo-easymail") ?>. <?php _e("Tip: copy &#39;alo-easymail.css&#39; to your theme directory and edit it there. Useful to prevent the loss of styles when you upgrade the plugin", "alo-easymail") ?>.</span></td>
</tr>

<?php 
if ( get_option('alo_em_no_activation_mail') == "yes" ) {
	$checked_embed_css = 'checked="checked"';
} else {
	$checked_embed_css = "";
}
?>
<tr valign="top">
<th scope="row"><?php _e("Disable activation e-mail", "alo-easymail") ?>:</th>
<td><input type="checkbox" name="no_activation_mail" id="no_activation_mail" value="yes" <?php echo $checked_embed_css ?> /> <span class="description"><?php _e("If yes, a new subscriber is automatically activated without confirmation e-mail", "alo-easymail") ?>.</span></td>
</tr>

<?php
/*
// maybe useless in v.2...
if ( get_option('alo_em_filter_br') != "no" ) {
	$checked_filter_br = 'checked="checked"';
} else {
	$checked_filter_br = "";
}
*/
if ( get_option('alo_em_filter_the_content') != "no" ) {
	$checked_filter_the_content = 'checked="checked"';
} else {
	$checked_filter_the_content = "";
}
?>
<tr valign="top">
<th scope="row"><?php _e("Filters to the newsletter text", "alo-easymail") ?>:</th>
<td>
<input type="checkbox" name="filter_the_content" id="filter_the_content" value="yes" <?php echo $checked_filter_the_content ?> /><span class="description"> <?php esc_html_e(__("Apply 'the_content' filters and shortcodes to newsletter content", "alo-easymail")) ?></span>
</td>
</tr>


<?php
if ( get_option('alo_em_js_rec_list') == "yes" ) {
	$checked_js_rec_list = 'checked="checked"';
} else {
	$checked_js_rec_list = "";
}
?>
<tr valign="top">
<th scope="row"><?php _e("Load only plugin javascript on list generation screen", "alo-easymail") ?>:</th>
<td>
<input type="checkbox" name="js_rec_list" id="js_rec_list" value="yes" <?php echo $checked_js_rec_list ?> /> <span class="description"><?php _e("Load only plugin javascript files on list of recipients thickbox", "alo-easymail" ) ?>. <?php _e("Useful to prevent conflicts with javascripts of other plugins", "alo-easymail" ) ?>.</span>
</td>
</tr>


<?php  
if ( get_option('alo_em_debug_newsletters') ) {
	$selected_debug_newsletters = get_option('alo_em_debug_newsletters');
} else {
	$selected_debug_newsletters = "";
}
?>
<tr valign="top">
<th scope="row"><?php _e("Debug newsletters", "alo-easymail") ?>:</th>
<td>
<select name='debug_newsletters' id='debug_newsletters'>";
	<option value=''><?php _e("no", "alo-easymail") ?></option>
	<?php $values_debug_newsletters = array ( "to_author" => __("send all emails to the author", "alo-easymail"), "to_file" => __("put all emails into a log file", "alo-easymail") );
	foreach( $values_debug_newsletters as $key => $label ) :
		echo "<option value='$key' ". ( ( $key == $selected_debug_newsletters )? " selected='selected'": "") .">$label</option>";
	endforeach; ?>
</select>
<br /><span class="description"><?php _e("If you choose a debug mode the newsletters won&#39;t be sent to the selected recipients", "alo-easymail") ?>:<br />
<ul style="margin-left:20px;font-size:90%">
<li><code><?php _e("send all emails to the author", "alo-easymail") ?></code>: <?php _e("all messages will be sent to the newsletter author", "alo-easymail") ?>.</li>
<li><code><?php _e("put all emails into a log file", "alo-easymail") ?></code>: <?php _e("all messages will be recorded into a log file", "alo-easymail") ?> 
(<?php printf( __("called %s and saved in %s", "alo-easymail"), "&quot;user_{AUTHOR-ID}_newsletter_{NEWSLETTER-ID}.log&quot;", "&quot;".WP_CONTENT_DIR."&quot;" ) ?>): <?php _e("the log file is accessible on your server and contains personal information so you have to delete it as soon as possible!", "alo-easymail") ?></li>
</ul>
</span></td>
</tr>

<?php 
if ( get_option('alo_em_show_credit_banners') == "yes" ) {
	$checked_credit_banners = 'checked="checked"';
} else {
	$checked_credit_banners = "";
}
?>
<tr valign="top">
<th scope="row"><?php _e("Show credit banners in back-end", "alo-easymail") ?>:</th>
<td><input type="checkbox" name="credit_banners" id="credit_banners" value="yes" <?php echo $checked_credit_banners ?> /> <span class="description"><?php _e("You are free to hide the credits, but in that case it's a common practice to make a small donatation via Paypal to the plugin author", "alo-easymail") ?>.</span></td>
</tr>

<?php 
if ( get_option('alo_em_delete_on_uninstall') == "yes" ) {
	$checked_delete_on_uninstall = 'checked="checked"';
} else {
	$checked_delete_on_uninstall = "";
}
?>
<tr valign="top">
<th scope="row"><?php _e("Delete all plugin data on deactivation", "alo-easymail") ?>:</th>
<td><span class="description"><?php _e("On plugin deactivation, all plugin options, preferences and database tables (including all newsletters and subscribers data) will be definitely deleted", "alo-easymail");?>. <?php _e("If you need these data make sure you do a database backup before plugin deactivation", "alo-easymail");?>.</span><br />
<input type="checkbox" name="delete_on_uninstall" id="delete_on_uninstall" value="yes" <?php echo $checked_delete_on_uninstall ?> /><label for="delete_on_uninstall"> <?php _e("Delete all plugin data on deactivation", "alo-easymail") ?></label><br />
<input type="checkbox" name="delete_on_uninstall_2" id="delete_on_uninstall_2" value="yes" <?php echo $checked_delete_on_uninstall ?> /><label for="delete_on_uninstall_2"> <?php _e("Yes, I understand", "alo-easymail") ?>. <?php _e("Delete all plugin data on deactivation", "alo-easymail") ?></label>
</td>
</tr>

</tbody> </table>

<p class="submit">
<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input type="hidden" name="task" value="tab_general" /> <?php // reset task ?>
<!--<span id="autosave"></span>-->
<input type="submit" name="submit" value="<?php _e('Update', 'alo-easymail') ?>" class="button-primary" />
</p>
</form>

</div> <!-- end general -->

<?php endif; /* only admin can */ ?>

<!-- --------------------------------------------
TEXTS
--------------------------------------------  -->

<div id="texts">

<form action="#texts" method="post">
<h2><?php _e("Texts", "alo-easymail") ?></h2>

<table class="form-table"><tbody>

<?php
if ( alo_em_multilang_enabled_plugin() == false ) {
	echo '<tr valign="top">';
	echo '<td colspan="2">';
		echo '<div class="text-alert">';
		echo '<p>'. __('No multilanguage plugin is enabled, so you will only see texts in the main language of the site', 'alo-easymail') .'.</p>';
		echo '<p>'. __('Recommended plugins, fully compatible with EasyMail, for a complete multilingual functionality', 'alo-easymail') .': ';
		echo '<a href="http://wordpress.org/extend/plugins/qtranslate/" target="_blank">qTranslate</a>';
		echo '.</p>';
		//echo '<p>'. sprintf( __('Type the texts in all available languages (they are found in %s)', 'alo-easymail'), '<em>'.WP_LANG_DIR.'</em>' ) .".</p>";
		echo '<p>'. __('If you like here you can list the languages available', 'alo-easymail') .':<br />';
		$langs_list = ( get_option( 'alo_em_langs_list' ) != "" ) ? get_option( 'alo_em_langs_list' ) : "";
		echo '<input type="text" name="langs_list" value="' . $langs_list .'"  />';
		echo '<input type="submit" name="submit" value="'. __('Update', 'alo-easymail') .'" class="button" /> ';
		echo '<span class="description">'. __('List of two-letter language codes separated by commas', 'alo-easymail'). ' ('. sprintf( '<a href="http://en.wikipedia.org/wiki/List_of_ISO_639-1_codes" target="_blank">%s</a>', __('iso 639-1 codes', 'alo-easymail') ) . '). '. __('Sample:', 'alo-easymail') .' en,de,it</span>';
		echo '</p>';
		echo '<p>'. __("The plugin looks for the subscriber&#39;s language in the browser setting and sends the e-mail accordingly", 'alo-easymail') . '.</p>';
		echo '<p>'. __('If you are not using a multilanguage site ignore this piece of information', 'alo-easymail') .'.</p>';
		
		echo '</div>';
	echo '</td></tr>';
}
?>


<tr valign="top">
<th scope="row">
<h4><?php _e("Widget/Page Texts", "alo-easymail") ?></h4>
</th><td></td>
</tr>


<?php 
// Texts fields

foreach ( $text_fields as $text_field ) : ?>
	
	<tr valign="top">
	<th scope="row">
	<?php 
	switch ($text_field) {
		case "optin_msg": 	_e("Optin message", "alo-easymail"); break;
		case "optout_msg": 	_e("Optout message", "alo-easymail"); break;
		case "lists_msg": 	_e("Invite to join mailing lists", "alo-easymail"); break;				
		case "disclaimer_msg": 	_e("Policy claim", "alo-easymail"); break;	
		case "preform_msg": 	_e("Top claim", "alo-easymail"); break;	
	}
	?>:
	</th>
	<td><span class="description"><?php _e("Leave blank to use default text", "alo-easymail") ?>:</span>
	<?php 
	switch ($text_field) {
		case "optin_msg": 	_e("Yes, I would like to receive the Newsletter", "alo-easymail"); break;
		case "optout_msg": 	_e("No, please do not email me", "alo-easymail"); break;
		case "lists_msg": 	_e("You can also sign up for specific lists", "alo-easymail"); break;
		case "disclaimer_msg": 
			echo "(". __("empty", "alo-easymail"). ") ";
			echo '<br /><span class="description">'. __("If filled in it will appear at the bottom of widget/page. Useful to show/link more info about privacy", "alo-easymail"). '.</span>';  
			break;			
		case "preform_msg": 
			echo "(". __("empty", "alo-easymail"). ") ";
			echo '<br /><span class="description">'. __("If filled in it will appear at the top of widget/page. Useful to invite to subscribe", "alo-easymail"). '.</span>';  
			break;							
	}
	?>
	<div id="<?php echo $text_field ?>_container">
	
	<?php
	$custom_texts 	= get_option( 'alo_em_custom_'. $text_field );
	
	// Set tabs and fields
	if ( $languages ) {
		$lang_li = array();
		$lang_div = array();	
		foreach ( $languages as $key => $lang ) {
			$lang_li[$lang] = '<li><a href="#'.$text_field.'_div_'.$lang.'"><strong>' . alo_em_get_lang_name( $lang ) .'</strong></a></li>';
			$lang_div[$lang] = '<div id ="'.$text_field.'_div_'.$lang.'">';
			$lang_text = ( !empty( $custom_texts[$lang] ) ) ? esc_attr($custom_texts[$lang]) : "";
			$lang_div[$lang] .= '<input type="text" name="'.$text_field.'_'.$lang.'" value="' . $lang_text .'" id="'.$text_field.'_'.$lang.'" maxlength="100" style="width:100%" />';
			$lang_div[$lang] .= '</div>';
		}
	}
	?>
	<ul id="<?php echo $text_field?>_tabs">
	<?php echo implode ( "\n\n", $lang_li); ?>
	</ul>

<?php echo implode ( "\n\n", $lang_div);?>
</div>

</td>
</tr>

<?php endforeach; // text_fields
?>



<tr valign="top">
<th scope="row">
<h4><?php _e("Communications", "alo-easymail") ?></h4>
</th><td></td>
</tr>

<tr valign="top">
<th scope="row"><?php _e("Activation e-mail", "alo-easymail") ?>:</th>
<td><span class="description"><?php _e("Leave blank to use default text", "alo-easymail") ?>.</span>
<div id="activamail_container">
	<?php 
	$subjects = get_option( 'alo_em_txtpre_activationmail_subj' );
	$mails = get_option( 'alo_em_txtpre_activationmail_mail' );
	// Set tabs and fields
	if ( $languages ) {
		$lang_li = array();
		$lang_div = array();	
		foreach ( $languages as $key => $lang ) {
			$lang_li[$lang] = '<li><a href="#activamail_div_'.$lang.'">'. /*alo_em_get_lang_flag($lang, false) .*/ ' <strong>' . alo_em_get_lang_name( $lang ) .'</strong>';
			$lang_li[$lang] .= '</a></li>';
			$lang_div[$lang] = '<div id ="activamail_div_'.$lang.'"><span class="description">'. __("Subject", "alo-easymail") .'</span><br />';
			$lang_subj = ( !empty($subjects[$lang]) ) ? esc_attr($subjects[$lang]) : "";
			$lang_div[$lang] .= '<input type="text" name="activamail_subj_'.$lang.'" value="' . $lang_subj .'" id="activamail_subj_'.$lang.'" maxlength="100" style="width:100%;margin-bottom:8px" /><br />';
			$lang_mail = ( !empty($mails[$lang]) ) ? esc_html($mails[$lang]) : "";
			$lang_div[$lang] .= '<span class="description">'.__("Main body", "alo-easymail").'</span><br /><textarea name="activamail_mail_'.$lang.'" rows="6" style="width:100%" />' . $lang_mail .'</textarea>';			
			$lang_div[$lang] .= '</div>';
		}
	}
	?>
	<ul id="activamail_tabs">
	<?php echo implode ( "\n\n", $lang_li); ?>
	</ul>

<?php echo implode ( "\n\n", $lang_div);?>
</div>

<p><?php _e("You can use the following tags", "alo-easymail");?>:</p>
<ul style="margin-left:20px">
	<li><code>%BLOGNAME%</code>: <?php _e("the blog name", "alo-easymail");?></li>
	<li><code>%NAME%</code>: <?php _e("the subscriber name", "alo-easymail");?></li>
	<li><code>%ACTIVATIONLINK%</code>: <?php _e("the url that the new subscriber must click/visit to confirm the subscription", "alo-easymail");?></li>
</ul>	
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e("Unsubscription disclaimer", "alo-easymail") ?>:</th>
<td><span class="description"><?php _e("Leave blank to use default text", "alo-easymail") ?>:</span><br />
<?php
echo "&lt;p&gt;&lt;em&gt;". __("You have received this message because you subscribed to our newsletter. If you want to unsubscribe: ", "alo-easymail")." ";
echo __("visit this link", "alo-easymail") ."&lt;br /&gt; %UNSUBSCRIBELINK%";
echo "&lt;/em&gt;&lt;/p&gt;";
?>
<div id="unsub_footer_container">
	<?php 
	$custom_texts = get_option( 'alo_em_custom_unsub_footer' );
	if ( $languages ) {
		$lang_li = array();
		$lang_div = array();	
		foreach ( $languages as $key => $lang ) {
			$lang_li[$lang] = '<li><a href="#unsub_footer_div_'.$lang.'"><strong>' . alo_em_get_lang_name( $lang ) .'</strong></a></li>';
			$lang_div[$lang] = '<div id ="unsub_footer_div_'.$lang.'">';
			$lang_text = ( !empty( $custom_texts[$lang] ) ) ? esc_html($custom_texts[$lang]) : "";
			$lang_div[$lang] .= '<textarea name="unsub_footer_'.$lang.'" rows="3" style="width:100%" />' . $lang_text .'</textarea>';
			$lang_div[$lang] .= '</div>';
		}
	}
	?>
	<ul id="unsub_footer_tabs">
	<?php echo implode ( "\n\n", $lang_li); ?>
	</ul>

<?php echo implode ( "\n\n", $lang_div);?>
</div>

<p><?php _e("You can use the following tags", "alo-easymail");?>:</p>
<ul style="margin-left:20px">
	<li><code>%BLOGNAME%</code>: <?php _e("the blog name", "alo-easymail");?></li>
	<li><code>%UNSUBSCRIBELINK%</code>: <?php _e("the url that the new subscriber must click/visit to unsubscribe the newsletter", "alo-easymail");?></li>
</ul>	
</td>
</tr>


<tr valign="top">
<th scope="row"><?php _e("Read newsletter online", "alo-easymail") ?>:</th>
<td><span class="description"><?php _e("Leave blank to use default text", "alo-easymail") ?>:</span><br />
<?php
echo "&lt;p&gt;&lt;em&gt;". __("To read the newsletter online you can visit this link", "alo-easymail") .": %NEWSLETTERLINK% &lt;/em&gt;&lt;/p&gt;";
?>
<div id="viewonline_msg_container">
	<?php 
	$custom_texts = get_option( 'alo_em_custom_viewonline_msg' );
	if ( $languages ) {
		$lang_li = array();
		$lang_div = array();	
		foreach ( $languages as $key => $lang ) {
			$lang_li[$lang] = '<li><a href="#viewonline_msg_div_'.$lang.'"><strong>' . alo_em_get_lang_name( $lang ) .'</strong></a></li>';
			$lang_div[$lang] = '<div id ="viewonline_msg_div_'.$lang.'">';
			$lang_text = ( !empty( $custom_texts[$lang] ) ) ? esc_html($custom_texts[$lang]) : "";
			$lang_div[$lang] .= '<textarea name="viewonline_msg_'.$lang.'" rows="3" style="width:100%" />' . $lang_text .'</textarea>';
			$lang_div[$lang] .= '</div>';
		}
	}
	?>
	<ul id="viewonline_msg_tabs">
	<?php echo implode ( "\n\n", $lang_li); ?>
	</ul>

<?php echo implode ( "\n\n", $lang_div);?>
</div>

<p><?php _e("You can use the following tags", "alo-easymail");?>:</p>
<ul style="margin-left:20px">
	<li><code>%NEWSLETTERLINK%</code>: <?php _e("the newsletter web url", "alo-easymail");?></li>
</ul>	
</td>
</tr>


</tbody> </table>
    
<p class="submit">
<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input type="hidden" name="task" value="tab_texts" /> <?php // reset task ?>
<!--<span id="autosave"></span>-->
<input type="submit" name="submit" value="<?php _e('Update', 'alo-easymail') ?>" class="button-primary" />
</p>
</form>

</div> <!-- end Texts -->


<!-- --------------------------------------------
BATCH SENDING
--------------------------------------------  -->

<?php if ( current_user_can('manage_options') ) : /* only admin can */ ?>

<div id="batchsending">

<form action="#batchsending" method="post">
<h2><?php _e("Batch sending", "alo-easymail") ?></h2>


<table class="form-table"><tbody>

<?php
if ( defined( 'ALO_EM_DAYRATE' ) || defined( 'ALO_EM_BATCHRATE' ) || defined( 'ALO_EM_SLEEPVALUE' ) ) {
	echo '<tr valign="top">';
	echo '<td colspan="2">';
		echo '<div class="text-alert">';
		echo '<p>'. sprintf( __('Some parameters are already setted up in %s, so the values below could be ignored', 'alo-easymail'), '<em>wp-config.php</em>')  .'.</p>';
		echo '</div>';
	echo '</td></tr>';
}
?>

<tr valign="top">
<th scope="row"><label for="dayrate"><?php _e("Maximum number of emails that can be sent in a 24-hr period", "alo-easymail") ?>:</label></th>
<td><input type="text" name="dayrate" value="<?php echo get_option('alo_em_dayrate') ?>" id="dayrate" size="5" maxlength="5" />
<span class="description">(300 - 10000)</span></td>
</tr>

<tr valign="top">
<th scope="row"><label for="batchrate"><?php _e("Maximum number of emails that can be sent per batch", "alo-easymail") ?>:</label></th>
<td><input type="text" name="batchrate" value="<?php echo get_option('alo_em_batchrate') ?>" id="batchrate" size="5" maxlength="3" />
<span class="description">(10 - 300)</span></td>
</tr>

<tr valign="top">
<th scope="row"><label for="sleepvalue"><?php _e("Interval between emails in a single batch, in milliseconds", "alo-easymail") ?>:</label></th>
<td><input type="text" name="sleepvalue" value="<?php echo (int)get_option('alo_em_sleepvalue') ?>" id="sleepvalue" size="5" maxlength="4" />
<span class="description">(0 - 5000) <?php _e("Default", "alo-easymail") ?>: 0.<br /><?php _e("Usually you do not have to modify this value", "alo-easymail") ?>. <?php _e("It is useful if your provider allows a maximum number of emails that can be sent per second or minute", "alo-easymail") ?>. <?php _e("The higher this value, the lower the number of emails sent for each batch", "alo-easymail") ?>. </span></td>
</tr>

</tbody> </table>

<div style="background-color:#ddd;margin-top:15px;padding:10px 20px 15px 20px"><h4><?php _e("Important advice to calculate the best limit", "alo-easymail") ?></h4>
<ol style="font-size:80%;">
	<li><?php _e("Ask your provider the cut-off of emails you can send per day. Multiplying the hourly limit by 24 is not the right way to calculate it: very often the resulting number is much higher than the actual cut-off.", "alo-easymail") ?></li>
	<li><?php _e("Subtract from this cut-off the number of emails you want to send from your blog (e.g. registration procedures, activation and unsubscribing of EasyMail, notices from other plugins etc.).", "alo-easymail") ?></li>
	<li><?php _e("If in doubt, just choose a number definitely lower than the cut-off: you'll have more chances to have your mail delivered, and less chances to end up in a blacklist...", "alo-easymail") ?></li>
	<li><?php _e("For more info, visit the FAQ of the site.", "alo-easymail") ?> <a href="http://www.eventualo.net/blog/wp-alo-easymail-newsletter-faq/#faq-8" target="_blank" title="<?php _e("For more info, visit the FAQ of the site.", "alo-easymail") ?>">&raquo;</a></li>      	  
</ol>
</div>

<p class="submit">
<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input type="hidden" name="task" value="tab_batch" /> <?php // reset task ?>
<!--<span id="autosave"></span>-->
<input type="submit" name="submit" value="<?php _e('Update', 'alo-easymail') ?>" class="button-primary" />
</p>
</form>

</div> <!-- end Batch sending -->

<?php endif; /* only admin can */ ?>

<!-- --------------------------------------------
PERMISSIONS
--------------------------------------------  -->

<?php if ( current_user_can('manage_options') ) : /* only admin can */ ?>

<?php // load roles names
$rolenames = $wp_roles->get_names(); // get a list of values, containing pairs of: $role_name => $display_name
// get roles to check cap
$get_author = get_role( 'author' );
$get_editor = get_role( 'editor' );
?>
<div id="permissions">

<form action="#permissions" method="post">
<h2><?php _e("Permissions", "alo-easymail") ?></h2>

<table class="form-table"><tbody>
<tr valign="top">
<th scope="row"><?php _e("The lowest role can send newsletters", "alo-easymail") ?>:</th>
<td>
<?php
/*
if ( $get_author->has_cap ('send_easymail_newsletters') ) {
	$selected_editor	= "";
	$selected_author	= "selected='selected'";
	$selected_admin		= "";
} else if ( $get_editor->has_cap ('send_easymail_newsletters') ) {
	$selected_editor	= "selected='selected'";
	$selected_author	= "";
	$selected_admin		= "";
} else { // admin
	$selected_editor	= "";
	$selected_author	= "";
	$selected_admin		= "selected='selected'";
}
*/
?>
<!--
<select name="can_send_newsletters" id="can_send_newsletters">
	<option value='admin' <?php echo $selected_admin; ?> ><?php echo translate_user_role ($rolenames['administrator']) ?> </option>
	<option value='editor' <?php echo $selected_editor; ?> ><?php echo translate_user_role ($rolenames['editor']) ?> </option>
	<option value='author' <?php echo $selected_author; ?> ><?php echo translate_user_role ($rolenames['author']) ?> </option>
</select><br />
-->
<span class="description">
	<?php printf( __("The authorised roles are the same with the %s capability", "alo-easymail"), "<code>edit_post</code>" ); ?>.<br />
	<?php _e("The user with this capability can manage own newsletters (view the report, delete)", "alo-easymail") ?>.
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e("The lowest role can manage newsletters", "alo-easymail") ?>:</th>
<td>
<?php 
/*
if ( $get_editor->has_cap ('manage_easymail_newsletters') ) {
	$selected_editor	= "selected='selected'";
	$selected_admin		= "";
} else { // admin
	$selected_editor	= "";
	$selected_admin		= "selected='selected'";
}
*/
?>
<!--
<select name="can_manage_newsletters" id="can_manage_newsletters">
	<option value='admin' <?php echo $selected_admin; ?> ><?php echo translate_user_role ($rolenames['administrator']) ?> </option>
	<option value='editor' <?php echo $selected_editor; ?> ><?php echo translate_user_role ($rolenames['editor']) ?> </option>
</select><br />
-->
<span class="description">
	<?php printf( __("The authorised roles are the same with the %s capability", "alo-easymail"), "<code>edit_posts</code>" ); ?>.<br />
	<?php _e("The user with this capability can manage newsletters of all users (view the report, delete)", "alo-easymail") ?>.
	<?php // _e("Note: to let a user manage newsletters of other users, this user must have the capability to manage subscribers too", "alo-easymail") ?>.
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e("The lowest role can manage subscribers", "alo-easymail") ?>:</th>
<td>
<?php 
if ( $get_editor->has_cap ('manage_easymail_subscribers') ) {
	$selected_editor	= "selected='selected'";
	$selected_admin		= "";
} else { // admin
	$selected_editor	= "";
	$selected_admin		= "selected='selected'";
}
?>
<select name="can_manage_subscribers" id="can_manage_subscribers">
	<option value='admin' <?php echo $selected_admin; ?> ><?php echo translate_user_role ($rolenames['administrator']) ?> </option>
	<option value='editor' <?php echo $selected_editor; ?> ><?php echo translate_user_role ($rolenames['editor']) ?> </option>
</select>
<br />
<span class="description"> <?php _e("The user with this capability can manage subscribers (add, delete, assign to mailing lists...)", "alo-easymail") ?>.
</span>
</td>
</tr>

<tr valign="top">
<th scope="row"><?php _e("The lowest role can manage options", "alo-easymail") ?>:</th>
<td>
<?php 
if ( $get_editor->has_cap ('manage_easymail_options') ) {
	$selected_editor	= "selected='selected'";
	$selected_admin		= "";
} else { // admin
	$selected_editor	= "";
	$selected_admin		= "selected='selected'";
}
?>
<select name="can_manage_options" id="can_manage_options">
	<option value='admin' <?php echo $selected_admin; ?> ><?php echo translate_user_role ($rolenames['administrator']) ?> </option>
	<option value='editor' <?php echo $selected_editor; ?> ><?php echo translate_user_role ($rolenames['editor']) ?> </option>
</select><br />
<span class="description"> <?php _e("The user with this capability can set up these setting sections", "alo-easymail") ?>: 
<?php _e("Texts", "alo-easymail") ?>, 
<?php _e("Mailing Lists", "alo-easymail") ?>.<br />
<?php _e("Other sections can be modified only by administrators", "alo-easymail") ?>.
</span>
</td>
</tr>

</tbody> </table>

<p class="submit">
<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID ?>" />
<input type="hidden" name="task" value="tab_permissions" /> <?php // reset task ?>
<input type="submit" name="submit" value="<?php _e('Update', 'alo-easymail') ?>"  class="button-primary" />
</p>
</form>

</div> <!-- end permissions -->

<?php endif; /* only admin can */ ?>

<!-- --------------------------------------------
MAILING LISTS 
--------------------------------------------  -->
<div id="mailinglists">

<h2><?php _e("Mailing Lists", "alo-easymail"); ?></h2>

<?php //echo "<pre style='font-size:80%'>"; print_r( $_REQUEST ); echo "</pre>"; // DEBUG ?>

<?php 
// If exists, get the id list to work on	
if ( isset( $_REQUEST['list_id'] ) ) {
	$list_id = stripslashes ( $wpdb->escape ( $_REQUEST['list_id'] ) );
	if ( !is_numeric ( $list_id ) ) $list_id = false;
} else {
	$list_id = false;
}
	
// Updating Request...
if ( isset( $_REQUEST['task'] ) ) {
	switch ( $_REQUEST['task'] ) {
		case "edit_list":	// EDIT an existing Mailing list
			if ( $list_id ) {
				$mailinglists = alo_em_get_mailinglists ( 'hidden,admin,public' );
				$list_name = $mailinglists [$list_id]["name"];
				$list_available = $mailinglists [$list_id]["available"];	
				$list_order = $mailinglists [$list_id]["order"];		
			} else {
				echo '<div id="message" class="error"><p>'. __("Error during operation.", "alo-easymail") .'</p></div>';
			}				
			break;
		case "save_list":	// SAVE a mailing list (add or update)
			if ( isset($_REQUEST['submit_list']) ) {
				//$list_name = stripslashes( trim( $_POST['elp_list_name'] ) );
				
				// List name	
				$list_name	= array();
				foreach ( $languages as $key => $lang ) {
					if (isset($_POST['listname_'.$lang]) )	$list_name[$lang] = stripslashes(trim($_POST['listname_'.$lang]));
				}
				
				$list_available = stripslashes( trim( $_POST['elp_list_available'] ) );
				$list_order = stripslashes( trim( $_POST['elp_list_order'] ) );
				if ( $list_name && $list_available && is_numeric($list_order) ) {
					$mailinglists = alo_em_get_mailinglists ( 'hidden,admin,public' );
					if ( $list_id )  { // update
						$mailinglists [$list_id] = array ( "name" => $list_name, "available" => $list_available, "order" => $list_order );
					} else { // or add a new
						if ( empty($mailinglists) ) { // if 1st list, skip index 0
							$mailinglists [] = array ( "name" => "not-used", "available" => "deleted", "order" => "");
						}	
						$mailinglists [] = array ( "name" => $list_name, "available" => $list_available, "order" => $list_order);
					}
					if ( alo_em_save_mailinglists ( $mailinglists ) ) {
						unset ( $list_id );
						unset ( $list_name );
						unset ( $list_available );						
						unset ( $list_order );	
						echo '<div id="message" class="updated fade"><p>'. __("Updated", "alo-easymail") .'</p></div>';
					} else {
						echo '<div id="message" class="error"><p>'. __("Error during operation.", "alo-easymail") .'</p></div>';
					}
				} else {
					echo '<div id="message" class="error"><p>'. __("Inputs are incompled or wrong. Please check and try again.", "alo-easymail") .'</p></div>';
				}
			}	
			break;
		case "del_list":	// DELETE a Mailing list
			if ( $list_id  ) {
				$mailinglists = alo_em_get_mailinglists ( 'hidden,admin,public' );
				//$mailinglists [$list_id]["available"] = "deleted";
				unset ( $mailinglists [$list_id] );
				if ( alo_em_save_mailinglists ( $mailinglists ) && alo_em_delete_all_subscribers_from_lists ($list_id) ) {	
					unset ( $list_id );
					unset ( $list_name );
					unset ( $list_available );	
					unset ( $list_order );				
					echo '<div id="message" class="updated fade"><p>'. __("Updated", "alo-easymail") .'</p></div>';
				} else {
					echo '<div id="message" class="error"><p>'. __("Error during operation.", "alo-easymail") .'</p></div>';
				}					
			} else {
				echo '<div id="message" class="error"><p>'. __("Error during operation.", "alo-easymail") .'</p></div>';
			}				
			break;								
	}
}
?>
	   	
<div style="padding: 10px">
<?php _e("You can setup mailing lists. For each you have to specify the name, the order (the lowest appear at top) and the availability", "alo-easymail") ?>:
<ul style="margin:10px">
<li><code><?php _e('hidden', 'alo-easymail')?></code>: <span class="description"><?php _e('the list can be shown only here in settings and nowhere in the site', 'alo-easymail')?></span></li>
<li><code><?php _e('admin side only', 'alo-easymail')?></code>: <span class="description"><?php _e('the list is available only for administratrion use (settings, sending page, subscribers), so subscribers cannot see it', 'alo-easymail')?></span></li>
<li><code><?php _e('entire site', 'alo-easymail')?></code>: <span class="description"><?php _e('the list is available in the whole site, so subscribers can see it', 'alo-easymail')?></span></li>
</ul>
</div>

<h3><?php if ( isset ( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_id ) { _e("Edit list", "alo-easymail"); } else { _e("New list", "alo-easymail"); } ?></h3>
<!-- Edit the new/selected list-->
<form action="#mailinglists" method="post">
<table <?php if ( isset ( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_id) echo "style='background-color:#FFFFC0'" ?> ><tbody>
<tr valign="top">
	<th><?php _e('List name', 'alo-easymail') ?></th>
	<th><?php _e('Availability', 'alo-easymail') ?></th>
	<th><?php _e('Order', 'alo-easymail') ?></th>
	<th></th>
</tr>	
<tr>
<td>

<!--
<input type="text" name="elp_list_name" value="<?php if ( isset ( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_id ) echo $list_name; ?>" id="elp_list_name" size="30" maxlength="50" />
-->

<div id="listname_container">
	<?php 
	// Set tabs and fields
	if ( $languages ) {
		$lang_li = array();
		$lang_div = array();	
		foreach ( $languages as $key => $lang ) {
			$lang_li[$lang] = '<li><a href="#listname_div_'.$lang.'"><strong>'. alo_em_get_lang_flag($lang, 'code') . '</strong>';
			$lang_li[$lang] .= ( isset( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_id && !alo_em_translate_multilangs_array ( $lang, $list_name, false ) ) ? '<img src="'.ALO_EM_PLUGIN_URL.'/images/12-exclamation.png" alt="" style="vertical-align:middle;margin-left:2px;margin-top:-2px;" title="'. __("no translation for this language, yet", "alo-easymail") .'!" />' : '';
			$lang_li[$lang] .= '</a></li>';
			$lang_div[$lang] = '<div id ="listname_div_'.$lang.'">';
			$name_value = ( isset( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_id ) ? esc_attr( alo_em_translate_multilangs_array ( $lang, $list_name, false ) ) : "";
			$lang_div[$lang] .= '<input type="text" name="listname_'.$lang.'" value="' . $name_value .'" id="listname_'.$lang.'" maxlength="100" style="width:100%;" />';	
			$lang_div[$lang] .= '</div>';
		}
	}
	?>
	<ul id="activamail_tabs">
	<?php echo implode ( "\n\n", $lang_li); ?>
	</ul>

<?php echo implode ( "\n\n", $lang_div);?>
</div>



</td>

<td><select name="elp_list_available" id="elp_list_available">
		<option value='hidden' <?php if ( isset ( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_id && $list_available == 'hidden') echo 'selected="selected"'; ?> ><?php _e('hidden', 'alo-easymail') ?> </option>
		<option value='admin' <?php if ( isset ( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_id && $list_available == 'admin') echo 'selected="selected"'; ?> ><?php echo __('admin side only', 'alo-easymail') ?> </option>
		<option value='public' <?php if ( isset ( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_id && $list_available == 'public') echo 'selected="selected"'; ?> ><?php echo __('entire site', 'alo-easymail') ?> </option>
	</select></td>
<td><input type="text" name="elp_list_order" value="<?php if ( isset ( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_order ) { echo $list_order; }else{ echo '0'; }; ?>" id="elp_list_order" size="3" maxlength="3" /></td>
<td>
	<input type="hidden" name="user_ID" value="<?php echo (int) $user_ID ?>" />
	<input type="hidden" name="task" value="save_list" />
	<?php if ( isset ( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_id ) { ?>
		<input type="hidden" name="list_id" value="<?php echo $list_id ?>" />
	<?php } else { ?>
		<input type="hidden" name="list_id" value="" />	
	<?php }  ?>
	<input type="submit" name="submit_list" value="<?php _e('Save', 'alo-easymail') ?>"  class="button-primary" />
	<?php if ( isset ( $_REQUEST['task'] ) && $_REQUEST['task'] == 'edit_list' && $list_id ) { ?>
		<a href='options-general.php?page=alo-easymail/alo-easymail_options.php#mailinglists' title="<?php _e('Cancel', 'alo-easymail') ?>" ><?php _e('Cancel', 'alo-easymail') ?></a>
	<?php } ?>
</td>
</tr>
</tbody> </table>

</form>

<h3><?php _e("Mailing Lists", "alo-easymail") ?></h3>    
<table class="widefat">
<thead><tr valign="top">
<th scope="col" style="width:40%"><?php _e('List name', 'alo-easymail') ?></th>
<th scope="col"><?php _e('Availability', 'alo-easymail') ?></th>
<th scope="col"><?php _e('Order', 'alo-easymail') ?></th>
<th scope="col"><?php _e('Subscribers', 'alo-easymail') ?></th>
<th scope="col"><?php _e('Action', 'alo-easymail') ?></th>
</tr></thead>
<tbody>
<?php

$tab_mailinglists = alo_em_get_mailinglists( 'hidden,admin,public' );
if ($tab_mailinglists) {
	foreach ( $tab_mailinglists as $list => $val) { 
		if ($val['available'] == "deleted") continue; 
		?>
		<tr>
			<td><strong><?php echo alo_em_translate_multilangs_array ( alo_em_get_language(), $val['name'], true ) ?></strong></td>
			<td><?php
				switch ($val['available']) {
					case "hidden":
						echo __('hidden', 'alo-easymail');
						break;
					case "admin":
						echo __('admin side only', 'alo-easymail');
						break;
					case "public":
						echo __('entire site', 'alo-easymail');
						break;
					default:
				}
				?>
			</td>
			<td><strong><?php echo $val['order'] ?></strong></td>
			
			<td><?php echo count ( alo_em_get_recipients_subscribers( $list ) ) ?></td>
			
			<td><?php
				echo "<a href='edit.php?post_type=newsletter&page=alo-easymail/alo-easymail_options.php&amp;task=edit_list&amp;list_id=". $list . "&amp;rand=".rand(1,99999)."#mailinglists' title='".__("Edit list", "alo-easymail")."' >";
				echo "<img src='".ALO_EM_PLUGIN_URL."/images/16-edit.png' alt='" . __("Edit list", "alo-easymail") ."' /></a>";
				echo " ";
				echo "<a href='edit.php?post_type=newsletter&page=alo-easymail/alo-easymail_options.php&amp;task=del_list&amp;list_id=". $list . "&amp;rand=".rand(1,99999)."#mailinglists' title='".__("Delete list", "alo-easymail")."' ";
				echo " onclick=\"return confirm('".__("Do you really want to DELETE this list?", "alo-easymail")."');\">";
				echo "<img src='".ALO_EM_PLUGIN_URL."/images/trash.png' alt='" . __("Delete list", "alo-easymail") ."' /></a>";
				?>
			</td>
		</tr>
	<?php 
	}
} else { ?>
	<tr><td colspan="4"><?php _e('There are no available lists', 'alo-easymail') ?></td></tr>
<?php
}
?>
</tbody> </table>

<?php //echo "<pre style='font-size:80%'>"; print_r( $tab_mailinglists ); echo "</pre>"; // DEBUG ?>

</div> <!-- end Mailing Lists -->

<p><?php alo_em_show_credit_banners( true ); ?></p>

</div><!-- end wrap -->
