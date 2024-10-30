<div class="wrap">
    <h1><?php _e('Settings', 'beyondconnect') ?></h1>
    <?php settings_errors(); ?>
    <button class="tablink" onclick="openPage('Connection', this)"
            id="defaultOpen"><?php _e('Connection', 'beyondconnect') ?></button>
    <button class="tablink"
            onclick="openPage('Paymentgateways', this)"><?php _e('Payment Gateways', 'beyondconnect') ?></button>
    <button class="tablink" onclick="openPage('Widgets', this)"><?php _e('Widgets', 'beyondconnect') ?></button>
    <button class="tablink" onclick="openPage('Shortcodes', this)"><?php _e('Shortcodes', 'beyondconnect') ?></button>
    <button class="tablink" onclick="openPage('Options', this)"><?php _e('Options', 'beyondconnect') ?></button>

    <form method="post" action="options.php">
        <div id="Connection" class="tabcontent">
            <?php
            settings_fields('beyondconnect_components_settings');
            do_settings_sections('beyondconnect_connection_page');
            submit_button();
            ?>
        </div>

        <div id="Paymentgateways" class="tabcontent">
            <?php
            settings_fields('beyondconnect_components_settings');
            do_settings_sections('beyondconnect_paymentgateways_page');
            submit_button();
            ?>
        </div>

        <div id="Widgets" class="tabcontent">
            <?php
            settings_fields('beyondconnect_components_settings');
            do_settings_sections('beyondconnect_widgets_page');
            submit_button();
            ?>
        </div>

        <div id="Shortcodes" class="tabcontent">
            <?php
            settings_fields('beyondconnect_components_settings');
            do_settings_sections('beyondconnect_shortcodes_page');
            submit_button();
            ?>
        </div>

        <div id="Options" class="tabcontent">
            <?php
            settings_fields('beyondconnect_components_settings');
            do_settings_sections('beyondconnect_options_page');
            submit_button();
            ?>
        </div>
    </form>
</div>