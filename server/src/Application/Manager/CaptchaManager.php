<?php
namespace App\Application\Manager;

use App\Application\Manager\ResponseManager;
use Melihovv\Base64ImageDecoder\Base64ImageDecoder;
use Padcmoi\BundleApiSlim\SanitizeData;
use Padcmoi\BundleApiSlim\SimplyCaptcha;

class CaptchaManager
{
    /**
     * Affiche l'image du captcha si elle existe
     *
     * @param {ResponseInterface}
     *
     * @return {String/undefined}
     */
    public static function picture($response)
    {
        if (self::has()) {
            $decoder = new Base64ImageDecoder(SimplyCaptcha::get(), $allowedFormats = ['jpeg', 'png', 'gif']);
            header("Content-type: " . 'image/' . $decoder->getFormat());
            $response->getBody()->write($decoder->getDecodedContent());
            return $response;
        }
    }

    /**
     * Possède un captcha
     *
     * @return {Boolean}
     */
    public static function has()
    {
        return SimplyCaptcha::get() ? true : false;
    }

    /**
     * Verifie si un captcha est requis
     * construit une réponse
     *
     * @return {Boolean}
     */
    public static function check()
    {
        if (self::has()) {
            ResponseManager::add(['captcha' => true]);
            ResponseManager::add(['message' => 'require captcha code']);
            return true;
        } else {
            ResponseManager::add(['captcha' => false]);
            return false;
        }
    }

    /**
     * Soumet un code captcha,
     * s'il est est valide retourne vrai ou faux le cas échéant
     *
     * @return {boolean}
     */
    public static function submit()
    {
        // Données entrantes et nettoyages
        $input = SanitizeData::clean();

        return SimplyCaptcha::check(isset($input['captcha']) ? $input['captcha'] : '');
    }

    /**
     * Soumet un code captcha,
     * s'il est est valide retourne vrai ou faux le cas échéant
     *
     * @return {boolean}
     */
    public static function make()
    {
        SimplyCaptcha::create(); // Génére un nouveau captcha en cas d'erreur de mot de passe
        ResponseManager::add(['captcha' => true]);
        ResponseManager::add(['message' => 'require captcha code']);
    }
}