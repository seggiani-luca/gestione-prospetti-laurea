<?php namespace GestioneProspettiLaurea\services;

use GestioneProspettiLaurea\representation\ProspettoCommissione;
use GestioneProspettiLaurea\representation\ProspettoStudente;
use GestioneProspettiLaurea\representation\Anagrafica;
use GestioneProspettiLaurea\representation\Esame;
use FPDF;

if (!defined("ABSPATH")) {
    exit();
}

/*
 * Si occupa di assistere CreaProspetti a disegnare i prospetti di laurea, interagendo con la libreria FPDF.
 */
class DisegnaProspetti
{
    // dimensione del font nel prospetto
    private static $font_size = 10;

    // altezza linea nel prospetto
    private static $h_linee = 5;

    // larghezza delle tabelle nel prospetto
    private static $w_casella = 190;

    // $indentazione nel prospetto
    private static $indent = 95;

    /*
     * Il prospetto commissione da cui vogliamo generare i pdf.
     */
    private $prospetto;

    /*
     * Configurazione del corso di laurea al quale i prospetti sono relativi.
     */
    private $conf;

    /*
     * Costruisce una classe per il disegno di un prospetto commissione, con una certa configurazione.
     */
    public function __construct($prospetto, $conf)
    {
        $this->prospetto = $prospetto;
        $this->conf = $conf;
    }

    /*
     * Genera il prospetto commissione, cioè crea un pdf e vi disegna il frontespizio e i prospetti laureando con
     * simulazione di voto per ogni studente.
     */
    public function generaProspettoCommissione($path)
    {
        $pdf = new FPDF();

        $this->disegnaFrontespizio($pdf);

        // disegna i prospetti studente
        foreach ($this->prospetto->prospetti_studente as $prospetto_studente) {
            $this->disegnaProspettoStudente($pdf, $prospetto_studente);
            $this->disegnaSimulazioneVoto($pdf, $prospetto_studente->simulazione);
        }

        $pdf->output("F", $path);
    }

    public function generaProspettiStudente(callable $ottieni_percorso)
    {
        foreach ($this->prospetto->prospetti_studente as $prospetto_studente) {
            // ottieni percorso
            $path = $ottieni_percorso($prospetto_studente->anagrafica->matricola);

            // genera prospetto studente
            $pdf = new FPDF();

            $this->disegnaProspettoStudente($pdf, $prospetto_studente);

            $pdf->output("F", $path);
        }
    }

    /*
     * Disegna il frontespizio del prospetto commissione.
     */
    private function disegnaFrontespizio($pdf)
    {
        // ottieni dati
        $studenti = $this->prospetto->ottieniStudenti();

        // imposta una pagina FPDF per la renderizzazione
        $pdf->AddPage();
        $pdf->SetDrawColor(0, 0, 0);

        // --- titolo ---
        $pdf->SetFont("Arial", "B", self::$font_size);

        $pdf->Cell(0, self::$h_linee, $this->prospetto->corso, 0, 1, "C");
        $pdf->Cell(
            0,
            self::$h_linee,
            utf8_decode("LAUREANDOSI 2 - 2025 Luca Seggiani @ Università di Pisa"),
            0,
            1,
            "C",
        );

        $pdf->Ln(2);

        // --- tabella studenti ---
        $pdf->Cell(self::$w_casella, self::$h_linee, "LISTA LAUREANDI", 1);
        $pdf->Ln();

        $w = self::$w_casella / 4;

        $pdf->Cell($w, self::$h_linee, "COGNOME", 1);
        $pdf->Cell($w, self::$h_linee, "NOME", 1);
        $pdf->Cell($w, self::$h_linee, "CDL", 1);
        $pdf->Cell($w, self::$h_linee, "VOTO DI LAUREA", 1);
        $pdf->Ln();

        $pdf->SetFont("Arial", "", self::$font_size);

        foreach ($studenti as $studente) {
            $pdf->Cell($w, self::$h_linee, $studente->cognome, 1);
            $pdf->Cell($w, self::$h_linee, $studente->nome, 1);
            $pdf->Cell($w, self::$h_linee, "", 1);
            $pdf->Cell($w, self::$h_linee, "        /110", 1);
            $pdf->Ln();
        }

        $pdf->Ln(2);
    }

