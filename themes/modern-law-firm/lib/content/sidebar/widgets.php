<?php
/**
 * Register sidebars and widgets
 */
function roots_widgets_init() {
    global $ciSidebars;
    $ciSidebars = array(
        'sidebar-primary' => "Primary",
        'sidebar-alt'     => "Alternative sidebar",
        'sidebar-footer'  => "Footer",
    );

    // Sidebars
    $sidebarOptions = array(
        'before_widget' => '<section class="widget %1$s %2$s">',
        'after_widget'  => '</section>',
        'before_title'  => '<h3>',
        'after_title'   => '</h3>',
    );

    foreach( $ciSidebars as $sidebarSlug => $sidebarName ) {
        $sidebarOptions['name'] = __( $sidebarName, MLF_TEXT_DOMAIN );
        $sidebarOptions['id'] = $sidebarSlug;
        register_sidebar( $sidebarOptions );
    }

    // Widgets
    register_widget( 'Roots_Vcard_Widget' );
}
add_action( 'widgets_init', 'roots_widgets_init' );







/**
 * Example vCard widget
 */
if( !class_exists('Roots_Vcard_Widget') ) {
    class Roots_Vcard_Widget extends WP_Widget {
        private $fields = array(
            'title'          => 'Title (optional)',
            'street_address' => 'Street Address',
            'locality'       => 'City/Locality',
            'region'         => 'State/Region',
            'postal_code'    => 'Zipcode/Postal Code',
            'tel'            => 'Telephone',
            'email'          => 'Email'
        );

        function __construct() {
            $widget_ops = array( 'classname' => 'widget_roots_vcard', 'description' => __( 'Use this widget to add a vCard', MLF_TEXT_DOMAIN ) );

            $this->WP_Widget( 'widget_roots_vcard', __( 'Roots: vCard', MLF_TEXT_DOMAIN ), $widget_ops );
            $this->alt_option_name = 'widget_roots_vcard';

            add_action( 'save_post', array( &$this, 'flush_widget_cache' ) );
            add_action( 'deleted_post', array( &$this, 'flush_widget_cache' ) );
            add_action( 'switch_theme', array( &$this, 'flush_widget_cache' ) );
        }

        function widget( $args, $instance ) {
            $cache = wp_cache_get( 'widget_roots_vcard', 'widget' );

            if( !is_array( $cache ) ) {
                $cache = array();
            }

            if( !isset($args['widget_id']) ) {
                $args['widget_id'] = null;
            }

            if( isset($cache[$args['widget_id']]) ) {
                echo $cache[$args['widget_id']];

                return;
            }

            ob_start();
            extract( $args, EXTR_SKIP );

            $title = apply_filters( 'widget_title', empty($instance['title']) ? __( 'vCard', MLF_TEXT_DOMAIN ) : $instance['title'], $instance, $this->id_base );

            foreach( $this->fields as $name => $label ) {
                if( !isset($instance[$name]) ) {
                    $instance[$name] = '';
                }
            }

            echo $before_widget;

            if( $title ) {
                echo $before_title, $title, $after_title;
            } ?>
            <p class="vcard" itemscope itemtype="http://schema.org/Attorney">
                <a class="fn org url" href="<?php echo home_url( '/' ); ?>"><span itemprop="name"><?php echo apply_filters( 'widget_text', get_bloginfo( 'name' ) ); ?></span></a><br>
                  <span class="adr" itemprop="address" itemscope itemtype="http://schema.org/PostalAddress">
                    <span class="street-address" itemprop="streetAddress"><?php echo apply_filters( 'widget_text', $instance['street_address'] ); ?></span><br>
                    <span class="locality" itemprop="addressLocality"><?php echo apply_filters( 'widget_text', $instance['locality'] ); ?></span>,
                    <span class="region" itemprop="addressRegion"><?php echo apply_filters( 'widget_text', $instance['region'] ); ?></span>
                    <span class="postal-code"><?php echo apply_filters( 'widget_text', $instance['postal_code'] ); ?></span><br>
                  </span>
                <?php $phone = apply_filters( 'widget_text', $instance['tel'] ) ?>
                <span class="tel" itemprop="telephone"><a class="value" href="tel:<?php echo ciFilterNumbersOnly($phone); ?>"><?php echo $phone; ?></a></span><br>
                <a class="email" href="mailto:<?php echo $instance['email']; ?>"><?php echo apply_filters( 'widget_text', $instance['email'] ); ?></a>
            </p>
            <?php
            echo $after_widget;

            $cache[$args['widget_id']] = ob_get_flush();
            wp_cache_set( 'widget_roots_vcard', $cache, 'widget' );
        }

        function update( $new_instance, $old_instance ) {
            $instance = array_map( 'strip_tags', $new_instance );

            $this->flush_widget_cache();

            $alloptions = wp_cache_get( 'alloptions', 'options' );

            if( isset($alloptions['widget_roots_vcard']) ) {
                delete_option( 'widget_roots_vcard' );
            }

            return $instance;
        }

        function flush_widget_cache() {
            wp_cache_delete( 'widget_roots_vcard', 'widget' );
        }

        function form( $instance ) {
            foreach( $this->fields as $name => $label ) {
                ${$name} = isset($instance[$name]) ? esc_attr( $instance[$name] ) : '';
                ?>
                <p>
                    <label
                        for="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"><?php _e( "{$label}:", MLF_TEXT_DOMAIN ); ?></label>
                    <input class="widefat" id="<?php echo esc_attr( $this->get_field_id( $name ) ); ?>"
                           name="<?php echo esc_attr( $this->get_field_name( $name ) ); ?>" type="text"
                           value="<?php echo ${$name}; ?>">
                </p>
            <?php
            }
        }
    }
}
