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
        $this->assertEquals("<?php /* {some_var} */ echo htmlentities(\$_o(\$p, 'some_var'))?>", $output);
        $parser->registerVariable('my_var');
        $output = $parser->parse('${my_var}');
        $this->assertEquals("<?php /* {my_var} */ echo htmlentities(\$my_var)?>", $output);
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
        $this->assertEquals("<?php /* {some_var->foo} */ echo htmlentities(\$_o(\$p, 'some_var')->foo)?>", $output);
        $output = $parser->parse('${some_var->FOO}');
        $this->assertEquals("<?php /* {some_var->FOO} */ echo htmlentities(\$_o(\$p, 'some_var')->FOO)?>", $output);
        $output = $parser->parse('${some_var->foo(bar,bin, baz)}');
        $this->assertEquals(
            "<?php /* {some_var->foo(bar,bin, baz)} */ echo htmlentities(\$_o(\$p, 'some_var')->foo(\$_o(\$p, 'bar'),\$_o(\$p, 'bin'), \$_o(\$p, 'baz')))?>", 
            $output
        );
        
        $output = $parser->parse(
            '${some_var->foo(bar,"bin", baz,  \'boo biscuits\'  )}'
        );
        $this->assertEquals(
            "<?php /* {some_var->foo(bar,\"bin\", baz,  'boo biscuits'  )} */ echo htmlentities(\$_o(\$p, 'some_var')->foo(\$_o(\$p, 'bar')," .
            "\"bin\", \$_o(\$p, 'baz'),  'boo biscuits'  ))?>", 
            $output
        );

        $output = $parser->parse(
            '${some_var->foo(bar,"bin", baz,  \'boo biscuit\\\'s brother\'  )}'
        );
        $this->assertEquals(
            "<?php /* {some_var->foo(bar,\"bin\", baz,  'boo biscuit\\'s brother'  )} */ echo htmlentities(\$_o(\$p, 'some_var')->foo(\$_o(\$p, 'bar'),\"bin\"" .
            ", \$_o(\$p, 'baz'),  'boo biscuit\'s brother'  ))?>", 
            $output
        );
        
        // nested methods
        $output = $parser->parse(
            '${some_var->foo(bar, "bin")->bar()->bob("bo\"oo\"",bin)}'
        );
        $this->assertEquals(
            "<?php /* {some_var->foo(bar, \"bin\")->bar()->bob(\"bo\\\"oo\\\"\",bin)} */ echo htmlentities(\$_o(\$p, 'some_var')->foo(\$_o(\$p, 'bar'), \"bin\")" .
            "->bar()->bob(\"bo\\\"oo\\\"\",\$_o(\$p, 'bin')))?>", 
            $output
        );
        
        // null testing
        $output = $parser->parseExpression("error != null &amp;&amp; error->getText() != ''");
        $this->assertEquals(
            "\$_o(\$p, 'error') != null && \$_o(\$p, 'error')->getText() != ''",
            $output);
            
        $output = $parser->parseExpression("error->getText(null, 'foo') != ''");
        $this->assertEquals(
            "\$_o(\$p, 'error')->getText(null, 'foo') != ''",
            $output);            
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
            "<?php /* {some_var[0]} */ echo htmlentities(\$_o(\$p, 'some_var')[0])?>", 
            $output
        );
        $output = $parser->parse('${some_var["value"]}');
        $this->assertEquals(
            "<?php /* {some_var[\"value\"]} */ echo htmlentities(\$_o(\$p, 'some_var')[\"value\"])?>", 
            $output
        );        
        $output = $parser->parse('${some_var["value"][0]}');
        $this->assertEquals(
            "<?php /* {some_var[\"value\"][0]} */ echo htmlentities(\$_o(\$p, 'some_var')[\"value\"][0])?>", 
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
            "<?php /* {some_var + some_other_var} */ echo htmlentities(\$_o(\$p, 'some_var') + \$_o(\$p, 'some_other_var'))?>", 
            $output
        );
        $output = $parser->parse('${some_var < some_other_var}');
        $this->assertEquals(
            "<?php /* {some_var < some_other_var} */ echo htmlentities(\$_o(\$p, 'some_var') < \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var <= some_other_var}');
        $this->assertEquals(
            "<?php /* {some_var <= some_other_var} */ echo htmlentities(\$_o(\$p, 'some_var') <= \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var > some_other_var}');
        $this->assertEquals(
            "<?php /* {some_var > some_other_var} */ echo htmlentities(\$_o(\$p, 'some_var') > \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var >= some_other_var}');
        $this->assertEquals(
            "<?php /* {some_var >= some_other_var} */ echo htmlentities(\$_o(\$p, 'some_var') >= \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var == some_other_var}');
        $this->assertEquals(
            "<?php /* {some_var == some_other_var} */ echo htmlentities(\$_o(\$p, 'some_var') == \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var != some_other_var}');
        $this->assertEquals(
            "<?php /* {some_var != some_other_var} */ echo htmlentities(\$_o(\$p, 'some_var') != \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${(some_var % some_other_var) == 0}');
        $this->assertEquals(
            "<?php /* {(some_var % some_other_var) == 0} */ echo htmlentities((\$_o(\$p, 'some_var') % \$_o(\$p, 'some_other_var')) == 0)?>", $output
        );
        $output = $parser->parse('${some_var / some_other_var}');
        $this->assertEquals(
            "<?php /* {some_var / some_other_var} */ echo htmlentities(\$_o(\$p, 'some_var') / \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var * some_other_var}');
        $this->assertEquals(
            "<?php /* {some_var * some_other_var} */ echo htmlentities(\$_o(\$p, 'some_var') * \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${some_var - some_other_var}');
        $this->assertEquals(
            "<?php /* {some_var - some_other_var} */ echo htmlentities(\$_o(\$p, 'some_var') - \$_o(\$p, 'some_other_var'))?>", $output
        );
        $output = $parser->parse('${(a + b + c) * (d - 3)}');
        $this->assertEquals(
            "<?php /* {(a + b + c) * (d - 3)} */ echo htmlentities((\$_o(\$p, 'a') + \$_o(\$p, 'b') + \$_o(\$p, 'c')) * (\$_o(\$p, 'd') - 3))?>", 
            $output
        );
        $output = $parser->parse('${(a + b + c) && (d - 3)}');
        $this->assertEquals(
            "<?php /* {(a + b + c) && (d - 3)} */ echo htmlentities((\$_o(\$p, 'a') + \$_o(\$p, 'b') + \$_o(\$p, 'c')) && (\$_o(\$p, 'd') - 3))?>", 
            $output
        );
        $output = $parser->parse('${(a + b + c) || (d - 3)}');
        $this->assertEquals(
            "<?php /* {(a + b + c) || (d - 3)} */ echo htmlentities((\$_o(\$p, 'a') + \$_o(\$p, 'b') + \$_o(\$p, 'c')) || (\$_o(\$p, 'd') - 3))?>",
            $output
        );
        $output = $parser->parse('${foo == \'a\'}');
        $this->assertEquals("<?php /* {foo == 'a'} */ echo htmlentities(\$_o(\$p, 'foo') == 'a')?>", $output);
        $output = $parser->parse('${foo == "b"}');
        $this->assertEquals("<?php /* {foo == \"b\"} */ echo htmlentities(\$_o(\$p, 'foo') == \"b\")?>", $output);        
        
        $output = $parser->parse('${(foo == "b" ? "black" : "red")}');
        $this->assertEquals(
            "<?php /* {(foo == \"b\" ? \"black\" : \"red\")} */ echo htmlentities((\$_o(\$p, 'foo') == \"b\" ? \"black\" : \"red\"))?>", 
            $output
        );        
        $output = $parser->parseExpression('director->isLoggedIn() &amp;&amp; director->isSiteAdmin()');
        $this->assertEquals(
            "\$_o(\$p, 'director')->isLoggedIn() && \$_o(\$p, 'director')->isSiteAdmin()", 
            $output
        );
                

        $parser->registerVariable("_field");
        $output = $parser->parse('${_field->class != \'\' ? _field->class : \'\'}${(_field->error != \'\' ? \' errored\' : \'\')}');
        $this->assertEquals(
        '<?php /* {_field->class != \'\' ? _field->class : \'\'} */ echo htmlentities($_field->class != \'\' ? $_field->class : \'\')?><?php /* {(_field->error != \'\' ? \' errored\' : \'\')} */ echo htmlentities(($_field->error != \'\' ? \' errored\' : \'\'))?>',
        $output);
        $parser->unregisterVariable("_field");
        
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
        $this->assertEquals("<?php /* {fn:count(myarray)} */ echo htmlentities(count(\$_o(\$p, 'myarray')))?>", $output);
        $output = $parser->parse('${fn:str_replace(\'foo\', "bar", myarray)}');
        $this->assertEquals(
            "<?php /* {fn:str_replace('foo', \"bar\", myarray)} */ echo htmlentities(str_replace('foo', \"bar\", \$_o(\$p, 'myarray')))?>",
            $output
        );

        $parser->registerVariable('onsubmit');
        $output = $parser->parse('${fn:strlen(onsubmit) > 0 ? onsubmit : \'return validate(this);\'}');
        $parser->unregisterVariable('onsubmit');
        $this->assertEquals(
            '<?php /* {fn:strlen(onsubmit) > 0 ? onsubmit : \'return validate(this);\'} */ echo htmlentities(strlen($onsubmit) > 0 ? $onsubmit : \'return validate(this);\')?>',
            $output
        );
        
        $parser->registerVariable('_field');
        $output = $parser->parse('${fn:implode(\',\',_field->validators)}');
        $parser->unregisterVariable('_field');
        
        $this->assertEquals(
            '<?php /* {fn:implode(\',\',_field->validators)} */ echo htmlentities(implode(\',\',$_field->validators))?>',
            $output
        );
        
        // can we nest?
        $parser->registerVariable('_field');
        $output = $parser->parse('${fn:count(fn:implode(\',\',_field->validators))}');
        $parser->unregisterVariable('_field');
        
        $this->assertEquals(
            '<?php /* {fn:count(fn:implode(\',\',_field->validators))} */ echo htmlentities(count(implode(\',\',$_field->validators)))?>',
            $output
        );
        
        // can we handle static?
        $parser->registerVariable('_field');
        $output = $parser->parse('${fn:MyClass::parse(_field->validators)}');
        $parser->unregisterVariable('_field');
        
        $this->assertEquals(
            '<?php /* {fn:MyClass::parse(_field->validators)} */ echo htmlentities(MyClass::parse($_field->validators))?>',
            $output
        );

        
        $parser->registerVariable('col');
        //$output = $parser->parse("\${fn:strpos(col, '&lt;img') &gt;= 0 ? 'centeredImg' : ''}");
        $output = $parser->parse("\${fn:strpos(col, '&lt;img') &gt;= 0 ? 'centeredImg' : ''}", false);
        $parser->unregisterVariable('col');
        $this->assertEquals(
            'strpos($col, \'<img\') >= 0 ? \'centeredImg\' : \'\'',
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
        $this->assertEquals("<?php /* #{myval} */ echo \$_o(\$p, 'myval')?>", $output);

        
        $output = $parser->parse(
            '${some_var->foo(bar, "bin")->bar()->bob("bo\"oo\"",bin)}'
        );
        $this->assertEquals(
            "<?php /* {some_var->foo(bar, \"bin\")->bar()->bob(\"bo\\\"oo\\\"\",bin)} */ echo htmlentities(\$_o(\$p, 'some_var')->foo(\$_o(\$p, 'bar'), \"bin\")" .
            "->bar()->bob(\"bo\\\"oo\\\"\",\$_o(\$p, 'bin')))?>", 
            $output
        );
    }
    
    public function testDescending()
    {
        $parser = new HTML_Template_Nest_Parser();
        $output = $parser->parseExpression('"foo"');
        $this->assertEquals('"foo"', $output);
        
        $output = $parser->parseExpression('"foo" . " bar"');
        $this->assertEquals('"foo" . " bar"', $output);
        
        $output = $parser->parseExpression("'foo' . ' bar'");
        $this->assertEquals("'foo' . ' bar'", $output);
        
        $output = $parser->parseExpression("fn:count(foo)");
        $this->assertEquals("count(\$_o(\$p, 'foo'))", $output);        
        
        $output = $parser->parseExpression("fn:count(null)");
        $this->assertEquals("count(null)", $output);

        $output = $parser->parseExpression("fn:count(foo, bar )");
        $this->assertEquals("count(\$_o(\$p, 'foo'), \$_o(\$p, 'bar') )", $output);        
        
        $output = $parser->parseExpression("foo->bar");
        $this->assertEquals("\$_o(\$p, 'foo')->bar", $output);        
        
        $output = $parser->parseExpression("foo->bar->boo->bin");
        $this->assertEquals("\$_o(\$p, 'foo')->bar->boo->bin", $output);

        $output = $parser->parseExpression("foo");
        $this->assertEquals("\$_o(\$p, 'foo')", $output);        
    }
    
    public function testQuoted() 
    {
        $parser = new HTML_Template_Nest_Parser();
        $output = $parser->parse('document.location = \'${referer}\'; return false;', false, "\"");
        $this->assertEquals("document.location = '\" . \$_o(\$p, 'referer') . \"'; return false;", $output);
    }
    public function testNested()
    {
        $parser = new HTML_Template_Nest_Parser();
        $output = $parser->parse('${(production->isNew() ? \'Create\' : \'Save\')} Production');
        $this->assertEquals("<?php /* {(production->isNew() ? 'Create' : 'Save')} */ echo htmlentities((\$_o(\$p, 'production')->isNew() ? 'Create' : 'Save'))?> Production", $output);
    }
    
}
