<?php

/**
 * @package Boss Child Theme
 * The parent theme functions are located at /boss/buddyboss-inc/theme-functions.php
 * Add your own functions in this file.
 */


/**
 * Sets up theme defaults
 *
 * @since Boss Child Theme 1.0.0
 */
function boss_child_theme_setup() {
    /**
     * Makes child theme available for translation.
     * Translations can be added into the /languages/ directory.
     * Read more at: http://www.buddyboss.com/tutorials/language-translations/
     */
    // Translate text from the PARENT theme.
    load_theme_textdomain('boss', get_stylesheet_directory() . '/languages');

    // Translate text from the CHILD theme only.
    // Change 'boss' instances in all child theme files to 'boss_child_theme'.
    // load_theme_textdomain( 'boss_child_theme', get_stylesheet_directory() . '/languages' );
}

add_action('after_setup_theme', 'boss_child_theme_setup');

/**
 * Enqueues scripts and styles for child theme front-end.
 *
 * @since Boss Child Theme  1.0.0
 */
function boss_child_theme_scripts_styles() {
    /**
     * Scripts and Styles loaded by the parent theme can be unloaded if needed
     * using wp_deregister_script or wp_deregister_style.
     *
     * See the WordPress Codex for more information about those functions:
     * http://codex.wordpress.org/Function_Reference/wp_deregister_script
     * http://codex.wordpress.org/Function_Reference/wp_deregister_style
     * */
    /*
     * Styles
     */
    wp_enqueue_style('boss-child-custom', get_stylesheet_directory_uri() . '/css/custom.css');
}

add_action('wp_enqueue_scripts', 'boss_child_theme_scripts_styles', 9999);


/* * **************************** CUSTOM FUNCTIONS ***************************** */

/**
 * Подключаем языковую поддержку
 */
add_action('after_setup_theme', 'mv_load_theme_textdomain');
 
function mv_load_theme_textdomain(){
	load_theme_textdomain( 'mv-xxi-translate', get_stylesheet_directory() . '/languages' );
}

/**
 *
 * Удаляем все CSS стили, которые задают настройки темы
 * 
 */
function remove_redux_css(){ 
remove_action( 'wp_head', 'boss_generate_option_css', 99 ); 
} 
add_action( 'wp_head', 'remove_redux_css' );


/*  стилизуем текст кнопки в диалоговом окне входа */

function mv_login_logo() {
    ?>
    <style type="text/css">
        .wp-core-ui .button-primary{
            text-shadow: none!important;
        }
    </style>
<?php

}

add_action('login_enqueue_scripts', 'mv_login_logo');

/* перенаправляем на страницу У вас нет доступа к данной информации при клике на курс незарегисрированными пользователями */
/* редирект на страницу у вас нет доступа к данному контенту - работает, но это неудобно для пользователей :( */
/*
add_filter("learndash_access_redirect", function($link, $post_id) { 
  //Modify the $link here
  $link = home_url() . '/noaccess';
  PC::debug($link);
  return $link;
  }, 10, 2);
*/ 


/*  сообщение об отсутствии прав доступа - не работает! :( */
add_filter("learndash_content_access", function($restriction_message, $post) {
    $restriction_message = 'Сожалеем, но у вас нет доступа к даннной информации :(';
    PC::debug($restriction_message);
}, 5, 2);

/*
 * 	Перенаправление на тест урока после завершения последней темы урока
 * 	Redirect to a lesson quiz after completing last lesson topic 
 */

/* This snippet hooks into the 'learndash_completion_redirect' to allow override of the redirect link
 */
add_filter('learndash_completion_redirect', function( $link, $post_id ) {
    // We only want to do this for Topics. But the below code can be adapted to work for Lessons
    if (get_post_type($post_id) == 'sfwd-topic') { /* отбираем Темы */
        //PC::debug($lesson_id);
        // First we get the topic progress. This will return all the sibling topics. (родственные Темы)
        // More important it will show the next item 
        $progress = learndash_get_course_progress(null, $post_id);
        // Normally when the user completed topic #3 of #5 the 'next' element will point to the #4 topic. 
        // But when the student reaches the end of the topic chain it will be empty. 
        if (!empty($progress) && ( isset($progress['next']) ) && ( empty($progress['next']) )) {
            // So this is where we now want to get the parent lesson_id and determine if it has a quiz
            $lesson_id = learndash_get_setting($post_id, 'lesson');
            if (!empty($lesson_id)) {
                $lesson_quizzes = learndash_get_lesson_quiz_list($lesson_id);
                if (!empty($lesson_quizzes)) { /* если у урока есть тест */
                    // If we have some lesson quizzes we loop through these to find the first one not completed by the user. 
                    // This should be the first one but we don't want to assume. 
                    foreach ($lesson_quizzes as $lesson_quiz) { /* выбираем первый незавершенный тест */
                        if ($lesson_quiz['status'] == 'notcompleted') {
                            // Once we find a non-completed quiz we set the $link to the quiz 
                            // permalink then break out of out loop
                            $link = $lesson_quiz['permalink'];
                            break;
                        }
                    }
                } else { /*  если же у урока нет тестов - нужно перенаправить на следующий урок */
                    //PC::debug($lesson_id);
                }
            }
        }
    }

    // Always return $link
    return $link;
}, 20, 2);

/* BuddyPress changing */
/* Решаем проблемы с отображением линков в ленте активностей */
/* Все работает, но я пока забанил - пусть ищут ошибку ребята из Будди босс - лохи!
add_filter('bp_get_activity_action', 'mv_remove_avatar_activity', 10, 1);

function mv_remove_avatar_activity($args) {
    global $activities_template;
    return $activities_template->activity->action;
}
*/
/**
 * Подключим файл стилей для админки
 */

function mv_admin_styles() {
  wp_enqueue_style('mv-admin-styles', get_stylesheet_directory_uri() . '/mv_admin_style.css');
}
add_action('admin_enqueue_scripts', 'mv_admin_styles');

/**
 *  Подключаем пользовательские шорткоды - читай мои 
 */


/**
 * plugin_dir_path()
 *  Может быть использована для получения полного системного пути до каталога любого файла. Это не обязательно это должен быть файл плагина. 
 */
require_once( plugin_dir_path( __FILE__ ) . 'shortcodes/shortcodes.php' );