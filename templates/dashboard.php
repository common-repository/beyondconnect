<div class="wrap">
    <h1><?php _e('Dashboard', 'beyondconnect') ?></h1>
    <h2><?php _e('Availability Services', 'beyondconnect') ?></h2>
    <?php

    use Inc\Beyond;

    $option = get_option('beyondconnect_option');

    $response = Beyond::getValues('', '', true);

    echo __('Status', 'beyondconnect') . " API: " . $response . "<br /><br />";

    $entities = Beyond::getValues('', 'value', false);

    if (empty($entities)) {
        _e('Entities: not found', 'beyondconnect');
        return;
    }

    foreach ($entities as $entity) {
        echo $entity['name'] . ": " . Beyond::getValues($entity['url'], '', true) . "<br />";
    }

    echo str_pad('', 4096) . "\n";

    ob_flush();
    flush();
    ob_end_flush();
    ?>
</div>
