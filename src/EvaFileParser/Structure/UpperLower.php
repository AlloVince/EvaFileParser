<?php
/**
 * EvaFileParser
 * Large file parse tool by event driven
 *
 * @link      https://github.com/AlloVince/EvaFileParser
 * @copyright Copyright (c) 2013 AlloVince (http://avnpc.com/)
 * @license   http://framework.zend.com/license/new-bsd New BSD License
 * @author    AlloVince
 */

namespace EvaFileParser\Structure;

use EvaFileParser\Parser;
use EvaFileParser\Exception;

class UpperLower
{
    private static $saves = array();

    public static function saveVar($varName, $value)
    {
        self::$saves[$varName] = $value;
    }

    public static function loadVar($varName)
    {
		if (isset(self::$saves[$varName])) {
            return self::$saves[$varName];
        }
        return null;
    }

    public static function writeTo($filePath, $content, $mode = "w")
    {
        if (!$handle = fopen($filePath, $mode)) {
            throw new Exception\IOException(sprintf('File %s open failed', $filePath));
        }

        if (fwrite($handle, $content) === false) {
            throw new Exception\IOException(sprintf('File %s write failed', $filePath));
        }

        @fclose($handle);
        return true;
    }

    public static function onFileOpen($parser)
    {
        $output = $parser->getOutput();
        if($output){
            @unlink($output);
        }
    }

    public static function onParse($parser)
    {
        $output = $parser->getOutput();
        $lastContent = self::loadVar('lastContent');
        $content = $parser->getContent();
        self::saveVar('lastContent', $content);

        //-4 because maybe content \n
        $content = $lastContent ? substr($lastContent, -4) . $content : $content;

        $res = self::parse($content);
        if($res){
            self::writeTo($parser->getOutput(), $res, 'a');
        }
    }

    protected static function parse($text)
    {
        $textArray = str_split($text);
        $count = count($textArray);
        $i = 0;
        $text = '';


        for($i; $i < $count; $i++){
            $letter = ord($textArray[$i]);
            if($letter > 64 && $letter < 91){
                $text .= '1';
            } elseif($letter > 96 && $letter < 123){
                $text .= '0';
            } else {
                unset($textArray[$i]);
            }
        }
        $textArray = array_values($textArray);

        //边界条件
        $text = '0' . $text . '0';
        array_push($textArray, '');
        array_unshift($textArray, '');

        $pos = 0;
        $res = '';
        while(false !== ( $pos = stripos($text, '011101110', $pos) )){
            $pos += 4;
            $res .= $textArray[$pos];
        } 
        return $res;
    }
}
