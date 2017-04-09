<?php

/**
 * Class Authorship_Sniffs_Metrics_LatestSpacesAfterBracketSniff
 * Implements metric which counts number of whitespaces at end of line
 * after opening brace
 */
class Authorship_Sniffs_Metrics_LatestSpacesAfterBracketSniff implements PHP_CodeSniffer_Sniff
{

    /**
     * @var array Holds array like ([line number] => number of whitespaces,...)
     */
    private $lineSpaces = array();
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
            $this->lineCount = null;
            $this->lineSpaces = array();
            $this->latestFile = $phpcsFile->getFilename();
        }

        // First of all, get SLOC number
        if (!isset($this->lineCount)) {
            $this->lineCount = $this->getLineCount($tokens);
            $phpcsFile->addWarning($this->lineCount, null, 'sloc');
        }

        // Store current token and fetch by next tokens
        $currentToken = $tokens[$stackPtr];
        $nextSpacePtr = $stackPtr;
        while(true) {
            $nextSpacePtr++;
            $spaceToken = $tokens[$nextSpacePtr];
            // Exit at line end
            if ($spaceToken['line'] !== $currentToken['line']) {
                break;
            }
            // In case of whitespace after opening curly bracket...
            if( $spaceToken['code'] == T_WHITESPACE) {
                if (ord($spaceToken['content'][0]) == 32) { // Just double-check
                    $spaceCount = preg_match_all('/\\s/u', $spaceToken['content']);
                    // ...initialize array and count whitespaces
                    $this->safeSet(
                        $this->lineSpaces,
                        array($spaceToken['line']),
                        $spaceCount
                    );
                }
            } else {
                // If there is another token on the same line after whitespaces -- reset stored result
                unset($this->lineSpaces[$spaceToken['line']]);
                continue;
            }
        }
        // Put warning to display computed value
        if (isset($this->lineSpaces[$currentToken['line']])) {
            $phpcsFile->addWarning(
                $this->lineSpaces[$currentToken['line']],
                $nextSpacePtr,
                'spaces'
            );
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

    /**
     * Set value $val in the array $array like: $array[$indexes[0]]...[$indexes[N]] = $val
     * @param array $array Array to work with
     * @param array $indexes Array of indexes to set
     * @param mixed $val Value to set
     */
    private function safeSet(&$array, $indexes, $val) {
        $tmp0 = $array;
        foreach ($indexes as $i => $index) {
            if(!isset($a[$index])) {
                ${'tmp'.$i}[$index] = $i+1 == count($indexes) ? $val : array();
                $k = $i+1;
                ${'tmp'.$k} = &${'tmp'.$i}[$index];
            }
        }
        $array = $tmp0;
    }

}
