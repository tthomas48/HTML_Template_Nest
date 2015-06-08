<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Compiles .
 * nst files into .php files
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
 * @category HTML_Template
 * @package Nest
 * @author Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version SVN: $Id$
 * @link http://pear.php.net/package/HTML_Template_Nest
 * @see HTML_Template_Nest_View
 * @since File available since Release 1.0
 *       
 */
use Memio\Memio\Config\Build;
use Memio\Model\File;
use Memio\Model\Object;
use Memio\Model\Property;
use Memio\Model\Method;
use Memio\Model\Argument;

/**
 * Compiles .
 * nst files into .php files
 *
 * The HTML_Template_Nest_Compiler takes .nst input files and converts them into
 * .php files that can be included just like any other file. They
 * are expected to be included via the HTML_Template_Nest_View class.
 *
 * @category HTML_Template
 * @package Nest
 * @author Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version Release: 1.3.6
 * @link http://pear.php.net/package/HTML_Template_Nest
 * @see HTML_Template_Nest_View*
 * @since Class available since Release 1.0.0
 */
class HTML_Template_Nest_Compiler extends php_user_filter
{

    public $parser;

    public function __construct()
    {
        stream_filter_register("nst.filter", "HTML_Template_Nest_Compiler");
        $this->parser = new HTML_Template_Nest_Parser();
    }

    function filter($in, $out, &$consumed, $closing)
    {
        $input = "";
        $lastBucket = NULL;
        while ($bucket = stream_bucket_make_writeable($in)) {
            
            $lastBucket = $bucket;
            $input .= $bucket->data;
            $consumed += $bucket->datalen;
        }
        // $bucket = stream_bucket_new($out, "a" . $input . "b");
        if ($lastBucket != NULL) {
            if ($this->parser == NULL) {
                $this->parser = new HTML_Template_Nest_Parser();
            }
            
            $lastBucket->data = $this->compile("filter.data", $input);
            stream_bucket_append($out, $lastBucket);
        }
        return PSFS_PASS_ON;
    }

    /**
     * Compile a nst into a php file and cache the output
     *
     * @param string $filename
     *            path to file to compile
     *            
     * @return ViewInstance
     */
    public function compileAndCache($viewPath, $viewName)
    {
        $viewPath = $viewPath . "/" . $viewName;
        $viewName = basename($viewPath, ".nst");
        $fullPath = str_replace($viewName, '', $viewPath . "/" . $viewName);
        $uncompiledFilename = $fullPath . "/" . $viewName . ".nst";
        $compiledFilename = $fullPath . "/" . $viewName . ".php";
        $className = "nestView_" . preg_replace('/[^a-z0-9]/', '', $viewName);
        
        $stat = stat($uncompiledFilename);
        $lastModified = $stat[9];
        
        $viewInstance = null;
        $compile = true;
        if (file_exists($compiledFilename) && class_exists($className)) {
            require_once $compiledFilename;
            try {
                $viewInstance = new $className();
                $compile = $viewInstance->lastModified != $lastModified;
                if (! $compile) {
                    return $viewInstance;
                }
            } catch (\Exception $e) {}
        }
        
        $compiler = new HTML_Template_Nest_Compiler();
        $output = $compiler->compile($uncompiledFilename);
        $code = $this->getTemplateCode($className, $lastModified, $output);
        file_put_contents($compiledFilename, '<' . "?php\n" . $code);
        
        require_once $compiledFilename;
        $viewInstance = new $className();
        
        return $viewInstance;
    }

    public function getTemplateCode($className, $lastModified, $output)
    {
        $lastModifiedProperty = new Property('lastModified');
        $lastModifiedProperty->makePublic();
        $lastModifiedProperty->setDefaultValue($lastModified);
        
        $methodBody = '
        $_o = function($params, $key) { return array_key_exists($key, $params) ? $params[$key] : null; };
        ob_start();
        $initialReporting = error_reporting();
        try {
            error_reporting(E_ERROR | E_USER_ERROR | E_PARSE);
            set_error_handler(function($errno, $errstr, $errfile, $errline) {
              if(($errno === E_ERROR) || ($errno === E_USER_ERROR) || ($errno === E_PARSE)) {
                throw new HTML_Template_Nest_EvalException($errno, $errstr, $errfile, $errline);
              }
            });
            ?>' . $output . '<?php
        } catch(HTML_Template_Nest_EvalException $e) {
            ob_clean();
            error_reporting($initialReporting);
            throw new HTML_Template_Nest_ViewException($e, $this->output);
        }
        error_reporting($initialReporting);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;
';
        
        $renderMethod = Method::make('render')->addArgument(new Argument('Array', 'p'))->setBody($methodBody);
        
        $obj = Object::make($className)->addProperty($lastModifiedProperty)->addMethod($renderMethod);
        
        $prettyPrinter = Build::prettyPrinter();
        return $prettyPrinter->generateCode($obj);
    }

