<?php
namespace App\Application\Controllers;

use Padcmoi\BundleApiSlim\SimplyCaptcha;
use Padcmoi\BundleApiSlim\Token\JwtToken;
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
        // echo CsrfToken::create();
        // // var_dump(JwtToken::getUid('aze.dfg.azz'));
        //         echo JwtToken::EXPIRE * 25 / 100;
        // var_dump(JwtToken::getUid($test_token));
        // new JwtToken();
        // phpinfo();
        // $response->getBody()->write("URN /home <br/><br/>");
        // $response->getBody()->write("URN /users <br/><br/>");
        // $response->getBody()->write("URN /foo/:jesuispersonnalisable <br/><br/>");

        // if (CsrfToken::update('ZTYxMGNiNDFjZTE2MzMwNDU3NTI')) {echo 'true';} else {echo 'false';}
        // JwtToken::purge();
        // $dodo = JwtToken::create();
        // // echo '<br/><br/>' . $dodo . '<br/><br/>';
        // JwtToken::tryRenew($dodo);
        $test_token = 'eyJ0eXAiOiJKV1QiLCJhbGciOiJIUzI1NiJ9.eyJpc3MiOiJodHRwOi8vYmFzZXBocC1wb3VyLWFwaS1zbGltLnRlc3QiLCJzdWIiOiJhY2Nlc3NfdG9rZW4iLCJleHAiOjE2MzMzNDYxNTYsImlhdCI6MTYzMzM0NjA5Niwicm5kIjoiYmNlM2U2Y2IwY2IyNDk4ZDRjMjI3N2FhYzIwNTlkNDkiLCJ1aWQiOjN9.tLCkIpjqfuDJfZzWUcG2UkZ8_8E_-riDHuB2-vJ0a4E';
        echo 'uid=' . JwtToken::getUid($test_token);
        // var_dump(JwtToken::check($test_token));

        // SanitizeData::without(['ab', 'baa', 'aa']);
        // SanitizeData::clean(false, ['strip_tags', 'htmlspecialchars']);
        // SanitizeData::clean(true, []);

        // echo '<br/>-' . SanitizeData::cleanIt('<script>alert("Test")   </script>', ['htmlspecialchars', 'strip_tags']) . '<br/>';

        // echo '<br/>-' . Misc::snakeCase('aze ert uUu . tt.oo__aa//jjj;içp') . '<br/>';
        // echo Misc::stringGenerator('aaa') . '<br/>';
        // echo Misc::stringGenerator() . '<br/>';

        // var_dump(SanitizeData::show());
        // var_dump(json_encode(SanitizeData::show()));

        var_dump(SimplyCaptcha::check('K46J29'));

        SimplyCaptcha::create();
        // var_dump(SimplyCaptcha::create());

        // Test2::go();

        $response->getBody()->write('');
        return $response;
    }

    public function phpinfo(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {
        phpinfo();
        $response->getBody()->write("");
        return $response;
    }

    public function helloWorld(ServerRequestInterface $request, ResponseInterface $response, array $args): ResponseInterface
    {

        phpinfo();
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