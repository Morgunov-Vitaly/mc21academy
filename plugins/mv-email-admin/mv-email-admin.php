<?php
/*
  Plugin Name: mv-email-admin
  Plugin URI: https://morgunovv.wordpress.com/
  Description: Отправляет письма с уведомлением о регистрации нового пользователя на дополнительные адреса
  Version: Номер версии плагина, например: 2.0
  Author: Vitaly Morgunov
  Author URI: https://vk.com/v.morgunov
 */

/* Включаем дебаг почты */

// define the wp_mail_failed callback 
function action_wp_mail_failed($wp_error) {
    return error_log(print_r($wp_error, true));
}

// add the action 
add_action('wp_mail_failed', 'action_wp_mail_failed', 10, 1);

/**
 * Создаем страницу настроек плагина
 */
add_action('admin_menu', 'add_mv_plugin_page');

function add_mv_plugin_page() {
    /**
     * Добавляет дочернюю страницу (подменю) в меню админ-панели «Настройки» (Settings).
     * add_options_page( $page_title, $menu_title, $capability, $menu_slug, $function );
     * 
     * $page_title(строка) (обязательный)  Текст, который будет использован в теге title на странице, настроек.
     *
     * $menu_title(строка) (обязательный)  Текст, который будет использован в качестве называния для пункта меню.
     *
     * $capability(строка) (обязательный)  Название права доступа для пользователя, чтобы ему был показан этот пункт меню. Таблицу возможностей смотрите здесь. 
     * Этот параметр отвечает и за доступ к странице  этого пункта меню.
     *
     * $menu_slug(строка) (обязательный)
     * Идентификатор меню. Нужно вписывать уникальную строку.
     * 
     * Не используйте волшебную константу __FILE__ и пробелы. Пробелы будут вырезаны при формировании URL!
     * 
     * Можно, также указать путь от папки плагина до файла, который будет отвечать за страницу настроек плагина, пр. my-plugin/options.php. 
     * В этом случае, следующий параметр $function указывать не обязательно.
     * 
     * $function(строка)
     * Название функции, которая отвечает за код страницы этого пункта меню.
     * По умолчанию: ''
     */
    add_options_page('Настройки плагина рассылок', 'MV email admin', 'manage_options', 'mv_email_admin', 'mv_email_admin_options_page_output');
}

function mv_email_admin_options_page_output() {
    ?>
    <div class="wrap">
        <h2><?php echo get_admin_page_title() ?></h2>

        <form action="options.php" method="POST">
            <?php
            /**
             * settings_fields( $option_group );
             * Выводит скрытые поля формы на странице настроек (option_page, _wpnonce, ...).
             * $option_group(строка) (обязательный) Название группы настроек. 
             * Должно совпадать с первым параметром $option_group из register_setting( $option_group, ... ).
             */
            settings_fields('mv_option_group');     // скрытые защитные поля
            /**
             * do_settings_sections( $page );
             * Выводит на экран все блоки опций, относящиеся к указанной странице настроек в админ-панели.
             * 
             * $page(строка) (обязательный) Идентификатор страницы админ-панели на которой нужно вывести блоки опций. 
             * Должен совпадать с параметром $page из add_settings_section( $id, $title, $callback, $page ). По умолчанию: нет
             */
            do_settings_sections('mv_email_admin_page'); // секции с настройками (опциями). У нас она всего одна 'section_id'
            submit_button();
            ?>
        </form>
    </div>
    <?php
}

/**
 * Регистрируем настройки.
 * Настройки будут храниться в массиве, а не одна настройка = одна опция.
 */
add_action('admin_init', 'mv_email_admin_plugin_settings');

