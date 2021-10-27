<?php
namespace App\Application\Manager;

use Padcmoi\BundleApiSlim\DataMerge;

class ResponseManager
{
    /**
     * Ajoute dans un tableau les differentes réponses
     *
     * @void
     */
    public static function add(array $data)
    {
        DataMerge::instance()->add($data);
    }

    /**
     * Construit la réponse finale et retourne la vue
     *
     * @param {ResponseInterface}
     *
     * @return {Array}
     */
    public static function makeJSON($response)
    {
        $response->getBody()->write(json_encode(DataMerge::instance()->build(), JSON_PRETTY_PRINT));
        return $response->withHeader('Content-Type', 'application/json');
    }
}