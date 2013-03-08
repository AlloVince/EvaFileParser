<?php
use EvaFileParser\Event;
use EvaFileParser\Parser;

Event::listen(Parser::EVENT_FILE_OPEN, 'EvaFileParser\Structure\UpperLower::onFileOpen');
Event::listen(Parser::EVENT_PARSE, 'EvaFileParser\Structure\UpperLower::onParse');
