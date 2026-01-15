<?php
namespace GestioneProspettiLaurea\representation;

use DateTime;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Rappresenta un esame di uno studente, estratto da GestioneCarrieraStudente.
 */
class Esame
{
    // corso dell'esame
    public readonly string $corso;

    // codice dell'esame
    public readonly string $codice;

    // descrizione dell'esame (sostanzialmente il nome)
    public readonly string $descrizione;

    // cfu erogati dall'esame
    public readonly int $cfu;

    // voto conseguito al'esame
    public readonly int $voto;

    // data dell'esame (è DateTime per permettere l'ordinamento)
    public readonly DateTime $data;

    // segnala se l'esame è in media
    public bool $in_media;

    // segnala se l'esame è nella sottomedia
    public bool $in_sottomedia;

    /*
     * Costruisce un esame prendendone il corso, il codice e la descrizione (cioè il nome), i CFU erogati, il voto
     * conseguito e il flag di media. Il flag di sottomedia è influenzato solo dai filtri è vale true di default.
     */
    private function __construct($corso, $codice, $descrizione, $cfu, $voto, $data, $in_media)
    {
        $this->corso = $corso;
        $this->codice = $codice;
        $this->descrizione = $descrizione;
        $this->cfu = $cfu;
        $this->voto = $voto;
        $this->data = $data;
        $this->in_media = $in_media;
        $this->in_sottomedia = true;
    }

    /*
     * Fabbrica che ottiene un esame dai dati raw e la configurazione del corso.
     */
    public static function daRaw($esame_raw, $conf)
    {
        // ottieni voto, riportando l'eventuale lode in numero
        $voto = $esame_raw["VOTO"] ?? 0;
        if ($voto == "30  e lode") {
            $voto = $conf->{"calcolo-voto"}->{"valore-lode"};
        }

        // ottieni la data dell'esame
        $data_esame = DateTime::createFromFormat("d/m/Y", $esame_raw["DATA_ESAME"]);

        // ottieni se l'esame è in media: questo campo verrà aggiornato dai filtri, intanto lo basiamo sui flag
        $in_media = Esame::isInMedia($esame_raw);

        // crea l'esame
        $esame = new Esame(
            $esame_raw["CORSO"],
            $esame_raw["COD"],
            $esame_raw["DES"],
            $esame_raw["PESO"] ?? 0,
            $voto,
            $data_esame,
            $in_media,
        );

        return $esame;
    }

    /*
     * Helper che individua se un esame è stato passato o meno. Si adopera la disgiunzione di 2 condizioni sui valori
     * estratti da GestioneCarrieraStudente:
     * - La nullità del campo GIUDIZIO;
     * - La nullità del campo VOTO.
     */
    public static function isPassato($raw)
    {
        return !self::isNull($raw["GIUDIZIO"]) || !self::isNull($raw["VOTO"]);
    }

    /*
     * Helper che individua se un esame è in media o no. Si adopera la congiunzione di due valori estratti da
     * GestioneCarrieraStudente:
     * - La nullità o uguaglianza a zero del campo SOVRAN_FLG;
     * - La non nullità del campo VOTO.
     */
    private static function isInMedia($raw)
    {
        return (self::isNull($raw["SOVRAN_FLG"]) || $raw["SOVRAN_FLG"] == 0) && !self::isNull($raw["VOTO"]);
    }

    /*
     * Determina se un campo dei dati estratti da GestioneCarrieraStudenti è nullo valutando 3 condizioni:
     * - Se il campo è direttamente nullo;
     * - Se il campo è la stringa vuota;
     * - Se il campo è un oggetto contenente l'unica proprietà @nil = true (come si ottiene per campi inesistenti ma
     *   richiesti nel caso di conversione da altri formati come XML).
     */
    private static function isNull($value)
    {
        if ($value === null) {
            return true;
        }

        if (is_string($value) && trim($value) === "") {
            return true;
        }

        if (is_array($value) && isset($value["@nil"]) && $value["@nil"] === "true") {
            return true;
        }

        return false;
    }
}
