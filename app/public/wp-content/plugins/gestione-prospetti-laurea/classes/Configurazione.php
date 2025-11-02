<?php
namespace GestioneProspettiLaurea;

use JsonSchema\Validator;
use RuntimeException;
use stdClass;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Si occupa di gestire un oggetto di tipo stdClass contenente le informazioni di configurazione (in $configurazione),
 * caricare, validare e salvare tale oggetto in memoria in formato JSON, ed espone metodi per la modifica delle
 * informazioni di configurazione ivi contenute.
 */
require_once plugin_dir_path(dirname(__FILE__)) . "vendor/autoload.php";

class Configurazione
{
    // percorso del file di configurazione
    private static $path_corsi;

    // percorso dello schema
    private static $path_schema;

    // dati estratti dal file di configurazione
    private $configurazione;

    // segnala se la configurazione è stata modificata
    private $modifica = false;

    public function __construct()
    {
        // assegna i path se non già assegnati
        if (!isset(self::$path_corsi)) {
            self::$path_corsi = plugin_dir_path(dirname(__FILE__)) . "config/conf.json";
        }
        if (!isset(self::$path_schema)) {
            self::$path_schema = plugin_dir_path(dirname(__FILE__)) . "config/schema.json";
        }

        // carica e valida configurazione
        $configurazione = self::caricaConfigurazione();
        self::validaConfigurazione($configurazione);

        // applica all'oggetto corrente
        $this->configurazione = $configurazione;
    }

    public function __destruct()
    {
        if ($this->modifica == true) {
            $this->salvaConfigurazione();
        }
    }

    private static function caricaConfigurazione()
    {
        // carica configurazione dal JSON
        $json = file_get_contents(self::$path_corsi);
        if ($json === false) {
            throw new RuntimeException("Impossibile leggere il file JSON a " . self::$path_corsi);
        }

        $dati = json_decode($json, false);
        if ($dati === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("JSON non valido in " . self::$path_corsi);
        }

        return $dati;
    }

    private function salvaConfigurazione()
    {
        // salva configurazione nel JSON
        $dati = json_encode($this->configurazione, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        if ($dati === false) {
            throw new RuntimeException("Impossibile codificare JSON");
        }

        if (file_put_contents(self::$path_corsi, $dati) === false) {
            throw new RuntimeException("Errore durante il salvataggio del file JSON a " . self::$path_corsi);
        }
    }

    private static function validaConfigurazione($configurazione)
    {
        // carica schema
        $json_schema = file_get_contents(self::$path_schema);
        if ($json_schema === false) {
            throw new RuntimeException("Impossibile leggere il file JSON a " . self::$path_schema);
        }

        $schema = json_decode($json_schema, false);
        if ($schema === null && json_last_error() !== JSON_ERROR_NONE) {
            throw new RuntimeException("JSON non valido in " . self::$path_schema);
        }

        // valida dati
        $validator = new Validator();
        $validator->validate($configurazione, $schema);

        if (!$validator->isValid()) {
            $errors = array_map(fn($e) => "[{$e["property"]}] {$e["message"]}", $validator->getErrors());
            throw new \RuntimeException("JSON di configurazione non valido:\n" . implode("\n", $errors));
        }
    }

    public function ottieniCorsi()
    {
        // estrai array corsi dalla configurazione
        $corsi = $this->configurazione->{"corsi-di-laurea"};

        return $corsi;
    }

    public function ottieniNomiCorsi()
    {
        $corsi = $this->ottieniCorsi();

        // ottieni array nomi corsi (non vuoti) da array corsi
        $nomi_corsi = [];
        foreach ($corsi as $corso) {
            $nome = $corso->{"nome"};
            if (!empty($nome)) {
                $nomi_corsi[] = $nome;
            }
        }

        return $nomi_corsi;
    }

    private function ottieniIndicePerNome($nome_corso)
    {
        $corsi = $this->ottieniCorsi();

        // cerca il corso richiesto fra quelli definiti
        if (!empty($nome_corso)) {
            foreach ($corsi as $i => $corso_di_laurea) {
                if ($corso_di_laurea->{"nome"} == $nome_corso) {
                    // restituisci l'indice fra i corsi
                    return $i;
                }
            }
        }

        // se non hai trovato nulla, è errore
        throw new RuntimeException("Nessun corso con nome " . $nome_corso);
    }

    public function eliminaEsami($nome_corso, $esami_selezionati)
    {
        $this->modifica = true;

        // ottieni il corso
        $id = $this->ottieniIndicePerNome($nome_corso);
        $corso = &$this->configurazione->{"corsi-di-laurea"}[$id];

        // ottieni llista esami
        $esami_tutti = $corso->{"esami-in-media"};

        // filtra gli esami presenti prendendo solo quelli non da rimuovere
        $esami_filtrati = array_filter($esami_tutti, function ($esame) use ($esami_selezionati) {
            return !in_array($esame->{"COD"}, $esami_selezionati);
        });

        // metti gli esami filtrati nel corso
        $corso->{"esami-in-media"} = array_values($esami_filtrati);
    }

    public function aggiungiEsame($nome_corso, $COD, $DES)
    {
        // se il codice o il nome sono vuoti, non aggiungere
        if (empty($COD) || empty($DES)) {
            return;
        }

        $this->modifica = true;

        // ottieni il corso
        $id = $this->ottieniIndicePerNome($nome_corso);
        $corso = &$this->configurazione->{"corsi-di-laurea"}[$id];

        // crea il nuovo esame
        $nuovo_esame = new stdClass();

        $nuovo_esame->{"COD"} = $COD;
        $nuovo_esame->{"DES"} = $DES;

        // aggiungi l'esame al corso
        $corso->{"esami-in-media"}[] = $nuovo_esame;
    }

    public function aggiornaCorso($nome_corso, $opzioni)
    {
        $this->modifica = true;

        // ottieni il corso
        $id = $this->ottieniIndicePerNome($nome_corso);
        $corso = &$this->configurazione->{"corsi-di-laurea"}[$id];

        // aggiorna configurazione calcolo
        $calcolo = &$corso->{"calcolo-voto"};

        $calcolo->{"formula-di-voto"} = $opzioni["formula-di-voto"];
        $calcolo->{"t-min"} = (int) $opzioni["t-min"];
        $calcolo->{"t-max"} = (int) $opzioni["t-max"];
        $calcolo->{"t-step"} = (int) $opzioni["t-step"];
        $calcolo->{"c-min"} = (int) $opzioni["c-min"];
        $calcolo->{"c-max"} = (int) $opzioni["c-max"];
        $calcolo->{"c-step"} = (int) $opzioni["c-step"];
        $calcolo->{"valore-lode"} = (int) $opzioni["valore-lode"];
        $calcolo->{"bonus"} = $opzioni["bonus"];
    }

    public function eliminaCorso($nome_corso)
    {
        $this->modifica = true;

        // elimina il corso
        $id = $this->ottieniIndicePerNome($nome_corso);
        unset($this->configurazione->{"corsi-di-laurea"}[$id]);
    }

    public function aggiungiCorso($nome_nuovo_corso)
    {
        $this->modifica = true;

        // se il nome è vuoto, non aggiungere
        if (empty($nome_nuovo_corso)) {
            return;
        }

        // crea il nuovo corso
        $nuovo_corso = $this->configurazione->{"corso-di-laurea-default"};
        $nuovo_corso->{"nome"} = $nome_nuovo_corso;

        // aggiungi il nuovo corso
        $this->configurazione->{"corsi-di-laurea"}[] = $nuovo_corso;
    }
}
