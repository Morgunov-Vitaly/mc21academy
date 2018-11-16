<?php

/* 
 * Здесь размещаем шорткоды
 * 
 */

/**
 * Шорткод [mv-vc] 
 * для вставки элемента управления скоростью воспроизведения видео
 */
class mv_video_control {
  static $add_script;
  static function init () {
      add_shortcode('mv-vc', array(__CLASS__, 'mv_video_control_func'));
      add_action('init', array(__CLASS__, 'register_script'));
      add_action('wp_footer', array(__CLASS__, 'print_script'));
  }
  static function mv_video_control_func( $atts ) {
      self::$add_script = true; 
      $mv_content = '<p class="mv_video_control">'
              . '<input id="x1" name="mv_video_speed" type="radio" value="1" checked><label for="x1">1x</label>'
              . '<input id="x125" name="mv_video_speed" type="radio" value="1.25"><label for="x125">1,25x</label>'
              . '<input id="x15" name="mv_video_speed" type="radio" value="1.5"><label for="x15">1,5x</label>'
              . '<input id="x2" name="mv_video_speed" type="radio" value="2"><label for="x2">2x</label>'                
              . '</p>';      
      return $mv_content;
  }
  static function register_script() {
      wp_register_script( 'mv_video_control_js', get_stylesheet_directory_uri() . '/shortcodes/js/mv_video_control.js');
  }
 
  static function print_script () {
      if ( !self::$add_script ) return;
      wp_print_scripts('mv_video_control_js');
  }
}
mv_video_control::init();

/* / Шорткод для вставки клавиш управления скоростью воспроизведения видео */