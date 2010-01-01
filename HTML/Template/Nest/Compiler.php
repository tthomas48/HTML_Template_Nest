<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Compiles .nst files into .php files
 *
 * The HTML_Template_Nest_Compiler takes .nst input files and converts them into
 * .php files that can be included just like any other file. They
 * are expected to be included via the HTML_Template_Nest_View class.
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
 * @see       HTML_Template_Nest_View
 * @since     File available since Release 1.0
 */
require_once "HTML/Template/Nest/Parser.php";
require_once "HTML/Template/Nest/CompilerException.php";
/**
 * Compiles .nst files into .php files
 *
 * The HTML_Template_Nest_Compiler takes .nst input files and converts them into
 * .php files that can be included just like any other file. They
 * are expected to be included via the HTML_Template_Nest_View class.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_View*
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Compiler
{
    public $parser;

    /**
     * Compile a nst into a php file and cache the output
     *
     * @param string $filename path to file to compile
     *
     * @return null
     */
    public function compileAndCache($filename)
    {
        $outputFilename = str_replace(".nst", ".php", $filename);
        file_put_contents($outputFilename, $this->compile($filename));
    }

    /**
     * Compile a nst into a php file and return the output as a string
     *
     * @param string $filename path to file to compile
     *
     * @throws HTML_Template_Nest_CompilerException
     * @return string compiled output
     */
    public function compile($filename)
    {
        set_error_handler(array($this,'errorHandler'));
        
        $output = "";
        try {
            $this->parser = new HTML_Template_Nest_Parser();
            $document = new DomDocument();
            try {
                $document->loadXml(file_get_contents($filename));
            } catch(Exception $e) {
                restore_error_handler();
                $message= "Unable to parse: $filename; " . $e->getMessage();
                throw new HTML_Template_Nest_CompilerException($message);
            }
            $output = $this->compileDocument($document);
        } catch(DomException $e) {
            restore_error_handler();
            $message= "Unable to parse: $filename; " . $e->getMessage();
            throw new HTML_Template_Nest_CompilerException($message);
        }
        restore_error_handler();
        return $output;
    }

    /**
     * Compile a DomDocument into a php file and return the output as a string
     *
     * @param DomDocument $document document to compile
     *
     * @return string compiled output
     */
    public function compileDocument($document)
    {
        return $this->processChildren($document);
    }

    /**
     * Process a node and all its children. Called recursively.
     *
     * @param DomNode $node node to process
     *
     * @return string php output after compiling nodes
     */
    protected function processChildren($node)
    {
        $output = "";
        $taglib = $node->lookupNamespaceURI($node->prefix);
        
        $tag = null;
        if (strpos($taglib, "urn:nsttl:") !== false) {
            $tag = $this->_loadTag($node);
        }


        if ($tag != null) {
            $output .= $tag->start();
        } elseif (strlen($node->localName) && !$node->hasChildNodes()) {
            $output .= "<" . $node->nodeName . $this->addAttributes($node) . "/>";
            return $output;
        } elseif (strlen($node->localName)) {
            $output .= "<" . $node->nodeName  . $this->addAttributes($node) . ">";
        }

        if ($node->nodeType == XML_TEXT_NODE) {
            $output .= $this->parser->parse($node->nodeValue);
        }

        if ($tag != null) {
            $nodeChildren = $tag->getNodeChildren();
            foreach ($nodeChildren as $child) {
                $output .= $this->processChildren($child);
            }            
        } elseif ($node->hasChildNodes()) {
            $nodeChildren = $node->childNodes;
            
            // we have to copy off the current children list in
            // case one of the children modifies the dom and
            // confuses the parser
            $childrenList = array();
            foreach ($nodeChildren as $child) {
                $childrenList[] = $child;
            }
            foreach ($childrenList as $child) {
                $output .= $this->processChildren($child);
            }            
        }


        if ($tag != null) {
            $output .= $tag->end();
        } elseif (strlen($node->localName)) {
            $output .= "</" . $node->nodeName . ">";
        }
        return $output;
    }

    /**
     * Create a string encapsulating all attributes for the node. Node
     * values are parsed.
     *
     * @param DomNode $node node to process
     *
     * @return string php output after compiling attributes
     */
    protected function addAttributes($node) 
    {
        $output = "";
        foreach ($node->attributes as $attribute) {
            $output .= " " . $attribute->name . "=\""; 
            $output .= $this->parser->parse($attribute->value) . "\"";
        }
        return $output;
    }


    /**
     * Loads a tag. Includes the taglibrary and then returns the appropriate
     * tag.
     *
     * @param DomNode $node node to process with tag
     *
     * @return HTML_Template_Nest_Tag appropriate tag
     */
    private function _loadTag($node)
    {
        $taglib = $node->lookupNamespaceURI($node->prefix);

        $attributes = array();
        foreach ($node->attributes as $attr) {
            $attributes[$attr->name] = $attr->value;
        }

        $taglib = str_replace("urn:nsttl:", "", $taglib);
        include_once str_replace("_", "/", $taglib) . ".php";

        $className = $taglib;
        if (strpos($taglib, "/") !== false) {
            $className = substr($taglib, strrpos($taglib, "/") + 1);
        }
        $class = new $className();
        $tag = $class->getTagByName($this, $node, $attributes);
        return $tag;
    }

    /**
     * Error handler for xml parsing
     * 
     * @param int    $errno   error number
     * @param string $errstr  error string
     * @param string $errfile file error occurred
     * @param int    $errline line number error occurred
     * 
     * @return boolean error handled
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($errno==E_WARNING 
            && (substr_count($errstr, "DOMDocument::loadXML()") > 0)
        ) {
            throw new DOMException($errstr);
        } elseif ($errno==E_WARNING 
            && (substr_count($errstr, "DOMDocumentFragment::appendXML()") > 0)
        ) {
            throw new DOMException($errstr);
        } else {
            return false;
        }
    }
} 