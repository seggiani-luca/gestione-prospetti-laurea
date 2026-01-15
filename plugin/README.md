# Gestione prospetti laurea
Un'applicazione per la gestione dei prospetti di laurea scritta su WordPress.

Questa repo contiene l'implementazione dell'applicazione come plugin.

## Dettagli
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

