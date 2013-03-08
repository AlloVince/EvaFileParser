<?php
use EvaFileParser\Event;
use EvaFileParser\Parser;
use EvaFileParser\Debuger;

Event::listen(Parser::EVENT_FILE_OPEN, 'EvaFileParser\Structure\UpperLower::onFileOpen');
Event::listen(Parser::EVENT_PARSE, 'EvaFileParser\Structure\UpperLower::onParse');
if($debug){
    Event::listen(Parser::EVENT_PARSE, function($parser){
        Debuger::log('Processing : line ' . $parser->getCurrentLine() . ' to line ' . ($parser->getCurrentLine() + $parser->getMaxLine()));
    });
}
