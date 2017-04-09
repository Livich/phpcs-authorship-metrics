<?php

/**
 * Class Authorship_Sniffs_Metrics_STY1cSniff
 * Implements: "Percentage of open braces ({) that are the last character in a line"
 */
class Authorship_Sniffs_Metrics_STY1cSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * @var array Holds array like ([pointer to brace] => (boolean) is last on the line)
     */
    private $isOpenBraceLast = array();

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
            $this->isOpenBraceLast = array();
            $this->latestFile = $phpcsFile->getFilename();
        }

        // Store current token and fetch by next tokens
        $currentToken = $tokens[$stackPtr];
        $currentPtr = $stackPtr;
        while(true) {
            $currentPtr++;
            $nextToken = $tokens[$currentPtr];
            // Check brace position and exit at line end
            if ($nextToken['line'] !== $currentToken['line']) {
                if (!isset($this->isOpenBraceLast[$stackPtr])) {
                    $this->isOpenBraceLast[$stackPtr] = true;
                }
                break;
            }
            // In case of any token except newline after opening curly bracket:
            // TODO: move newline characters from here
            if (!in_array($nextToken['content'], array("\n", "\r", "\r\n"))) {
                $this->isOpenBraceLast[$stackPtr] = false;
            }
        }

        // Last brace processed?
        if ($phpcsFile->findNext(T_OPEN_CURLY_BRACKET, $currentPtr) === false) {
            $this->publish($phpcsFile);
        }
    }

    private function publish(PHP_CodeSniffer_File $phpcsFile) {
        // Compute actual value
        $latest = 0;
        $nonLatest = 0;
        foreach($this->isOpenBraceLast as $isLast){
            if ($isLast) {
                $latest++;
                continue;
            }
            $nonLatest ++;
        }

        // Publish
        $a = array_keys($this->isOpenBraceLast);
        $latestPtr = end($a);
        $phpcsFile->addWarning($latest/($latest+$nonLatest), $latestPtr, 'value');
    }

}
