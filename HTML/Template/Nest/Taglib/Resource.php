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
 * @category HTML_Template
 * @package Nest
 * @author Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version SVN: $Id$
 * @link http://pear.php.net/package/HTML_Template_Nest
 * @since File available since Release 1.0
 */

/**
 * The resource tag library provides basic tags for including and automatically
 * minifying javascript and css.
 *
 *
 * @category HTML_Template
 * @package Nest
 * @author Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license http://www.opensource.org/licenses/bsd-license.php New BSD License
 * @version Release: 1.3.7
 * @link http://pear.php.net/package/HTML_Template_Nest
 * @since Class available since Release 1.0.0
 */
class HTML_Template_Nest_Taglib_Resource extends HTML_Template_Nest_Taglib
{

    public static $BASE_PATH = "./";

    public static $SASS_BINARY = "/usr/bin/sass";

    public static $JSX_BINARY = "/usr/local/bin/jsx";

    protected $tags = array(
        "js" => "HTML_Template_Nest_Taglib_Resource_Javascript",
        "jsxfile" => "HTML_Template_Nest_Taglib_Resource_JsxFile",
        "jsfile" => "HTML_Template_Nest_Taglib_Resource_JavascriptFile",
        "css" => "HTML_Template_Nest_Taglib_Resource_Css",
        "cssfile" => "HTML_Template_Nest_Taglib_Resource_CssFile",
        "scssfile" => "HTML_Template_Nest_Taglib_Resource_ScssFile",
        "snippet" => "HTML_Template_Nest_Taglib_Resource_Snippet",
        "snippets" => "HTML_Template_Nest_Taglib_Resource_Snippets"
    );
}

abstract class HTML_Template_Nest_Taglib_Resource_Minifier extends HTML_Template_Nest_Tag
{

    private $childrenList = array();

    private $minFiles = array();

    private $initialized = false;

    public function init()
    {
        if ($this->initialized) {
            return true;
        }
        
        $this->childrenList = $this->renderer->getChildren();
        foreach ($this->childrenList as $child) {
            $this->renderer->removeChild($child);
        }
        $this->initialized = true;
        return false;
    }

    public function scss($name)
    {
        $files = array();
        $position = 0;
        foreach ($this->childrenList as $child) {
            if ($child->getLocalName() == "scssfile") {
                
                $localfile = $child->getAttribute("localfile");
                $file = $child->getAttribute("name");
                if (empty($localfile)) {
                    $localfile = $file;
                }
                $new_filename = str_replace(".scss", ".css", $localfile);

                $this->parser->addFileDependency(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $localfile);

                $files[] = array(
                    "before" => $localfile,
                    "after" => $new_filename,
                    "position" => $position
                );
                $position ++;
            }
            if ($child->getLocalName() == "cssfile") {
                $position ++;
            }
        }
        foreach ($files as $file_components) {
            $file = $file_components["before"];
            
            $is_url = strstr($file, '://') !== FALSE;
            
            $this->minFiles[$file_components["position"]] = $file_components["after"];
            
            $after = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file_components["after"];
            if ($is_url) {
                $after = $file_components["after"];
            }
            
            $before = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file;
            if ($is_url) {
                $before = $file_components["before"];
            }
            
            exec(HTML_Template_Nest_Taglib_Resource::$SASS_BINARY . " --update -f " . escapeshellarg($before) . ":" . escapeshellarg($after));
        }
    }

