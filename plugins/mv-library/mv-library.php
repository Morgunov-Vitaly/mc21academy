<?php

/*
  Plugin Name: mv-library
  Plugin URI:
  Description: Плагин для интеграции корпоративной библиотеки с портала Битрикс24
  Version: 1.0.0
  Author: Моргунов Виталий
  Author URI: https://vk.com/v.morgunov
  License: GPLv2
 */

/*
  Copyright (C) 2018 Моргунов Виталий

  This program is free software; you can redistribute it and/or
  modify it under the terms of the GNU General Public License
  as published by the Free Software Foundation; either version 2
  of the License, or (at your option) any later version.

  This program is distributed in the hope that it will be useful,
  but WITHOUT ANY WARRANTY; without even the implied warranty of
  MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
  GNU General Public License for more details.

  You should have received a copy of the GNU General Public License
  along with this program; if not, write to the Free Software
  Foundation, Inc., 59 Temple Place - Suite 330, Boston, MA  02111-1307, USA.
 */
defined('ABSPATH') or die('No script kiddies please!'); //Защита от прямого вызова скрипта

/* Локализация плагина */
add_action('plugins_loaded', 'mv_library_load_plugin_textdomain');

function mv_library_load_plugin_textdomain() {
    load_plugin_textdomain('mv_library', false, dirname(plugin_basename(__FILE__)) . '/languages/');
}

/* /Локализация плагина */

/**
 * Создаем обработчик шорткода [mv-libary] за шорткод - гибкость - вставляем, где хотим, но хуже производительность
 * создаем процедурно (функционально) или создаем класс ОПП? 
 * или создаем файл шаблона страницы? -выше производительность, но не так гибко 
 * Подключить VUE JS в футер, подключить стили (если будем использовать компонентный подход, то не произойдет ли это автоматически? ) * 
 */
add_shortcode('mv-library', 'mv_library_function');

function mv_library_function() {
    
    echo '<div id="app"></div>';   
     
}

/**
 * Подключаем скрипты и стили
 */

add_action('wp_footer', 'enqueue_mv_library_js_css'); /* с хуком wp_footer выдает ошибку нужно либо добавить условие на существование $post->post_content либо юзать wp_enqueue_scripts */
/* Подвешиваем к хуку функцию подключения стилей */

function enqueue_mv_library_js_css() {
    global $post;
    /* Проверяем наличие шорткода  в посте */
    if ( isset($post->post_content) && has_shortcode($post->post_content, 'mv-library')) {
        // если в контенте есть шорткод [mv-library]

        /* Подключаем скрипты */
        //Регистрируем
	wp_register_script('mv-library_js', plugins_url('js/build.js', __FILE__)); 
        //Подключаем
	wp_enqueue_script('mv-library_js');
        /* / Подключаем скрипты */

        /* Подключаем стили */
        //Регистрируем
        wp_register_style('mv_library_css', plugins_url('css/smart-grid.css', __FILE__));
        //Подключаем
        wp_enqueue_style('mv_library_css');
        /* / Подключаем стили */

    
    }
}
/* / Подключаем скрипты и стили  */