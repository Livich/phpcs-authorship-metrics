#!/usr/bin/env bash

rm -r ./vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/Authorship/
yes | cp -rf ./src/AuthorshipMetrics/Authorship/ ./vendor/squizlabs/php_codesniffer/CodeSniffer/Standards/
