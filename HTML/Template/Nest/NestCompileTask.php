<?php
/* vim: set expandtab tabstop=4 shiftwidth=4 softtabstop=4: */

/**
 * Phing task to compiles .nst files into .php files
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
 * @see       HTML_Template_Nest_Compiler
 * @since     File available since Release 1.0
 */

/**
 * Phing task to compiles .nst files into .php files
 *
 * @category  HTML_Template
 * @package   Nest
 * @author    Tim Thomas <tthomas48@php.net>
 * @copyright 2009 The PHP Group
 * @license   http://www.opensource.org/licenses/bsd-license.php  New BSD License
 * @version   Release: @package_version@
 * @link      http://pear.php.net/package/HTML_Template_Nest
 * @see       HTML_Template_Nest_Compiler
 * @since     Class available since Release 1.0.0
 */
class NestCompileTask extends Task
{
    protected $file;    // the source file (from xml attribute)
    protected $filesets = array(); // all fileset objects assigned to this task

    protected $errorProperty;
    protected $haltOnFailure = false;
    protected $hasErrors = false;
    private $_badFiles = array();
    protected $compiler = null;

    /**
     * Constructor
     * 
     * @return instance
     */
    public function __construct()
    {
        $this->compiler = new HTML_Template_Nest_Compiler();
    }

    /**
     * The haltonfailure property
     * 
     * @param boolean $haltOnFailure halt on failure
     * 
     * @return null
     */
    public function setHaltOnFailure($haltOnFailure)
    {
        $this->haltOnFailure = $haltOnFailure;
    }

    /**
     * Set file to compile
     * 
     * @param PhingFile $file file to compile
     * 
     * @return null
     */
    public function setFile(PhingFile $file)
    {
        $this->file = $file;
    }

    /**
     * Set the property name to put errors.
     * 
     * @param string $errorProperty propery name
     * 
     * @return null
     */
    public function setErrorProperty($errorProperty)
    {
        $this->errorProperty = $errorProperty;
    }

    /**
     * Nested creator, creates a FileSet for this task
     *
     * @return FileSet The created fileset object
     */
    function createFileSet()
    {
        $num = array_push($this->filesets, new FileSet());
        return $this->filesets[$num-1];
    }

    /**
     * Compile PhingFile or a FileSet
     * 
     * @return null
     */
    public function main()
    {
        if (!isset($this->file) and count($this->filesets) == 0) {
            throw new BuildException(
                "Missing either a nested fileset or attribute 'file'"
            );
        }

        if ($this->file instanceof PhingFile) {
            $this->compile($this->file->getParent(), $this->file->getName());
        } else { // process filesets
            $project = $this->getProject();
            foreach ($this->filesets as $fs) {
                $ds = $fs->getDirectoryScanner($project);
                $files = $ds->getIncludedFiles();
                $dir = $fs->getDir($project)->getPath();
                foreach ($files as $file) {
                    $this->compile($dir, $file);
                }
            }
        }

        $files = implode(', ', $this->_badFiles);
        if ($this->haltOnFailure && $this->hasErrors) {
            throw new BuildException('Compile errors in PHP files: ' . $files);    
        } else {
            $this->log('Compile errors in PHP files: ' . $files, Project::MSG_INFO);
        }
    }

    /**
     * Performs the actual compilation from nst to php file
     *
     * @param string $file file to compile
     * 
     * @return void
     */
    protected function compile($dir, $file)
    {
        $filename = basename($dir. DIRECTORY_SEPARATOR . $file);
        $directoryName = str_replace($filename, '', $dir . DIRECTORY_SEPARATOR . $file); 
        
        if (file_exists($dir.DIRECTORY_SEPARATOR.$file)) {
            if (is_readable($dir.DIRECTORY_SEPARATOR.$file)) {
                try {
                    $this->compiler->compileAndCache($directoryName, $filename);
                } catch(Exception $e) {
                    if ($this->errorProperty) {
                        $this->project->setProperty(
                            $this->errorProperty, 
                            $e->getMessage()
                        );
                    }
                    $this->log($e->getMessage(), Project::MSG_ERR);
                    $this->_badFiles[] = $dir.DIRECTORY_SEPARATOR.$file;
                    $this->hasErrors = true;
                    return;
                }
                $this->log($file . ': Compiled successfully', Project::MSG_INFO);
            } else {
                throw new BuildException('Permission denied: '.$file);
            }
        } else {
            throw new BuildException('File not found: '.$file);
        }
    }
}
