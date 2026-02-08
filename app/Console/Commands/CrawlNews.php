<?php

namespace App\Console\Commands;

use App\Services\NewsCrawlerService;
use Illuminate\Console\Command;

class CrawlNews extends Command
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'news:crawl {--source=}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Crawl news from all sources or a specific source';

    /**
     * Execute the console command.
     */
    public function handle(NewsCrawlerService $crawler)
    {
        $this->info('Starting news crawl...');

        try {
            $count = $crawler->crawlAll();

            $this->info("Successfully crawled {$count} articles!");

            return Command::SUCCESS;

        } catch (\Exception $e) {
            $this->error('Error: ' . $e->getMessage());
            return Command::FAILURE;
        }
    }
}
