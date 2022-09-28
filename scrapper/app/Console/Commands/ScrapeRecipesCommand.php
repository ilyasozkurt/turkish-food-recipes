<?php

namespace App\Console\Commands;

use Illuminate\Console\Command;
use Illuminate\Support\Facades\Http;
use Illuminate\Support\Facades\Storage;
use Sunra\PhpSimple\HtmlDomParser;
use Symfony\Component\Console\Helper\Table;

class ScrapeRecipesCommand extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'scrape:recipes {--force-download}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'This command scrapes recipes from yemek.com';

    /**
     * Execute the console command.
     *
     * @return int
     */
    public function handle()
    {

        $forceDownload = $this->option('force-download');

        if ($forceDownload) {
            $this->downloadSitemaps();
        }

        $this->scrapeRecipes();

        $this->info('Scraping recipes...');

        return Command::SUCCESS;

    }

    private function downloadSitemaps()
    {

        $this->info('Loading yemek.com sitemap index file');

        $sitemapSource = Http::get('https://yemek.com/sitemap_index.xml')->body();

        if (empty($sitemapSource) || !str_contains($sitemapSource, '<sitemapindex')) {
            $this->error('Sitemap index file is empty or wrong formatted!');
            return Command::FAILURE;
        }

        $this->info('Sitemap index file loaded', 'info');

        $sitemapXML = new \SimpleXMLElement($sitemapSource);
        $sitemapURLs = [];
        $recipeURLs = [];

        $this->info('Parsing recipe sitemaps');

        foreach ($sitemapXML->sitemap as $sitemap) {
            if (str_contains($sitemap->loc, 'recipe-sitemap')) {
                $sitemapURLs[] = (string)$sitemap->loc;
            }
        }

        $sitemapCount = count($sitemapURLs);

        $this->info('Collecting recipes from sitemaps');

        $sitemapProgress = $this->output->createProgressBar($sitemapCount);

        foreach ($sitemapURLs as $sitemapURL) {
            $recipeSitemapSource = Http::get($sitemapURL)->body();
            $recipeSitemapXML = new \SimpleXMLElement($recipeSitemapSource);
            foreach ($recipeSitemapXML->url as $recipeItem) {
                if ($recipeItem->loc != 'https://yemek.com/tarif/') {
                    $recipeURLs[] = (string)$recipeItem->loc;
                }
            }
            $sitemapProgress->advance();
        }

        $recipeCount = count($recipeURLs);

        $this->info('');
        $this->info($recipeCount . ' recipes collected from ' . $sitemapCount . ' sitemaps');

        $sitemapProgress->finish();

        Storage::put('recipe-urls.json', json_encode($recipeURLs));

        $this->info('Recipe URLs saved to recipe-urls.json');

    }

    private function scrapeRecipes()
    {

        include app_path('Libraries/simple_html_dom.php');

        $recipeURLs = json_decode(Storage::get('recipe-urls.json'), true);
        $progressBar = $this->output->createProgressBar(count($recipeURLs));

        foreach ($recipeURLs as $recipeURL) {

            $recipeSource = Http::get($recipeURL)->body();

            $recipeDOM = str_get_html($recipeSource);

            $recipeRAWData = $recipeDOM->find('#__NEXT_DATA__', 0)->innertext;

            if (empty($recipeRAWData)) {
                $this->error('Recipe data is empty!');
                continue;
            }

            $recipeRAWData = json_decode($recipeRAWData, true);
            $recipeContent = $recipeRAWData['props']['pageProps']['initialState']['content'];
            $recipeContentJSON = json_encode($recipeContent, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES | JSON_UNESCAPED_UNICODE);
            $recipeID = $recipeContent['PostId'];

            $this->forceFilePutContents('../data/' . $recipeID . '.json', $recipeContentJSON);

            $progressBar->advance();

        }

        $progressBar->finish();

        $this->info('');
        $this->info('Recipes scraped!');

    }

    private function forceFilePutContents(string $fullPathWithFileName, string $fileContents)
    {

        $exploded = explode(DIRECTORY_SEPARATOR, $fullPathWithFileName);

        array_pop($exploded);

        $directoryPathOnly = implode(DIRECTORY_SEPARATOR, $exploded);

        if (!file_exists($directoryPathOnly)) {
            mkdir($directoryPathOnly, 0775, true);
        }

        file_put_contents($fullPathWithFileName, $fileContents);

    }

}
