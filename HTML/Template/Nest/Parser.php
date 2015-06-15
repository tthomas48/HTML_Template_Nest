<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Compiles nest expressions into php code.
 *
 * Nest expressions are parsed into basic PHP statements. By default 
 * parameters are read out of the $p array. Nested statements can 
 * register local variables instead of using values from the $p array.
 *
 * PHP version 5
 *
 * This source file is subject to the New BSD license, That is bundled
 * with this package in the file LICENSE, and is available through
 * the world-wide-web at
 * http://www.opensource.org/licenses/bsd-license.php
 * If you did not receive a copy of the new BSDlicense and are unable
 * to obtain it through the world-wide-web, please send a note to
 * tthomas48@php.net so we can mail you a copy immediately.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   SVN: $Id$
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_Compiler
 * @since     File available since Release 1.0

 */
require_once 'ParseException.php';
/**
 * Compiles nest expressions into php code.
 *
 * Nest expressions are parsed into basic PHP statements. By default 
 * parameters are read out of the $p array. Nested statements can 
 * register local variables instead of using values from the $p array.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_Compiler
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Parser
{
    public $dependencies = [];
    
    private $_localVariables = [];
    /**
     * Registers a scoped variables. All unregistered variables are expected
     * to come out of the $p array.
     *
     * @param string $key variable name to register
     *
     * @return null
     */
    public function registerVariable($namespace, $key)
    {
        if (isset($this->_localVariables[$namespace][$key])) {
            $this->_localVariables[$namespace][$key]++;
            return;
        }
        $this->_localVariables[$namespace][$key] = 1;
    }
     
    /**
     * Unregisters a scoped variable. Once a variable has been unregistered
     * from all nested local scopes it is assumed to have gone out of range
     * and all further references to it will come out of the $p array.
     *
     * @param string $key variable name to unregister
     *
     * @return null
     */
    public function unregisterVariable($namespace, $key)
    {
        
        if (isset($this->_localVariables[$namespace][$key])) {
            $this->_localVariables[$namespace][$key]--;
        }
        if (isset($this->_localVariables[$namespace][$key]) && $this->_localVariables[$namespace][$key] < 0) {
            $this->_localVariables[$namespace][$key] = 0;
        }
    }

    /**
     * Checks to see if a variable name has been locally scoped.
     *
     * @param string $key variable name to check
     *
     * @return boolean true if the variable has been registered at least once
     */
    public function containsVariable($key)
    {
        foreach($this->_localVariables as $namespace => $keys) {
            if(!isset($keys[$key])) {
                continue;
            }
            if($keys[$key] > 0) {
                return true;
            }
        }
        return false;
    }
    
    public function getLocalVariableName($key)
    {
        // we reverse to start with the lowest nesting first
        $reversedVariables = array_reverse($this->_localVariables, true);
        foreach($reversedVariables as $namespace => $keys) {

            if(!isset($keys[$key])) {
                continue;
            }
            if($keys[$key] > 0) {
                // backwards compatability for tests. use at your own risk
                if($namespace == null) {
                    return $key;
                }
                return "nst_" . str_replace(':', '_', $namespace) ."_".$key;
            }
        }
        throw new HTML_Template_Nest_ParseException("Unable to find local variable $key");
    }
    
    public function getVariableName($namespace, $key)
    {
        if(!isset($this->_localVariables[$namespace])) {
            throw new HTML_Template_Nest_ParseException("Unable to find local variable $key");
        }
        return "nst_" . str_replace(':', '_', $namespace) ."_".$key;
    }
    /**
     * Checks if a string needs to be parse
     *
     * @param string $text text to test
     *
     * @return boolean true if string has parseable tokens
     */
    public function isParseable($text) {
         preg_match('/[$#]\{[^}]+\}/', $text, $tokens);
         return count($tokens) > 0;
    }

    /**
     * Parse a text string replacing tokens with php code.
     *
     * @param string $text text to parse
     *
     * @return string parsed text with tokens replaced with php code
     */
    public function parse($text, $addPhpBlock = true, $quoteChar = "")
    {
        // first escape anything we need to escape
        $text = addcslashes($text, $quoteChar);
        
        preg_match_all('/[$#]\{[^}]+\}/', $text, $tokens);
        foreach ($tokens[0] as $token) {
            if (count($token) > 0) {
                $escape = true;
                if(substr($token, 0, 1) == "#") {
                    $escape = false;
                }
                $parsedToken = "";
                if($addPhpBlock) {
                    // neutering the $ so that we don't accidentally parse as a token later in parsing
                    $parsedToken .= "<?php /* " . str_replace("$", "", $token) . " */ ";
                    $parsedToken .= "echo ";
                    $parsedToken .= ($escape ? "htmlentities(" : "");
                }
                if($quoteChar) {
                    $parsedToken .= $quoteChar . " . ";
                }
                //$parsedToken .= $this->parseToken($token, $quoteChar);
                $parsedToken .= $this->parseToken($token);
                if($quoteChar) {
                    $parsedToken .=  " . " . $quoteChar;
                }
                if($addPhpBlock) {
                    $parsedToken .= ($escape ? ")" : "");
                    $parsedToken .= "?>";
                }
                $text = str_replace($token, $parsedToken, $text);
            }
        }
        return $text;
    }

    /**
     * Parse a single token (${'someValue'}) and replace with php code.
     *
     * @param string $token token to parse
     *
     * @return string php code
     */
    public function parseToken($token, $quoteChar = "") 
    {
        $expression = substr($token, 2, strlen($token) - 3);
        return $this->parseExpression($expression, $quoteChar); 
    }

    /**
     * Parse an expression (the value in between the token delimiters 
     * ${'expression'}) and return php code
     *
     * @param string $expression expression to parse
     *
     * @return string php code
     */
    
    public function parseExpression($expression, $quoteChar = "")
    {
        try {
            $VAR_PATTERN = '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';
            
            $expression = html_entity_decode($expression);
            $remaining = $expression;
    
            $parsedString = "";
            $buffer = "";
            $needEndQuote = false;
            for($pos = 0, $expressionLength = strlen($expression); $pos < $expressionLength; $pos++) {
                $char = substr($expression, $pos, 1);
    
                switch($char) {
                    case "\"":
                    case "'":
                        $endPos = $this->_jumpLiteral($char, $pos, $expression);
                        $parsedString .= $buffer . substr($expression, $pos, $endPos - $pos + 1);
                        $buffer = "";
                        $pos = $endPos;
                        break;
                    case ",":
                        if(preg_match("/^" . $VAR_PATTERN . "$/", $buffer)) {
                            $buffer = $this->parseVariable($buffer, $quoteChar);
                        }
                        $parsedString .= $buffer . ",";
                        $buffer = ""; 
                        break;
                    case "-":
                        if(substr($expression, $pos + 1, 1) == ">") {
                            if(preg_match("/^" . $VAR_PATTERN . "$/", $buffer)) {
                                $buffer = $this->parseVariable($buffer, $quoteChar, "");
                                $needEndQuote = true;
                            }
                        }
                        $parsedString .= $buffer . $char;
                        $buffer = ""; 
                        break;
                    case "(":
                        $parsedString .= $buffer . $char;
                        $buffer = ""; 
                        break;
                    case "[":
                        if(preg_match("/^" . $VAR_PATTERN . "$/", $buffer)) {
                            $buffer = $this->parseVariable($buffer, $quoteChar, "");
                            $needEndQuote = true;
                        }
                        $parsedString .= $buffer . $char;
                        $buffer = ""; 
                        break;
                    case ")":
                    case "]":
                        if(preg_match("/^" . $VAR_PATTERN . "$/", $buffer)) {
                            $buffer = $this->parseVariable($buffer, $quoteChar);
                        }
                        $parsedString .= $buffer . $char;
                        $buffer = ""; 
                        break;
                    case "f":
                        if(substr($expression, $pos, 3) == 'fn:') {
                            // we technically need to make sure that this is followed by an actual method name
                            $pos = $pos + 2;
                            break;
                        }
                        $buffer .= $char;
                        break;
                    case "n":
                        if(substr($expression, $pos, 4) == 'new ') {
                            // we technically need to make sure that this is followed by an actual class name
                            $buffer .= substr($expression, $pos, 4);
                            $pos = $pos + 3;
                            break;
                        }
                        $buffer .= $char;
                        break;
                    case "+":
                    case " ":
                        if(preg_match("/^" . $VAR_PATTERN . "$/", $buffer)) {
                            $buffer = $this->parseVariable($buffer, $quoteChar);
                        }
                        $parsedString .= $buffer . $char;
                        $buffer = "";
                        break;
                    default:
                        $buffer .= $char;
                }
                 
            }

            if(preg_match("/^" . $VAR_PATTERN . "$/", $buffer)) {
                $buffer = $this->parseVariable($buffer, $quoteChar);
            }
            $parsedString .= $buffer;
            return $parsedString;
        } catch(HTML_Template_Nest_ParseException $e) {
            $e->setExpression($expression);
            throw $e;            
        }
    }
    
    private function _jumpLiteral($delimiter, $startPos, $expression) {

        for($pos = $startPos + 1, $expressionLength = strlen($expression); $pos < $expressionLength; $pos++) {
            $char = substr($expression, $pos, 1);
            switch($char) {
                case $delimiter:
                    if(substr($expression, $pos - 1, 1) != '\\') {
                        return $pos;
                    }
            }
        }
        throw new HTML_Template_Nest_ParseException("Unable to find end to string literal[$startPos]: " . substr($expression, $startPos));
    }
    
    /**
     * Parses a single variable and returns either a reference to the global
     * array or the local variable.
     *
     * @param string $variable the variable name to parse
     * 
     * @return string php code to echo the variable
     */
    public function parseVariable($variable, $startQuoteChar = "", $endQuoteChar = null)
    {
        if($endQuoteChar === null) {
            $endQuoteChar = $startQuoteChar;
        }
        if(strcasecmp($variable, "null") == 0) {
            return "null";
        }
        elseif(strcasecmp($variable, "true") == 0) {
            return "true";
        }
        elseif(strcasecmp($variable, "false") == 0) {
            return "false";
        }
        elseif ($this->containsVariable($variable)) {
            return "\$" . $this->getLocalVariableName($variable);
        }
        $prefix = "";
        $suffix = "";
        if($startQuoteChar != "") {
            $prefix = $startQuoteChar . " . ";
            if($endQuoteChar != null) {
                $suffix = " . " . $endQuoteChar;
            }
        }
        return $prefix . "\$_o(\$p, '" . $variable . "')" . $suffix;

    }
    
    public function addFileDependency($path)
    {
    
        $stat = stat($path);
        $lastModified = $stat[9];
    
        $this->dependencies[realpath($path)] = $lastModified;
    }    
}
