<?php
/*
 * Plugin Name: OSBN
 * Plugin URI: http://osbn.de/
 * Description: Dies ist das offizielle WordPress-Plugin f&uuml;r Mitglieder des Open-Source-Blog-Netzwerkes (OSBN).
 * Version: 1.1.2
 * Author: Valentin <admin@picomol.de>
 * Author URI: http://picomol.de/
 * URI: http://osbn.de/
 * License: GPLv3
 * 
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 * 
 */

/**
 * 
 * Settings page for OSBN plugin
 *
 * Thank you David Gwyer for the "Plugin Options Starter Kit"
 * http://wordpress.org/extend/plugins/plugin-options-starter-kit/
 * 
 */

// Set-up Action and Filter Hooks
register_activation_hook(__FILE__, 'osbn_add_defaults');
register_uninstall_hook(__FILE__, 'osbn_delete_plugin_options');
add_action('admin_init', 'osbn_init' );
add_action('admin_menu', 'osbn_add_options_page');
add_filter( 'plugin_action_links', 'osbn_plugin_action_links', 10, 2 );

// Delete options table entries ONLY when plugin deactivated AND deleted
function osbn_delete_plugin_options() {
	delete_option('osbn_options');
	delete_option('osbn_data');
	delete_option('widget_osbn_widget');
}

// Define default option settings
function osbn_add_defaults() {
	$tmp = get_option('osbn_options');
    if(($tmp['chk_default_options_db']=='1')||(!is_array($tmp))) {
		delete_option('osbn_options'); // so we don't have to reset all the 'off' checkboxes too! (don't think this is needed but leave for now)
		$arr = array(	"widget" => "1",
						"articles" => "1",
						"showlogo" => "1",
						"nofollow" => "1",
						"logo_color" => "grau",
						"article_count" => "10",
						"fixed_height" => "1",
						"widget_height" => "240",
						"widget_text" => 'Dieser Blog ist Mitglied im Open-Source-Blog-Netzwerk. <a href="http://osbn.de/">Schau vorbei</a>, wenn dich Open-Source interessiert, oder <a href="http://osbn.de/mitmachen/">mache mit</a>, wenn du selbst Blogger bist. Dies sind die zuletzt erschienenen Artikel:'
		);
		update_option('osbn_options', $arr);
	}
}

// Init plugin options to white list our options
function osbn_init(){
	register_setting( 'osbn_plugin_options', 'osbn_options', 'osbn_validate_options' );
}

// Add menu page
function osbn_add_options_page() {
	add_options_page('OSBN-Plugin Einstellungen', 'OSBN', 'manage_options', 'osbn-option-page', 'osbn_render_form');
}

