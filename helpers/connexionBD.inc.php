<?php

    function connexionBD($hote,$nom_BD,$utilisateur,$password)
    {
        try
        {     // Try: on essaie de se connecter à la BD
            $maBD = new PDO('mysql:host='.$hote.';dbname='.$nom_BD, $utilisateur,$password);
            $requete = $maBD->prepare('SET NAMES UTF8') ;
            $resultat = $requete->execute() ;
            return $maBD ;
        }
        catch(Exception $e)
        {     // Catch : sera exécuté si un problème survient (une exception) lors de la tentative définie dans try
            // Des info concernant le problème se trouvent dans la variable $e
            $messageErreur = '<p class="erreur">Echec de la connexion à la base de données';
            $messageErreur .= '<br />Erreur : '.$e->getMessage().'<br />';
            $messageErreur .= 'N° : '.$e->getCode().'</p>';
            //  throw new Exception($messageErreur) ;
            return false;
        }
  
    }

?>