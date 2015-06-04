<?php

    function afficheIdMaxUserJoomla($bdJoomla,$prefixeJoomla){
        $requeteAfficheIdMaxUser = $bdJoomla->prepare('SELECT MAX(id) as idmax
                                                       FROM '.$prefixeJoomla.'users');
        $okAfficheIdMaxUser = $requeteAfficheIdMaxUser->execute() ;
        $idMaxUser = $requeteAfficheIdMaxUser->fetchAll(PDO::FETCH_OBJ)  ;
        return $idMaxUser;   
    }

    function afficheIdMaxCategJoomla($bdJoomla,$prefixeJoomla){
        $requeteAfficheIdMaxCateg = $bdJoomla->prepare('SELECT MAX(id) as idmax
                                                        FROM '.$prefixeJoomla.'categories');
        $okAfficheIdMaxCateg = $requeteAfficheIdMaxCateg->execute() ;
        $idMaxCateg = $requeteAfficheIdMaxCateg->fetchAll(PDO::FETCH_OBJ)  ;
        return $idMaxCateg;   
    }

    function afficheIdMaxArtJoomla($bdJoomla,$prefixeJoomla){
        $requeteAfficheIdMaxArt = $bdJoomla->prepare('SELECT MAX(id) as idmax
                                                        FROM '.$prefixeJoomla.'content');
        $okAfficheIdMaxArt = $requeteAfficheIdMaxArt->execute() ;
        $idMaxArt = $requeteAfficheIdMaxArt->fetchAll(PDO::FETCH_OBJ)  ;
        return $idMaxArt;   
    }



?>