<?php
class HTML_Template_Nest_EvalException extends Exception
{
    private $errno;
    private $errstr;
    private $errfile;
    private $errline;
    /**
     * Constructor
     *
     * @param string  $message the error message
     * @param DomNode $node    the node where the error occurred
     *
     * @return HTML_Template_Nest_ParseException
     */
    public function __construct($errno, $errstr, $errfile, $errline)
    {
        $this->errno = $errno;
        $this->errstr = $errstr;
        $this->errfile = $errfile;
        // we have to add two lines to get our error handling setup, so actually line minus 2
        $this->errline = $errline - 2;
        parent::__construct($errstr);
    }
    
    public function getErrorLine() {
        return $this->errline;
    }
    
    public function getErrorString() {
        return $this->errstr;
    }
    
 }