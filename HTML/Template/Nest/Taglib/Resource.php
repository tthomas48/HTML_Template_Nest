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
require_once 'HTML/Template/Nest/Tag.php';
require_once 'HTML/Template/Nest/Taglib.php';


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

  protected $tags = array(
      "js" => "HTML_Template_Nest_Taglib_Resource_Javascript",
      "jsfile" => "HTML_Template_Nest_Taglib_Resource_JavascriptFile",
      "css" => "HTML_Template_Nest_Taglib_Resource_Css",
      "cssfile" => "HTML_Template_Nest_Taglib_Resource_CssFile",
      "snippet" => "HTML_Template_Nest_Taglib_Resource_Snippet",
  );
}

abstract class HTML_Template_Nest_Taglib_Resource_Minifier extends HTML_Template_Nest_Tag {

  public function minify($name, $child_tag, $min_function) {

    $children = $this->getNodeChildren();
    $childrenList = array();
    foreach ($children as $child) {
      $childrenList[] = $child;
    }
    $file = array();
    foreach($childrenList as $child) {
      if($child->localName == $child_tag) {
        $files[] = $child->getAttribute("name");
         
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
          $md5_file = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file . ".md5";
          if(!file_exists($md5_file)) {
            $compile = true;
            continue;
          }
          $old_md5 = file_get_contents($md5_file);
          $new_md5 = md5_file(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file);
          if($old_md5 != $new_md5) {
            $compile = true;
          }
        }
      }

      if($compile) {
        $output = "";
        foreach($files as $file) {
          $output .= call_user_func($min_function, file_get_contents(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file));

          $md5_file = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file . ".md5";
          file_put_contents($md5_file, md5_file(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file));
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

    $name = $this->getRequiredAttribute("name");

    $this->minify($name, "jsfile", array('JSMin', 'minify'));
    return "<script type=\"text/javascript\" src=\"$name\"></script>";
  }
}

class HTML_Template_Nest_Taglib_Resource_JavascriptFile extends HTML_Template_Nest_Tag {
  protected $declaredAttributes = array("name");

  public function start() {
    $name = $this->getRequiredAttribute("name");
    return "<script type=\"text/javascript\" src=\"$name\"></script>";
  }

}
class HTML_Template_Nest_Taglib_Resource_Css extends HTML_Template_Nest_Taglib_Resource_Minifier {

  protected $declaredAttributes = array("name");

  public function start() {
    if(isset($_REQUEST["nocache"]) && $_REQUEST["nocache"] == "true") {
      return "";
    }
    $name = $this->getRequiredAttribute("name");
    $this->minify($name, "cssfile", array("CssMin", 'minify'));
    return "<link rel=\"stylesheet\" href=\"$name\" />";
  }
}

class HTML_Template_Nest_Taglib_Resource_CssFile extends HTML_Template_Nest_Tag {
  protected $declaredAttributes = array("name");

  public function start() {
    $name = $this->getRequiredAttribute("name");
    return "<link rel=\"stylesheet\" href=\"$name\" />";
  }
}
class HTML_Template_Nest_Taglib_Resource_Snippet extends HTML_Template_Nest_Tag {
    protected $declaredAttributes = array("name");

  public function start() {
    $name = $this->getRequiredAttribute("name");
    $content = $this->node->ownerDocument->saveXml($this->node);
    
    $children = $this->getNodeChildren();
    $childrenList = array();
    foreach ($children as $child) {
      $childrenList[] = $child;
    }
    $file = array();
    foreach($childrenList as $child) {
      $this->node->removeChild($child);
    }
    
    
    return json_encode(array($name => $content));
  }
}