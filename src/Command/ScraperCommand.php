<?php

declare(strict_types=1);

namespace App\Command;

use App\Service\ScraperService;
use Symfony\Component\Console\Attribute\AsCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

#[AsCommand(
    name: 'site:scrape-products',
    description: 'Scrapes product information from the specified website',
    aliases: ['site:scraper'],
    hidden: false
)]
class ScraperCommand extends Command
{
    protected function configure(): void
    {
        $this->addArgument('url', InputArgument::REQUIRED, 'The URL of the website.');
        $this->setHelp('Provide the URL to the website to scrape');
    }

    protected function execute(InputInterface $input, OutputInterface $output): int
    {
        try {
            $url = $input->getArgument('url');

            if (!filter_var($url, FILTER_VALIDATE_URL)) {
                $output->writeln(" >>> $url is not a valid URL");

                return Command::FAILURE;
            }

            $products = (new ScraperService())->getProducts($url);

            $output->writeln(json_encode($products, JSON_PRETTY_PRINT));
            $output->writeln('');
            $output->writeln('Running command to scrape: '.$url);

            return Command::SUCCESS;
        } catch (\Throwable $exception) {
            $output->writeln('An error occurred: '.$exception->getMessage());

            return Command::FAILURE;
        }
    }
}
