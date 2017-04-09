<?php

/**
 * Class Authorship_Sniffs_Metrics_STY5Sniff
 * Implements: "Average white spaces to the right side of operators (+ - * / % = += -= *= /= %= ==)"
 */
class Authorship_Sniffs_Metrics_STY5Sniff implements PHP_CodeSniffer_Sniff
{

    /**
     * @var array Holds array like ([pointer to operator] => (int) spaces)
     */
    private $spaces = array();

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
        return array(
            T_PLUS,
            T_MINUS,
            T_MULTIPLY,
            T_DIVIDE,
            T_MODULUS,
            T_EQUAL,
            T_PLUS_EQUAL,
            T_MINUS_EQUAL,
            T_MUL_EQUAL,
            T_DIV_EQUAL,
            T_MOD_EQUAL,
            T_IS_EQUAL
        );

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
            $this->spaces = array();
            $this->latestFile = $phpcsFile->getFilename();
        }

        // Check if there is whitespace on the same line
        if ($tokens[$stackPtr+1]['code'] == T_WHITESPACE
            && $tokens[$stackPtr+1]['line'] == $tokens[$stackPtr]['line']
        ) {
            $content = $tokens[$stackPtr+1]['content'];
            // TODO: move EOL from here
            $content = str_replace(array("\r","\n"), '', $content);
            $spaceCount = preg_match_all($this->getPattern(), $content);
            $this->spaces[$stackPtr+1] = $spaceCount;
        } else {
            $this->spaces[$stackPtr+1] = 0;
        }

        // Last operator processed?
        if ($phpcsFile->findNext($this->register(), $stackPtr+1) === false) {
            $this->publish($phpcsFile);
        }
    }

    protected function getPattern() {
        return '/\\s/u';
    }

    private function publish(PHP_CodeSniffer_File $phpcsFile) {
        // Compute actual value
        $total = 0;
        $ptr = null;
        foreach($this->spaces as $ptr => $spaceCount){
            $total += $spaceCount;
        }
        $value = $total / count($this->spaces);

        // Publish
        $phpcsFile->addWarning($value, $ptr, 'value');
    }

}