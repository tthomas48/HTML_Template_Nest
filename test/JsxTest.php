<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

class HTML_Template_Nest_JsxTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests that jsx files are compiled and minified
     * 
     * @return unknown_type
     */   
    public function testJsx()
    {
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;
        HTML_Template_Nest_Taglib_Resource::$BASE_PATH = dirname(__FILE__) . "/";
        HTML_Template_Nest_Taglib_Resource::$JSX_BINARY = "/usr/local/bin/jsx";

        $view = new HTML_Template_Nest_View("jsx");
        $view->addAttribute("outval", "Output Me!");
        $filename = dirname(__FILE__) . "/viewoutput/jsx.html";
        $this->assertEquals(
            trim($view->render()), 
            trim(file_get_contents($filename))
        );        
        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . "/viewoutput/jsx.min.js"),
            file_get_contents(dirname(__FILE__) . "/res/jsx.min.047b16bb88bb6313e905f30e2f7e86bf.js")
        );

    }
}
