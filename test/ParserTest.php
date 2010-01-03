<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Tests expression parsing functionality.
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
 * @see       HTML_Template_Nest_Parser
 * @since     File available since Release 1.0
 */
require_once 'HTML/Template/Nest/Parser.php';
require_once 'PHPUnit/Framework/TestCase.php';
/**
 * Tests expression parsing functionality.
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_Parser
 * @since     Class available since Release 1.0.0
 */
class HTML_Template_Nest_ParserTest extends PHPUnit_Framework_TestCase
{

    /**
     * Test single variable token parsing
     * 
     * @return null
     */
    public function testSimpleTokens()
    {
        $parser = new HTML_Template_Nest_Parser();
        $output = $parser->parse('${some_var}');
        $this->assertEquals("<?php echo htmlentities(\$_o(\$p, 'some_var'))?>", $output);
        $parser->registerVariable('my_var');
        $output = $parser->parse('${my_var}');
        $this->assertEquals("<?php echo htmlentities(\$my_var)?>", $output);
    }

    /**
     * Test variables that call object methods or members
     * 
     * @return null
     */
    public function testObjectVariables()
    {

        $parser = new HTML_Template_Nest_Parser();
        $output = $parser->parse('${some_var->foo}');
        $this->assertEquals("<?php echo htmlentities(\$_o(\$p, 'some_var')->foo)?>", $output);
        $output = $parser->parse('${some_var->FOO}');
        $this->assertEquals("<?php echo htmlentities(\$_o(\$p, 'some_var')->FOO)?>", $output);
        $output = $parser->parse('${some_var->foo(bar,bin, baz)}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var')->foo(\$_o(\$p, 'bar'),\$_o(\$p, 'bin'),\$_o(\$p, 'baz')))?>", 
            $output
        );
        
        $output = $parser->parse(
            '${some_var->foo(bar,"bin", baz,  \'boo biscuits\'  )}'
        );
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var')->foo(\$_o(\$p, 'bar')," .
            "\"bin\",\$_o(\$p, 'baz'),'boo biscuits'))?>", 
            $output
        );

        $output = $parser->parse(
            '${some_var->foo(bar,"bin", baz,  \'boo biscuit\\\'s brother\'  )}'
        );
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var')->foo(\$_o(\$p, 'bar'),\"bin\"" .
            ",\$_o(\$p, 'baz'),'boo biscuit\'s brother'))?>", 
            $output
        );
        
        // nested methos
        $output = $parser->parse(
            '${some_var->foo(bar, "bin")->bar()->bob("bo\"oo\"",bin)}'
        );
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var')->foo(\$_o(\$p, 'bar'),\"bin\")" .
            "->bar()->bob(\"bo\\\"oo\\\"\",\$_o(\$p, 'bin')))?>", 
            $output
        );
    }
    
    /**
     * Test arrays
     * 
     * @return null
     */        
    public function testArrays()
    {
        $parser = new HTML_Template_Nest_Parser();
        $output = $parser->parse('${some_var[0]}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var')[0])?>", 
            $output
        );
        $output = $parser->parse('${some_var["value"]}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var')[\"value\"])?>", 
            $output
        );        
        $output = $parser->parse('${some_var["value"][0]}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var')[\"value\"][0])?>", 
            $output
        );        
    }
    
    /**
     * Test operators and parenthesis in expresions
     * 
     * @return null
     */    
    public function testOperators()
    {
        $parser = new HTML_Template_Nest_Parser();
        $output = $parser->parse('${some_var + some_other_var}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var') + \$_o(\$p, 'some_other_var'))?>", 
            $output
        );
        $output = $parser->parse('${some_var < some_other_var}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var') < \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var <= some_other_var}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var') <= \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var > some_other_var}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var') > \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var >= some_other_var}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var') >= \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var == some_other_var}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var') == \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var != some_other_var}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var') != \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${(some_var % some_other_var) == 0}');
        $this->assertEquals(
            "<?php echo htmlentities((\$_o(\$p, 'some_var') % \$_o(\$p, 'some_other_var')) == 0)?>", $output
        );
        $output = $parser->parse('${some_var / some_other_var}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var') / \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var * some_other_var}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var') * \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var - some_other_var}');
        $this->assertEquals(
            "<?php echo htmlentities(\$_o(\$p, 'some_var') - \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${(a + b + c) * (d - 3)}');
        $this->assertEquals(
            "<?php echo htmlentities((\$_o(\$p, 'a') + \$_o(\$p, 'b') + \$_o(\$p, 'c')) * (\$_o(\$p, 'd') - 3))?>", 
            $output
        );
        $output = $parser->parse('${(a + b + c) && (d - 3)}');
        $this->assertEquals(
            "<?php echo htmlentities((\$_o(\$p, 'a') + \$_o(\$p, 'b') + \$_o(\$p, 'c')) && (\$_o(\$p, 'd') - 3))?>", 
            $output
        );
        $output = $parser->parse('${(a + b + c) || (d - 3)}');
        $this->assertEquals(
            "<?php echo htmlentities((\$_o(\$p, 'a') + \$_o(\$p, 'b') + \$_o(\$p, 'c')) || (\$_o(\$p, 'd') - 3))?>",
            $output
        );
        $output = $parser->parse('${foo == \'a\'}');
        $this->assertEquals("<?php echo htmlentities(\$_o(\$p, 'foo') == 'a')?>", $output);
        $output = $parser->parse('${foo == "b"}');
        $this->assertEquals("<?php echo htmlentities(\$_o(\$p, 'foo') == \"b\")?>", $output);        
        
        $output = $parser->parse('${(foo == "b" ? "black" : "red")}');
        $this->assertEquals(
            "<?php echo htmlentities((\$_o(\$p, 'foo') == \"b\" ? \"black\" : \"red\"))?>", 
            $output
        );        
        
        $output = $parser->parseExpression('director->isLoggedIn() &amp;&amp; director->isSiteAdmin()');
        $this->assertEquals(
            "\$_o(\$p, 'director')->isLoggedIn() && \$_o(\$p, 'director')->isSiteAdmin()", 
            $output
        );        
    }
    
    /**
     * Test calling external functions with fn: syntax.
     * 
     * @return null
     */
    public function testFunctions()
    {
        $parser = new HTML_Template_Nest_Parser();
        $output = $parser->parse('${fn:count(myarray)}');
        $this->assertEquals("<?php echo htmlentities(count(\$_o(\$p, 'myarray')))?>", $output);
        $output = $parser->parse('${fn:str_replace(\'foo\', "bar", myarray)}');
        $this->assertEquals(
            "<?php echo htmlentities(str_replace('foo',\"bar\",\$_o(\$p, 'myarray')))?>",
            $output
        );
    }
    
   /**
     * Test #{} tokens
     * 
     * @return null
     */    
    public function testUnescaped()
    {
        $parser = new HTML_Template_Nest_Parser();
        $output = $parser->parse('#{myval}');
        $this->assertEquals("<?php echo \$_o(\$p, 'myval')?>", $output);        
    }
}
