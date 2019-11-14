<?php

/**
 * Mock de la classe Jeedom
 */
class jeedom
{
    /**
     * @var bool Réponse pour la commande jeedom::isCapable
     */
    public static $isCapableAnswer = false;

    /**
     * @var string Nom du matériel reconnu par Jeedom
     */
    public static $hardwareName;

    /**
     * Obtenir le nom du matériel.
     *
     * @return string Valeur de jeedom::$hardwareName
     */
    public static function getHardwareName()
    {
        return jeedom::$hardwareName;
    }

    /**
     * Test si Jeedom peut exécutée une commande.
     *
     * @param string $str Nom de la commande à utiliser (inutilisé)
     *
     * @return bool Valeur de jeedom::$isCapableAnswer
     */
    public static function isCapable($str)
    {
        return self::$isCapableAnswer;
    }

    public static function getApiKey($_plugin = 'core') {
		if ($_plugin == 'apipro') {
			if (config::byKey('apipro') == '') {
				config::save('apipro', config::genKey());
			}
			return config::byKey('apipro');
		}

		if ($_plugin == 'apimarket') {
			if (config::byKey('apimarket') == '') {
				config::save('apimarket', config::genKey());
			}
			return config::byKey('apimarket');
		}

		if (config::byKey('api', $_plugin) == '') {
			config::save('api', config::genKey(), $_plugin);
		}
		return config::byKey('api', $_plugin);
	}
}