function mv_email_admin_plugin_settings() {

    /**
     * register_setting( $option_group, $option_name, $sanitize_callback ); Регистрирует новую опцию и callback функцию (функцию обратного вызова) 
     * для обработки значения опции при её сохранении в БД.
     * 
     * 'option_group' - нужно для вывода скрытых полей, для защиты формы. Для settings_fields( 'option_group' );
     * 'option_name' - название самой опции, которая будет записываться в таблицу wp_options
     * 'sanitize_callback' - функция для изменения/очистки передаваемых данных. Её можно не указывать, тогда данные сохраняться как есть (как передаются).
     */
    register_setting('mv_option_group', 'mv_email_admin_option', 'mv_validation_callback');

    /**
     * add_settings_section( $id, $title, $callback, $page );
     * Создает новый блок (секцию), в котором выводятся поля настроек. Т.е. в этот блок затем добавляются опции, с помощью add_settings_field()
     *
     * $id(строка) (обязательный) Идентификатор секции, по которому нужно "цеплять" поля к секции. Строка, которая будет использована для id атрибутов тегов.
     * По умолчанию: нет
     *
     * $title(строка) (обязательный) Заголовок секции. По умолчанию: нет
     *
     * $callback(строка) (обязательный) Функция заполняет секцию описанием. Вызывается перед выводом полей. По умолчанию: нет
     *
     * $page(строка) (обязательный) Страница на которой выводить секцию. Должен совпадать с параметром $page в do_setting_sections( $page );
     * Или может совпадать с параметром $menu_slug из add_menu_page(), add_theme_page(), add_submenu_page().
     * Обычно $page и $menu_slug называются одинаково. По умолчанию: нет
     *
     */
    add_settings_section('mv_section_id', 'Настройки рассылки', '', 'mv_email_admin_page');


    /**
     * add_settings_field( $id, $title, $callback, $page, $section, $args );
     * Создает поле опции для указанной секции (указанного блока настроек).
     * 
     * $id(строка) (обязательный) 
     * Ярлык (slug) опции, используется как идентификатор поля. Используется в ID атрибуте тега.
     *
     * $title(строка) (обязательный)  Название поля.
     *
     * $callback(строка) (обязательный) Название функции обратного вызова. 
     * Функция должна заполнять поле нужным <input> тегом, который станет частью одной большой формы. 
     * Атрибут name должен быть равен параметру $option_name из 	register_setting(). 
     * Атрибут id обычно равен параметру $id. Результат должен сразу выводиться на экран (echo).
     *
     * $page(строка) (обязательный)  Страница меню в которую будет добавлено поле. Указывать нужно ярлык (slug) страницы.
     * Должен быть равен параметру $menu_slug из add_theme_page(). У базовых страниц WordPress названия равны: general, reading, writing и т.д. по аналогии...
     * Или должен быть равен параметру $page из do_settings_sections( $page );
     *
     * $section(строка)
     * Название секции настроек, в которую будет добавлено поле. По умолчанию default или может быть секцией добавленной функцией add_settings_section().
     * По умолчанию: default
     *
     * $args(массив/смешанный)
     * Дополнительные параметры, которые нужно передать callback функции. Например, в паре key/value мы можем передать параметр $id, 
     * который затем использовать для атрибута id поля input, чтобы по нажатию на label в итоговом выводе, фокус курсора попадал в наше поле.
     */
    /* Регистрируем поле с текстом темы письма  */
    add_settings_field(
            'mv_subject', '<label for="mv_subject">Введите текст темы сообщения </label>', 'fill_mv_subject_field', 'mv_email_admin_page', 'mv_section_id'
    );

    /* Регистрируем поле с адресом главного получателя уведомлений */
    add_settings_field(
            'mv_main_address', '<label for="mv_main_address_field">E-mail основного получателя уведомления </label>', 'fill_mv_main_address_field', 'mv_email_admin_page', 'mv_section_id'
    );

    /* Регистрируем поле с дополнительными адресами рассылки */
    add_settings_field(
            'cc_emails_list', '<label for="mv_cc_list">Список адресов рассылок<br><span>address1@mail.ru, address2@yandex.ru, ...</span></label>', 'fill_cc_emails_list_cc_field', 'mv_email_admin_page', 'mv_section_id'
    );

    /* Регистрируем текстовую область с текстом сообщения уведомления */
    add_settings_field(
            'mv_mv_message', 'Текст сообщения администратору (перед данными пользователя)', 'fill_mv_message_field', 'mv_email_admin_page', 'mv_section_id'
    );

    /* Регистрируем  поле со списком параметров для вставки в письмо уведомления вида:  ('наименование поля' : id поля; 'наименование поля 2' : id поля 2; ) */
    add_settings_field(
            'mv_filds_meta', '<label for="mv_filds_meta">Список полей данных пользователя<br><span>наименование поля: id поля; наименование поля 2: id поля 2; ...</span></label>', 'fill_mv_filds_meta_field', 'mv_email_admin_page', 'mv_section_id'
    );

    /**
     * Получает значение указанной настройки (опции).
     * get_option( $option, $default );
     *  
     * $option(строка) (обязательный) по умолчанию: нет. Название опции, значение которой нужно получить. Некоторые из доступных опций:
     *  admin_email - E-mail администратора блога.
     * blogname - название блога. Устанавливается в настройках.
     * blogdescription - описание блога. Устанавливается в настройках.
     * blog_charset - Кодировка блога. Устанавливается в настройках.
     * date_format - формат даты. Устанавливается в настройках.
     * default_category - категория постов по умолчанию. Устанавливается в настройках.
     * home - Адрес домашней страницы блога. Устанавливается в основных настройках.
     * siteurl - Адрес WordPress. Устанавливается в основных настройках.
     * template - название текущей темы.
     * start_of_week - день с которого начинается неделя. Устанавливается в основных настройках.
     * upload_path - каталог загрузки по умолчанию.  Устанавливается в настройках.
     * posts_per_page - максимальное число постов на странице.  Устанавливается в настройках чтения.
     * posts_per_rss -  максимальное число постов выводимых в фид.  Устанавливается в настройках чтения.
     *
     * $default(строка/число/логический) Значение по умолчанию, которое нужно вернуть, если опции в БД не существует.
     * По умолчанию: false	
     */

    /**
     * Заполняем поле cc_emails_list список дополнительных адресов уведомления
     */
    function fill_mv_subject_field() {
        $val = get_option('mv_email_admin_option');
        $val = $val ? $val['mv_subject'] : null;
        ?>
        <input id="mv_main_address_field" type="text" name="mv_email_admin_option[mv_subject]" value="<?php echo esc_attr($val) ?>" />
        <?php
    }

    /**
     * Заполняем поле mv_main_address главный получатель уведосления
     */
    function fill_mv_main_address_field() {
        $val = get_option('mv_email_admin_option');
        $val = $val['main_address'] ? $val['main_address'] : get_option('admin_email'); //Если поле не задано, то по умолчанию - адрес администратора
        ?>
        <input id="mv_main_address_field" type="text" name="mv_email_admin_option[main_address]" value="<?php echo esc_attr($val) ?>" />
        <?php
    }

    /**
     * Заполняем поле cc_emails_list список дополнительных адресов уведомления
     */
    function fill_cc_emails_list_cc_field() {
        $val = get_option('mv_email_admin_option');
        $val = $val ? $val['cc_emails_list'] : null;
        ?>
        <input id="mv_cc_list" type="text" name="mv_email_admin_option[cc_emails_list]" value="<?php echo esc_attr($val) ?>" />
        <?php
    }

    /**
     * Заполняем поле mv_mv_message  - текста уведомления
     */
    function fill_mv_message_field() {
        $val = get_option('mv_email_admin_option');
        $val = $val ? $val['mv_message'] : null;
        ?>
        <p><b>Текст письма уведомления о регистрации пользователя:</b></p>
        <p><textarea rows="20" cols="50" name="mv_email_admin_option[mv_message]"><?php echo esc_attr($val) ?></textarea></p>	
        <?php
    }

    /**
     * Заполняем поле mv_filds_meta - со списком параметров для вставки в письмо уведомления вида:  ('наименование поля' : id поля; 'наименование поля 2' : id поля 2; ) 
     */
    function fill_mv_filds_meta_field() {
        $val = get_option('mv_email_admin_option');
        $val = $val ? $val['mv_filds_meta'] : null;
        ?>
        <input  id="mv_filds_meta" type="text" name="mv_email_admin_option[mv_filds_meta]" value="<?php echo esc_attr($val) ?>" />
        <?php
    }

    /**
     * Валидация введенных данных
     */
    function mv_validation_callback($options) {
        //Надо что-то с валидацией замутить пока просто очищаю от html и php кода
        //mv_subject
        //main_address
        //mv_message
        //cc_emails_list
        //mv_filds_meta
//        foreach ($options as $name => & $val) {
//            if ($name == 'mv_subject') {
//                $val = strip_tags( $val );
//            }
//            if ($name == 'main_address') {
//                $val = strip_tags( $val );
//            }
//            if ($name == 'mv_message') {
//                //$val = intval( $val );
//            }
//            if ($name == 'cc_emails_list') {
//                $val = strip_tags( $val );
//            }      
//            if ($name == 'mv_filds_meta') {
//                $val = strip_tags( $val );
//            }             
//        }
        //die(print_r( $options )); // Array ( [input] => aaaa [checkbox] => 1 )

        return $options;
    }

}

