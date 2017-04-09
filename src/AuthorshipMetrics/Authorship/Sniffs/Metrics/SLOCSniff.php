<?php

/**
 * Class Authorship_Sniffs_Metrics_SLOCSniff
 * Implements SLOC metric
 */
class Authorship_Sniffs_Metrics_SLOCSniff implements PHP_CodeSniffer_Sniff
{
    /**
     * @var null|integer Holds SLOC number
     */
    private $lineCount = null;

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
        return array(T_OPEN_TAG);

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
            $this->lineCount = null;
            $this->latestFile = $phpcsFile->getFilename();
        }

        // Get SLOC number and put a warning
        if (!isset($this->lineCount)) {
            $this->lineCount = $this->getLineCount($tokens);
            $phpcsFile->addWarning($this->lineCount, null, 'value');
        }
    }

    /**
     * Get SLOC number for current file
     * @param array $tokens PHPCS token array
     * @return integer SLOC number
     */
    private function getLineCount($tokens) {
        $result = array();
        foreach($tokens as $token) {
            // TODO: more token types to skip?
            if(stristr($token['type'], 'T_DOC_')
                || stristr($token['type'], 'T_OPEN_')
                || stristr($token['type'], 'T_WHITESPACE')
            ) {
                continue;
            }
            $result[$token['line']] = 1;
        }
        return count($result);
    }

}
