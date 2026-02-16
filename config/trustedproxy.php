<?php

return [
    /*
     * Set trusted proxy IP addresses.
     *
     * Both IPv4 and IPv6 addresses are supported, along with CIDR notation.
     *
     * The "*" character is syntactic sugar within TrustedProxy to trust any proxy
     * that connects directly to your server, a requirement when you cannot know the address
     * of your proxy (e.g. if using Render, Heroku, AWS ELB, etc).
     */
    'proxies' => env('TRUSTED_PROXIES', '*'),

    /*
     * To trust one or more specific proxies that connect directly to your server,
     * use an array or a string separated by comma of IP addresses:
     */
    // 'proxies' => ['192.168.1.1', '10.0.0.1'],

    /*
     * Or, to trust all proxies that connect directly to your server, use a "*"
     */
    // 'proxies' => '*',

    /*
     * Which headers to use to detect proxy behavior (e.g. for IP address, SSL, port, and host).
     *
     * Options include:
     *
     * - Illuminate\Http\Request::HEADER_X_FORWARDED_FOR
     * - Illuminate\Http\Request::HEADER_X_FORWARDED_HOST
     * - Illuminate\Http\Request::HEADER_X_FORWARDED_PORT
     * - Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO
     * - Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
     */
    'headers' => env('TRUSTED_HEADERS', 
        \Illuminate\Http\Request::HEADER_X_FORWARDED_FOR |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_HOST |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PORT |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_PROTO |
        \Illuminate\Http\Request::HEADER_X_FORWARDED_AWS_ELB
    ),
];
