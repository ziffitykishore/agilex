<?php

/**
 * Most Loved Widget Class
 */
class li_most_loved_widget extends WP_Widget {

     /** constructor */
    function __construct()
    {
        parent::__construct('most_loved_widget', __('Most Loved Items', 'love_it'),
            array('description' => __('Show the most loved items', 'love_it')));
    }

    /** @see WP_Widget::widget */
    function widget($args, $instance) {
	extract( $args );
	$title 	= apply_filters('widget_title', $instance['title']);
	$number = strip_tags($instance['number']);

	echo $before_widget;
	if ( $title ) {
            echo $before_title . $title . $after_title; ?>
		<ul class="most-loved">
		    <?php
                    $args = array(
                                'numberposts' => $number,
                                'meta_key' => '_li_love_count',
                                'orderby' => 'meta_value',
                                'order' => 'DESC'
                            );
                    $most_loved = get_posts( $args );
                    foreach( $most_loved as $loved ) : ?>
			<li class="loved-item">
                            <a href="<?php echo get_permalink($loved->ID); ?>"><?php echo get_the_title($loved->ID); ?></a><br/>
			</li>
		    <?php endforeach; ?>
		</ul>
        <?php }
	echo $after_widget;
    }

    /** @see WP_Widget::update */
    function update($new_instance, $old_instance)
    {
        $instance = $old_instance;
        $instance['title'] = strip_tags($new_instance['title']);
        $instance['number'] = strip_tags($new_instance['number']);
        return $instance;
    }

    /** @see WP_Widget::form */
    function form($instance)
    {
        $title = !empty($instance['title']) ? strip_tags($instance['title']) : '';
        $number = !empty($instance['number']) ? absint($instance['number']) : '';
        ?>
        <p>
        <label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('Title:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" />
        </p>
        <p>
        <label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('Number to Show:'); ?></label>
        <input class="widefat" id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" />
        </p>
        <?php
    }

}
add_action('widgets_init', create_function('', 'return register_widget("li_most_loved_widget");'));
