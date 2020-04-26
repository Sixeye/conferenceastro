<?php

namespace App\Tests;

use App\Entity\Commentaire;
use App\SpamChecker;
use Monolog\Test\TestCase;
use Symfony\Component\HttpClient\MockHttpClient;
use Symfony\Component\HttpClient\Response\MockResponse;
use Symfony\Contracts\HttpClient\ResponseInterface;

class SpamCheckerTest extends TestCase
{
    public function testSpamScoreWithInvalidRequest()
    {
        /*
       $commentaire = new Commentaire();
       $commentaire->setCreatedAtValue();
       $context = [];

       $client = new MockHttpClient([new MockResponse('invalid', ['response_headers' => ['x-akismet-debug-help: Invalid key']])]);
       $checker = new SpamChecker($client, 'abcde');

       $this->expectException(\RuntimeException::class);
       $this->expectExceptionMessage('Unable to check for spam: invalid (Invalid key).');
        $checker->getSpamScore($commentaire, $context); */

    }
}