    /*
     * Disegna il prospetto relativo ad uno studente.
     */
    private function disegnaProspettoStudente($pdf, $prospetto)
    {
        // ottieni dati
        $media = round($prospetto->ottieniMediaPesata(), 3);
        $sottomedia = round($prospetto->ottieniMediaPesata(true), 3);

        $cfu_media = $prospetto->ottieniCFU();
        $cfu_curricolari = $prospetto->ottieniCFU(true);

        $carriera_filt = $prospetto->carriera;

        // imposta una pagina FPDF per la renderizzazione
        $pdf->AddPage();
        $pdf->SetDrawColor(0, 0, 0);

        // --- titolo ---
        $pdf->SetFont("Arial", "B", self::$font_size);

        $pdf->Cell(0, self::$h_linee, $this->prospetto->corso, 0, 1, "C");
        $pdf->Cell(0, self::$h_linee, "CARRIERA E SIMULAZIONE DEL VOTO DI LAUREA", 0, 1, "C");

        $pdf->Ln(2);

        // --- casella superiore ---
        $pdf->SetFont("Arial", "", self::$font_size);

        $num_linee = 5 + ($this->conf->{"calcolo-voto"}->{"bonus"} ? 1 : 0);
        $h_casella_sup = $num_linee * self::$h_linee;

        $y = $pdf->GetY();
        $pdf->Rect(10, $y, self::$w_casella, $h_casella_sup);

        $pdf->SetXY(10, $y);

        $pdf->Cell(self::$indent, self::$h_linee, "Matricola:", 0, 0);
        $pdf->Cell(0, self::$h_linee, $prospetto->anagrafica->matricola, 0, 1);

        $pdf->Cell(self::$indent, self::$h_linee, "Nome:", 0, 0);
        $pdf->Cell(0, self::$h_linee, $prospetto->anagrafica->nome, 0, 1);

        $pdf->Cell(self::$indent, self::$h_linee, "Cognome:", 0, 0);
        $pdf->Cell(0, self::$h_linee, $prospetto->anagrafica->cognome, 0, 1);

        $pdf->Cell(self::$indent, self::$h_linee, "E-mail:", 0, 0);
        $pdf->Cell(0, self::$h_linee, $prospetto->anagrafica->mail, 0, 1);

        $pdf->Cell(self::$indent, self::$h_linee, "Data:", 0, 0);
        $pdf->Cell(0, self::$h_linee, $this->prospetto->data, 0, 1);

        if ($this->conf->{"calcolo-voto"}->{"bonus"}) {
            $pdf->Cell(self::$indent, self::$h_linee, "Bonus:", 0, 0);
            $pdf->Cell(0, self::$h_linee, $prospetto->bonus ? "Si" : "No", 0, 1);
        }

        $pdf->Ln(2);

        // --- tabella esami ---
        $pdf->SetFont("Arial", "B", self::$font_size);

        $pdf->Cell(self::$w_casella, self::$h_linee, "CARRIERA", 1);
        $pdf->Ln();

        $w0 = self::$w_casella * (2 / 3);

        $num_colonne = 3 + ($this->conf->{"calcolo-voto"}->{"calcola-sottomedia"} ? 1 : 0);
        $w1 = (self::$w_casella * (1 / 3)) / $num_colonne;

        $pdf->Cell($w0, self::$h_linee, "ESAME", 1);
        $pdf->Cell($w1, self::$h_linee, "CFU", 1);
        $pdf->Cell($w1, self::$h_linee, "VOTO", 1);
        $pdf->Cell($w1, self::$h_linee, "MEDIA", 1);
        if ($this->conf->{"calcolo-voto"}->{"calcola-sottomedia"}) {
            $pdf->Cell($w1, self::$h_linee, "INF", 1);
        }
        $pdf->Ln();

        $pdf->SetFont("Arial", "", self::$font_size);

        foreach ($carriera_filt as $esame) {
            $pdf->Cell($w0, self::$h_linee, $esame->descrizione, 1);
            $pdf->Cell($w1, self::$h_linee, $esame->cfu, 1);
            $pdf->Cell($w1, self::$h_linee, $esame->voto, 1);
            $pdf->Cell($w1, self::$h_linee, $esame->in_media ? "X" : "", 1);
            if ($this->conf->{"calcolo-voto"}->{"calcola-sottomedia"}) {
                $pdf->Cell($w1, self::$h_linee, $esame->in_sottomedia ? "X" : "", 1);
            }
            $pdf->Ln();
        }

        $pdf->Ln(2);

        // --- casella inferiore ---
        $num_linee = 4 + ($this->conf->{"calcolo-voto"}->{"calcola-sottomedia"} ? 1 : 0);
        $h_casella_inf = $num_linee * self::$h_linee;

        $y = $pdf->GetY();
        $pdf->Rect(10, $y, self::$w_casella, $h_casella_inf);

        $pdf->SetXY(10, $y);

        $pdf->Cell(self::$indent, self::$h_linee, "Media pesata (M):", 0, 0);
        $pdf->Cell(0, self::$h_linee, $media, 0, 1);

        $pdf->Cell(self::$indent, self::$h_linee, "Crediti che fanno media (CFU):", 0, 0);
        $pdf->Cell(0, self::$h_linee, $cfu_media, 0, 1);

        $pdf->Cell(self::$indent, self::$h_linee, "Crediti curricolari conseguiti:", 0, 0);
        $pdf->Cell(0, self::$h_linee, $cfu_curricolari . "/" . $this->conf->{"calcolo-voto"}->{"cfu-richiesti"}, 0, 1);

        $pdf->Cell(self::$indent, self::$h_linee, "Formula calcolo voto di laurea:", 0, 0);
        $pdf->Cell(0, self::$h_linee, $this->conf->{"calcolo-voto"}->{"formula-di-voto"}, 0, 1);

        if ($this->conf->{"calcolo-voto"}->{"calcola-sottomedia"}) {
            $pdf->Cell(self::$indent, self::$h_linee, "Media pesata esami INF:", 0, 0);
            $pdf->Cell(0, self::$h_linee, $sottomedia, 0, 1);
        }

        $pdf->Ln(2);
    }

