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

namespace EvaFileParserTest\StructureTest;

use EvaFileParser\Parser;
use EvaFileParser\Exception;
use EvaFileParser\Structure\UpperLower;

class UpperLowerTest extends \PHPUnit_Framework_TestCase
{
    public function testInnerParse()
    {
        $text = '';
        $this->assertEquals('', UpperLower::parse($text));

        $text = 'foobar';
        $this->assertEquals('', UpperLower::parse($text));

        $text = 'ABCdEFG';
        $this->assertEquals('d', UpperLower::parse($text));


        $text = 'aABCdEFGa';
        $this->assertEquals('d', UpperLower::parse($text));

        $text = 'AABCdEFG';
        $this->assertEquals('', UpperLower::parse($text));

        $text = 'ABCdEFGG';
        $this->assertEquals('', UpperLower::parse($text));

        $text = 'GABCdEFGG';
        $this->assertEquals('', UpperLower::parse($text));

        $text = '!ABCdEFG#';
        $this->assertEquals('d', UpperLower::parse($text));

        $text = '!ABCdEFG#';
        $this->assertEquals('d', UpperLower::parse($text));

        $text = 'ABCddEFG';
        $this->assertEquals('', UpperLower::parse($text));

        $text = 'ABCdEFGasadHKJHKHOIUOIaaa';
        $this->assertEquals('d', UpperLower::parse($text));

        $text = 'sajdlkajABCdEFG';
        $this->assertEquals('d', UpperLower::parse($text));
    }


    public function testTwoParts()
    {
        $parser = new Parser();
        $parser->setContent('ABCdEFG');
        UpperLower::saveVar('lastContent', "JKLJa\n");
        UpperLower::onParse($parser);
        $this->assertEquals('d', UpperLower::getRes());

        $parser = new Parser();
        $parser->setContent('ABCdEFG');
        UpperLower::saveVar('lastContent', "JKLJ\n");
        UpperLower::onParse($parser);
        $this->assertEquals('', UpperLower::getRes());

        $parser = new Parser();
        $parser->setContent('ABCdEFGasdsada');
        UpperLower::saveVar('lastContent', "sasHKLaKLJb\n");
        UpperLower::onParse($parser);
        $this->assertEquals('d', UpperLower::getRes());
    
    }
}
