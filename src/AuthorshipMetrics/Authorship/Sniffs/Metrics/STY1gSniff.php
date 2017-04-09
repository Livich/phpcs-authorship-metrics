<?php

/**
 * Class Authorship_Sniffs_Metrics_STY1gSniff
 * Implements: "Average indentation in white spaces after open braces ({)"
 */
class Authorship_Sniffs_Metrics_STY1gSniff extends Authorship_Sniffs_Metrics_STY1hSniff
{
    protected function getPattern() {
        return '/\\s/u';
    }
}