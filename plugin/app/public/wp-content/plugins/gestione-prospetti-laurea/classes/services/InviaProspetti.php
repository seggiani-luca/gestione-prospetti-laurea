<?php
namespace GestioneProspettiLaurea\services;

use GestioneProspettiLaurea\repository\Prospetti;
use GestioneProspettiLaurea\repository\Configurazione;

use PHPMailer\PHPMailer\PHPMailer;
use PHPMailer\PHPMailer\Exception;

if (!defined("ABSPATH")) {
    exit();
}
/*
 * Implementa il caso d'uso InviaProspetti, e quindi si occupa di gestire l'invio dei prospetti creati agli studenti
 * attraverso PHPMailer.
 */
class InviaProspetti
{
    // delay fra le email
    private const MAIL_DELAY = 2;

    // classe di configurazione corrente
    private $configurazione;

    /*
     * Costruttore, inizializza la classe di configurazione corrente.
     */
    public function __construct()
    {
        $this->configurazione = Configurazione::ottieniConfigurazione();
    }

    /*
     * Invia un prospetto ad uno studente, prendendo la configurazione del corso, la mail dello studente e il percorso
     * del prospetto studente.
     */
    public function inviaProspetto($conf_corso, $indirizzo, $path)
    {
        // inizializza il mailer
        $mail = new PHPMailer(true);
        $mail->isSMTP();
        $mail->Host = "mixer.unipi.it";
        $mail->Port = 25;
        $mail->SMTPSecure = "tls";
        $mail->SMTPAuth = false;
        $mail->CharSet = "UTF-8";
        $mail->setFrom("no-reply-laureandosi@ing.unipi.it");

        // inserici l'indirizzo e-mail dello studente
        $mail->AddAddress($indirizzo);

        // allega il prospetto
        $mail->AddAttachment($path, "prospetto.pdf");

        // prendi oggetto e corpo dalla configurazione
        $mail->Subject = $conf_corso->{"mail"}->{"oggetto"};
        $mail->Body = $conf_corso->{"mail"}->{"corpo"};

        // invia la e-mail
        $mail->send();
    }

    /*
     * Invia tutti i prospetti ancora da inviare relativi ad un appello di laurea, individuato da corso di laurea
     * dell'appello. I prospetti ancora da inviare sono ottenuti controllando la tabella laureandi, consumandone una
     * riga per volta. Restituisce il numero di email inviate.
     */
    public function inviaProspetti($corso)
    {
        if (empty($corso)) {
            throw new \Exception("Fornite opzioni invalide per invio prospetti");
        }

        // ottieni la configurazione del corso
        $conf_corso = Configurazione::ottieniConfigurazioneCorso($this->configurazione, $corso);

        // ottieni percorso tabella  laureandi
        $tab_laureandi = Prospetti::percorsoTabellaLaureandi($corso);

        if (!file_exists($tab_laureandi)) {
            throw new \Exception("Nessuna mail da inviare");
        }

        $count = 0;
        while (true) {
            // leggi tabella laureandi
            $laureandi = file($tab_laureandi, FILE_IGNORE_NEW_LINES);
            if (empty($laureandi)) {
                unlink($tab_laureandi);
                break;
            }

            // prendi il primo laureando
            $laureando = array_shift($laureandi);

            // ottieni i dati del laureando
            $dati = preg_split("/\s+/", $laureando);
            $matricola = $dati[0];
            $mail = $dati[1];
            $path = $dati[2];

            // elimina il laureando processato
            if (!empty($laureandi)) {
                file_put_contents($tab_laureandi, implode("\n", $laureandi) . "\n");
            } else {
                file_put_contents($tab_laureandi, "");
            }

            $count += 1;

            // aspetta
            sleep(self::MAIL_DELAY);
        }

        return $count;
    }
}
