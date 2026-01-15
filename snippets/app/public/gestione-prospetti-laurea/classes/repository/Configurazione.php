<?php
namespace GestioneProspettiLaurea\repository;

use JsonSchema\Validator;
use stdClass;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Si occupa di creare un oggetto di tipo stdClass contenente le informazioni di configurazione, sincronizzarne lo stato
 * con il JSON in conf/, e validarlo. Fornisce helper per i campi usati di frequente (corsi, nomi dei corsi).
 */
class Configurazione
{
    // percorso relativo della configurazione di default
    private const PATH_DEFAULT = "config/default.json";

    // percorso relativo della directory della configurazione dei corsi
    private const PATH_CORSI = "config/corsi";

    // percorso relativo dello schema
    private const PATH_SCHEMA = "config/schema.json";

    /*
     * Metodo fabbrica che ottiene una classe di configurazione dal JSON in PATH_CONF, la valida e la restituisce.
     */
    public static function ottieniConfigurazione()
    {
        // prendi configurazione di default
        $conf = new \stdClass;
        $conf->{"corso-di-laurea-default"} = JSON::leggiJSON(SNIPPETS_BASE_PATH . self::PATH_DEFAULT);

        // carica la configurazione dei corsi
        $conf->{"corsi-di-laurea"} = [];
        foreach (glob(SNIPPETS_BASE_PATH . self::PATH_CORSI . "/*.json") as $file) {
            $corso = JSON::leggiJSON($file);
    
            $conf->{"corsi-di-laurea"}[] = $corso;
        }
    
        // valida configurazione
        self::validaConfigurazione($conf);
    
        return $conf;
    }

    /*
     * Valida una classe di configurazione contro uno schema estratto dal JSON in PATH_SCHEMA.
     */
    private static function validaConfigurazione($conf)
    {
        // carica schema
        $schema = JSON::leggiJSON(SNIPPETS_BASE_PATH . self::PATH_SCHEMA);

        // valida dati
        $validator = new Validator();
        $validator->validate($conf, $schema);

        // se non valido, lancia un messaggio di errore strutturato
        if (!$validator->isValid()) {
            $errors = array_map(fn($e) => "[{$e["property"]}] {$e["message"]}", $validator->getErrors());
            throw new \Exception("JSON di configurazione non valido:\n" . implode("\n", $errors));
        }
    }

    /*
     * Helper che ottiene la lista dei corsi di laurea da una classe di configurazione.
     */
    public static function ottieniCorsi($conf)
    {
        // estrai array corsi dalla configurazione
        $corsi = $conf->{"corsi-di-laurea"};

        return $corsi;
    }

    /*
     * Helper che ottiene il nome completo del corso, ottenuto concatenando il nome col nome corto.
     */
    public static function nomeCompleto($corso) {
        return $corso->{"nome"} . " (" . $corso->{"nome-corto"} . ")";
    }

    /*
     * Helper che ottiene la configurazione di uno specifico corso sulla base del nome.
     */
    public static function ottieniConfigurazioneCorso($conf, $nome_corso)
    {
        // ottieni la configurazione del corso
        foreach ($conf->{"corsi-di-laurea"} as $conf_corso) {
            if (
                $conf_corso->{"nome"} == $nome_corso 
                || $conf_corso->{"nome-corto"} == $nome_corso
                || self::nomeCompleto($conf_corso) == $nome_corso
            ) {
                return $conf_corso;
            }
        }

        // se inesistente, lancia un errore
        throw new \Exception("Corso " . $nome_corso . " richiesto inesistente");
    }

    /*
     * Salva una classe di configurazione nel JSON in PATH_CONF.
     */
    public static function salvaConfigurazione($conf)
    { 
        // ripulisci configurazione corsi
        array_map("unlink", glob(SNIPPETS_BASE_PATH . self::PATH_CORSI . "/*.json"));

        // scrivi configurazione corsi
        foreach ($conf->{"corsi-di-laurea"} as $corso) {
            $path = SNIPPETS_BASE_PATH
                  . self::PATH_CORSI
                  . "/{$corso->{'nome-corto'}}.json";
    
            JSON::scriviJSON($path, $corso);
        }
    }
}
