# Turkish Food Recipe Dataset + Scraper

A database which includes 20000+ recipes from Turkish cousine. The recipes are scraped from [yemek.com](https://yemek.com/).

## How to install and use Scrapper?

1. `git clone https://github.com/ilyasozkurt/turkish-food-recipes && cd turkish-food-recipes/scrapper`
2. `composer install`
3. `php artisan scrape:recipes --force-download`

The `--force-download` option downloads all sitemaps instead of reading from cache.

## How to use the dataset?

You can see all datas under `/data/` folder. The dataset is in JSON format.

Enjoy!