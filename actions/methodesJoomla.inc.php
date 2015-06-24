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

    function stringURLSafe($string)
    {
        //remove any '-' from the string they will be used as concatonater
        $str = str_replace('-', ' ', $string);
 
        //enlever accents
        $str = iconv('UTF-8', 'ASCII//TRANSLIT', $str);
 
        // convert certain symbols to letter representation
        $str = str_replace(array('&', '"', '<', '>'), array('a', 'q', 'l', 'g'), $str);
 
        // remove any duplicate whitespace, and ensure all characters are alphanumeric
        $str = preg_replace(array('/\s+/','/[^A-Za-z0-9\-]/'), array('-',''), $str);
 
        // lowercase and trim
        $str = trim(strtolower($str));
        
        $str = str_replace(array('sup','lg'),array('',''), $str);
     
        return $str;
    }



?>