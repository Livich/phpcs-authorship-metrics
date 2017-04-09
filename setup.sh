#!/usr/bin/env bash

#composer update
rm -r ./vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/Authorship/
yes | cp -rf ./src/AuthorshipMetrics/Authorship/ ./vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/
