<?php
declare(strict_types=1);

defined('BASEPATH') || exit('No direct script access allowed');

use System\Controller;
use System\Response;

final class Api extends Controller
{
    public function health(): Response
    {
        return Response::json([
            'success' => true,
            'data' => [
                'name' => 'Codekop PHP MVC',
                'version' => '1.0.0',
                'php' => PHP_VERSION,
                'timezone' => date_default_timezone_get(),
            ],
        ]);
    }

    public function echo(): Response
    {
        return Response::json([
            'success' => true,
            'data' => $this->request->json(),
        ]);
    }

    public function route(): Response
    {
        return Response::json([
            'success' => true,
            'data' => [
                'parameters' => $this->request->routeParameters(),
                'query' => $this->request->query(),
            ],
        ]);
    }
}
