<?php namespace GestioneProspettiLaurea\services;

use GestioneProspettiLaurea\representation\ProspettoCommissione;
use GestioneProspettiLaurea\representation\ProspettoStudente;
use GestioneProspettiLaurea\representation\Anagrafica;
use GestioneProspettiLaurea\representation\Esame;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Si occupa di assistere CreaProspetti a simulare i voti nei prospetti di laurea.
 */
class SimulaVoto
{
    /*
     * Il prospetto studente da cui vogliamo simulare i voti.
     */
    private $prospetto;

    /*
     * Configurazione del corso di laurea al quale i prospetti sono relativi.
     */
    private $conf;

    /*
     * Costruisce una classe per il calcolo del voto di un prospetto studente, con una certa configurazione.
     */
    public function __construct($prospetto, $conf)
    {
        $this->prospetto = $prospetto;
        $this->conf = $conf;
    }

    /*
     * Effettua la simulazione di voto, e restituisce una lista di coppie variabile iterata - voto. L'iteratore è il
     * valore assegnato al voto di tesi o al voto di commissione (rispettivamente se la variabile "itera-t-c" della
     * configurazione del corso è vera o falsa), mentre il voto è quello ottenuto applicando tale valore.
     */
    public function ottieniSimulazioneVoto()
    {
        // ottieni informazioni calcolo
        $calcolo_voto = $this->conf->{"calcolo-voto"};

        // ottieni configurazione
        $t_c = $calcolo_voto->{"itera-t-c"};

        // inizializza loop
        $iter = $t_c ? $calcolo_voto->{"t-min"} : $calcolo_voto->{"c-min"};
        $max_iter = $t_c ? $calcolo_voto->{"t-max"} : $calcolo_voto->{"c-max"};
        $iter_step = $t_c ? $calcolo_voto->{"t-step"} : $calcolo_voto->{"c-step"};

        // se il passo è 0 c'è stato un errore, fai un solo passo
        if ($iter_step == 0) {
            $iter_step = $max_iter;
        }

        $sim = [];

        // itera la variabile
        while ($iter <= $max_iter) {
            $sim_step = new \stdClass();
            $sim_step->iter = $iter;

            // ottieni valori
            $cfu = $this->prospetto->ottieniCFU();
            $m = $this->prospetto->ottieniMediaPesata();
            if ($t_c) {
                $t = $iter;
                $c = 0;
            } else {
                $t = 0;
                $c = $iter;
            }

            // calcola voto simulato
            $sim_step->voto = $this->calcolaVoto($cfu, $m, $c, $t);

            $sim[] = $sim_step;
            $iter += $iter_step;
        }

        return $sim;
    }

    /*
     * Helper che simula il voto sostituendo le variabili nella formula definita nella configurazione del corso, e
     * valutandola:
     * - "CFU" => $cfu: i crediti curricolari conseguiti;
     * - "M" => $m: la media pesata;
     * - "C" => $c: il voto di commissione;
     * - "T" => $t: il voto di tesi.
     */
    public function calcolaVoto($cfu, $m, $c, $t)
    {
        // ottieni la formula
        $formula = $this->conf->{"calcolo-voto"}->{"formula-di-voto"};

        // sostituisci le variabili
        $vars = ["CFU" => $cfu, "M" => $m, "C" => $c, "T" => $t];

        foreach ($vars as $key => $value) {
            $formula = str_replace($key, $value, $formula);
        }

        // valuta e restituisci
        return eval("return $formula;");
    }
}
