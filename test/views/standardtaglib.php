<?php
class nestView__home_tthomas_git_HTML_Template_Nest_test_viewsstandardtaglib
{
    public $lastModified = 1345497157;

    public function render(Array $p)
    {

        $_o = function($params, $key) { return array_key_exists($key, $params) ? $params[$key] : null; };
        ob_start();
        register_shutdown_function(array($this, 'fatal_template_error'));
        
        $initialReporting = error_reporting();
        try {
            error_reporting(E_ERROR | E_USER_ERROR | E_PARSE);
            set_error_handler(function($errno, $errstr, $errfile, $errline) {
              if(($errno === E_ERROR) || ($errno === E_USER_ERROR) || ($errno === E_PARSE)) { 
                throw new HTML_Template_Nest_EvalException($errno, $errstr, $errfile, $errline); 
              }
            });
            ?><html>
    <body>
        <?php /*<c:if>*/?><?php if(1 == 1) {?>
            Show me
<?php } ?><?php /*</c:if>*/?>
        <?php /*<c:if>*/?><?php if(1 == 2) {?>
            Don't show me
<?php } ?><?php /*</c:if>*/?>
        <?php /* {outval} */ echo htmlentities($_o($p, 'outval'))?>

        <?php /*<c:if>*/?><?php if('a' == 'b') {?>
            Not me.
<?php /*</c:if>*/?>
        <?php /*<c:elseif>*/?><?php } elseif('b' == 'c') {?>
            Also not me.
<?php /*</c:elseif>*/?>
        <?php /*<c:else>*/?><?php } else {?>
            But me!
<?php } ?><?php /*</c:else>*/?>

        <ul<?php ob_start()?><?php /* {bulletClass} */ echo htmlentities($_o($p, 'bulletClass'))?><?php $output = ob_get_clean();echo ' class="' . $output . '"';?> id="unorderListTest">
            <?php /*<c:foreach>*/?><?php foreach($_o($p, 'myArray') as $nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724643e72aa_myvar) {
?>
                <li><?php /* {myvar} */ echo htmlentities($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724643e72aa_myvar)?></li>
            <?php }?><?php /*</c:foreach>*/?>
        </ul>
        <table>
            <tr>
                <th>Key</th>
                <th>Value</th>
                <?php /*<c:foreach>*/?><?php $nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724643e7328_mypos = 0;
foreach($_o($p, 'associativeArray') as $nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724643e7328_mykey => $nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724643e7328_myvalue) {
?>
                    <tr<?php ob_start()?>tableRow<?php /* {(mypos % 2 == 0) ? 1 : 2 } */ echo htmlentities(($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724643e7328_mypos % 2 == 0) ? 1 : 2 )?><?php $output = ob_get_clean();echo ' class="' . $output . '"';?>>
                        <td><?php /* {mykey} */ echo htmlentities($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724643e7328_mykey)?></td>
                        <td><?php /* {myvalue} */ echo htmlentities($nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724643e7328_myvalue)?></td>
                    </tr>
                <?php $nst_c_foreachHTML_Template_Nest_Taglib_Standard_ForeachTag55724643e7328_mypos++;
}?><?php /*</c:foreach>*/?>
            </tr>
        </table>
        Make sure the correct variable prints once out of context:
        <?php /* {var} */ echo htmlentities($_o($p, 'var'))?>
    </body>
    
<?php /*<c:set>*/?><?php $nst_c_setHTML_Template_Nest_Taglib_Standard_SetTag55724643e7373_mylocalvar = $_o($p, 'bulletClass');
?>
    <?php /* {mylocalvar} */ echo htmlentities($nst_c_setHTML_Template_Nest_Taglib_Standard_SetTag55724643e7373_mylocalvar)?>
<?php /*</c:set>*/?>    
Empty: <?php /* {mylocalvar} */ echo htmlentities($_o($p, 'mylocalvar'))?>

<?php /*<c:set>*/?><?php $nst_c_setHTML_Template_Nest_Taglib_Standard_SetTag55724643e73bc_mylocalvar = "bulletClass";
?>
    <?php /* {mylocalvar} */ echo htmlentities($nst_c_setHTML_Template_Nest_Taglib_Standard_SetTag55724643e73bc_mylocalvar)?>
<?php /*</c:set>*/?>
Empty: <?php /* {mylocalvar} */ echo htmlentities($_o($p, 'mylocalvar'))?>
<?php /*<c:for>*/?><?php for($nst_c_forHTML_Template_Nest_Taglib_Standard_ForTag55724643e7405_i = 0; $nst_c_forHTML_Template_Nest_Taglib_Standard_ForTag55724643e7405_i < 10; $nst_c_forHTML_Template_Nest_Taglib_Standard_ForTag55724643e7405_i = $nst_c_forHTML_Template_Nest_Taglib_Standard_ForTag55724643e7405_i + 1) {
?>
    Should print 10 times.
<?php }?><?php /*</c:for>*/?>
</html><?php
        } catch(HTML_Template_Nest_EvalException $e) {
            ob_clean();
            error_reporting($initialReporting);
            throw new HTML_Template_Nest_ViewException($e, $this->output);
        }
        error_reporting($initialReporting);
        $contents = ob_get_contents();
        ob_end_clean();
        return $contents;

    }
}
