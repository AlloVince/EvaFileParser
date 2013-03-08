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

namespace EvaFileParser;

use SplFileObject;

class Parser
{
    const MODE_BY_BLOCK = 'block';
    const MODE_BY_LINE = 'line';

    const EVENT_FILE_OPEN = 'file.open';
    const EVENT_BEFORE_PARSE = 'before.parse';
    const EVENT_AFTER_PARSE = 'after.parse';
    const EVENT_PARSE = 'parse';
    const EVENT_PARSE_LINE = 'parse.line';
    const EVENT_BLOCK_READ = 'block.read';
    const EVENT_FILE_CLOSE = 'file.close';

    /**
    * Parse Mode
    * value could be MODE_BY_BLOCK or MODE_BY_LINE, default is MODE_BY_LINE
    * 
    * @var string
    */
    protected $mode;

    /**
    * @var SplFileObject
    */
    protected $file;

    /**
    * Source file path
    *
    * @var string 
    */
    protected $filePath;

    /**
    * File seek pointer offset
    * only effect when mode = MODE_BY_BLOCK
    *
    * @var int 
    */
    protected $offset = 0;

    /**
    * File seek current line number
    * only effect when mode = MODE_BY_LINE
    *
    * @var int 
    */
    protected $currentLine = 1;

    /**
    * File seek start line number
    * only effect when mode = MODE_BY_LINE
    *
    * @var int 
    */
    protected $startLine = 1;

    /**
    * Max lines reading in one parse process
    * only effect when mode = MODE_BY_LINE
    *
    * @var int 
    */
    protected $maxLine = 100;

    /**
    * File lock flag
    *
    * @var boolean 
    */
    protected $lock = false;

    /**
    * Current parse progress, 0 - 100
    *
    * @var int
    */
    protected $progress = 0;

    /**
    * Current content of one parse process
    *
    * @var string
    */
    protected $content;

    /**
    * Current line content
    *
    * @var string
    */
    protected $line;

    /**
    * Output file path
    *
    * @var string
    */
    protected $output;

    public function getMode()
    {
        if($this->mode) {
            return $this->mode;
        }

        return $this->mode = self::MODE_BY_LINE;
    }

    public function setMode($mode)
    {
        if(false === in_array($mode, array(self::MODE_BY_BLOCK, self::MODE_BY_LINE))){
            $this->mode = self::MODE_BY_LINE;
            return $this;
        }
        $this->mode = $mode;
        return $this;
    }

    public function getOffset()
    {
        return $this->offset;
    }

    public function setOffset($offset)
    {
        if(false === is_numeric($offset)){
            throw new Exception\InvalidArgumentException(sprintf('Offset require a number, %s input', $offset));
        }
        $this->offset = $offset;
        return $this;
    }

    public function getFilePath()
    {
        return $this->filePath;
    }

    public function setFilePath($filePath)
    {
        $this->filePath = (string) $filePath;
        return $this;
    }

    public function setFile(SplFileObject $file)
    {
        $this->file = $file;
        return $this;
    }

    public function getFile()
    {
        if($this->file){
            return $this->file;
        }

        return $this->file = $this->openFile();
    }

    public function setMaxLine($maxLine)
    {
        $this->maxLine = (int) $maxLine;
        return $this;
    }

    public function getMaxLine()
    {
        return $this->maxLine;
    }

    public function setStartLine($startLine)
    {
        $startLine = (int) $startLine;
        $startLine = $startLine < 1 ? 1 : $startLine;
        $this->startLine = $startLine;
        return $this;
    }

    public function getStartLine()
    {
        return $this->startLine;
    }

    public function getCurrentLine()
    {
        return $this->currentLine;
    }

    public function getOutput()
    {
        return $this->output;
    }

    public function setOutput($output)
    {
        $this->output = $output;
        return $this;
    }

    public function lockFile()
    {
        $this->getFile()->flock(\LOCK_EX);
    }

    public function unlockFile()
    {
        $this->getFile()->flock(\LOCK_UN);
    }

    public function getContent()
    {
        return $this->content;
    }

    public function setContent($content)
    {
        $this->content = $content;
        return $this;
    }

    public function getLine()
    {
        return $this->line;
    }

    public function openFile($filePath = null)
    {
        if($filePath){
            $this->setFilePath($filePath);
        }

        $filePath = $this->getFilePath();
        if(!$filePath){
            throw new Exception\InvalidArgumentException(sprintf('No file path input'));
        }

        if(false === is_readable($filePath)){
            throw new Exception\IOException(sprintf('File %s not readable', $filePath));
        }

        $this->file = new SplFileObject($filePath);
        Event::trigger(self::EVENT_FILE_OPEN, $this);

        return $this->file;
    }

    public function reachEnd()
    {
        return $this->getFile()->eof();
    }

    public function parse()
    {
        Event::trigger(self::EVENT_BEFORE_PARSE, $this);
        if($this->getMode() === self::MODE_BY_LINE){
            $this->parseByLine();
        } else {
            $this->parseByBlock();
        }
        Event::trigger(self::EVENT_AFTER_PARSE, $this);
    }

    /*
    public function split()
    {
        //split by: fixed lines | some text feature mark
    }
    */

    protected function parseByLine()
    {
        $startLine = $this->getStartLine() - 1;
        $maxLine = $this->getMaxLine();
        $file = $this->getFile();
    
        $file->seek($startLine);
        $content = '';
        $endLine = $startLine + $maxLine;
        for($i = $startLine; $i < $endLine; $i++) {
            $line = $file->current();

            //out of range will return false
            if(false === $line){
                break;
            }

            $this->line = $line;
            $this->currentLine = $i + 1;
            $content .= $line;
            Event::trigger(self::EVENT_PARSE_LINE, $this);
            $file->next();
        }

        $this->content = $content;
        
        Event::trigger(self::EVENT_PARSE, $this);
    }

    protected function parseByBlock()
    {
    
    }

    public function run()
    {
        $file = $this->openFile();

        while(!$this->reachEnd()){
            $this->parse();
            $this->setStartLine($this->getCurrentLine() + 1);
        }
    }

    public function __construct($filePath = null, $output = null)
    {
        if($filePath){
            $this->setFilePath($filePath);
        }

        if($output){
            $this->setOutput($output);
        }
    }

}
