<?php
/**
 * BuddyPress - Members Activate
 *
 * @package BuddyPress
 * @subpackage bp-legacy
 * @version 3.0.0
 */
?>

<div id="buddypress">

    <?php
    /**
     * Fires before the display of the member activation page.
     *
     * @since 1.1.0
     */
    do_action('bp_before_activation_page');
    ?>

    <div class="page mvv-activate" id="activate-page">

        <div id="template-notices" role="alert" aria-atomic="true">
            <?php
            /** This action is documented in bp-templates/bp-legacy/buddypress/activity/index.php */
            do_action('template_notices');
            ?>

        </div>

        <?php
        /**
         * Fires before the display of the member activation page content.
         *
         * @since 1.1.0
         */
        do_action('bp_before_activate_content');
        ?>

        <?php if (bp_account_was_activated()) : ?>

            <?php if (isset($_GET['e'])) : ?>
                <p>Ваши данные успешно отправлены!</p>
                <p>Для подтверждения регистрации перейдите по ссылке в письме, которое мы только что отправили на указанный вами e-mail.</p>
                <p>После подтверждения и одобрения администратором, вы сможете войти на сайт с вашими учетными данными.</p>
            <?php else : ?>
                <div class="mv_activation_success" style="text-align: center; margin: 30px auto 20px;">
                <h2 style="margin-bottom: 20px;">Ваша регистрация подтверждена!</h2>
                <p>Теперь ваша учетная запись отправлена на одобрение администратору.</p>
                <p>Вы получите дополнительное письмо об утверждении или отклонении вашей учетной записи</p>
                <p>В случае одобрения вы сможете <a href="<?php echo wp_login_url(bp_get_root_domain()); ?>">Войти</a> на сайт со своими учетными данными</p>                
                </div>
            <?php endif; ?>

        <?php else : ?>

            <h2 style="text-align: center; margin: 30px auto 20px;">Подтвердите регистрацию</h2>

            <form action="" method="post" class="standard-form" id="activation-form" style="text-align: center;">
                <input type="text" name="key" id="key" value="<?php echo esc_attr(bp_get_current_activation_key()); ?>" style="display: none;" />
                <p class="submit">
                    <input type="submit" name="submit" value="Подтвердить" />
                </p>

            </form>

        <?php endif; ?>

        <?php
        /**
         * Fires after the display of the member activation page content.
         *
         * @since 1.1.0
         */
        do_action('bp_after_activate_content');
        ?>

    </div><!-- .page -->

    <?php
    /**
     * Fires after the display of the member activation page.
     *
     * @since 1.1.0
     */
    do_action('bp_after_activation_page');
    ?>

</div><!-- #buddypress -->