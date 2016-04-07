<?php
/*
 * Plugin Name: RSS from Aljazeera
 * Plugin URI:  https://wordpress.org/plugins/aljazeera-rss/
 * Description: Display the latest Arabic RSS feed from aljazeera.net news website
 * Version:     1.2
 * Author:      Ahmed Essam
 * Author URI: http://www.ahmedspace.com
 * License:     GPL2
 * License URI: https://www.gnu.org/licenses/gpl-2.0.html
 */

function load_my_widget() { 
	register_widget( 'Aljazeera_rss' ); 
	// CSS of the widget
	wp_enqueue_style( 'aljazeera-style.css', 
	                  plugins_url( '/aljazeera-style.css', __FILE__ ) );
	// Custom css
	wp_enqueue_style( 'custom.css', plugins_url( '/custom.css' , __FILE__) );
	add_filter( 'wp_feed_cache_transient_lifetime', function() { return 600; } );
}

add_action( 'widgets_init', 'load_my_widget' );

class Aljazeera_rss extends WP_Widget {
	private $max_items_allowed = 25;
	private $num_items;
	private $desc_on;

	function __construct() {
		parent::__construct('aljazeera_rss',
		                    __( 'Aljazeera RSS', 'text_domain' ),
		                    array( 
			                    'description' => __( 'Get the latset news from aljazzera.net RSS', 'text_domain' ), )
		);
    }

	function widget( $args, $instance ) {
		extract( $args );
		// Get the variables
		$rss_url	  = esc_url( strip_tags( $instance['rss_url'] ) );
		$display_logo = isset( $instance['display_logo'] ) ?
		              $instance['display_logo'] : 
		              false;
		$display_link = isset( $instance['display_link'] ) ? 
		              $instance['display_link'] : 
		              false;
		$num_items	  = (int) $instance['num_items'];
		$display_description = isset( $instance['display_description']) ? 
		                     $instance['display_description'] : 
		                     false;
		
		// If the url is empty, abort
		if ( empty( $rss_url ) ) return;
		
		// Make sure num_items is between limits
		if ( $num_items > $this->max_items_allowed ) { 
			$num_items = $this->max_items_allowed; 
		} elseif ( $num_items < 1 ) { 
			$num_items = 1; 
		} 
		
		// Add variables to the class properties
		$this->desc_on	 = $display_description;
		$this->num_items = $num_items;
		$logo_src		 = plugins_url( '/aljazeera-logo.png', __FILE__ );
		
		echo $before_widget;
		if ( isset( $title ) ) {
			echo $before_title . $title . $after_title;
		}
		echo "<div id='aljazeera-rss-container'>";
		if ($display_logo === true) {
			echo "<img id='aljazeera-logo' src='$logo_src' 
                  alt='أخر الأخبار من موقع الجزيرة' />";
		}
		if ($display_link === true) {
			echo "<a href='http://www.aljazeera.net' " .
                 "target='_blank' id='aljazeera-link'>aljazeera.net</a>";
		}
		echo "<div id='aljazeera-rss-inner'>";
		// output the feed
		$this->output_feed($rss_url);
		echo "</div></div>" . $after_widget;
	}

	function update($new_instance, $old_instance) {
		$instance = $old_instance;
		$instance['display_logo'] = isset($new_instance['display_logo']) ? 
		                          true : 
		                          false; 
		$instance['display_link'] = isset($new_instance['display_link']) ? 
		                          true : 
		                          alse; 
		$instance['display_description'] = 
		                                 isset($new_instance['display_description']) ? 
		                                 true : 
		                                 false;
		$instance['num_items'] = (int) $new_instance['num_items'];
		$instance['rss_url']   = $new_instance['rss_url'];
		return $instance;
	}

