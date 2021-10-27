<?php
namespace App\Application\Controllers;

use App\Application\Manager\AccountManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Csrf
{
    public function generate(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return AccountManager::csrf_protection($response, $request->getHeaders(), true);
    }

    /**
     * You see in english lol
     */
    public function renew(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return AccountManager::csrf_protection($response, $request->getHeaders(), false);
    }

}