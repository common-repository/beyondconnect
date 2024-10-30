<div class="wrap">
    <h1><?php _e('Shortcodes', 'beyondconnect') ?></h1>
    <h2>
        <?php _e('Available Shortcodes', 'beyondconnect') ?>
    </h2>
    <?php settings_errors(); ?>

    <?php
    global $shortcode_tags;
    $prefix = "beyondconnect";
    ?>

    <ul>
        <?php

        foreach ($shortcode_tags as $code => $function) {
            if (substr($code, 0, strlen($prefix)) !== $prefix)
                continue;
            ?>
            <li>
                [<?php echo $code; ?>]
            </li>
            <?php
        }
        ?>
    </ul>
</div>