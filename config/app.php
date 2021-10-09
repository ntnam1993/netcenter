<?php

return [
    'disk' => env('APP_DISK'),
    's3DiskWithBucketData' => env('S3_DISK_DATA'),
    's3DiskWithBuckettEml' => env('S3_DISK_EML'),
    's3DiskWithBucketZip' => env('S3_DISK_ZIP'),
    'bucketData' => env('AWS_BUCKET_DATA'),
    'bucketEml' => env('AWS_BUCKET_EML'),
    'bucketZip' => env('AWS_BUCKET_ZIP'),
    // zexy
    'keyZexyAPI' => env('KEY_ZEXY_API'),
    'gyoshuCdZexyAPI' => env('GYOSHU_CD_ZEXY_API'),
    'ccAddress' => env('CC_ADDRESS')
];
