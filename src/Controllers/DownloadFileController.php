<?php
namespace Apie\Graphql\Controllers;

use Apie\Core\FileStorage\FileStorageInterface;
use Apie\Core\FileStorage\StoredFile;
use Apie\Graphql\Factories\JWKForFileDownloadFactory;

class DownloadFileController
{
    public function __construct(
        private readonly FileStorageInterface $fileStorage,
        private readonly JWKForFileDownloadFactory $jwkFactory,
    ) {
    }
    public function __invoke(string $encryptedStoragePath): StoredFile
    {
        $jwk = $this->jwkFactory->create();
        $decrypted = $jwk->decrypt(base64_decode($encryptedStoragePath), 'RSA-OAEP-256');
        // TODO create response
        return $this->fileStorage->loadFromStorage($decrypted);
    }
}
