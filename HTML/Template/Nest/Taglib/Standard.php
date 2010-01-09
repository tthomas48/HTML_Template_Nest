<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * The standard tag library provides the basic control structures needed to build
 * a dynamic web page.
 *
 * The tags included replace the basic control functionality such as if/elseif/else
 * and foreach. Include using xlmns:c="taglib/StandardTagLib" @todo Add Switch
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
 * @since     File available since Release 1.0
 */
require_once 'HTML/Template/Nest/Tag.php';
require_once 'HTML/Template/Nest/Taglib.php';


/**
 * The standard tag library provides the basic control structures needed to build
 * a dynamic web page.
 *
 * The tags included replace the basic control functionality such as if/elseif/else
 * and foreach. Include using xlmns:c="taglib/StandardTagLib" 
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Taglib_Standard extends HTML_Template_Nest_Taglib
{
    protected $tags = array(
        "if" => "HTML_Template_Nest_Taglib_Standard_IfTag", 
        "elseif" => "HTML_Template_Nest_Taglib_Standard_ElseIfTag", 
        "else" => "HTML_Template_Nest_Taglib_Standard_ElseTag", 
        "foreach" => "HTML_Template_Nest_Taglib_Standard_ForeachTag",
        "set" => "HTML_Template_Nest_Taglib_Standard_SetTag",
        
    );        
}

/**
 * If control structure.
 *
 * Takes the @test attribute. 
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Taglib_Standard_IfTag extends HTML_Template_Nest_Tag
{
    /**
     * Evaluated before the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::start()
     */   
    public function start() 
    {
        $test = $this->compiler->parser->parseExpression(
            $this->getRequiredAttribute("test")
        );
        return "<?php if($test) {?>";
    }

    /**
     * Evaluated after the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::end()
     */   
    public function end() 
    {
        $nextSibling = $this->findNextSibling($this->node);
        if ($nextSibling == null
            || ($nextSibling->localName != "else" 
            && $nextSibling->localName != "elseif")
        ) {
            return "<?php } ?>";
        }
    }    
}

/**
 * ElseIf control structure.
 *
 * Takes the @test attribute. 
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Taglib_Standard_ElseIfTag extends HTML_Template_Nest_Tag
{
    /**
     * Evaluated before the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::start()
     */   
    public function start() 
    {
        $test = $this->compiler->parser->parseExpression(
            $this->getRequiredAttribute("test")
        );
        return "<?php } elseif($test) {?>";
    }

    /**
     * Evaluated after the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::end()
     */   
    public function end() 
    {
        $nextSibling = $this->findNextSibling($this->node);
        if ($nextSibling == null
            || ($nextSibling->localName != "else"
            && $nextSibling->localName != "elseif")
        ) {
            return "<?php } ?>";
        }
    }    
}

/**
 * Else control structure.
 *
 * Takes no attributes. 
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Taglib_Standard_ElseTag extends HTML_Template_Nest_Tag
{
    /**
     * Evaluated before the content of the tag.
     *
     * @return string content to add to php file
     *  
     * @see HTML_Template_Nest_Tag::start()
     */   
    public function start() 
    {
        return "<?php } else {?>";
    }

    /**
     * Evaluated after the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::end()
     */   
    public function end() 
    {
        return "<?php } ?>";
    }    
}

/**
 * Foreach tag.
 *
 * Takes two attributes: @items the array of elements, @var the variable name 
 * for the value optionally @key. @rowcount if specified will contain the 
 * current row.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Taglib_Standard_ForeachTag extends HTML_Template_Nest_Tag
{
    /**
     * Evaluated before the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::start()
     */   
    public function start() 
    {
        $items = $this->compiler->parser->parseExpression(
            $this->getRequiredAttribute("items")
        );
        $var = $this->getRequiredAttribute("var");
        $this->registerVariable($var);
        
        $output = "";
        $position = $this->getOptionalAttribute("position");
        if (!empty($position)) {
            $this->registerVariable($position);
            $output = "\$$position = 0;\n";
        }
        
        $key = $this->getOptionalAttribute("key");
        if (!empty($key)) {
            $this->registerVariable($key);
            $output .= "foreach($items as \$$var => \$$key) {\n";
            return $this->wrapOutput($output);
        }
        $output .= "foreach($items as \$$var) {\n";
        return $this->wrapOutput($output);
    }

    /**
     * Evaluated after the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::end()
     */   
    public function end() 
    {
        $output = "";
        $var = $this->getRequiredAttribute("var");        
        $this->unregisterVariable($var);
        $position = $this->getOptionalAttribute("position");
        if (!empty($position)) {
            $this->unregisterVariable($position);
            $output = "\$$position++;\n";
        }
        $key = $this->getOptionalAttribute("key");
        if (!empty($key)) {
            $this->unregisterVariable($key);
        }
        $output .= "}";
        return $this->wrapOutput($output);
    }
}

/**
 * Set tag.
 *
 * Takes two attributes: @var the variable name for the value and @value.
 * @value can be a nest expression. 
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License 
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Taglib_Standard_SetTag extends HTML_Template_Nest_Tag
{
    /**
     * Evaluated before the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::start()
     */   
    public function start() 
    {
        $var = $this->getRequiredAttribute("var");
        $this->registerVariable($var);        
        
        $input = $this->getRequiredAttribute("value"); 
        $value = $this->compiler->parser->parse(
            $input, false
        );
        
        $output = "<?php ";
        if ($input != $value) {
            $output .= "\$$var = $value;\n";
        } else {
            $output .= "\$$var = \"" . addcslashes($value, "\"") . "\";\n";
        }
        $output .= "?>";
        return $output;        
    }
    
    /**
     * Evaluated after the content of the tag.
     *
     * @return string content to add to php file
     * 
     * @see HTML_Template_Nest_Tag::end()
     */   
    public function end() 
    {
        $var = $this->getRequiredAttribute("var");
        $this->unregisterVariable($var);
    }
}