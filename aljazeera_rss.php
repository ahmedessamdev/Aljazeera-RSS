<?php
/*
Plugin Name: Aljazeera RSS
Plugin URI : 
Description: Display the latest Aljazeera news using RSS
Version    : 1.0
Author     : Yehia
Author URI : 
*/

// Create a hook
/* Add our function to the widgets_init hook. */
add_action('widgets_init', 'load_my_widget');
function load_my_widget() { 
    register_widget('Aljazeera_rss'); 
    // CSS of the widget
    wp_enqueue_style( 'aljazeera_style.css', plugins_url('/aljazeera_rss/aljazeera_style.css'));
    // Feed cache recreation time (in seconds)
    add_filter( 'wp_feed_cache_transient_lifetime', function() { return 600; } );
}

class Aljazeera_rss extends WP_Widget {
    // Max items to get from the feed (to generate the form combo_box)
    private $max_items_allowed = 25;
    // The required items from the widget form
    private $num_items;
    private $desc_on;
    function __construct() {
        $widget_ops = array('classname' => 'Aljazeera_rss', 
                            'description' => 'Get the latset news from aljazzera.net RSS' );
        $control_ops = array('width' => 300, 
                             'height' => 350, 
                             'id_base' => 'aljazeera_rss');
        $this->WP_Widget( 'aljazeera_rss', 'Aljazeera RSS', $widget_ops, $control_ops );
    }

    function widget($args, $instance) {
        extract($args);
        // Get the variables
        $rss_url = esc_url(strip_tags($instance['rss_url']));
        $display_logo = isset($instance['display_logo']) ? $instance['display_logo'] : false;
        $display_link = isset($instance['display_link']) ? $instance['display_link'] : false;
        $num_items     = (int) $instance['num_items'];
        $display_news_description = isset($instance['display_news_description']) ? $instance['display_news_description'] : false;

        // If the url is empty, abort
        if ( empty($rss_url) ) return;

        // Adjust the number of items
        if ($num_items > $this->max_items_allowed) { $num_items = $this->max_items_allowed; }
        elseif ($num_items < 1) { $num_items = 1; } 

        // Add variables to the class properties as we will need them in outputting the feed
        $this->desc_on = $display_news_description;
        $this->num_items = $num_items;

        $logo_src = plugins_url('/aljazeera_rss/aljazeera_logo.png');
        echo $before_widget;
        if (isset($title)) echo $before_title . $title . $after_title;
        echo "<div id='aljazeera_rss_container'>";
        if ($display_logo == true) {
            echo "<img id='aljazeera_logo' src='$logo_src' alt='أخر الأخبار من موقع الجزيرة' />";
        }
        if ($display_link == true) {
            echo "<a href='http://www.aljazeera.net' target='_blank' id='aljazeera_link'>aljazeera.net</a>";
        }
        echo "<div id='aljazeera_rss_inner'>";
        // call a function to output a definition list
        $this->output_feed($rss_url);
        echo "</div>\n</div>" . $after_widget;

        //  Uncomment in case you need the feed main title, main description or the feed main link
        /**********************************
          if ( !is_wp_error($rss) ) {
          $item_title = esc_html(strip_tags($rss->get_title()));
          $link = esc_url(strip_tags($rss->get_permalink()));
          $item_desc = esc_attr(strip_tags(@html_entity_decode($rss->get_description(), 
          ENT_QUOTES, 
          get_option('blog_charset'))));
          }

        if ( empty($title) )
            $title = empty($desc) ? __('Unknown Feed') : $desc;

        // Check the link
        while ( stristr($link, 'http') != $link )
            $link = substr($link, 1);
        ****************************************/
    }

    function update($new_instance, $old_instance) {
        $instance = $old_instance;
        $instance['display_logo']        = isset($new_instance['display_logo']) ? true : false; 
        $instance['display_link']        = isset($new_instance['display_link']) ? true : false; 
        $instance['display_news_description'] = isset($new_instance['display_news_description']) ? true : false;
        $instance['num_items']           = (int) $new_instance['num_items'];
        $instance['rss_url']             = $new_instance['rss_url'];
        return $instance;
    }

