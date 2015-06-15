
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

        $rendered = trim($view->render());
        $this->assertRegExp('/<script type="text\/javascript" src="res\/jsx.min.\d+.js">\s<\/script>/ms', trim($rendered));
        $file = preg_replace('/.*src="(res\/jsx.min.\d+.js)".*/ms', '$1', $rendered);
        
        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . "/viewoutput/jsx.min.js"),
            file_get_contents(dirname(__FILE__) . "/" . $file)
        );

    }
}
