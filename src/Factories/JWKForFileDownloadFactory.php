<?php
namespace Apie\Graphql\Factories;

use Jose\Component\Key\JWK;
use Jose\Component\KeyManagement\JWKFactory;

class JWKForFileDownloadFactory
{
    private JWK $jwk;
    public function __construct(
        private readonly string $jwkFilePath = '/tmp/apie_file_download_jwk.json'
    ) {
    }

    public function create(): JWK
    {
        if (isset($this->jwk)) {
            return $this->jwk;
        }
        if (!file_exists($this->jwkFilePath)) {
            $this->jwk = JWKFactory::createRSAKey(
                4096,
                [
                    'alg' => 'RSA-OAEP-256',
                    'use' => 'enc'
                ]
            );
            file_put_contents($this->jwkFilePath, json_encode($this->jwk->toArray(), JSON_PRETTY_PRINT));
            return $this->jwk;
        }
        $jwkData = json_decode(file_get_contents($this->jwkFilePath), true);
        return new JWK($jwkData);
    }
}
