EvaFileParser
=============

Parse large file by PHP5, oop api & support Event Driven. Run under CLI mode please

Usages:

~~~~
use EvaFileParser\Parser;
use EvaFileParser\Event;

Event::listen(Parser::EVENT_FILE_OPEN, function($parser){
	echo 'file opened, file path : ' : $parser->getFile()->getRealPath();
});

Event::listen(Parser::EVENT_PARSE, function($parser){
	//current content
	echo $parser->getContent();
	//current line number
	echo $parser->getCurrentLine();
});

new Parser(__DIR__ . '/data/data.txt', __DIR__ . '/data/output.txt');
$parser->setMaxLine(100);
$parser->run();
~~~~


Support Events:

- Parser::EVENT_FILE_OPEN  trigger when file open
- Parser::EVENT_BEFORE_PARSE trigger before parse start on each parse process
- Parser::EVENT_AFTER_PARSE trigger after parse finished on each parse process
- Parser::EVENT_PARSE trigger each parse process
- Parser::EVENT_PARSE_LINE trigger when reading current line


Performance:

Parse a 99KB txt file, 1250 lines, 5000 lines once max:

    Mem usage : 215.38 kb
    Run Time :268.4ms

 
Parse a 13,163KB txt file, 304,003 lines, 5000 lines once max:

    Mem usage : 283.98 kb
	Run Time :25393ms

Old version:

    Mem usage : 99.47 kb
	Run Time :996.6ms