// Render the Plugin options form
function osbn_render_form() { ?>
	<div>
		<h2>OSBN-Plugin Einstellungen</h2>

		<form action="options.php" method="post">
			<?php settings_fields('osbn_plugin_options'); 
			$options = get_option('osbn_options'); 
			if ($_GET['settings-updated']) {
				updateOsbnData();
			} ?>
			
			<table class="form-table">
				<tr valign="top">
					<th scope="row">Allgemeine Einstellungen</th>
					<td>
						<p>
							<label>
								<input name="osbn_options[widget]" type="checkbox" value="1" disabled checked /> Das OSBN-Widget muss über die WordPress-<a href="<?php echo get_admin_url(); ?>widgets.php">Widget-Einstellungen</a> aktiviert werden
							</label>
						</p>

						<p>
							<label>
								<input name="osbn_options[nofollow]" type="checkbox" value="1" <?php if (isset($options['nofollow'])) { checked('1', $options['nofollow']); } ?> /> <a href="http://de.wikipedia.org/wiki/Nofollow">Nofollow</a>-Attribut bei Kommentar-Links von OSBN-Bloggern entfernen
							</label>
							<br />
							<small style="color:#666666; padding-left: 18px;">Dies führt zu einer besseren Verlinkung zwischen den Blogs und kann sich positiv auf die Listung in Suchmaschinen auswirken</small>
						</p>

					</td>
				</tr>
				
				<tr>
					<th scope="row">Logo</th>
					<td>
						<label>
							<input name="osbn_options[showlogo]" type="checkbox" value="1" <?php if (isset($options['showlogo'])) { checked('1', $options['showlogo']); } ?> /> Logo im Widget anzeigen
						</label>		
					</td>

				</tr>
				<tr>
					<th></th>
					<td>Farbe des Logos
						<select name='osbn_options[logo_color]'>
							<option value='violett' <?php selected('violett', $options['logo_color']); ?>>Violett</option>
							<option value='blau' <?php selected('blau', $options['logo_color']); ?>>Blau</option>
							<option value='gelb' <?php selected('gelb', $options['logo_color']); ?>>Gelb</option>
							<option value='grau' <?php selected('grau', $options['logo_color']); ?>>Grau</option>
							<option value='gruen' <?php selected('gruen', $options['logo_color']); ?>>Grün</option>
							<option value='orange' <?php selected('orange', $options['logo_color']); ?>>Orange</option>
							<option value='rot' <?php selected('rot', $options['logo_color']); ?>>Rot</option>
						</select>
					</td>
					
				</tr>	
				
				<tr>
					<th scope="row">Beschreibender Text</th>
					<td>
						<?php
							$args = array(
								"widget_text" => "osbn_options[widget_text]", 
								"textarea_rows" => "3"
								);
							wp_editor( $options['widget_text'], "osbn_options[widget_text]", $args );
						?>					
					</td>
				</tr>				
				
				
					
				<tr>
					<th scope="row">Artikel</th>
					<td>
						<label>
							<input name="osbn_options[articles]" type="checkbox" value="1" <?php if (isset($options['articles'])) { checked('1', $options['articles']); } ?> /> Aktuelle Artikel aus dem OSBN anzeigen
						</label>
						<br />
						<small style="color:#666666; padding-left: 18px;">Die letzten 10 Artikel werden angezeigt, die Aktualisierung erfolgt stündlich</small>
					</td>				
				</tr>
				
				<tr>
					<th scope="row"></th>
					<td>
						<label>
							<input name="osbn_options[fixed_height]" type="checkbox" value="1" <?php if (isset($options['fixed_height'])) { checked('1', $options['fixed_height']); } ?> /> Höhe der Artikelliste auf </label><input name="osbn_options[widget_height]" maxlength="3" size="3" type="text" value="<?php echo $options['widget_height']; ?>" /> Pixel begrenzen und Scrollleiste anzeigen
						
					</td>
				</tr>
				
				<tr>
					<th></th>
					<td>Anzahl angezeigter Artikel
						<select name='osbn_options[article_count]'>
							<option value='1' <?php selected('1', $options['article_count']); ?>>1</option>
							<option value='3' <?php selected('3', $options['article_count']); ?>>3</option>
							<option value='5' <?php selected('5', $options['article_count']); ?>>5</option>
							<option value='10' <?php selected('10', $options['article_count']); ?>>10</option>
							<option value='15' <?php selected('15', $options['article_count']); ?>>15</option>
							<option value='20' <?php selected('20', $options['article_count']); ?>>20</option>
							<option value='25' <?php selected('25', $options['article_count']); ?>>25</option>
							<option value='30' <?php selected('30', $options['article_count']); ?>>30</option>
						</select>
					</td>
				</tr>
				
						
			</table>	

			<p class="submit">
				<input type="submit" class="button-primary" value="<?php _e('Save Changes') ?>" />
			</p>		
			</form>
	</div>
	
<?php }

// Sanitize and validate input. Accepts an array, return a sanitized array.
function osbn_validate_options($input) {
	 // strip html from textboxes
	$input['textarea_one'] =  wp_filter_nohtml_kses($input['textarea_one']); // Sanitize textarea input (strip html tags, and escape characters)
	$input['txt_one'] =  wp_filter_nohtml_kses($input['txt_one']); // Sanitize textbox input (strip html tags, and escape characters)
	return $input;
}

// Display a Settings link on the main Plugins page
function osbn_plugin_action_links( $links, $file ) {

	if ( $file == plugin_basename( __FILE__ ) ) {
		$osbn_links = '<a href="'.get_admin_url().'options-general.php?page=osbn-option-page">'.__('Settings').'</a>';
		$links[] = $osbn_links;
	}

	return $links;
}


/**
 * OSBN Widget 
 */

class Osbn_Widget extends WP_Widget {
	
	public function __construct() {
		$this->var_sTextdomain = 'OSBN';

		if(function_exists('load_plugin_textdomain')) {
			load_plugin_textdomain($this->var_sTextdomain, PLUGINDIR . '/' . dirname(plugin_basename(__FILE__)) . '/l10n', dirname(plugin_basename(__FILE__)) . '/l10n');
		}

		$widget_options = array(
			'classname' => 'Osbn_Widget',
			'description' => __('Widget des Open-Source-Blog-Netzwerkes', $this->var_sTextdomain)
		);

		$control_options = array();

		$this->WP_Widget('Osbn_Widget', __('OSBN', $this->var_sTextdomain), $widget_options, $control_options);
	}


	// Widget form
	public function form($instance) {

		$instance = wp_parse_args((array) $instance, array(
			'osbn-widget-title' => 'Open-Source-Blog-Netzwerk'
		));

		// Title
		echo '<p>Titel:</p>';
		echo '<p><input id="' . $this->get_field_id('osbn-widget-title') . '" name="' . $this->get_field_name('osbn-widget-title') . '" type="text" value="' . $instance['osbn-widget-title'] . '" /></p>';
		echo '<p style="clear:both;"></p>';

	} 


