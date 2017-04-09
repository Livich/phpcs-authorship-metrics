<?php

/**
 * Class Authorship_Sniffs_Metrics_STY1hSniff
 * Implements: "Average indentation in tabs after open braces ({)"
 */
class Authorship_Sniffs_Metrics_STY1hSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * @var array Holds array like ([pointer to brace] => number of tabs,...)
     */
    private $tabsCounter = array();

    /**
     * @var string Latest file name which was processed
     */
    private $latestFile = '';

    /**
     * Register for tokens
     * @return array Array of tokens we interested in
     */
    public function register()
    {
        return array(T_OPEN_CURLY_BRACKET);

    }
    
    /**
     * Processes this test, when one of its tokens is encountered.
     *
     * @param PHP_CodeSniffer_File $phpcsFile The file being scanned.
     * @param int                  $stackPtr  The position of the current token
     *                                        in the stack passed in $tokens.
     *
     * @return void
     */
    public function process(PHP_CodeSniffer_File $phpcsFile, $stackPtr)
    {
        $tokens = $phpcsFile->getTokens();

        if($this->latestFile !== $phpcsFile->getFilename()) {
            $this->tabsCounter = array();
            $this->latestFile = $phpcsFile->getFilename();
        }

        // Store current token and fetch by next tokens
        $currentToken = $tokens[$stackPtr];
        $nextTabPtr = $stackPtr;
        while(true) {
            $nextTabPtr++;
            $tabToken = $tokens[$nextTabPtr];
            // Exit at line end
            if ($tabToken['line'] !== $currentToken['line']) {
                break;
            }
            // In case of tab after opening curly bracket...
            if( $tabToken['code'] == T_WHITESPACE) {
                if (strlen($tabToken['content']) <= 0) {
                    continue;
                }
                $tabCount = preg_match_all($this->getPattern(), $tabToken['content']);
                // ...initialize array and count whitespaces
                if(!isset($this->tabsCounter[$stackPtr])) {
                    $this->tabsCounter[$stackPtr] = 0;
                }
                $this->tabsCounter[$stackPtr] += $tabCount;
            } else {
                continue;
            }
        }

        // Last brace processed?
        if ($phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $nextTabPtr) === false) {
            $this->publish($phpcsFile);
        }
    }

    protected function getPattern() {
        return '/\\t/u';
    }

    private function publish (PHP_CodeSniffer_File $phpcsFile) {
        // Compute actual value
        $total = 0;
        $stackPtr = null;
        foreach ($this->tabsCounter as $stackPtr => $counter) {
            $total += $counter;
        }
        $avg = $total/count($this->tabsCounter);

        // Publish
        $phpcsFile->addWarning(
            $avg,
            $stackPtr,
            'value'
        );
    }

}
