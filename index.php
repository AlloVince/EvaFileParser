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


$memStart = memory_get_usage();
$timeStart = microtime(true);

$dir = __DIR__;
$autoloader = $dir . '/vendor/autoload.php';
$listener = $dir . '/listeners/text.php';
$datafile = $dir. '/data/data.txt';
$output = $dir . '/data/output.txt';
$maxLine = 5000;
$debug = 1;

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
use EvaFileParser\Debuger;

if (file_exists($listener)) {
    include $listener;
} else {
    die('No listener found');
}

$parser = new Parser($datafile, $output);
$parser->setMaxLine($maxLine);
$parser->run();


function convert($size)
{
    $unit=array('b','kb','mb','gb','tb','pb');
    return @round($size/pow(1024,($i=floor(log($size,1024)))),2).' '.$unit[$i];
}

$memEnd = memory_get_usage();
$timeEnd = microtime(true);

if($debug){
    Debuger::log('Mem usage : ' . convert($memEnd - $memStart));
    Debuger::log('Run Time :' . round(($timeEnd - $timeStart) * 1000, 1) . 'ms');
}
