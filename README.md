# Turkish Food Recipe Scraper

A scrapper which allows you to scrape 20000+ Turkish food recipes and export them to json files.

## How to install and use Scrapper?

1. `git clone https://github.com/ilyasozkurt/turkish-food-recipes && cd turkish-food-recipes/scrapper`
2. `composer install`
3. `php artisan scrape:recipes --force-download`

The `--force-download` option downloads all sitemaps instead of reading from cache.

## How to use the dataset?

After you run the command `php artisan scrape:recipes --force-download` you'll be able to see scrapped data under `/data/` directory on root of the project.

## Sample Data of Scrapping

You can find the sample data at [`/data/sample.json`](/data/sample.json) path.

Enjoy!

Sponsored by [trustlocale.com](https://trustlocale.com "Neighborhood Reviews, Insights")
