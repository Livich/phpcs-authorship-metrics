<?php
/**
 * Created by IntelliJ IDEA.
 * User: Sergiy
 * Date: 08.04.2017
 * Time: 22:52
 */

namespace AuthorshipMetrics\Tests;

use PHPUnit_Framework_TestCase;
use PHP_CodeSniffer_CLI;

class TestCase extends PHPUnit_Framework_TestCase
{
    private $phpcsCli;
    protected $phpcsParams;
    
    public function __construct(){
        parent::__construct();
        global $rootDir;
        exec($rootDir.'/setup.sh');
        $this->phpcsCli = new PHP_CodeSniffer_CLI();
        $this->phpcsParams = $this->phpcsCli ->getDefaults();
        $this->phpcsParams['standard'] = 'Authorship';
        $this->phpcsParams['files'] = array(
            $rootDir.'/src/AuthorshipMetrics/Tests/assets/braces.php',
            $rootDir.'/src/AuthorshipMetrics/Tests/assets/comments.php'
            );
        $this->phpcsParams['reports'] = array('xml'=>null);
    }
    public function phpcsStart()
    {
        $this->phpcsCli->process($this->phpcsParams);
    }
}