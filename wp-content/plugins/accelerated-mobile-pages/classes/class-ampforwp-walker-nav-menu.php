<?php
// Exit if accessed directly
if ( ! defined( 'ABSPATH' ) ) {
    exit;
}

/*Sidebar Nav menu Walker Start*/
  class Ampforwp_Walker_Nav_Menu extends Walker_Nav_Menu {

  function start_lvl(&$output, $depth=0, $args = array(), $has_children = 0) {
  static $column = 1;
    $indent = str_repeat("\t", $depth);
      //$output .= "\n$indent<ul class=\"sub-menu\">\n";

      // Change sub-menu to dropdown menu
  if ($depth > 0 && $has_children > 0 )
    {
    $column += 1;
    $output .= "\n$indent<input type=\"checkbox\" id=\"drop-$column\"><label for=\"drop-$column\" class=\"toggle\"></label><ul class=\"sub-menu\">\n";
  }else{
    $column += 1;
    $output .= "\n$indent<input type=\"checkbox\" id=\"drop-$column\"><label for=\"drop-$column\" class=\"toggle\"></label><ul class=\"sub-menu\">\n";
    
  }
  }
  
  function start_el ( &$output, $item, $depth = 0, $args = array(), $id = 0 ) {
    // Most of this code is copied from original Walker_Nav_Menu
    global $wp_query, $wpdb;
    $indent = ( $depth ) ? str_repeat( "\t", $depth ) : '';

    $class_names = $value = '';

    $classes = empty( $item->classes ) ? array() : (array) $item->classes;
    $classes[] = 'menu-item-' . $item->ID;

    $class_names = join( ' ', apply_filters( 'ampforwp_nav_menu_css_class', array_filter( $classes ), $item, $args ) );
    $class_names = ' class="' . esc_attr( $class_names ) . '"';

    $id = apply_filters( 'nav_menu_item_id', 'menu-item-'. $item->ID, $item, $args );
    $id = strlen( $id ) ? ' id="' . esc_attr( $id ) . '"' : '';

    $has_children = $wpdb->get_var($wpdb->prepare("SELECT COUNT(meta_id)
                            FROM {$wpdb->prefix}postmeta
                            WHERE meta_key='_menu_item_menu_item_parent'
                            AND meta_value='%d'", $item->ID) );

    $output .= $indent . '<li' . $id . $value . $class_names .'>';

    $atts = array();
    $atts['title']  = ! empty( $item->attr_title ) ? $item->attr_title : '';
    $atts['target'] = ! empty( $item->target )     ? $item->target     : '';
    $atts['rel']    = ! empty( $item->xfn )        ? $item->xfn        : '';
    $atts['href']   = ! empty( $item->url )        ? $item->url        : '';
    $atts = apply_filters( 'nav_menu_link_attributes', $atts, $item, $args, $depth );

    $attributes  = ! empty( $item->attr_title ) ? ' title="'  . esc_attr(  $atts['title'] ) .'"' : '';
    $attributes .= ! empty( $item->target )     ? ' target="' . esc_attr( $atts['target']     ) .'"' : '';
    $attributes .= ! empty( $item->xfn )        ? ' rel="'    . esc_attr( $atts['rel']        ) .'"' : '';
    $attributes .= ! empty( $item->url )        ? ' href="'   . esc_attr( $atts['href']        ) .'"' : '';

    // Check if menu item is in main menu
    if ( $depth == 0 && $has_children > 0  ) {
        // These lines adds your custom class and attribute
        $attributes .= ' class="dropdown-toggle"';
        $attributes .= ' data-toggle="dropdown"';
    }

    $item_output = $args->before;
  
    $item_output .= '<a'. $attributes .'>';
    $item_output .= $args->link_before . apply_filters( 'the_title', $item->title, $item->ID ) . $args->link_after;
    $item_output .= '</a>';
  
  // Add the caret if menu level is 0
    if ( $has_children > 0  ) {
        //$item_output .= '<label for="drop-"'.$depth.' class="toggle">+</label>';
    }
    $item_output .= $args->after;

    $output .= apply_filters( 'walker_nav_menu_start_el', $item_output, $item, $depth, $args );
  }

}
/*Sidebar Nav menu Walker end*/