    /*
     * Disegna la simulazione di voto di uno studente.
     */
    private function disegnaSimulazioneVoto($pdf, $simulazione)
    {
        // --- tabella simulazione ---
        $pdf->SetFont("Arial", "B", self::$font_size);

        $pdf->Cell(self::$w_casella, self::$h_linee, "SIMULAZIONE DI VOTO DI LAUREA", 1);
        $pdf->Ln();

        $pdf->Cell(
            self::$w_casella / 2,
            self::$h_linee,
            $this->conf->{"calcolo-voto"}->{"itera-t-c"} ? "VOTO TESI (T)" : "VOTO COMMISSIONE (C)",
            1,
        );
        $pdf->Cell(0, self::$h_linee, "VOTO LAUREA", 1);
        $pdf->Ln();

        $pdf->SetFont("Arial", "", self::$font_size);

        foreach ($simulazione as $sim) {
            $pdf->Cell(self::$w_casella / 2, self::$h_linee, $sim->iter, 1);

            $sim_voto = round($sim->voto, 3);
            $pdf->Cell(0, self::$h_linee, $sim_voto, 1);
            $pdf->Ln();
        }

        $pdf->Ln(2);

        // --- informazioni sul voto finale ---
        $pdf->MultiCell(
            self::$w_casella,
            self::$h_linee,
            "VOTO DI LAUREA FINALE: " . $this->conf->{"calcolo-voto"}->{"info-voto-finale"},
            0,
            "L",
        );
    }
}
