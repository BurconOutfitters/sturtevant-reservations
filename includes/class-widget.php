<?php
/**
 * Form & calendar widget.
 *
 * @package    Sturtevant_Reservations
 * @subpackage Includes
 *
 * @since      1.0.0
 * @author     Greg Sweet <greg@ccdzine.com>
 */

// If this file is called directly, abort.
if ( ! defined( 'WPINC' ) ) {
	die;
}

/**
 * Form & calendar widget.
 *
 * @since 1.0.0
 */
class SC_Res_Widget extends WP_Widget {

    function __construct() {

        $widget_ops = [
            'classname' => 'SC_Res_Widget',
            'description' => 'Displays a booking form'
        ];

        parent::__construct( 'SC_Res_Widget', 'Sturtevant Reservation Form', $widget_ops );

    }

    function form( $instance ) {

        $instance   = wp_parse_args(
            (array) $instance,
            [
                'title'      => '',
                'calendarid' => ''
            ]
        );
        $title      = $instance['title'];
        $calendarid = $instance['calendarid'];
?>
<p>
    <label for="<?php echo $this->get_field_id( 'title' ); ?>"><?php _e( 'Title:', 'sc-res' ) . ' '; ?>
        <input class="widefat" id="<?php echo $this->get_field_id( 'title' ); ?>" name="<?php echo $this->get_field_name( 'title' ); ?>" type="text" value="<?php echo esc_attr( $title ); ?>" />
    </label>
    <label for="<?php echo $this->get_field_id( 'calendarid' ); ?>"><?php _e( 'Calendar ID:', 'sc-res' ) . ' '; ?>
        <input class="widefat" id="<?php echo $this->get_field_id( 'calendarid' ); ?>" name="<?php echo $this->get_field_name( 'calendarid' ); ?>" type="text" value="<?php echo esc_attr( $calendarid ); ?>" />
    </label>
</p>
<?php
    }

    /**
     * Undocumented function
     *
     * @param [type] $new_instance
     * @param [type] $old_instance
     * @return void
     */
    function update( $new_instance, $old_instance ) {

        $instance               = $old_instance;
        $instance['title']      = $new_instance['title'];
        $instance['calendarid'] = $new_instance['calendarid'];

        return $instance;

    }

    /**
     * Undocumented function
     *
     * @param [type] $args
     * @param [type] $instance
     * @return void
     */
    function widget( $args, $instance ) {

        extract( $args, EXTR_SKIP );

        echo $before_widget;

        if ( empty( $instance['title'] ) ) {
            $title = ' ';
        } else {
            $title = apply_filters( 'widget_title', $instance['title'] );
        }

        $calendarid = $instance['calendarid'];

        if ( ! empty( $title ) ) {
            echo $before_title . $title . $after_title;
        }

        if ( $calendarid != '' ) {
            define ( 'DEX_BCCF_CALENDAR_FIXED_ID', $calendarid );
        }

        dex_bccf_get_public_form();

        echo $after_widget;

    }

}
add_action( 'widgets_init', create_function( '', 'return register_widget( "SC_Res_Widget" );' ) );