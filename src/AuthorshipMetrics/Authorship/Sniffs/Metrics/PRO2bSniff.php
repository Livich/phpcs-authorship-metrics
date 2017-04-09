<?php

/**
 * Class Authorship_Sniffs_Metrics_PRO2bSniff
 * Implements: "Mean function name length"
 */
class Authorship_Sniffs_Metrics_PRO2bSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * @var array Holds array like ([pointer] => (string) function name,...)
     */
    private $functionNames = array();

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
        return array(T_FUNCTION);
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
            $this->functionNames = array();
            $this->latestFile = $phpcsFile->getFilename();
        }

        // Store current token and fetch by next tokens
        $currentToken = $tokens[$stackPtr];
        $nextPtr = $stackPtr+1;
        while ($tokens[$nextPtr]['line'] == $currentToken['line']) {
            if($tokens[$nextPtr]['code'] == T_STRING) {
                // Looks like we've got the function name
                $this->functionNames[$nextPtr] = $tokens[$nextPtr]['content'];
                break;
            }
            $nextPtr++;
        }

        // Last function processed?
        if ($phpcsFile->findNext(T_FUNCTION, $stackPtr+1) === false) {
            $this->publish($phpcsFile);
        }
    }

    private function publish (PHP_CodeSniffer_File $phpcsFile) {
        // Compute actual value
        $stackPtr = null;
        $total = 0;
        foreach ($this->functionNames as $stackPtr => $name) {
            $total += strlen($name);
        }
        $metric = $total/count($this->functionNames);

        // Publish
        $phpcsFile->addWarning(
            $metric,
            $stackPtr,
            'value'
        );
    }

}
