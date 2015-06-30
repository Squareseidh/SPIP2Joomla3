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

    function stringURLSafe($string){
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

    function insertAuteurs($bdJoomla,$prefixeJoomla,$id,$name,$username,$email,$lastvisitDate){
        $password='d2064d358136996bd22421584a7cb33e:trd7TvKHx6dMeoMmBVxYmg0vuXEA4199'; //en clair signifie "secret"
        $block=0;
        $sendEmail=0;
        $registerDate='0000-00-00 00:00:00';
        $activation='';
        $params='{}';
        $lastResetTime='0000-00-00 00:00:00';
        $resetCount=0;
        $otpKey='';
        $otep='';
        $requireReset=1;
        
        $requeteAuteurs = $bdJoomla->prepare('INSERT INTO '.$prefixeJoomla.'users (id, name, username, email, password, block, sendEmail, registerDate, lastvisitDate, activation, params, lastResetTime, resetCount, otpKey, otep, requireReset) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)') ;
     
        // Exécution de la requête
        $okAuteurs = $requeteAuteurs->execute(array($id,$name,$username,$email,$password,$block,$sendEmail,$registerDate,$lastvisitDate,$activation,$params,$lastResetTime,$resetCount,$otpKey,$otep,$requireReset));
    }

    function insertRubriques($bdJoomla,$prefixeJoomla,$id,$parent_id,$path,$title,$alias,$description,$created_user_id,$created_time,$modified_time){
        $asset_id=0;
        $lft=0;
        $rgt=0;
        $level=0;
        $extension='com_content';
        $note='';
        $published=1;
        $checked_out=0;
        $checked_out_time='0000-00-00 00:00:00';
        $access=1;
        $params='{"category_layout":"","image":"","image_alt":""}';
        $metadesc='';
        $metakey='';
        $metadata='{"author":"","robots":""}';
        $hits=0;
        $language="*";
        $version=1;
        $modified_user_id=$created_user_id;
        
        $requeteRubriques = $bdJoomla->prepare('INSERT INTO '.$prefixeJoomla.'categories (id,asset_id,parent_id,lft,rgt,level,path,extension,title,alias,note,description,published,checked_out,checked_out_time,access,params,metadesc,metakey,metadata,created_user_id,created_time,modified_user_id,modified_time,hits,language,version) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)') ;
     
        // Exécution de la requête
        $okRubriques = $requeteRubriques->execute(array($id,$asset_id,$parent_id,$lft,$rgt,$level,$path,$extension,$title,$alias,$note,$description,$published,$checked_out,$checked_out_time,$access,$params,$metadesc,$metakey,$metadata,$created_user_id,$created_time,$modified_user_id,$modified_time,$hits,$language,$version));
        
    }

    function insertArticles($bdJoomla,$prefixeJoomla,$id,$title,$alias,$introtext,$catid,$created,$created_by,$hits,$modified){
        $asset_id=0;
        $fulltext='';
        $state=1;
        $modified=$created;
        $modified_by=0;
        $checked_out=0;
        $publish_up=$created;
        $publish_down='0000-00-00 00:00:00';
        $checked_out_time='0000-00-00 00:00:00';
        $images='{}';
        $urls='{}';
        $attribs='{}';
        $version=1;
        $ordering=0;
        $metakey='';
        $metadesc='';
        $access=1;
        $metadata='{"robots":"","author":"","rights":"","xreference":""}';
        $featured=0;
        $language='*';
        $xreference='';
        $created_by_alias='';
        
        $requeteArticles = $bdJoomla->prepare('INSERT INTO '.$prefixeJoomla.'content (`id`,`asset_id`,`title`,`alias`,`introtext`,`fulltext`,`state`,`catid`,`created`,`created_by`,`created_by_alias`,`modified`,`modified_by`,`checked_out`,`checked_out_time`,`publish_up`,`publish_down`,`images`,`urls`,`attribs`,`version`,`ordering`,`metakey`,`metadesc`,`access`,`hits`,`metadata`,`featured`,`language`,`xreference`) VALUES (?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?,?)') ;
        
     
        // Exécution de la requête
        $okArticles = $requeteArticles->execute(array($id,$asset_id,$title,$alias,$introtext,$fulltext,$state,$catid,$created,$created_by,$created_by_alias,$modified,$modified_by,$checked_out,$checked_out_time,$publish_up,$publish_down,$images,$urls,$attribs,$version,$ordering,$metakey,$metadesc,$access,$hits,$metadata,$featured,$language,$xreference));
        
        
    }



?>