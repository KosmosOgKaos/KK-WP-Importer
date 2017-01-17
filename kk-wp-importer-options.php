<?php

add_action( 'admin_menu', 'kk_wp_importer_add_admin_menu' );

function kk_wp_importer_add_admin_menu(  ) {
    add_options_page( 'KK WP Importer', 'KK WP Importer', 'manage_options', 'kk_wp_importer', 'kk_wp_importer_options_page' );
}

function kk_wp_importer_scripts() {
    wp_enqueue_script( 'kk_json_import_admin_page', plugin_dir_url( __FILE__ ) . 'kk-wp-importer.js', array('jquery'), '1.0' );
}

add_action( 'admin_enqueue_scripts', 'kk_wp_importer_scripts' );


function kk_wp_importer_options_page(  ) {
    ?>
    <h2>KK WP Importer</h2>
    <form action="" method="post">
        <input type="text" name="jsonFile"><br/>
        <input type="submit" value="submit">
    </form>
    <?php
    if (isset($_POST["jsonFile"]) && !empty($_POST["jsonFile"])) {
        $file = $_POST["jsonFile"];
        $json = file_get_contents($file);
        $json = json_decode($json);
        init_loop($json);
    }
}

function init_loop($json) {
    $nodes = array();
    foreach ($json as $field) {
        $node = new Node($field);
        array_push($nodes, $node);
    }
    foreach ($nodes as $node) {
        print_r($node);
        $node->create_post();
    }
}