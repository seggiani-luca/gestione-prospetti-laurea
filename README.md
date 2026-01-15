# Gestione prospetti laurea
Un'applicazione per la gestione dei prospetti di laurea scritta su WordPress.

Si forniscono 2 implementazioni: una basata sui plugin WordPress, sviluppata a scopo didattico e fornita per 
completezza e curiosità, e una basata su PHP snippets, aderente alle modalità del progetto.

## Dettagli plugin
Questa versione è contenuta nell'archivio `plugin`.

Usando un plugin WordPress come metodo di dislocazione, si ha che i file del progetto si trovano a partire dalla 
directory `app` di WordPress in `app/public/wp-content/plugins/gestione-prospetti-laurea`.

L'aggancio attraverso è fatto attraverso gli hook predefiniti di WordPress, a partire dallo script 
`gestione-prospetti-laurea.php`. In questo modo il menu di modifica della configurazione è fornito direttamente 
nell'interfaccia amministratore di WordPress.

Della pagina `admin.php` è definito solo il `body`: questo perché verrà incluso all'interno di una gerarchia di 
elementi già esistente nell'interfaccia amministratore di WordPress. Per questa pagina si fa quindi affidamento sugli 
stili già forniti. Per la pagina `user.php` si definisce invece l'intero documento HTML, e si fornisce uno stile in 
`public/css/style.css`. Il tema `gestione-prospetti-laurea` fornito assieme al progetto stilizza effettivamente solo la
pagina di 404.

## Dettagli PHP snippets
Questa versione è contenuta nell'archivio `snippets`.

Usando PHP snippets come metodo di dislocazione, si ha che i file del progetto si trovano a partire dalla directory 
`app` di WordPress in `app/public/gestione-prospetti-laurea`.

L'aggancio attraverso PHP Snippets è fatto sullo script `bootstrap.php`, in particolare con la funzione `bootstrap()`.
Questo script, lanciato dopo l'header delle pagine accessibili all'utente, provvede a controllare la pagina desiderata
e fornirla. Per questo motivo si definisce una pagina WordPress, `gpl-admin`, per il menu di modifica della 
configurazione.

Di entrambe le pagine `user.php` e `admin.php` è definito solo il `body`: l'`head` viene fornito da WordPress e 
generato prima dell'esecuzione dello snippet. Per questo motivo si fa affidamento sugli stili già forniti, e in 
particolare sul tema `gestione-prospetti-laurea` fornito assieme al progetto.
