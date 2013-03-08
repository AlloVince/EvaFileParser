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

    private static $res;

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

    public static function getRes()
    {
        return self::$res;
    }

    public static function writeTo($filePath, $content, $mode = "w")
    {
        if(!$filePath){
            return false;
        }

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
        //$content = $lastContent ? substr($lastContent, -4) . $content : $content;

        self::$res = $res = self::parse($content);
        if($res){
            self::writeTo($parser->getOutput(), $res, 'a');
        }
    }

    public static function parse($text)
    {
        $text = explode("\n", $text);
        $states = array(
            -1 => array(-1, 0),
            0 => array(1, 0),
            1 => array(2, 0),
            2 => array(3, 0),
            3 => array(-1, 4),
            4 => array(5, 0), 
            5 => array(6, 0), 
            6 => array(7, 0),
            7 => array(-1, 4),
        );
        $state = 0;
        $res = '';
        foreach($text as $l){
            $count = strlen($l);
            $i = 0;
            for($i; $i < $count; $i++){
                $cha = $l{$i};
                $letter = ord($cha);
                $isLower = 0;
                if($letter > 96 && $letter < 123){
                    $isLower = 1;
                }
                $nextState = $states[$state][$isLower];
                if($nextState === 4 && $state === 7){
                    $res .= $l{$i - 4};
                }
                $state = $nextState;
            }
        }

        return $res;
    }

    /*
    public static function parse($text)
    {
        $count = strlen($text);
        $fakeText = '';
        $newText = '';
        $i = 0;
        for($i; $i < $count; $i++){
            $cha = $text{$i};
            $letter = ord($cha);
            if($letter > 64 && $letter < 91){
                $fakeText .= '1';
                $newText .= $cha;
            } elseif($letter > 96 && $letter < 123){
                $fakeText .= '0';
                $newText .= $cha;
            }
        }

        //边界条件
        $fakeText = '0' . $fakeText . '0';

        $pos = 0;
        $res = '';
        while(false !== ( $pos = stripos($fakeText, '011101110', $pos) )){
            $pos += 3;
            $res .= $newText{$pos};
        } 
        return $res;
    }
    */


    /*
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
    */
}
