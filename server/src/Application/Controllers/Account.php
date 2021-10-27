<?php
namespace App\Application\Controllers;

use App\Application\Manager\AccountManager;
use App\Application\Manager\CaptchaManager;
use App\Application\Manager\ResponseManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Account
{
    public function captcha(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        return AccountManager::login_rules($response);
    }

    public function login(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        if (CaptchaManager::check()) {
            return ResponseManager::makeJSON($response);
        }

        AccountManager::login();

        return ResponseManager::makeJSON($response);
    }

    public function renewToken(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        AccountManager::renewToken();

        return ResponseManager::makeJSON($response);
    }

}