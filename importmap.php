<?php

/**
 * Returns the importmap for this application.
 *
 * - "path" is a path inside the asset mapper system. Use the
 *     "debug:asset-map" command to see the full list of paths.
 *
 * - "entrypoint" (JavaScript only) set to true for any module that will
 *     be used as an "entrypoint" (and passed to the importmap() Twig function).
 *
 * The "importmap:require" command can be used to add new entries to this file.
 */
return [
    'app' => [
        'path' => './assets/app.js',
        'entrypoint' => true,
    ],
    'daisyui' => [
        'version' => '4.12.22',
    ],
    'postcss-js' => [
        'version' => '4.0.1',
    ],
    'picocolors' => [
        'version' => '1.1.1',
    ],
    'css-selector-tokenizer' => [
        'version' => '0.8.0',
    ],
    'culori/require' => [
        'version' => '3.3.0',
    ],
    'daisyui/dist/full.min.css' => [
        'version' => '4.12.22',
        'type' => 'css',
    ],
    'camelcase-css' => [
        'version' => '2.0.1',
    ],
    'postcss' => [
        'version' => '8.4.33',
    ],
    'fastparse' => [
        'version' => '1.1.2',
    ],
    'cssesc' => [
        'version' => '3.0.0',
    ],
    'nanoid/non-secure' => [
        'version' => '3.3.7',
    ],
    'alpinejs' => [
        'version' => '3.14.7',
    ],
    'htmx.org' => [
        'version' => '2.0.4',
    ],
    'fireworks-js' => [
        'version' => '2.10.8',
    ],
    'app_obs' => [
        'path' => './assets/app_obs.js',
        'entrypoint' => true,
    ],
    'app_qr' => [
        'path' => './assets/app_qr.js',
        'entrypoint' => true,
    ],
];