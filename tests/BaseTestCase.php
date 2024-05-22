<?php

namespace App\Test;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DomCrawler\Crawler;

class BaseTestCase extends TestCase
{
    protected function getApplicationOutput(): string
    {
        ob_start();
        require_once __DIR__ . '/../public/index.php';
        $output = ob_get_contents();
        ob_end_clean();

        return !$output ? '' : $output;
    }

    protected function assertHasTag(string $tag, string $output): void
    {
        $crawler = new Crawler($output);

        $this->assertGreaterThanOrEqual(1, $crawler->filter($tag)->count());
    }

    protected function assertHasTagWithContent(string $tag, string $content, string $output): void
    {
        $crawler = new Crawler($output);

        $this->assertGreaterThanOrEqual(1, $crawler->filter($tag)->reduce(
            fn (Crawler $node) => $node->text() === $content
        )->count());
    }
}
