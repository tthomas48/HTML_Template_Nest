<?php 
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * The resource tag library provides tags for minifying javascript and css.
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

/**
 * The resource tag library provides basic tags for including and automatically
 * minifying javascript and css.
 *
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: 1.3.7
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_Taglib_Resource extends HTML_Template_Nest_Taglib
{
  
  public static $BASE_PATH = "./";
  public static $SASS_BINARY = "/usr/bin/sass";

  protected $tags = array(
      "js" => "HTML_Template_Nest_Taglib_Resource_Javascript",
      "jsfile" => "HTML_Template_Nest_Taglib_Resource_JavascriptFile",
      "css" => "HTML_Template_Nest_Taglib_Resource_Css",
      "cssfile" => "HTML_Template_Nest_Taglib_Resource_CssFile",
      "scssfile" => "HTML_Template_Nest_Taglib_Resource_ScssFile",
      "snippet" => "HTML_Template_Nest_Taglib_Resource_Snippet",
      "snippets" => "HTML_Template_Nest_Taglib_Resource_Snippets",
  );
}

abstract class HTML_Template_Nest_Taglib_Resource_Minifier extends HTML_Template_Nest_Tag {

  public function scss($name) {

    $children = $this->getNodeChildren();
    $childrenList = array();
    foreach ($children as $child) {
      $childrenList[] = $child;
    }
    $files = array();
    foreach($childrenList as $child) {
      if($child->localName == "scssfile") {

        $localfile = $child->getAttribute("localfile");
        $file = $child->getAttribute("name");
        if(empty($localfile)) {
          $localfile = $file;
        }
	$new_name = str_replace(".scss", ".css", $name);
        $new_filename = str_replace(".scss", ".css", $localfile);


        $files[] = 
        array("before" => $localfile,
        "after" => $new_filename);
        $new_child = new DomElement("cssfile", "", "urn:nsttl:HTML_Template_Nest_Taglib_Resource");
         
        $this->node->insertBefore($new_child, $child);
        $new_child->setAttribute("name", $new_name);
        $new_child->setAttribute("localfile", $new_filename);

        
        $this->node->removeChild($child);
      }
    }
    foreach($files as $file_components) {
      $file = $file_components["before"];
      $compile = false;
      
      $is_url = strstr($file, '://') !== FALSE;
      
      $md5_file = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file . ".md5";
      if($is_url) {
          $md5_file = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . md5($file) . ".md5";
      }
      $old_md5 = NULL;
      if(file_exists($md5_file)) {
        $old_md5 = file_get_contents($md5_file);
      }
      if($is_url) {
        //TODO: this needs to do some time based caching for http
        $new_md5 = md5(file_get_contents($file));
      } else {
        $new_md5 = md5_file(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file);
      }
      if($old_md5 != $new_md5) {
        $compile = true;
      }

      if($compile) {
        $before = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file;
        if($is_url) {
          $before = $file_components["before"];
        }
        
        $after = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file_components["after"];
        if($is_url) {
            $after = $file_components["after"];
        }
        exec($SASS_BINARY . " --update -f $before:$after"); 
        if(file_exists("$after.md5")) {
          unlink("$after.md5");
        }
        file_put_contents($md5_file, $new_md5);
      }
    }
  }

 
  public function minify($name, $child_tag, $min_function) {

    $children = $this->getNodeChildren();
    $childrenList = array();
    foreach ($children as $child) {
      $childrenList[] = $child;
    }
    $files = array();
    foreach($childrenList as $child) {
      if($child->localName == $child_tag) {
        $localfile = $child->getAttribute("localfile");
        if(empty($localfile)) {
          $localfile = $child->getAttribute("name");
        }
        $files[] = $localfile;
         
      }
      $this->node->removeChild($child);
    }
    
    
    $min_file_exists = file_exists(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $name);
    if(HTML_Template_Nest_View::$CACHE == false || !$min_file_exists) {

      $compile = true;
      if($min_file_exists) {
        $compile = false;
        // check the md5s to see if we need to recompile

        
        foreach($files as $file) {
          $is_url = strstr($file, '://') !== FALSE;
          
          $md5_file = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file . ".md5";
          if($is_url) {
            $md5_file = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . md5($file) . ".md5";
          }
          
          if(!file_exists($md5_file)) {
            $compile = true;
            continue;
          }
          $old_md5 = file_get_contents($md5_file);
          if($is_url) {
            $new_md5 = md5(file_get_contents($file));
          } else {
            $new_md5 = md5_file(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file);
          }
          if($old_md5 != $new_md5) {
            $compile = true;
          }
        }
      }

      if($compile) {
        $output = "";
        foreach($files as $file) {
          $is_url = strstr($file, '://') !== FALSE;
          $contents = "";
          if($is_url) {
            $contents = file_get_contents($file);
          } else {
            $contents = file_get_contents(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file);
          }
            
          
          $output .= call_user_func($min_function, $contents);

          if($is_url) {
            $md5_file = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . md5($file) . ".md5";
            file_put_contents($md5_file, md5($contents));
          } else {
            $md5_file = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file . ".md5";
            file_put_contents($md5_file, md5_file(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file));
          }
        }
        file_put_contents(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $name, $output);
      }
    }


    foreach($children as $child) {
      $this->node->removeChild($child);
    }
  }
}

class HTML_Template_Nest_Taglib_Resource_Javascript extends HTML_Template_Nest_Taglib_Resource_Minifier {

  protected $declaredAttributes = array("name");

  public function start() {
    if(isset($_REQUEST["nocache"]) && $_REQUEST["nocache"] == "true") {
      return "";
    }

    $name = $this->compiler->parser->parse(
        $this->getRequiredAttribute("name")
    );
    $localfile = $this->getOptionalAttribute("localfile", $name);

    $this->minify($localfile, "jsfile", array('JSMin', 'minify'));
    return "<script type=\"text/javascript\" src=\"$name\"></script>";
  }
}

class HTML_Template_Nest_Taglib_Resource_JavascriptFile extends HTML_Template_Nest_Tag {
  protected $declaredAttributes = array("name");

  public function start() {
    $name = $this->compiler->parser->parse(
        $this->getRequiredAttribute("name")
    );
    return "<script type=\"text/javascript\" src=\"$name\"></script>";
  }

}
class HTML_Template_Nest_Taglib_Resource_Css extends HTML_Template_Nest_Taglib_Resource_Minifier {

  protected $declaredAttributes = array("name");

  public function start() {
    if(isset($_REQUEST["nocache"]) && $_REQUEST["nocache"] == "true") {
      return "";
    }
    $name = $this->compiler->parser->parse(
        $this->getRequiredAttribute("name")
    );
    $localfile = $this->getOptionalAttribute("localfile", $name);
    
    $this->scss($localfile);
    $this->minify($localfile, "cssfile", array("CssMin", 'minify'));
    return "<link rel=\"stylesheet\" href=\"$name\" />";
  }
}

class HTML_Template_Nest_Taglib_Resource_CssFile extends HTML_Template_Nest_Tag {
  protected $declaredAttributes = array("name");

  public function start() {
    $name = $this->compiler->parser->parse(
        $this->getRequiredAttribute("name")
    );
    return "<link rel=\"stylesheet\" href=\"$name\" />";
  }
}

class HTML_Template_Nest_Taglib_Resource_ScssFile extends HTML_Template_Nest_Taglib_Resource_Minifier {
  protected $declaredAttributes = array("name");

  public function start() {
    $name = $this->compiler->parser->parse(
        $this->getRequiredAttribute("name")
    );
    $localfile = $this->getOptionalAttribute("localfile", $name);

    
    $output_name = str_replace(".scss", ".css", $localfile);
    $this->scss($localfile);
    $this->minify($output_name, "scssfile", array("CssMin", 'minify'));
    return "<link rel=\"stylesheet\" href=\"$name\" />";
  }
}

class HTML_Template_Nest_Taglib_Resource_Snippets extends HTML_Template_Nest_Tag {
    //protected $declaredAttributes = array("name");
    
    public function start() {
    $name = $this->getRequiredAttribute("name");
    $content = array();
    
    $children = $this->getNodeChildren();
    $childrenList = array();
    foreach ($children as $child) {
      $childrenList[] = $child;
    }
    $file = array();
    foreach($childrenList as $child) {
      if($child->localName == "snippet") {
        $content[] = $this->compiler->loadTag($child)->start();
      }
      $this->node->removeChild($child);
    }
    
    return "var $name = {" . implode(",", $content) . "}";
  }    
  public function isPhpEnabled()
  {
      return false;
  }  
}
class HTML_Template_Nest_Taglib_Resource_Snippet extends HTML_Template_Nest_Tag {
    //protected $declaredAttributes = array("name");

  public function start() {
    $name = $this->getRequiredAttribute("name");
    $content = "";
    
    $children = $this->getNodeChildren();
    $childrenList = array();
    foreach ($children as $child) {
      $childrenList[] = $child;
    }
    $file = array();
    foreach($childrenList as $child) {
      $content .= $this->node->ownerDocument->saveXml($child);
      $this->node->removeChild($child);
    }
    
    
    return "'" . $name ."': " . json_encode($content);
  }
  public function isPhpEnabled()
  {
      return false;
  }
  
}
