# PHPCS Authorship Metrics implementation
 
Authorship metrics set got from:
>H. Ding and M. H. Samadzadeh, “Extraction of Java program fingerprints for software authorship identification,” Journal of Systems and Software, vol. 72, no. 1, pp. 49–57, 2004.


## Usage...

* Clone the repository

###### ...with PHPUnit

If you want to check out the project, you may run new metrics set via PHPUnit:

* Call `composer update`
* Call `./setup.sh` to add `/AuthorshipMetrics/` to PHPCS package which is located at `/vendor/squizlabs/php_codesniffer/`
* Call `phpunit -c ./src/phpunit.xml`

###### ...in your project

* Look at the `./setup.sh` contents. You may copy `/AuthorshipMetrics/` as is to your PHPCS distribution and use new metrics without further effort