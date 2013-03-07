<?php
/**
 * EvaThumber
 * URL based image transformation php library
 *
 * @link      https://github.com/AlloVince/EvaThumber
 * @copyright Copyright (c) 2012-2013 AlloVince (http://avnpc.com/)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @author    AlloVince
 */

error_reporting(E_ALL);

// Check php version
if( version_compare(phpversion(), '5.3.0', '<') ) {
    die(printf('PHP 5.3.0 is required, you have %s', phpversion()));
}


$dir = __DIR__;
$autoloader = $dir . '/vendor/autoload.php';
$localConfig = $dir . '/config.local.php';

if (file_exists($autoloader)) {
    $loader = include $autoloader;
} else {
    die('Dependent library not found, run "composer install" first.');
}

/** Debug functions */
function p($r, $usePr = false)
{
    echo sprintf("<pre>%s</pre>", var_dump($r));
}

$loader->add('EvaFileParser', $dir . '/src');

use EvaFileParser\Parser;
use EvaFileParser\Event;

Event::listen(Parser::EVENT_FILE_OPEN, function($parser){
    //p($parser->getFile()->getSize());
});

Event::listen(Parser::EVENT_PARSE, function($parser){
    //p($parser->getContent());
});

$parser = new Parser(__DIR__ . '/data/dict.txt');
$parser->setMaxLine(50);
$parser->run();
