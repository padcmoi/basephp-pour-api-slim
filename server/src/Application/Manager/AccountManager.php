<?php
namespace App\Application\Manager;

use App\Application\Manager\CaptchaManager;
use App\Application\Manager\ResponseManager;
use Padcmoi\BundleApiSlim\Database;
use Padcmoi\BundleApiSlim\SanitizeData;
use Padcmoi\BundleApiSlim\Token\CsrfToken;
use Padcmoi\BundleApiSlim\Token\JwtToken;

class AccountManager
{
    /**
     * Method GET/PUT
     */
    public static function csrf_protection($response, array $headers, $allow_newtoken = false)
    {
        $csrf_token = isset($headers['csrf-token']) ? $headers['csrf-token'][0] : '';

        CsrfToken::checkIP(); // Avec vérification des IP

        if (CsrfToken::check($csrf_token)) {
            CsrfToken::update($csrf_token);
        } else if ($allow_newtoken) {
            $csrf_token = CsrfToken::create();
        } else {
            $csrf_token = '';
        }

        ResponseManager::add(['csrf_token' => $csrf_token]);

        return ResponseManager::makeJSON($response);
    }

    /**
     * Method GET
     */
    public static function login_rules($response)
    {
        if (CaptchaManager::has()) {
            return CaptchaManager::picture($response);
        } else {
            ResponseManager::add(['captcha' => false]);
            return ResponseManager::makeJSON($response);
        }
    }

    /**
     * Method POST
     */
    public static function login()
    {
        // Captcha soumission
        CaptchaManager::submit();

        // Données entrantes et nettoyages
        $input = SanitizeData::clean();
        $login = isset($input['login']) ? $input['login'] : '';
        $plainPassword = isset($input['password']) ? $input['password'] : '';

        // Recherche en base de données du login (email) et retourne son éventuel ID & Password serializé
        $req = Database::fetchAll(
            "SELECT `id`,`pass` FROM `utilisateur` WHERE `email` = :email LIMIT 1",
            array(':email' => $login)
        );
        // Cas Limite ! donne des valeurs par défaut
        $uid = isset($req[0]['id']) ? intval($req[0]['id']) : -1;
        $serializedPassword = isset($req[0]['pass']) ? $req[0]['pass'] : md5(mt_rand(100, 999));

        // Fonction PHP de comparaison pour les mots de passe BCrypt
        // Autorise ou non l'authentification et retourne un jeton d'authentification
        if (!CaptchaManager::has() && password_verify($plainPassword, $serializedPassword)) {
            // Auth ok
            ResponseManager::add(['loggedin' => true]);
            ResponseManager::add(['access_token' => JwtToken::create($uid)]);
        } else {
            // Auth fail
            ResponseManager::add(['loggedin' => false]);
            ResponseManager::add(['access_token' => '']);
            CaptchaManager::make(); // Génére un nouveau captcha en cas d'erreur de mot de passe
            ResponseManager::add(['message' => 'bad login']);
        }
    }

    /**
     * Method PUT
     */
    public static function renewToken()
    {
        $data = SanitizeData::clean();
        $access_token = isset($data['access_token']) ? $data['access_token'] : '';

        if (JwtToken::check($access_token)) {
            ResponseManager::add(['valid' => 1]);
            ResponseManager::add(['access_token' => JwtToken::tryRenew($access_token)]);
        } else {
            ResponseManager::add(['access_token' => '']);
        }
    }

}