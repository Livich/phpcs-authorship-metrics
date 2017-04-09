<?php

/**
 * Class Authorship_Sniffs_Metrics_STY2aSniff
 * Implements: "Percentages of pure comment lines among lines containing comments"
 */
class Authorship_Sniffs_Metrics_STY2aSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * @var array Holds array like ([pointer to comment] => (boolean) is pure comment line,...)
     */
    private $isPure = array();

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
        return array(T_COMMENT);
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
            $this->isPure = array();
            $this->latestFile = $phpcsFile->getFilename();
        }

        // Store current token and fetch by next tokens
        $currentToken = $tokens[$stackPtr];
        $nextPtr = $stackPtr;
        while(true) {
            $nextPtr++;
            if (!isset($tokens[$nextPtr])) {
                $this->isPure[$stackPtr] = true;
                break;
            }
            $nextToken = $tokens[$nextPtr];
            // Exit at line end
            if ($nextToken['line'] !== $currentToken['line']) {
                if (!isset($this->isPure[$stackPtr])) {
                    $this->isPure[$stackPtr] = true;
                }
                break;
            }
            // In case of any token except newline after comment:
            // TODO: move newline characters from here
            if (!in_array($nextToken['content'], array("\n", "\r", "\r\n"))) {
                $this->isPure[$stackPtr] = false;
            }
        }

        // Mark the comment as non-pure 'cause there is something before on the same line
        // and it is non-whitespace
        if ($stackPtr >= 1 && $this->isPure[$stackPtr]) {
            $nextPtr = $stackPtr - 1;
            while($tokens[$nextPtr]['line'] == $currentToken['line']) {
                if ($nextPtr < 0) {
                    $this->isPure[$stackPtr] = true;
                    break;
                }
                if($tokens[$nextPtr]['code'] !== T_WHITESPACE) {
                    $this->isPure[$stackPtr] = false;
                    break;
                }
                $nextPtr --;
            }
        }

        // Last comment processed?
        if ($phpcsFile->findNext(T_COMMENT, $stackPtr+1) === false) {
            $this->publish($phpcsFile);
        }
    }

    private function publish (PHP_CodeSniffer_File $phpcsFile) {
        // Compute actual value
        $stackPtr = null;
        $pure = 0;
        $nonPure = 0;
        foreach ($this->isPure as $stackPtr => $isPure) {
            if ($isPure) {
                $pure++;
                continue;
            }
            $nonPure++;
        }
        $metric = $pure / ($pure+$nonPure);

        // Publish
        $phpcsFile->addWarning(
            $metric,
            $stackPtr,
            'value'
        );
    }

}
