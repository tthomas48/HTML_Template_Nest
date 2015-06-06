<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Tests the resource tag library
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
 * @see       HTML_Template_Nest_Taglib_Standard
 * @since     File available since Release 1.0
 */
/**
 * Tests the resource tag library
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_Taglib_Standard
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_ResourceTest extends PHPUnit_Framework_TestCase
{
    /**
     * Tests the various tags of the resource tag library using the
     * resourcetaglib.nst file.
     * 
     * @return unknown_type
     */   
    public function testMinify()
    {
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;
        HTML_Template_Nest_Taglib_Resource::$BASE_PATH = dirname(__FILE__) . "/";


        $view = new HTML_Template_Nest_View("resourcetaglib");
        $filename = dirname(__FILE__) . "/viewoutput/resourcetaglib.html";
        $this->assertEquals(
            trim($view->render()), 
            trim(file_get_contents($filename))
        );       
        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . "/viewoutput/css.min.css"),
            file_get_contents(dirname(__FILE__) . "/res/css.min.db45bda72b702e346f91f3adbe1345da.css")
        );
        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . "/viewoutput/js.min.js"),
            file_get_contents(dirname(__FILE__) . "/res/js.min.83bf736035367a0b493361f094f7ef5f.js")
        );
    }
    
    public function testSnippet() {
    
        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;
        HTML_Template_Nest_Taglib_Resource::$BASE_PATH = dirname(__FILE__) . "/";


        $view = new HTML_Template_Nest_View("snippet");
        $this->assertEquals('var test_snippets = {       \'mysnippet\': "        <div class=\"myclass\" id=\"foo\">\n            Some text {{foo}}\n        <\/div>\n      ",
      \'otherbit\': "          Just some text in here.\n      "     };',
        trim($view->render()));
    }
    public function testFilter() {

      foreach (glob(dirname(__FILE__) . "/res/*.md5") as $filename) {
        unlink($filename);
      }

        HTML_Template_Nest_View::$CACHE = false;
        HTML_Template_Nest_View::addIncludePath(dirname(__FILE__) . "/views");
        HTML_Template_Nest_View::$HTML_ERRORS = false;
        HTML_Template_Nest_Taglib_Resource::$BASE_PATH = dirname(__FILE__) . "/";

        $view = new HTML_Template_Nest_View("filter");
        $output = $view->render();
        $this->assertEquals(
        '<link rel="stylesheet" href="res/scss.min.17ab4a24de5b8c3af1de3c700e080ba1.css" />' . "\t" . '<script type="text/javascript" src="res/js.filter.min.0b6ecf17e30037994d3ffee51b525914.js">'."\n".'</script>',
        
        trim($output));
        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . "/viewoutput/js.filter.min.js"),
            file_get_contents(dirname(__FILE__) . "/res/js.filter.min.js.64fcd8bc28984aac21adc6248ac43441")
        );
        $this->assertEquals(
            file_get_contents(dirname(__FILE__) . "/viewoutput/c.css"),
            file_get_contents(dirname(__FILE__) . "/res/scss.min.css.5e29db497fddc2ba6e588878c4af5f03")
        );
    }

   
}
