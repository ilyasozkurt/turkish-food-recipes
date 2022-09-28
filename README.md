# Turkish Food Recipe Dataset + Scraper

A scrapper which allows you to scrape 20000+ food recipes from Turkish cousine.

## How to install and use Scrapper?

1. `git clone https://github.com/ilyasozkurt/turkish-food-recipes && cd turkish-food-recipes/scrapper`
2. `composer install`
3. `php artisan scrape:recipes --force-download`

The `--force-download` option downloads all sitemaps instead of reading from cache.

## How to use the dataset?

After you run the command `php artisan scrape:recipes --force-download` you'll be able to see `/data/` directory on root of the project. 

The dataset is in JSON format. 

Enjoy!