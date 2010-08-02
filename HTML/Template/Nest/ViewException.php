<?php
class HTML_Template_Nest_ViewException extends Exception
{
    
    public function __construct($exception, $output)
    {
        $lines = explode("\n", $output);
        
        $message = "Error on line " . $exception->getErrorLine() . ", " . $exception->getErrorString() . ":\n";
        for($i = 0, $il = count($lines); $i < $il; $i++) {
            $line = $lines[$i];
            $message .= $i . ":" . $line . "\n"; 
        }
        
        parent::__construct($message);
    }
}