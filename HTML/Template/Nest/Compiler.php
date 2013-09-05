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
require_once "Parser.php";
require_once "CompilerException.php";
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
 * @version   Release: 1.3.6
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_View*
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Compiler extends php_user_filter
{
	public $parser;
	public $tagStack = array();
	
	public function __construct() {
	   stream_filter_register("nst.filter", "HTML_Template_Nest_Compiler");
     $this->parser = new HTML_Template_Nest_Parser();
	}
	
    function filter($in, $out, &$consumed, $closing) {
      $input = "";
      $lastBucket = NULL;
      while ($bucket = stream_bucket_make_writeable($in)) {
          
          $lastBucket = $bucket;
          $input .= $bucket->data;
          $consumed += $bucket->datalen;
      }
      //$bucket = stream_bucket_new($out, "a" . $input . "b");
      if($lastBucket != NULL) {
        if($this->parser == NULL) {
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
	public function compile($filename, $contents = NULL)
	{
		set_error_handler(array($this,'errorHandler'));

		$output = "";
		try {
			if($contents == NULL) {
			    $contents = file_get_contents($filename);
			}

			// if the content doesn't start with an opening tag treat it as
			// plain text content and parse and return
			if(!preg_match("/^\s*</", $contents)) {
				return $this->parser->parse($contents);
			}

			$document = new DomDocument();
			try {
				$document->loadXml($contents, LIBXML_NOCDATA);
			} catch(Exception $e) {
				restore_error_handler();
				$message= "Unable to parse: $filename; " . $e->getMessage();
				throw new HTML_Template_Nest_CompilerException($message);
			}
			$output = $this->compileDocument($document);
		} catch(HTML_Template_Nest_ParseException $e) {
			throw new HTML_Template_Nest_CompilerException("In file $filename\n" . $e->getMessage() . "\n" . $e->getTraceAsString());
		} catch(HTML_Template_Nest_TaglibException $e) {
			throw new HTML_Template_Nest_CompilerException("In file $filename\n" . $e->getMessage());
		} catch(HTML_Template_Nest_CompilerException $e) {
			throw new HTML_Template_Nest_CompilerException("In file $filename\n" . $e->getMessage());
		} catch(DomException $e) {
			restore_error_handler();
print $e;
print $content;
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
		$doctype = $document->doctype;
		$output = "";
		if($doctype) {
			$output .= "<!DOCTYPE " . $doctype->name . " PUBLIC \"" . $doctype->publicId . "\" \"" . $doctype->systemId . "\">\n";
		}
    $renderStack = $this->initChildren($document);
		$output .= $this->processChildren($renderStack);
		return $output;
	}

	public function compileNode($node)
	{
    $renderStack = $this->initChildren($node);
		return $this->processChildren($renderStack);
	}

	private function getNamespace($node) {
		$taglib = $node->lookupNamespaceURI($node->prefix);
		if($node instanceof DomElement && $taglib == null && strpos($node->nodeName, ":") !== false) {
			// must be some sort of DOM bug. Look and see if we can figure it out from simplexml
			$sxe = simplexml_import_dom($node);
			$namespaces = $sxe->getNamespaces();
			foreach($namespaces as $prefix => $namespace) {
				if(strpos($node->nodeName, $prefix . ":") == 0) {
					$taglib = $namespace;
					break;
				}
			}
		}
		return $taglib;
	}

	/**
	 * Initialize all tags. Called recursively.
	 *
	 * @param DomNode $node node to process
	 */
	protected function initChildren($node)
	{

    $renderStack = array();
		$taglib = $this->getNamespace($node);

		$tag = null;
		$rootTag = false;
		if (strpos($taglib, "urn:nsttl:") !== false) {
			$tag = $this->loadTag($node);
      $tag->init();
		}
		elseif ($taglib == "http://nest.sourceforge.net/" && trim($node->localName) == 'root') {
			$rootTag = true;
		}

		// just a text node, parse it for variables and return it
		if ($node->nodeType == XML_TEXT_NODE) {
      $renderStack[] = new HTML_Template_Nest_RenderNode($node);
			return $renderStack;
		}

		// self-contained tag. just return it
		if (!$rootTag && strlen($node->localName) && $tag == null && !$node->hasChildNodes()) {
      $renderStack[] = new HTML_Template_Nest_RenderNode($node);
			return $renderStack;
		}

		// process tags or children
		if ($tag != null) {
			$nodeChildren = $tag->getNodeChildren();

			$childrenList = array();
			foreach ($nodeChildren as $child) {
				$childrenList[] = $child;
			}
      $tagRenderStack = array();
			foreach ($childrenList as $child) {
				$tagRenderStack[] = $this->initChildren($child);
		  }	
      $renderStack[] = new HTML_Template_Nest_RenderNode($tag, $tagRenderStack);

		} elseif ($node->hasChildNodes()) {
			$nodeChildren = $node->childNodes;

			// we have to copy off the current children list in
			// case one of the children modifies the dom and
			// confuses the parser
			$childrenList = array();
			foreach ($nodeChildren as $child) {
				$childrenList[] = $child;
			}

      $nodeRenderStack = array();
      foreach ($childrenList as $child) {
        $nodeRenderStack[] = $this->initChildren($child);
      } 
      $renderStack[] = new HTML_Template_Nest_RenderNode($node, $nodeRenderStack);
    }
    return $renderStack;
	}


	/**
	 * Process a node and all its children. Called recursively.
	 *
	 * @param DomNode $node node to process
	 *
	 * @return string php output after compiling nodes
	 */
	protected function processChildren($renderStack)
	{

		$output = "";
    foreach($renderStack as $renderNode) {

    $item = $renderNode->parent;

    $rootTag = false;

    if(is_a($item, '\DOMNode')) {
  		$taglib = $this->getNamespace($item);
	  	if ($taglib == "http://nest.sourceforge.net/" && trim($item->localName) == 'root') {
  			$rootTag = true;
	  	}
    }

		$tag = null;
		if (is_a($item, '\HTML_Template_Nest_Tag')) {
			$tag = $item;
			$this->tagStack[] = Array($tag);
			$output .= $tag->getAttributeDeclarations();
		}

		// just a text node, parse it for variables and return it
		if (is_a($item, '\DOMNode') && $item->nodeType == XML_TEXT_NODE) {
			$output .= $this->parser->parse($item->nodeValue);
      continue;
		}

		// self-contained tag. just return it
		if (!$rootTag && is_a($item, '\DOMNode') && strlen($item->localName) && $tag == null && !$item->hasChildNodes()) {
			$output .= "<" . $item->nodeName . $this->addAttributes($item) . $this->processNamespace($item) . "/>";
      continue;
		}
 

		// process tags or children
		if ($tag != null) {
      $node = $tag->getNode();
		  if($tag->isPhpEnabled()) {
			   $output .= "<?php /*<" . $node->tagName . ">*/?>";
			}
			$output .= $tag->start();
     
      foreach($renderNode->children as $child) {
         $output .= $this->processChildren($child);
      } 
		} elseif (is_a($item, '\DOMNode') && $item->hasChildNodes()) {
      foreach($renderNode->children as $child) {
         $output .= $this->processChildren($child);
      } 
		}

		if($tag != null) {
			$output = $tag->filter($output);
		}

		// process opening tags, and append the parsed children, doing this
		// at this point allows us to have children modify parent attributes
		if (!$rootTag && is_a($item, '\DOMNode') && strlen($item->localName) && $tag == null) {

			$output = "<" . $item->nodeName  . $this->addAttributes($item) . $this->processNamespace($item) .  ">" . $output;
		}

		// process closing tags

		if ($tag != null) {
			$output .= $tag->end();
			$output .= $tag->getAttributeUnsets();
			if($tag->isPhpEnabled()) {
  		        $output .= "<?php /*</" . $tag->getNode()->tagName . ">*/?>";
  		    }
		} elseif (!$rootTag && is_a($item, '\DOMNode') && strlen($item->localName)) {
			$output .= "</" . $item->nodeName . ">";
    }
    }
		return $output;
	}

	public function getParentByType($type) {
		for($i = count($this->tagStack) - 1; $i >= 0; $i--) {
			$stack = $this->tagStack[$i];
			foreach($stack as $tag) {
				if(is_a($tag, $type)) {
					return $tag;
				}
			}
		}
		return null;
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
		$uris = array();
		$output = "";
		foreach ($node->attributes as $attribute) {
			$uri = $this->getNamespace($attribute);
			if(!empty($uri) && !in_array($uri, $uris)) {
				$uris[] = $uri;
				$output .= " xmlns:" . $attribute->prefix . "=\"$uri\" ";
			}
			if(strpos($attribute->name, 'prune.') !== 0 && !$this->parser->isParseable($attribute->value)) {
				$q = '"';
				if(strpos($attribute->value, '"') !== false) {
					$q = "'";
				}
				$output .= " " . $attribute->name . "=" . $q . $attribute->value . $q;
				continue;
			}
			$value = $this->parser->parse($attribute->value);
			$output .= '<?php ob_start()?>';
			$output .= str_replace('"', '&quot;', $value);
			$output .= '<?php $output = ob_get_clean();';

			$name = $attribute->name;
			$prefix = (!empty($attribute->prefix) ? $attribute->prefix . ":" : "");
			if(strpos($attribute->name, 'prune.') === 0) {
				$name = str_replace("prune.", "", $name);
				$output .= 'if(!empty($output)) { ';
			}



			$output .= 'echo \' ' . $prefix . $name . '="\' . $output . \'"\';';
			if(strpos($attribute->name, 'prune.') === 0) {
				$output .= "}";
			}
			$output .= '?>';
		}
		return $output;
	}


	/**
	 * Processes any namespaces that are not nest tag libraries.
	 *
	 * @param DomNode $node node to process
	 *
	 * @return string string of xml namespace declarations
	 */
	protected function processNamespace($node)
	{
		$output = "";
		if(strlen($node->prefix) == 0) {
			if($node->namespaceURI != NULL) {
				$output .= " xmlns=\"" . $node->namespaceURI . "\" ";
			}
			return $output;
		}
		$uri = $this->getNamespace($node);
		$output .= " xmlns:" . $node->prefix . "=\"" . str_replace('"', '&quot;', $uri) . "\"";
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
	public function loadTag($node)
	{
		$taglib = $this->getNamespace($node);

		$attributes = array();
		foreach ($node->attributes as $attr) {
			$attributes[$attr->name] = $attr->value;
		}

		$taglib = str_replace("urn:nsttl:", "", $taglib);
		$taglibFile = str_replace("_", "/", $taglib) . ".php";

		$className = $taglib;
		$isDefault = false;
		if(!class_exists($className, true)) {
			$isDefault = true;
			$className = "HTML_Template_Nest_Taglib";
		}
		$class = new $className();
		if($isDefault) {
			$class->setLibraryDirectory($taglib);
		}
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
		} elseif ($errno==E_WARNING)
		{
			throw new Exception($errstr);
		} else {
			return false;
		}
	}

	/*
	 * Pulled from: http://php.net/manual/en/function.file-exists.php
	* Expanden file_exists function
	* Searches in include_path
	*/
	private static function file_exists_ip($filename) {
		if(function_exists("get_include_path")) {
			$include_path = get_include_path();
		} elseif(false !== ($ip = ini_get("include_path"))) {
			$include_path = $ip;
		} else {
			return false;
		}

		if(false !== strpos($include_path, PATH_SEPARATOR)) {
			if(false !== ($temp = explode(PATH_SEPARATOR, $include_path)) && count($temp) > 0) {
				for($n = 0; $n < count($temp); $n++) {
					if(false !== @file_exists($temp[$n] . "/" . $filename)) {
						return true;
					}
				}
				return false;
			} else {return false;
			}
		} elseif(!empty($include_path)) {
			if(false !== @file_exists($include_path)) {
				return true;
			} else {return false;
			}
		} else {return false;
		}
	}
}
