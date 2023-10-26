<?php

namespace App\Service;

use Aws\S3\S3Client;

class UploadAwsService {

    private array $credentials;

    public function __construct(private string $accessKey, private string $secretKey, private string $region, private string $bucketName)
    {
        $this->credentials = [
            'credentials' => [
                'key'    => $this->accessKey,
                'secret' => $this->secretKey,
            ],
            'region' => $this->region,
            'version' => 'latest'
        ];
    }

    public function upload(string $localPath, string $awsPath) 
    {
        $s3Client = new S3Client($this->credentials);

        try {
            $s3Client->putObject([
                'Bucket' => $this->bucketName,
                'Key'    => $awsPath,
                'SourceFile' => $localPath,
            ]);

            unlink($localPath);
        } catch (\Exception $e) {
            throw $e->getMessage();
        }
    }
}