	function form($instance) {
		/* Setup default widget settings. */
		$defaults = array(
			'display_logo'        => true, 
			'display_link'        => true,
			'display_description' => true,
			'num_items'           => 5, 
			'rss_url'             => 'http://www.aljazeera.net/aljazeerarss/3c66e3fb-a5e0-4790-91be-ddb05ec17198/4e9f594a-03af-4696-ab98-880c58cd6718',
		);
		$instance = wp_parse_args( (array) $instance, $defaults ); ?>
<p>
    <input class="checkbox" 
           type="checkbox" 
           id="<?php 
			 echo $this->get_field_id( 'display_logo' ); 
		   ?>" 
           name="<?php 
			 echo $this->get_field_name( 'display_logo' ); 
		   ?>" 
           <?php checked( $instance['display_logo'], true ); ?> 
           > 
    <label for="<?php 
	  echo $this->get_field_id( 'display_logo' ); 
	?>">Display Aljazeera logo</label>
</p>
<p>
    <input class="checkbox" 
           type="checkbox" 
           id="<?php 
			 echo $this->get_field_id( 'display_link' ); 
		   ?>" 
           name="<?php 
			 echo $this->get_field_name('display_link'); 
		   ?>" 
           <?php checked( $instance['display_link'], true ); ?> 
           >
    <label for="<?php 
	  echo $this->get_field_id('display_link'); 
	?>"
           >Display aljazeera.net link</label>
</p>
<p>
    <input class="checkbox" 
           type="checkbox" 
           id="<?php 
			 echo $this->get_field_id( 'display_description' ); 
		   ?>" 
           name="<?php 
			 echo $this->get_field_name( 'display_description' ); 
		   ?>" 
           <?php checked( $instance['display_description'], true ); ?> 
           > 
    <label for="<?php echo 
	  $this->get_field_id( 'display_description' ); 
	?>"
           >Display description</label>
</p>
<p>
    <label for="<?php 
	  echo $this->get_field_id( 'num_items' ); 
	?>"
           >Number of items:</label> 
    <select id="<?php 
	  echo $this->get_field_id( 'num_items' ); 
	?>" 
            name="<?php 
			  echo $this->get_field_name( 'num_items' ); 
			?>"
            >
        <?php for( $i=1; $i <= $this->max_items_allowed; $i++) : ?>
        <option <?php if ( $i === $instance['num_items'] ): ?>
				selected='selected' 
				<?php endif; ?>
                ><?php echo $i; ?></option>
        <?php endfor; ?>
    </select>
</p>
<p>
    <label for="<?php 
	  echo $this->get_field_id( 'rss_url' ); 
	?>">RSS URL:</label>
    <input id="<?php 
	  echo $this->get_field_id( 'rss_url' ); 
	?>" 
           name="<?php 
			 echo $this->get_field_name( 'rss_url' ); 
		   ?>" 
           value="<?php 
			 echo $instance['rss_url']; 
		   ?>" 
           style="width:100%;"
           >
</p>

<?php }

        // Output the feed
		private function output_feed( $url ) {
			$guids = array();
			// Get the feed using simplepie
			$rss = fetch_feed( $url );
			if ( is_wp_error( $rss ) ) { 
				echo "<p>عذرا حدثت مشكلة فى إحضار الأخبار</p>";
				return;
			}

            // Limit the items to the number we need
            $num_items_available = $rss->get_item_quantity( $this->num_items );
            // Build an array of all the items objects, starting with element 0.
            $rss_items = $rss->get_items( 0, $num_items_available ); 
?>
<dl>
    	<?php foreach ( $rss_items as $rss_item ) :
            	// Check for duplicates
            	$temp_guid = $rss_item->get_id( true );
				if ( in_array( $temp_guid, $guids ) ) {
					continue;
				} else {
					$guids[] = $temp_guid;
				}
		?>
		<dt class="rss-title">
			<a href='<?php 
			  echo esc_url( $rss_item->get_permalink() ); 
			?>'
			title='<?php 
			  echo $rss_item->get_date( 
			  get_option( "date_format" ) . 
			  " | h:s A" ); 
			?>'
			><?php echo esc_html( $rss_item->get_title() ); ?></a>
		</dt>
					<?php if ($this->desc_on === true) : ?>
<dd class="rss-content">
    					<?php
						// Sanitizing the feed
						$desc = @html_entity_decode( 
							$rss_item->get_description(), 
							ENT_QUOTES, 
							get_option( 'blog_charset' ) );
						$desc = strip_tags( $desc );
						$desc = esc_attr( $desc );
						$desc = str_replace( array("\n", "\r"), ' ', $desc );
						// change existing [...] to [&hellip;].
						if ( '[...]' === substr( $desc, -5 )) {
							$desc = substr( $desc, 0, -5 ) . '[&hellip;]';
						} elseif ('[&hellip;]' !== substr($desc, -10)) {
							$desc .= ' [&hellip;]';
						}
						$desc = esc_html( $desc );
						echo $desc;
						?>
</dd>
						<?php else: 
						/* Create speperrator if there is no description */
						?>
<span class="sep"></span>
					<?php endif; ?>
				<?php endforeach; ?>
</dl>
<?php 
		} // -- output_feed
  } // Class

?>
