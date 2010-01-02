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
    private $_localVariables = array();
    /**
     * Registers a scoped variables. All unregistered variables are expected
     * to come out of the $p array.
     *
     * @param string $key variable name to register
     *
     * @return null
     */
    public function registerVariable($key)
    {
        if (isset($this->_localVariables[$key])) {
            $this->_localVariables[$key]++;
            return;
        }
        $this->_localVariables[$key] = 1;
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
    public function unregisterVariable($key)
    {
        if (isset($this->_localVariables[$key])) {
            $this->_localVariables[$key]--;
        }
        if ($this->_localVariables[$key] < 0) {
            $this->_localVariables[$key] = 0;
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
        if (!isset($this->_localVariables[$key])) {
            return false;
        }
        return $this->_localVariables[$key] > 0;
    }

    /**
     * Parse a text string replacing tokens with php code.
     *
     * @param string $text text to parse
     *
     * @return string parsed text with tokens replaced with php code
     */
    public function parse($text)
    {
        preg_match_all('/(\$\{[^}]+\})/', $text, $tokens);
        foreach ($tokens as $token) {
            if (count($token) > 0) {
                $parsedToken = "<?php echo " . $this->parseToken($token[0]) . "?>";
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
    public function parseToken($token) 
    {
        $expression = substr($token, 2, strlen($token) - 3);
        return $this->parseExpression($expression); 
    }

    /**
     * Parse an expression (the value in between the token delimiters 
     * ${'expression'}) and return php code
     *
     * @param string $expression expression to parse
     *
     * @return string php code
     */
    public function parseExpression($expression)
    {
        
        $VAR_PATTERN = '[a-zA-Z_\x7f-\xff][a-zA-Z0-9_\x7f-\xff]*';        
        // If it's only a single php variable, then
        $remaining = $expression;
        if (preg_match("/^$VAR_PATTERN$/", $expression)) {
            $expression = $this->parseVariable($expression);
            $remaining = "";
        }

        // this handles members and methods
        $SINGLE_QUOTE_PATTERN = "(?:\"([^\"]|(?:\\\"))*\")";
        $DOUBLE_QUOTE_PATTERN = "(?:'([^']|(?:\\'))*')";
        $MEMBER_PATTERN = '/((?P<variable>' . $VAR_PATTERN . 
            ')(?P<operation>(?:->' . $VAR_PATTERN . 
            '(?:\((?P<params>(?:\s*(?:(?:' . 
            $VAR_PATTERN. ')|' . $SINGLE_QUOTE_PATTERN . 
            '|' . $DOUBLE_QUOTE_PATTERN . 
            ')\s*,?\s*)*)\))?)+))/';
        if (preg_match_all($MEMBER_PATTERN, $expression, $matches, PREG_SET_ORDER)) {
            
            $variable = $matches[0]["variable"];
            $operation = $matches[0]["operation"];
            if (array_key_exists("params", $matches[0])) {
                $paramList = array();
                $params = $matches[0]["params"];
                if (strlen($params)) {
                    $paramList = explode(",", $params);
                    for ($i = 0, $il = count($paramList); $i < $il; $i++) {
                        $value = trim($paramList[$i]);
                        if (preg_match("/^$VAR_PATTERN$/", $value)) {
                            $value = $this->parseVariable($value);
                        }
                        $paramList[$i] = $value;
                    }
                }
                $operation = str_replace(
                    $params, implode(",", $paramList), $operation
                );
            }
            
            $expression = str_replace(
                $expression, 
                $matches[0][0], 
                $this->parseVariable($variable) . $operation
            );
            $remaining = str_replace($matches[0][0], "", $remaining);
        }

        
        $FN_PATTERN = '/(fn:' . $VAR_PATTERN . 
            '(?:\((?P<params>(?:\s*(?:(?:' . $VAR_PATTERN . 
            ')|' . $SINGLE_QUOTE_PATTERN . '|' . 
            $DOUBLE_QUOTE_PATTERN . ')\s*,?\s*)*)\)))/';
        if (preg_match_all($FN_PATTERN, $remaining, $matches, PREG_SET_ORDER)) {
            $match = substr($matches[0][0], 3);
            if (array_key_exists("params", $matches[0])) {
                $params = $matches[0]["params"];
                if (strlen($params)) {
                    $paramList = explode(",", $params);
                    for ($i = 0, $il = count($paramList); $i < $il; $i++) {
                        $value = trim($paramList[$i]);
                        if (preg_match("/^$VAR_PATTERN$/", $value)) {
                            $value = $this->parseVariable($value);
                        }
                        $paramList[$i] = $value;
                    }
                }
                $match = str_replace($params, implode(",", $paramList), $match);
                $expression = str_replace($matches[0][0], $match, $expression);
                $remaining = str_replace($matches[0][0], "", $remaining);
            }
        }        
        
        // handle any string literals, we just remove them from remaining, they
        // get left as is in the expression
        $singleQuoteMatch = preg_match_all(
            '/' . $SINGLE_QUOTE_PATTERN . '/', $remaining, $matches
        );
        if ($singleQuoteMatch) {
            foreach ($matches[0] as $match) {
                $remaining = str_replace($match, "", $remaining);
            }
        }
        
        $doubleQuoteMatch = preg_match_all(
            '/' . $DOUBLE_QUOTE_PATTERN . '/', $remaining, $matches
        ); 
        if ($doubleQuoteMatch) {
            foreach ($matches[0] as $match) {
                $remaining = str_replace($match, "", $remaining);
            }
        }
        
        // handle any remaining tags
        $REMAINING_VAR_PATTERN = '(?:^(' . $VAR_PATTERN . 
            '))|(?:[^>$](' . $VAR_PATTERN . ')$)';
        if (preg_match_all('/' . $VAR_PATTERN . '/', $remaining, $matches)) {
            foreach ($matches[0] as $match) {
                $value = $this->parseVariable($match);
                $expression = str_replace($match, $value, $expression);
                $remaining = str_replace($match, "", $remaining);
            }
        }

        return $expression;
    }

    /**
     * Parses a single variable and returns either a reference to the global
     * array or the local variable.
     *
     * @param string $variable the variable name to parse
     * 
     * @return string php code to echo the variable
     */
    public function parseVariable($variable)
    {
        if ($this->containsVariable($variable)) {
            return "\$" . $variable;
        }
        return "\$p['" . $variable . "']";

    }
}