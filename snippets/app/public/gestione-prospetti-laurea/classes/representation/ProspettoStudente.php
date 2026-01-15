<?php
namespace GestioneProspettiLaurea\representation;

use DateTime;
use stdClass;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Rappresenta un prospetto studente, inteso come l'anagrafica di uno studente e la sua carriera, e informazioni
 * sull'appello di laurea.
 */
class ProspettoStudente
{
    // anagrafica dello studente
    public readonly Anagrafica $anagrafica;

    // carriera dello studente
    public readonly array $carriera;

    // segnala se lo studente ha il bonus
    public readonly bool $bonus;

    // simulazione del voto dello studente, inizializzata dopo la costruzione
    public ?array $simulazione = null;

    /*
     * Costruisce un prospetto studente prendendone l'anagrafica, la carriera, e il flag di bonus.
     */
    private function __construct($anagrafica, $carriera, $bonus)
    {
        $this->anagrafica = $anagrafica;
        $this->carriera = $carriera;
        $this->bonus = $bonus;
    }

    /*
     * Fabbrica che ottiene il prospetto dello studente con data matricola relativo all'appello di laurea rappresentato
     * da corso e data, forniti i dati raw ottenuti da GestioneCarrieraStudente.
     */
    public static function daRaw($matricola, $anagrafica_raw, $carriera_raw, $corso, $data, $conf)
    {
        // ripulisci dati raw
        $anagrafica_raw = $anagrafica_raw["Entries"]["Entry"];
        $carriera_raw = $carriera_raw["Esami"];

        // ottieni anagrafica
        $anagrafica = Anagrafica::daRaw($matricola, $anagrafica_raw, $carriera_raw);

        // controlla se il corso dello studente combacia con quello dell'appello
        if (!str_contains($anagrafica->corso, $corso)) {
            throw new \Exception("Prospetti non creati: matricola $matricola non nel corso selezionato");
        }

        // valuta se ha bonus
        $bonus = self::valutaBonus($anagrafica, $data, $conf);

        // ottieni carriera
        $carriera = Carriera::daRaw($carriera_raw, $anagrafica, $bonus, $conf);

        // crea il prospetto studente
        $prospetto_studente = new ProspettoStudente($anagrafica, $carriera, $bonus);

        return $prospetto_studente;
    }

    /*
     * Helper che valuta se il bonus va applicato sulla base della configurazione del corso e dell'anno di
     * immatricolazione dello studente.
     */
    private static function valutaBonus($anagrafica, $data, $conf)
    {
        // se il corso non prevede bonus sicuramente non c'è
        if (!$conf->{"calcolo-voto"}->{"bonus"}) {
            return false;
        }

        // usa l'anno di immatricolazione per valutare se siamo entro aprile del quarto anno
        $fine_bonus = DateTime::createFromFormat("Y-m-d", $anagrafica->anno_immatricolazione + 4 . "-05-01");
        return DateTime::createFromFormat("Y-m-d", $data) < $fine_bonus;
    }

    /*
     * Helper che restituisce la lista dei soli esami in media. In particolare:
     * - Se quale è false, ottiene gli esami in media;
     * - Se quale è true, ottiene gli esami in sottomedia.
     */
    private function ottieniEsamiMedia($quale = false)
    {
        // rimuovi gli elementi filtrati
        $carriera_med = array_values(
            array_filter($this->carriera, function ($esame) use ($quale) {
                return $quale ? $esame->in_sottomedia : $esame->in_media;
            }),
        );

        return $carriera_med;
    }

    /*
     * Ottiene la media pesata degli esami dello studente. In particolare:
     * - Se quale è false, usa gli esami in media;
     * - Se quale è true, usa gli esami in sottomedia.
     */
    public function ottieniMediaPesata($quale = false)
    {
        $num = 0;
        $den = 0;

        // conta
        foreach ($this->ottieniEsamiMedia($quale) as $esame) {
            $num += $esame->cfu * $esame->voto;
            $den += $esame->cfu;
        }

        // dividi e restituisci
        return $den == 0 ? 0 : $num / $den;
    }

    /*
     * Ottiene i CFU degli esami dello studente. In particolare:
     * - Se tutti è false, ottiene i CFU degli esami in media dello studente;
     * - Se tutti è true, ottiene tutti i CFU curricolari degli esami dello studente.
     */
    public function ottieniCFU($tutti = false)
    {
        $cfu = 0;

        // conta
        foreach ($tutti ? $this->carriera : $this->ottieniEsamiMedia() as $esame) {
            $cfu += $esame->cfu;
        }

        return $cfu;
    }
}
