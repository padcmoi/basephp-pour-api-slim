<?php
namespace App\Application\Utils\Token;

use Ahc\Jwt\JWT;
use App\Application\Utils\DatabaseManager;
use Exception;

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
        if (isset($_ENV['JWT_KEY'])) {
            $expire = isset($_ENV['JWT_EXPIRE']) ? intval($_ENV['JWT_EXPIRE']) : intval(self::EXPIRE);
            $jwt = new JWT($_ENV['JWT_KEY'], 'HS256', $expire, 10);
            return $jwt;
        } else {
            throw new Exception('JWT KEY introuvable dans .env');
        }
    }

    /**
     * Purge les jetons obsolètes
     *
     * @void
     */
    public static function purge()
    {
        $expire = isset($_ENV['JWT_EXPIRE']) ? intval($_ENV['JWT_EXPIRE']) : intval(self::EXPIRE);
        DatabaseManager::delete(
            "DELETE FROM `token` WHERE TIME_TO_SEC( TIMEDIFF(CURRENT_TIMESTAMP() , `expire_at`) ) > :exp",
            array(":exp" => $expire)
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

        $expire = isset($_ENV['JWT_EXPIRE']) ? intval($_ENV['JWT_EXPIRE']) : intval(self::EXPIRE);
        $nbf = intval($expire - $expire * 25 / 100);

        $lastInsertId = DatabaseManager::insert(
            "INSERT INTO `token` SET
                `payload` = md5(:payload),
                `header` = 'jwt',
                `uid` = :uid,
                `not_renew_before` = DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL :nbf SECOND),
                `expire_at` = DATE_ADD(CURRENT_TIMESTAMP(), INTERVAL :exp SECOND)",
            array(':payload' => $serializedToken, ':uid' => $uid, ':nbf' => $nbf, ':exp' => $expire)
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
    public static function tryRenew(string $serializedToken)
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
    public static function check(string $serializedToken, bool $checkNbf = false)
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
                // retourne http code car une manque une clé au payload et/ou son contenu
                http_response_code(403);exit;
            } else if (!isset($payload['iss']) || $payload['iss'] != $_SERVER['REQUEST_SCHEME'] . '://' . $_SERVER['HTTP_HOST']) {
                // retourne http code car une manque une clé au payload et/ou son contenu
                http_response_code(403);exit;
            } else {
                return true;
            }

        } else {
            return false; // Invalide dans la base de données
        }
    }
}