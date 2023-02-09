<?php

declare( strict_types = 1 );

require '../app/App.php';
require '../app/Sale.php';
//require '../app/helpers.php';

$app = new App( $argv[1], $argv[2] );
$app->get_profit_by_category();

?>