    public function minify($name, $child_tag, $min_function)
    {
        
        $files = array();
        foreach ($this->childrenList as $child) {
            if ($child->getLocalName() == $child_tag) {
                $localfile = $child->getAttribute("localfile");
                if (empty($localfile)) {
                    $localfile = $child->getAttribute("name");
                }

                $is_url = strstr($localfile, '://') !== FALSE;
                if (!$is_url) {
                  $this->parser->addFileDependency(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $localfile);
                }
                $files[] = $localfile;
            }
        }
        
        foreach ($this->minFiles as $position => $file) {
            $tmpFiles = array_slice($files, 0, $position, true);
            $tmpFiles[] = $file;
            $files = array_merge($tmpFiles, array_slice($files, $position, count($files), true));
        }
        
        $output = "";
        foreach ($files as $file) {
            
            
            $is_url = strstr($file, '://') !== FALSE;
            
            $contents = "";
            if ($is_url) {
                $contents = file_get_contents($file);
            } else {
                $contents = file_get_contents(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file);
            }
            
            $output .= call_user_func($min_function, $contents);
            
        }
        
        if (substr($name, - 3) === ".js") {
            $output_filename = str_replace(".js", "." . time() . ".js", $name);
        } elseif (substr($name, - 4) === ".css") {
                $output_filename = str_replace(".css", "." . time() . ".css", $name);
        }
        file_put_contents(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $output_filename, $output);
        return $output_filename;
    }

    public function jsx($name)
    {
        $files = array();
        $position = 0;
        foreach ($this->childrenList as $child) {
            if ($child->getLocalName() == "jsxfile") {
                
                $localfile = $child->getAttribute("localfile");
                $file = $child->getAttribute("name");
                if (empty($localfile)) {
                    $localfile = $file;
                }

                $new_filename = "";
                if(strpos($localfile, ".jsx") !== false) {
                  $new_filename = str_replace(".jsx", "-jsx.js", $localfile);
                }
                elseif(strpos($localfile, ".js") !== false) {
                  $new_filename = str_replace(".js", "-jsx.js", $localfile);
                }

                $this->parser->addFileDependency(HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $localfile);
                
                $files[] = array(
                    "before" => $localfile,
                    "after" => $new_filename,
                    "position" => $position
                );
                $position ++;
            }
            if ($child->getLocalName() == "jsfile") {
                $position ++;
            }
        }
        foreach ($files as $file_components) {
            $file = $file_components["before"];
            
            $is_url = strstr($file, '://') !== false;
            
            $this->minFiles[$file_components["position"]] = $file_components["after"];
            
            $after = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file_components["after"];
            if ($is_url) {
                $after = $file_components["after"];
            }
            
            $before = HTML_Template_Nest_Taglib_Resource::$BASE_PATH . $file;
            if ($is_url) {
                $before = $file_components["before"];
            }
            $cmd = HTML_Template_Nest_Taglib_Resource::$JSX_BINARY . " " . escapeshellarg($before);
            $output = `$cmd`;
            file_put_contents($after, $output);
        }
    }
}

class HTML_Template_Nest_Taglib_Resource_Javascript extends HTML_Template_Nest_Taglib_Resource_Minifier
{

    protected $declaredAttributes = array(
        "name"
    );

    public function start()
    {
        if (isset($_REQUEST["nocache"]) && $_REQUEST["nocache"] == "true") {
            return "";
        }
        
        $name = $this->parser->parse($this->getRequiredAttribute("name"));
        $localfile = $this->getOptionalAttribute("localfile", $name);
        $basepath = $this->parser->parse($this->getOptionalAttribute("basepath", ""));
        
        $this->jsx($localfile);
        $name = $this->minify($localfile, "jsfile", array(
            'JSMin',
            'minify'
        ));
        return "<script type=\"text/javascript\" src=\"" . $basepath . "$name\">\n</script>";
    }
}

class HTML_Template_Nest_Taglib_Resource_JavascriptFile extends HTML_Template_Nest_Tag
{

    protected $declaredAttributes = array(
        "name"
    );

    public function start()
    {
        $name = $this->parser->parse($this->getRequiredAttribute("name"));
        $basepath = $this->parser->parse($this->getOptionalAttribute("basepath", ""));
        
        return "<script type=\"text/javascript\" src=\"" . $basepath . "$name\">\n</script>";
    }
}

class HTML_Template_Nest_Taglib_Resource_Css extends HTML_Template_Nest_Taglib_Resource_Minifier
{

    protected $declaredAttributes = array(
        "name"
    );