    function form($instance) {
        /* Set up some default widget settings. */
        $defaults = array(
                          'display_logo'        => true, 
                          'display_link'        => true,
                          'display_news_description' => true,
                          'num_items'            => 5, 
                          'rss_url'             => 'http://aljazeera.net/AljazeeraRss/c992b9df-12d8-42ee-a0b0-f7fa7b2d6df8/3ea5221b-aab2-4774-9417-5416dac996db'
                          );
        $instance = wp_parse_args( (array) $instance, $defaults ); ?>
          <p>
              <input class="checkbox" type="checkbox" <?php checked($instance['display_logo'], true); ?> id="<?php echo $this->get_field_id('display_logo'); ?>" name="<?php echo $this->get_field_name('display_logo'); ?>" /> 
              <label for="<?php echo $this->get_field_id('display_logo'); ?>">Display Aljazeera logo</label>
          </p>
          <p>
              <input class="checkbox" type="checkbox" <?php checked($instance['display_link'], true); ?> id="<?php echo $this->get_field_id('display_link'); ?>" name="<?php echo $this->get_field_name('display_link'); ?>" /> 
              <label for="<?php echo $this->get_field_id('display_link'); ?>">Display aljazeera.net link</label>
          </p>
          <p>
              <input class="checkbox" type="checkbox" 
                     <?php checked($instance['display_news_description'], true); ?> 
                     id="<?php echo $this->get_field_id('display_news_description'); ?>" 
                     name="<?php echo $this->get_field_name('display_news_description'); ?>" 
                     /> 
              <label for="<?php echo $this->get_field_id('display_news_description'); ?>">Display description</label>
          </p>
          <p>
              <label for="<?php echo $this->get_field_id('num_items'); ?>">Number of items:</label> 
              <select id="<?php echo $this->get_field_id('num_items'); ?>" 
                      name="<?php echo $this->get_field_name('num_items'); ?>">
                  <?php for( $i=1 ; $i <= $this->max_items_allowed ; ++$i) : ?>
                  <option <?php if ($i  == $instance['num_items']): echo "selected='selected'"; endif;?>>
                       <?php echo $i; ?>
                </option>
                <?php endfor; ?>
              </select>
          </p>
          <p>
              <label for="<?php echo $this->get_field_id('rss_url'); ?>">RSS URL:</label>
              <input id="<?php echo $this->get_field_id('rss_url'); ?>" 
                     name="<?php echo $this->get_field_name('rss_url'); ?>" 
                     value="<?php echo $instance['rss_url']; ?>" 
                     style="width:100%;" />
          </p>

<?php }
        // Process the feed (copied some of the WP_Widget_RSS class)
        private function output_feed($url) {
            // Get the feed using simplepie integerated in wordpress
            $rss = fetch_feed($url);
            // Check that the object is created correctly 
            if ( is_wp_error($rss) ) { 
                echo "<p>عذرا حدثت مشكلة فى إحضار الأخبار</p>";
                return;
            }

            // Get the available items limiting them to the number of items we require
            $num_items_available = $rss->get_item_quantity($this->num_items);
            // Build an array of all the items objects, starting with element 0.
            $rss_items = $rss->get_items(0, $num_items_available); ?>
              <dl>
                   <?php foreach ($rss_items as $rss_item) : ?>
                    <dt class="rss_title">
                        <a href='<?php echo esc_url( $rss_item->get_permalink() ); ?>'
                          title='<?php echo $rss_item->get_date(get_option("date_format") . " | h:s A"); ?>' data='<?php echo get_option("date_format"); ?>'><?php 
                   echo esc_html( $rss_item->get_title() ); 
                    ?></a>
                    </dt>
                   <?php if ($this->desc_on == true) : ?>
                         <dd class="rss_content"><?php
                    // Sanitizing the feed, copied from WP_Widget_RSS class but in more detail
                    $desc = @html_entity_decode($rss_item->get_description(), ENT_QUOTES, get_option('blog_charset'));
                    $desc = strip_tags($desc);
                    $desc = esc_attr($desc);
                    $desc = str_replace(array("\n", "\r"), ' ', $desc);
                    // Append ellipsis. Change existing [...] to [&hellip;].
                    if ( '[...]' == substr( $desc, -5 ) )
                        $desc = substr( $desc, 0, -5 ) . '[&hellip;]';
                    elseif ( '[&hellip;]' != substr( $desc, -10 ) )
                        $desc .= ' [&hellip;]';
                    
                    $desc = esc_html($desc);
                    echo $desc;
                   ?></dd>
                   <?php else: /* Create speperrator */?>
                   <span class="sep"></span>
                   <?php endif; ?>
                   <?php endforeach; ?>
             </dl>
             <?php }
}

?>
