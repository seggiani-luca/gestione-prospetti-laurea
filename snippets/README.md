# Gestione prospetti laurea
Un'applicazione per la gestione dei prospetti di laurea scritta su WordPress.

Questa repo contiene l'implementazione dell'applicazione su PHP snippets.

## Dettagli
Usando PHP snippets come metodo di dislocazione, si ha che i file del progetto si trovano a partire dalla directory 
`app` di WordPress in `app/public/gestione-prospetti-laurea`.

L'aggancio attraverso PHP Snippets è fatto sullo script `bootstrap.php`, in particolare con la funzione `bootstrap()`.
Questo script, lanciato dopo l'header delle pagine accessibili all'utente, provvede a controllare la pagina desiderata
e fornirla. Per questo motivo si definisce una pagina WordPress, `gpl-admin`, per il menu di modifica della 
configurazione.

Di entrambe le pagine `user.php` e `admin.php` è definito solo il `body`: l'`head` viene fornito da WordPress e 
generato prima dell'esecuzione dello snippet. Per questo motivo si fa affidamento sugli stili già forniti, e in 
particolare sul tema `gestione-prospetti-laurea` fornito assieme al progetto.
