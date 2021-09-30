<?php
namespace App\Application\Utils\Token;

use Ahc\Jwt\JWT;
use App\Application\Utils\DatabaseManager;

class JwtManager
{
    const EXPIRE = 3600;

    /**
     * Créer une instance JWT
     *
     * @return {Object}
     */
    protected static function instance()
    {
        $jwt = new JWT('secret', 'HS256', self::EXPIRE, 10);
        return $jwt;
    }

    /**
     * Purge les jetons obsolètes
     *
     * @void
     */
    public static function purge()
    {
        DatabaseManager::delete(
            "DELETE FROM `token` WHERE TIME_TO_SEC( TIMEDIFF(CURRENT_TIMESTAMP() , `expire_at`) ) > 0"
        );
    }

    /**
     * Création d'un jeton et sauvegarde de ce dernier en DB
     * en cas d'echec, la methode est éxecuté de nouveau jusqu'à obtention
     * A surveiller! Boucle de la mort probable ou a refactorer
     *
     * @param {Int} $uid
     *
     * @return {String}
     */
    public static function create($uid = null)
    {

        $serializedToken = self::instance()->encode([
            "sub" => "access_token",
            "iss" => $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST'],
            "uid" => $uid,
            "rnd" => mt_rand(1000, 9999),
        ]);
        echo '<br/><br/>Create</br/><br/>';
        $lastInsertId = DatabaseManager::insert(
            "INSERT INTO `token` SET
                `payload` = md5(:payload),
                `header` = 'jwt',
                `uid` = :uid,
                `not_renew_before` = DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL :nbf SECOND),
                `expire_at` = DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL :exp SECOND)",
            array(':payload' => $serializedToken, ':uid' => $uid, ':nbf' => intval(self::EXPIRE - self::EXPIRE * 25 / 100), ':exp' => self::EXPIRE)
        );

        // A surveiller! Boucle de la mort probable ou a refactorer
        if ($lastInsertId == 0) {
            self::create($uid);
        }

        return $serializedToken;
    }

    /**
     * Renouvelle le jeton à condition que la valeur NBF soit dépassée
     * sinon retourne l'ancien jeton comme étant valide
     *
     * @param {String} $serializedToken
     *
     * @return {String}
     */
    public static function renew(string $serializedToken)
    {
        if (self::check($serializedToken, true)) {
            $payload = self::getUid($serializedToken);
            $uid = isset($payload['uid']) ? $payload['uid'] : null;
            return self::create($uid);
        } else {
            return $serializedToken;
        }
    }

    /**
     * Lit dans le payload du jeton l'identifiant utilisateur
     *
     * @param {String} $serializedToken
     *
     * @return {Int}
     */
    public static function getUid($serializedToken)
    {
        if (self::check($serializedToken)) {
            return isset($payload['uid']) ? $payload['uid'] : -1;
        } else {
            return -1;
        }
    }

    /**
     * Vérifie si le jeton existe en BD, si il n'est pas expiré
     * Si $checkNbf est actif, vérifie
     * si le demandeur est en droit de demander un nouveau jeton
     *
     * @param {String} $serializedToken
     * @param {Boolean} $checkNbf
     *
     * @return {Boolean}
     */
    protected static function check(string $serializedToken, bool $checkNbf = false)
    {
        self::purge(); // On purge avant les jetons expirés

        $add_nbf = $checkNbf ? ' AND TIME_TO_SEC( TIMEDIFF(CURRENT_TIMESTAMP() , `not_renew_before `) ) > 0 ' : '';

        $result = DatabaseManager::rowCount(
            "SELECT * FROM `token` WHERE
                `payload` = MD5(:payload) AND
                `header` = 'jwt' AND
                TIME_TO_SEC( TIMEDIFF(CURRENT_TIMESTAMP() , `expire_at`) ) < 0
                " . $add_nbf . "
            LIMIT 1",
            array(':payload' => $serializedToken)
        );

        if ($result >= 1) {
            $payload = self::instance()->decode($serializedToken);

            if (!isset($payload['sub']) || $payload['sub'] != 'access_token') {
                http_response_code(403);
                exit;
                return false;
            } else if (!isset($payload['iss']) || $payload['iss'] != $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']) {
                http_response_code(403);
                exit;
                return false;
            } else {
                return true;
            }

        } else {
            return false;
        }
    }
}
