# Site Scraper
This library provides a tool to scrape product information from the https://wltest.dns-systems.net/ website.

# Installation
- Ensure you have composer installed in  your environment.
- Clone the repository and checkout the master branch
- Run `composer install` to install the required dependencies

# Usage
The command to run the scraper is located in the `bin` directory. Use the command below from the root directory
to run the scrapper providing the URL to the page as the argument to the command. The console command will output a
a JSON list of products sorted in descending order of annual price.

```
    php ./bin/runner.php site:scrape-products https://wltest.dns-systems.net/
```
To see the available list of commands run `php ./bin/runner.php`

# Testing
The library includes PHP unit test for the scraper which verifies that the required number of products are downloaded
and each has the required attributes. You can either use the composer script to run the test as follows:
```angular2html
    composer unit-test
```

or run phpunit directly from the vendor directory as follows:
```angular2html
    ./vendor/bin/phpunit
```