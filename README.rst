==========================
Guzzle Progress Subscriber
==========================

Adds upload and download progress events to transfers.

.. code-block:: php

    <?php

    require 'vendor/autoload.php';

    use GuzzleHttp\Client;
    use GuzzleHttp\Subscriber\Progress\Progress;

    $uploadCallback = function ($expected, $total, $client, $request) {
        printf("Upload: %d %% \r", 100 * ($total / $expected));
    };

    $downloadCallback = function ($expected, $total, $client, $request, $res) {
        printf("Download: %d %% \r", 100 * ($total / $expected));
    };

    $progress = new Progress($uploadCallback, $downloadCallback);

    $client = new Client();
    $client->put('http://httpbin.org/put', [
        'body'        => str_repeat('.', 10000),
        'subscribers' => [$progress],
    ]);

    echo "\n";

Installing
----------

This project can be installed using Composer. Add the following to your
composer.json:

.. code-block:: javascript

    {
        "require": {
            "guzzlehttp/progress-subscriber": "~1.0"
        }
    }

Constructor Options
-------------------

The ``GuzzleHttp\Subscriber\Progress\Progress`` class accepts the following
constructor arguments:

``$uploadProgress``
    (callable) A function that is invoked each time data is read from the
    upload stream. The event receives the expected number of bytes to transfer
    in the first argument, the total number of bytes transferred in the
    second argument, the client used to send the request in the third argument,
    and the request being sent in the fourth argument.

``$downloadProgress``
    (callable) A function that is invoked each time data is written to the
    response body. The event receives the expected number of bytes to download
    in the first argument, the total number of bytes downloaded in the
    second argument, the client that sent the request in the third argument,
    the request that was sent in the fourth argument, and the response being
    received in the fifth argument.