    public function start()
    {
        if (isset($_REQUEST["nocache"]) && $_REQUEST["nocache"] == "true") {
            return "";
        }
        $name = $this->parser->parse($this->getRequiredAttribute("name"));
        $localfile = $this->getOptionalAttribute("localfile", $name);
        $basepath = $this->parser->parse($this->getOptionalAttribute("basepath", ""));
        
        $this->scss($localfile);
        $name = $this->minify($localfile, "cssfile", array(
            "CssMin",
            'minify'
        ));
        return "<link rel=\"stylesheet\" href=\"" . $basepath . "$name\" />";
    }    
}

class HTML_Template_Nest_Taglib_Resource_CssFile extends HTML_Template_Nest_Tag
{

    protected $declaredAttributes = array(
        "name"
    );

    public function start()
    {
        $name = $this->parser->parse($this->getRequiredAttribute("name"));
        $basepath = $this->parser->parse($this->getOptionalAttribute("basepath", ""));
        return "<link rel=\"stylesheet\" href=\"" . $basepath . "$name\" />";
    }
}

class HTML_Template_Nest_Taglib_Resource_ScssFile extends HTML_Template_Nest_Taglib_Resource_Minifier
{

    protected $declaredAttributes = array(
        "name"
    );

    public function start()
    {
        $name = $this->parser->parse($this->getRequiredAttribute("name"));
        $localfile = $this->getOptionalAttribute("localfile", $name);
        $basepath = $this->parser->parse($this->getOptionalAttribute("basepath", ""));
        
        $output_name = str_replace(".scss", ".css", $localfile);
        $this->scss($localfile);
        $name = $this->minify($output_name, "scssfile", array(
            "CssMin",
            'minify'
        ));
        return "<link rel=\"stylesheet\" href=\"" . $basepath . "$name\" />";
    }
}

class HTML_Template_Nest_Taglib_Resource_JsxFile extends HTML_Template_Nest_Taglib_Resource_Minifier
{

    protected $declaredAttributes = array(
        "name"
    );

    public function start()
    {
        $name = $this->parser->parse($this->getRequiredAttribute("name"));
        $localfile = $this->getOptionalAttribute("localfile", $name);
        $basepath = $this->parser->parse($this->getOptionalAttribute("basepath", ""));
        
        $this->jsx($localfile);
        $name = $this->minify($localfile, "jsfile", array(
            'JSMin',
            'minify'
        ));
        return "<script type=\"text/javascript\" src=\"" . $basepath . "$name\">\n</script>";
    }
}

class HTML_Template_Nest_Taglib_Resource_Snippets extends HTML_Template_Nest_Tag
{

    public function start()
    {
        $children = $this->getNodeChildren();
        $childrenList = array();
        foreach ($children as $child) {
            if ($child->localName == "snippet") {
                $childrenList[] = $child;
            }
        }
        $files = array();
        for ($i = 0; $i < count($childrenList); $i ++) {
            $child = $childrenList[$i];
            if ($i == count($childrenList) - 1) {
                break;
            }
            $child->setAttribute("addcomma", "true");
        }
    }

    public function filter($output)
    {
        $name = $this->getRequiredAttribute("name");
        
        $output = '<?php ob_start(); ?>' . $output;
        $output .= '<?php $output = ob_get_clean();';
        $output .= 'print $output';
        $output .= '?>';
        return "var $name = { $output };";
    }

    public function isPhpEnabled()
    {
        return false;
    }
}

class HTML_Template_Nest_Taglib_Resource_Snippet extends HTML_Template_Nest_Tag
{

    public function filter($output)
    {
        $name = $this->getRequiredAttribute("name");
        
        $output = '<?php ob_start(); ?>' . $output;
        $output .= '<?php $output = ob_get_clean();';
        $output .= 'print json_encode($output)';
        $output .= '?>';
        $output = "'" . $name . "': $output";
        
        if ($this->node->getAttribute("addcomma") == "true") {
            $output .= ",";
        }
        return $output;
    }

    public function isPhpEnabled()
    {
        return false;
    }
}