    /**
     * Compile a nst into a php file and return the output as a string
     *
     * @param string $filename
     *            path to file to compile
     *            
     * @throws HTML_Template_Nest_CompilerException
     * @return string compiled output
     */
    public function compile($filename, $contents = NULL)
    {
        set_error_handler(array(
            $this,
            'errorHandler'
        ), E_ALL);
        
        $output = "";
        try {
            if ($contents == NULL) {
                $contents = file_get_contents($filename);
            }
            
            // if the content doesn't start with an opening tag treat it as
            // plain text content and parse and return
            if (! preg_match("/^\s*</", $contents)) {
                return $this->parser->parse($contents);
            }
            
            $document = new DomDocument();
            try {
                if (! $document->loadXml($contents, LIBXML_NOCDATA)) {
                    throw new Exception("Parsing error: " . implode("\n", libxml_get_errors()));
                }
            } catch (Exception $e) {
                restore_error_handler();
                $message = "Unable to parse: $filename; " . $e->getMessage();
                throw new HTML_Template_Nest_CompilerException($message);
            }
            $output = $this->compileDocument($document);
        } catch (HTML_Template_Nest_ParseException $e) {
            throw new HTML_Template_Nest_CompilerException("In file $filename\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
        } catch (HTML_Template_Nest_TaglibException $e) {
            throw new HTML_Template_Nest_CompilerException("In file $filename\n" . $e->getMessage());
        } catch (HTML_Template_Nest_CompilerException $e) {
            throw new HTML_Template_Nest_CompilerException("In file $filename\n" . $e->getMessage());
        } catch (HTML_Template_Nest_TagException $e) {
            throw $e;
        } catch (DomException $e) {
            restore_error_handler();
            $message = "Unable to parse: $filename; " . $e->getMessage();
            throw new HTML_Template_Nest_CompilerException($message);
        } catch (Exception $e) {}
        restore_error_handler();
        return $output;
    }

    /**
     * Compile a DomDocument into a php file and return the output as a string
     *
     * @param DomDocument $document
     *            document to compile
     *            
     * @return string compiled output
     */
    public function compileDocument($document)
    {
        $doctype = $document->doctype;
        $output = "";
        if ($doctype) {
            $output .= "<!DOCTYPE " . $doctype->name . " PUBLIC \"" . $doctype->publicId . "\" \"" . $doctype->systemId . "\">\n";
        }
        
        $output .= $this->render($document);
        return $output;
    }

    public function compileNode($node)
    {
        $document = new DomDocument();
        $document->appendChild($node);
        return $this->render($document);
    }

    private function render(DomDocument $domDocument)
    {
        $document = new HTML_Template_Nest_Node_Document($this->parser, $domDocument);
        
        while (! $document->isRenderable()) {
            $document->init();
        }
        return $document->render();
    }

    /**
     * Error handler for xml parsing
     *
     * @param int $errno
     *            error number
     * @param string $errstr
     *            error string
     * @param string $errfile
     *            file error occurred
     * @param int $errline
     *            line number error occurred
     *            
     * @return boolean error handled
     */
    public static function errorHandler($errno, $errstr, $errfile, $errline)
    {
        if ($errno == E_WARNING && (substr_count($errstr, "DOMDocument::loadXML()") > 0)) {
            throw new DOMException($errstr);
        } elseif ($errno == E_WARNING && (substr_count($errstr, "DOMDocumentFragment::appendXML()") > 0)) {
            throw new DOMException($errstr);
        } elseif ($errno == E_WARNING) {
            throw new DomException($errstr);
        } else {
            return false;
        }
    }

    /*
     * Pulled from: http://php.net/manual/en/function.file-exists.php
     * Expanden file_exists function
     * Searches in include_path
     */
    private static function file_exists_ip($filename)
    {
        if (function_exists("get_include_path")) {
            $include_path = get_include_path();
        } elseif (false !== ($ip = ini_get("include_path"))) {
            $include_path = $ip;
        } else {
            return false;
        }
        
        if (false !== strpos($include_path, PATH_SEPARATOR)) {
            if (false !== ($temp = explode(PATH_SEPARATOR, $include_path)) && count($temp) > 0) {
                for ($n = 0; $n < count($temp); $n ++) {
                    if (false !== @file_exists($temp[$n] . "/" . $filename)) {
                        return true;
                    }
                }
                return false;
            } else {
                return false;
            }
        } elseif (! empty($include_path)) {
            if (false !== @file_exists($include_path)) {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}
