<?php
/*
Plugin Name: Web4pro NoIE
Plugin URI:
Description: Shows a stub page for users, who coming from internet explorer 6 or 7
Version: 1.0
Author: Web4pro
Author URI: http://www.web4pro.net/
*/
add_action('template_redirect', 'w4p_redirect');
add_action('wp_enqueue_scripts', 'w4p_add_style');
add_action('admin_menu', 'w4p_noie_admin');

function w4p_noie() //Detection browser
{
    $user_agent = $_SERVER['HTTP_USER_AGENT'];
    $browserIE = false;

    $version_choice = 'w4p_noie_version'; //Get options from admin page
    $enabler = 'w4p_noie_enable';
    $opt_choise = get_option($version_choice);
    $opt_enable = get_option($enabler);

    if ($opt_enable == 'enable') {
        if (preg_match('/MSIE ([0-9].[0-9]{1,2})/', $user_agent, $version)) {
            if ($opt_choise == '7') {
                if ($version[1] <= 7) {
                    $browserIE = true;
                }
            } elseif ($opt_choise == '6') {
                if ($version[1] <= 6) {
                    $browserIE = true;
                }
            }
        }
    }
    return $browserIE;
}

function w4p_redirect() //If browser if IE 6 or 7 - redirect to plug template
{
    if (w4p_noie()) {
        include('noie_template.php');
        exit;
    }

}

function w4p_add_style() //Registering css
{
    if (w4p_noie()) {
        wp_register_style('w4p_style', plugins_url('css/style.css', __FILE__));
        wp_enqueue_style('w4p_style');
    }
}

function w4p_noie_admin() //Creating admin menu
{
    if (function_exists('add_menu_page')) {
        add_menu_page(
            __('No IE'),
            __('No Internet Explorer'),
            8,
            basename(__FILE__),
            'w4p_noie_admin_form'
        );
    }
}

function w4p_noie_admin_form() //Creating admin menu page with settings
{
    $version_choice = 'w4p_noie_version';
    $hidden_field_name = 'w4p_noie_submit_hidden';
    $enabler = 'w4p_noie_enable';

    $opt_choise = get_option($version_choice);
    $opt_enable = get_option($enabler);


    if ($_POST[$hidden_field_name] == 'Y') {
        $opt_choise = $_POST[$version_choice];
        $opt_enable = $_POST[$enabler];

        update_option($version_choice, $opt_choise);
        update_option($enabler, $opt_enable);
        ?>
        <div class="updated"><p><strong><?php _e('Options saved.'); ?></strong></p></div>
    <?php
    }
    ?>
    <div class="wrap">
        <h2><?php _e('No Internet Explorer plugin options') ?></h2>

        <form name="form1" method="post" action="<?php echo str_replace('%7E', '~', $_SERVER['REQUEST_URI']); ?>">
            <input type="hidden" name="<?php echo $hidden_field_name; ?>" value="Y">

            <p><?php _e('Enable IE detection?'); ?></p>
            <select name="w4p_noie_enable">
                <option value="disable" <?php selected($opt_enable, 'disable', true); ?>><?php _e('Disable'); ?></option>
                <option value="enable" <?php selected($opt_enable,'enable', true); ?>><?php _e('Enable'); ?></option>
            </select>

            <p><?php _e('Detect IE versions'); ?></p>
            <select name="w4p_noie_version">
                <option value="7" <?php selected($opt_choise, 7, true); ?>><?php _e('Internet Explorer 7.0 and older'); ?></option>
                <option value="6" <?php selected($opt_choise, 6, true);  ?>><?php _e('Internet Explorer 6.0'); ?></option>
            </select>

            <p class="submit">
                <input type="submit" name="Submit" value="<?php _e('Save') ?>"/>
            </p>
        </form>
    </div>
<?php
}

