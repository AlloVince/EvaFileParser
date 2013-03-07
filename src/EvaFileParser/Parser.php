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

class Parser
{
    const MODE_BY_BLOCK = 'block';
    const MODE_BY_LINE = 'line';

    const EVENT_FILE_OPEN = 'file.open';
    const EVENT_BEFORE_PARSE = 'before.parse';
    const EVENT_AFTER_PARSE = 'after.parse';
    const EVENT_PARSE = 'parse';
    const EVENT_BLOCK_READ = 'block.read';
    const EVENT_FILE_CLOSE = 'file.close';

    protected $mode;

    protected $file;

    protected $filepath;

    protected $offset = 0;

    protected $fileLock = false;

    protected $progress = 0;

    public function getMode()
    {
        return $this->mode;
    }

    public function setMode()
    {
    
    }
}
