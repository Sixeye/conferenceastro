<?php


namespace App;

use App\Entity\Commentaire;
use Symfony\Contracts\HttpClient\HttpClientInterface;

class SpamChecker
{

    private $client;
    private $endpoint;

    public function __construct(HttpClientInterface $client, string $akismetKey)
    {
        $this->client = $client;
        $this->endpoint = sprintf('https://%s.rest.akismet.com/1.1/comment-check', $akismetKey);
    }

    public function getSpamScore(Commentaire $commentaire, array $context): int
    {
        $response = $this->client->request('POST', $this->endpoint, [
      'body' => array_merge($context, [
          'blog'                 => 'https://127.0.0.1:8000/',
          'commentaire_type'     => 'commentaire',
          'commentaire_texte'    => $commentaire->getTexte(),
          'commentaire_filename' => $commentaire->getFilename(),
          'blog_lang'            => 'fr',
          'blog_charset'         => 'UTF-8',
          'is_test'              => true,
      ]),
    ]);
        $headers = $response->getHeaders();
        if ('discard' === ($headers['x-akismet-pro-tip'][0]??'')){
            return 2;
        }

        $context = $response->getContent();
        if (isset($headers['x-akismet-debug-help'][0])){
            throw new \RuntimeException(sprintf('Unable to check for spam: %s(%s).', $context, $headers['x-akismet-debug-help'][0]));
        }
        return 'true' === $context ? 1 : 0;
    }

}