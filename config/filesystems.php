<?php

$mediaDriver = env('MEDIA_DISK_DRIVER', 'local');
$supabaseEndpoint = (string) env('SUPABASE_STORAGE_ENDPOINT');
$supabasePublicBucket = env('SUPABASE_PUBLIC_BUCKET', 'lost-found-public');

$publicDiskUrl = env('SUPABASE_PUBLIC_URL') ?: env('MEDIA_PUBLIC_URL');

if (! $publicDiskUrl && $mediaDriver === 's3' && $supabaseEndpoint !== '') {
    // The S3 endpoint ends with "/storage/v1/s3"; public object URLs drop that suffix.
    $publicDiskUrl = rtrim(preg_replace('#/storage/v1/s3/?$#', '', $supabaseEndpoint), '/')
        .'/storage/v1/object/public/'.$supabasePublicBucket;
}

$publicDiskUrl = $publicDiskUrl ?: rtrim(env('APP_URL', 'http://localhost'), '/').'/storage';

// Supabase Storage speaks S3 but does not implement AWS flexible checksums.
// Leaving them at the SDK default ("when_supported") makes uploads fail.
$s3Options = [
    'key' => env('SUPABASE_STORAGE_KEY'),
    'secret' => env('SUPABASE_STORAGE_SECRET'),
    'region' => env('SUPABASE_STORAGE_REGION', 'ap-south-1'),
    'endpoint' => $supabaseEndpoint ?: null,
    'use_path_style_endpoint' => (bool) env('SUPABASE_STORAGE_PATH_STYLE', true),
    'request_checksum_calculation' => 'when_required',
    'response_checksum_validation' => 'when_required',
    'http' => [
        // Keep serverless requests from hanging if the storage API is slow.
        'connect_timeout' => (float) env('SUPABASE_STORAGE_CONNECT_TIMEOUT', 5),
        'timeout' => (float) env('SUPABASE_STORAGE_TIMEOUT', 30),
    ],
];

return [

    /*
    |--------------------------------------------------------------------------
    | Default Filesystem Disk
    |--------------------------------------------------------------------------
    */

    'default' => env('FILESYSTEM_DISK', 'local'),

    /*
    |--------------------------------------------------------------------------
    | Filesystem Disks
    |--------------------------------------------------------------------------
    |
    | The "public" and "local" disks can run on the local filesystem (default)
    | or on S3-compatible object storage such as Supabase Storage. Set
    | MEDIA_DISK_DRIVER=s3 on serverless hosts where the disk is read-only.
    |
    */

    'disks' => [

        'local' => [
            'driver' => $mediaDriver,
            'root' => $mediaDriver === 's3' ? '' : storage_path('app/private'),
            'serve' => true,
            'throw' => false,
            'report' => false,
            // S3 / Supabase Storage options (ignored by the local driver).
            ...$s3Options,
            'bucket' => env('SUPABASE_PRIVATE_BUCKET', 'lost-found-private'),
        ],

        'public' => [
            'driver' => $mediaDriver,
            'root' => $mediaDriver === 's3' ? '' : storage_path('app/public'),
            'url' => $publicDiskUrl,
            'serve' => false,
            'throw' => false,
            'report' => false,
            // S3 / Supabase Storage options (ignored by the local driver).
            ...$s3Options,
            'bucket' => $supabasePublicBucket,
        ],

        's3' => [
            'driver' => 's3',
            'key' => env('AWS_ACCESS_KEY_ID'),
            'secret' => env('AWS_SECRET_ACCESS_KEY'),
            'region' => env('AWS_DEFAULT_REGION'),
            'bucket' => env('AWS_BUCKET'),
            'url' => env('AWS_URL'),
            'endpoint' => env('AWS_ENDPOINT'),
            'use_path_style_endpoint' => env('AWS_USE_PATH_STYLE_ENDPOINT', false),
            'throw' => false,
            'report' => false,
        ],

    ],

    /*
    |--------------------------------------------------------------------------
    | Symbolic Links
    |--------------------------------------------------------------------------
    */

    'links' => [
        public_path('storage') => storage_path('app/public'),
    ],

];
