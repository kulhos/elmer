<?php
/**
 * Created by PhpStorm.
 * User: kulhan
 * Date: 17.12.2015
 * Time: 18:06
 */

require_once("/home/kulhan/creds.php");
date_default_timezone_set('UTC');
require 'dibi.phar';

try {
    dibi::connect(array(
        'driver' => 'mysql',
        'database' => 'netfort_cz',
        'host' => $wgDBhost,
        'username' => $wgDBuser,
        'password' => $wgDBpassword
    ));
    // echo 'Connected';
} catch (DibiException $e) {
    echo get_class($e), ': ', $e->getMessage(), "\n";
    exit('Connection failed');
}

