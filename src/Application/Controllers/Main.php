<?php
namespace App\Application\Controllers;

use App\Application\Utils\Token\CsrfManager;
use App\Application\Utils\Token\JwtManager;
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\ServerRequestInterface;

class Main
{

    public function index(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        /*To generate a cryptographically secure token */

        //Generate a random string.
        // $token = openssl_random_pseudo_bytes(16);
        // echo 'TOKEN: ' . $token;

        // //Convert the binary data into hexadecimal representation.
        // $token = bin2hex($token);

        // $str = bin2hex(random_bytes(5)) . time();
        // echo rtrim(strtr(base64_encode(bin2hex(random_bytes(5)) . time()), '+/', '-_'), '=');
        // echo '<br/><br/>';
        // echo bin2hex(random_bytes(5));
        // echo '<br/><br/>';
        // echo CsrfManager::create();
        if (CsrfManager::update('ZTYxMGNiNDFjZTE2MzMwNDU3NTI')) {echo 'true';} else {echo 'false';}

        JwtManager::purge();

        $dodo = JwtManager::create();
        // // var_dump(JwtManager::getUid('aze.dfg.azz'));
        // echo '<br/><br/>';

        JwtManager::tryRenew($dodo);
        //         echo JwtManager::EXPIRE * 25 / 100;

        $test_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJzdWIiOiJhY2Nlc3NfdG9rZW4iLCJpc3MiOiJodHRwOi8vYmFzZXBocC1wb3VyLWFwaS1zbGltLnRlc3QiLCJ1aWQiOm51bGwsInJuZCI6ODc2MSwiZXhwIjoxNjMzMDExNTc4fQ.K0kKuYjr-sGdz9enCLz4z3vYxtVNSqatotlfjYZHovI';

        echo JwtManager::getUid($test_token);

        // var_dump(JwtManager::getUid($test_token));
        var_dump(JwtManager::check($test_token));

        // new JwtManager();
        // phpinfo();
        $response->getBody()->write('');

        // $response->getBody()->write("URN /home <br/><br/>");
        // $response->getBody()->write("URN /users <br/><br/>");
        // $response->getBody()->write("URN /foo/:jesuispersonnalisable <br/><br/>");

        return $response;
    }

    public function helloWorld(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        echo "<br/><br/><br/>";

        $response->getBody()->write("<br/><br/><br/>HelloWorld");
        return $response;
    }

    public function helloUsers(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

    }

    public function fooHi(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        $payload = json_encode(['hello' => 'world', 'arg' => $args['hi']], JSON_PRETTY_PRINT);
        $response->getBody()->write($payload);

        return $response->withHeader('Content-Type', 'application/json');
    }

    public function getJwt(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        //         $token = new Token();

        // // Standard claims are supported
        //         $token->addClaim(new Claim\Audience(['audience_1', 'audience_2']));
        //         $token->addClaim(new Claim\Expiration(new \DateTime('30 minutes')));
        //         $token->addClaim(new Claim\IssuedAt(new \DateTime('now')));
        //         $token->addClaim(new Claim\Issuer('your_issuer'));
        //         $token->addClaim(new Claim\JwtId('your_id'));
        //         $token->addClaim(new Claim\NotBefore(new \DateTime('now')));
        //         $token->addClaim(new Claim\Subject('your_subject'));

        //         $jwt = new Jwt();

        //         $algorithm = new None();
        //         $encryption = Factory::create($algorithm);
        //         $serializedToken = $jwt->serialize($token, $encryption);

        // // var_dump($serializedToken);

        //         $token = $jwt->deserialize($serializedToken);

        //         var_dump($token);

        //         $context = new Context($encryption);
        //         $context->setAudience('audience_1');
        //         $context->setIssuer('your_issuer');
        //         $context->setSubject('your_subject');

        //         try {
        //             $jwt->verify($token, $context);
        //         } catch (VerificationException $e) {
        //             echo '-->' . $e->getMessage();
        //         }

    }

}