	// Save widget settings
	public function update($new_instance, $old_instance) {
				$instance = $old_instance;

		$new_instance = wp_parse_args((array) $new_instance, array(
			'osbn-widget-title' => ''
		));

		$instance['osbn-widget-title'] = (string) strip_tags($new_instance['osbn-widget-title']);

		return $instance;
	} 


	// Show widget
	public function widget($args, $instance) {
		extract($args);

		$osbn_widget_title = (empty($instance['osbn-widget-title'])) ? '' : apply_filters('osbn_widget_title', $instance['osbn-widget-title']);
		$osbn_data = get_option('osbn_data', array());
		$osbn_data['timediff'] = date('H:i', $osbn_data['timestamp']). ' Uhr';

		echo $before_widget;
		
		if(!empty($osbn_widget_title)) {
			echo $before_title . $osbn_widget_title . $after_title;
		} // END if(!empty($title))

		$options = get_option('osbn_options');

		echo '<p><a target="_blank" href="http://osbn.de/" title="Open-Source-Blog-Netzwerk">';
		
		if ($options['showlogo'] == 1) {
			echo '<img style="float: left; margin: 5px 10px 2px 0;" src="'.plugins_url('osbn/img/osbn-button-'.$options['logo_color'].'.png').'" alt="OSBN" />';
		}
		
		echo '</a>'.$options['widget_text'].'</p>';

		if ($options['articles'] == 1) {
			if (empty($osbn_data['articles']) && $osbn_data['timestamp'] < (time() - 600)) {
				updateOsbnData();
			}
			if ($options['fixed_height']) {
				echo '<ul style="height: '.$options['widget_height'].'px; overflow-y: auto; clear: both; padding-right: 5px">'.$osbn_data['articles'].'<li></li></ul>';
			} else {
				echo '<ul style="clear: both;">'.$osbn_data['articles'].'<li></li></ul>';
			}
			echo '';
		}
		echo $after_widget;
	} 

}


// Initialize widget
add_action('widgets_init', create_function('', 'register_widget("Osbn_Widget");'));


// Update OSBN data
function updateOsbnData() {
	$options = get_option('osbn_options');
	$domain =  preg_replace('/^www\./','',$_SERVER['SERVER_NAME']);
	$osbn_data['blogs'] = unserialize(file_get_contents('http://osbn.de/osbn_bloglist.txt'));
	$osbn_data['articles'] = file_get_contents('http://osbn.de/osbn_articlelist.php?domain='.$domain.'&count='.$options['article_count'].'');
	$osbn_data['timestamp'] = time();
	update_option('osbn_data', $osbn_data);		
}

// Get new articles from osbn.de (hourly)
if ( !wp_next_scheduled('osbn_hook') ) {
	wp_schedule_event( time(), 'hourly', 'osbn_hook' );
}

add_action('osbn_hook', 'updateOsbnData');


/**
 * 
 * Initialize "follow-my-friends" from Sebastian Gaul 
 *
 * Thank you Sebastian Gaul for the "Follow My Friends" plugin
 * http://sgaul.de/2012/11/03/testversion-von-follow-my-friends-verfugbar/
 * 
 */

$options = get_option('osbn_options');

require_once ('inc/follow-my-friends.php');

if (function_exists('add_filter') && ($options['nofollow'] != 0)) {
	add_filter('get_comment_author_link', 'ofmy_removeChosenSingleQuotedNofollowAttributesFromText');
	add_filter('get_comment_text', 'ofmy_removeChosenNofollowAttributesFromText');
}

function ofmy_removeChosenNofollowAttributesFromText($text, $attributeDelimiter = '"') {
	$ofmf = new FollowMyFriends(
		new FMF_AnchorStartingTagFactory($attributeDelimiter)
	);
	$ofmf->friendlyUrls = osbnFriendlyUrls();
	return $ofmf->removeNofollowFromFriendlyLinks($text);
}

function ofmy_removeChosenSingleQuotedNofollowAttributesFromText($text) {
	return ofmy_removeChosenNofollowAttributesFromText($text, "'");
}


// Follow Urls Array
function osbnFriendlyUrls() {
	$osbn_data = get_option('osbn_data', array());

	if (empty($osbn_data['blogs'])) {
		updateOsbnData();
		$osbn_data = get_option('osbn_data', array());
	}	

	$blogs = array();
	foreach ($osbn_data['blogs'] as $val) {
		$blogs[] = 'http://'.$val;
		$blogs[] = 'http://www.'.$val;
	}
	return $blogs;
}


?>