/**
 *  Меняем письмо уведомление о создании нового пользователя
 * перезапишет стандартную функцию WordPress - она подключаемая - обернута в if function_exists
 * wp_new_user_notification() 
 * Уведомляет по почте администратора сайта о регистрации нового пользователя 
 * и отправляет пользователю письмо с логином и паролем для авторизации.
 * 
 * wp_new_user_notification( $user_id, $plaintext_pass, $notify );
 * $user_id(число) (обязательный) ID пользователя. По умолчанию: нет
 * 
 * $plaintext_pass(строка/устарел)
 * Устарел с версии 4.3.1. и указывается как null.
 * Пароль пользователя. Указывать надо обычный пароль, а не md5() код.
 * По умолчанию: null
 * 
 * $notify(строка)
 * Определяет тип уведомления. C версии 4.6.
 * admin или пустая строка ('') - уведомление получит только админ.
 * 'user' - уведомление получит только созданный пользователь.
 * 'both' - уведомления получат админ и созданный пользователь.
 * По умолчанию: ''
 */

/**
 * Переопределяем функцию wp_new_user_notification( $user_id, null, 'both' );  
 */
function wp_new_user_notification($user_id, $deprecated = null, $notify = '') {
    if ($deprecated !== null) {
        _deprecated_argument(__FUNCTION__, '4.3.1');
    }

    global $wpdb, $wp_hasher;
    $user = get_userdata($user_id);

    // The blogname option is escaped with esc_html on the way into the database in sanitize_option
    // we want to reverse this for the plain text arena of emails.
    $blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

    if ('user' !== $notify) { // если уведомлять не только пользователя
        $switched_locale = switch_to_locale(get_locale());

        /* вытаскиваем значения опций из страницы настроек плагина */
        $mv_options = get_option('mv_email_admin_option');
        $mv_subject = $mv_options['mv_subject'] ? $mv_options['mv_subject'] : __('New User Registration mv-email-admin'); // Тема письма
        $mv_main_address = $mv_options['main_address'] ? $mv_options['main_address'] : get_option('admin_email'); // Главный адресат рассылки main_address
        $message = $mv_options['mv_message'] ? $mv_options['mv_message'] : sprintf(__('New user registration on your site %s:'), $blogname) . "\r\n\r\n";  //Текст сообщения 
        $cc_mail = $mv_options['cc_emails_list'] ? $mv_options['cc_emails_list'] : null; // Копии письма - адресаты cc_emails_list
        $mv_filds_meta_field_string = $mv_options['mv_filds_meta'] ? $mv_options['mv_filds_meta'] : null; // мета опции полей с данными о пользователе

        /* translators: %s: site title */

        /**
         * explode(";", $mv_filds_meta_field_string);
         * разбивает строку $mv_filds_meta_field_string на элементы массива по разделителю ";" mv_filds_meta  из  mv_email_admin_option[mv_filds_meta]
         * получим массив вида 
         * [0] =>'наименование поля 0: id поля 0'
         * [1] =>'наименование поля 1: id поля 1'
         * [2] =>'наименование поля 2: id поля 2'
         */
        $mv_filds_meta_field_array = explode(";", $mv_filds_meta_field_string);

        foreach ($mv_filds_meta_field_array as $value) {
            /* цикл - распарсиваем  строку с мета опциями полей */
            $mv_field_array = explode(":", trim($value)); //Получаем массив вида  $mv_field_array[0] => 'наименование поля', $mv_field_array[1] => id поля
            $mv_field_id = trim($mv_field_array[1]); // id поля
            $mv_field_name = trim($mv_field_array[0]); // 'наименование поля
            if ($mv_field_id) {
                /* Вытаскиваем значения полей BuddyPress xprofile  */
                $mv_option = xprofile_get_field_data($mv_field_id, $user_id); // Забираем параметр
                if ($mv_option) {
                    $message .= $mv_field_name . ": " . strip_tags($mv_option) . "\r\n"; // Добавляем параметр
                }
            }
        }

        /* translators: %s: user login */
        $message .= "ID Пользователя: " . $user_id . "\r\n";

        $message .= sprintf(__('Username: %s'), $user->user_login) . "\r\n";

        /* translators: %s: user email address */
        $message .= sprintf(__('Email: %s'), $user->user_email); //. "\r\n"

        $admin_email = get_option('admin_email');
        if (empty($admin_email)) {
            $admin_email = 'noreply@mc21academy.ru';
        }

        $headers = array(
            "From: \"{$blogname}\" <{$admin_email}>\n",
            "Content-Type: text/plain; charset=\"" . get_option('blog_charset') . "\"\n",
            "Cc: " . $cc_mail . "\n",
            "reply-to: " . $user->user_email . "\n",
        );
        // 'From: ' . $blogname . ' <noreply@mc21academy.ru>'
        // text/html

        $wp_new_user_notification_email_admin = array(
            "to" => $mv_main_address,
            /* translators: Password change notification email subject. %s: Site title */
            "subject" => $mv_subject,
            "message" => $message,
            "headers" => $headers
        );

        /**
         * Filters the contents of the new user notification email sent to the site admin.
         *
         * @since 4.9.0
         *
         * @param array   $wp_new_user_notification_email {
         *     Used to build wp_mail().
         *
         *     @type string $to      The intended recipient - site admin email address.
         *     @type string $subject The subject of the email.
         *     @type string $message The body of the email.
         *     @type string $headers The headers of the email.
         * }
         * @param WP_User $user     User object for new user.
         * @param string  $blogname The site title.
         */
        $wp_new_user_notification_email_admin = apply_filters('wp_new_user_notification_email_admin', $wp_new_user_notification_email_admin, $user, $blogname);

        @wp_mail(
                        $wp_new_user_notification_email_admin['to'], wp_specialchars_decode(sprintf($wp_new_user_notification_email_admin['subject'], $blogname)), $wp_new_user_notification_email_admin['message'], $wp_new_user_notification_email_admin['headers']);

        if ($switched_locale) {
            restore_previous_locale();
        }
    }

    // `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
    if ('admin' === $notify || ( empty($deprecated) && empty($notify) )) { //если уведомлять только администратора
        return;
    }

    // Generate something random for a password reset key.
    $key = wp_generate_password(20, false);

    /** This action is documented in wp-login.php */
    do_action('retrieve_password_key', $user->user_login, $key);

    // Now insert the key, hashed, into the DB.
    if (empty($wp_hasher)) {
        require_once ABSPATH . WPINC . '/class-phpass.php';
        $wp_hasher = new PasswordHash(8, true);
    }
    $hashed = time() . ':' . $wp_hasher->HashPassword($key);
    $wpdb->update($wpdb->users, array('user_activation_key' => $hashed), array('user_login' => $user->user_login));

    $switched_locale = switch_to_locale(get_user_locale($user));

    /* translators: %s: user login */
    $message = sprintf(__('Username: %s'), $user->user_login) . "\r\n\r\n";
    $message .= __('To set your password, visit the following address:') . "\r\n\r\n";
    $message .= '<' . network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login') . ">\r\n\r\n";

    $message .= wp_login_url() . "\r\n";

    $wp_new_user_notification_email = array(
        'to' => $user->user_email,
        /* translators: Password change notification email subject. %s: Site title */
        'subject' => __('[%s] Your username and password info'),
        'message' => $message,
        'headers' => '',
    );

    /**
     * Filters the contents of the new user notification email sent to the new user.
     *
     * @since 4.9.0
     *
     * @param array   $wp_new_user_notification_email {
     *     Used to build wp_mail().
     *
     *     @type string $to      The intended recipient - New user email address.
     *     @type string $subject The subject of the email.
     *     @type string $message The body of the email.
     *     @type string $headers The headers of the email.
     * }
     * @param WP_User $user     User object for new user.
     * @param string  $blogname The site title.
     */
    $wp_new_user_notification_email = apply_filters('wp_new_user_notification_email', $wp_new_user_notification_email, $user, $blogname);

    wp_mail(
            $wp_new_user_notification_email['to'], wp_specialchars_decode(sprintf($wp_new_user_notification_email['subject'], $blogname)), $wp_new_user_notification_email['message'], $wp_new_user_notification_email['headers']
    );

    if ($switched_locale) {
        restore_previous_locale();
    }
}