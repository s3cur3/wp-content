<?php


function printCustomCSS() {
    $css = of_get_option('custom_css');

    if( $css ) {
        echo "\n\n<!-- Custom styles from the admin menu --><style>\n";
        echo $css;
        echo "</style>\n\n";
    }
}

add_action('ci_styles', 'printCustomCSS');




 