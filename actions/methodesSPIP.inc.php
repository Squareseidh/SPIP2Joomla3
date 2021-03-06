<?php
    include 'helpers/filtres.php';
    include 'helpers/texte.php';


    function afficheAuteursSPIP($bdSPIP,$prefixeSPIP){
         $requeteAfficheAut = $bdSPIP->prepare('SELECT id_auteur, nom, email, login, en_ligne
                                                FROM '.$prefixeSPIP.'auteurs
                                                WHERE statut != "5poubelle"');
        $okAfficheAut = $requeteAfficheAut->execute();
        $auteurs = $requeteAfficheAut->fetchAll(PDO::FETCH_OBJ);
        return $auteurs;
    }

    function afficheRubriquesSPIP($bdSPIP,$prefixeSPIP){
        $requeteAfficheRub = $bdSPIP->prepare('SELECT id_rubrique, id_parent, titre, descriptif, texte, date, maj
                                               FROM '.$prefixeSPIP.'rubriques
                                               WHERE statut="publie"');
        $okAfficheRub = $requeteAfficheRub->execute() ;
        $rubriques = $requeteAfficheRub->fetchAll(PDO::FETCH_OBJ);
        return $rubriques;
    }

    function afficheArticlesSPIP($bdSPIP,$prefixeSPIP){
        $requeteAfficheArt = $bdSPIP->prepare('SELECT id_article, titre, id_rubrique, texte, date, visites, date_modif
                                               FROM '.$prefixeSPIP.'articles
                                               WHERE statut = "publie"');
        $okAfficheArt = $requeteAfficheArt->execute() ;
        $articles = $requeteAfficheArt->fetchAll(PDO::FETCH_OBJ);
        return $articles;
    }

    function afficheDocumentById($bdSPIP,$prefixeSPIP,$id_document){
        $requeteAfficheDocumentById = $bdSPIP->prepare('SELECT d.fichier, d.extension
                                                        FROM '.$prefixeSPIP.'documents d
                                                        WHERE d.id_document = '.$id_document);
        $okAfficheDocumentById = $requeteAfficheDocumentById->execute();
        $document = $requeteAfficheDocumentById->fetchAll(PDO::FETCH_OBJ);
        return $document;
    }

    function formatLinktoJoomla($letexte,$elementartmax,$elementrubmax, $bdSPIP,$prefixeSPIP,$dossierJoomla){
        if (preg_match_all(',\[([^][]*)->(>?)([^]]*)\],msS', $letexte, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $regs) {
                if (preg_match_all('/(rub|art|doc)([0-9]+)/', $regs[3], $matches, PREG_SET_ORDER)) {
                    foreach ($matches as $regsregs) {
                        if($regsregs[1]== "art"){
                            $idmaxarticle=intval($elementartmax[0]->idmax);
                            $idarticle=intval($regsregs[2]);
                            $nouvelidarticle=$idmaxarticle+$idarticle;
                            
                            $requeteRubassoc = $bdSPIP->prepare('SELECT id_rubrique
                                                                FROM '.$prefixeSPIP.'articles
                                                                WHERE id_article ='.$regsregs[2]);
                            $okRubassoc = $requeteRubassoc->execute() ;
                            $rubassoc = $requeteRubassoc->fetchAll(PDO::FETCH_OBJ);
                            
                            $rubrique=intval($rubassoc[0]->id_rubrique)+intval($elementrubmax[0]->idmax);
                        
                            $baliselien='<a href="index.php?option=com_content&amp;view=article&amp;id='.$nouvelidarticle.'&amp;catid='.$rubrique.'&amp;Itemid=102">'.$regs[1].' A MODIFIER</a>'; //MODIFIER ITEM ID
                        } elseif ($regsregs[1]== "rub"){
                            $baliselien='<a href='.$regs[3].'>'.$regs[1].'A MODIFIER</a>';
                        } else {
                            /* SI C EST UN DOCUMENT*/
                            $requeteAfficheDocumentById = $bdSPIP->prepare('SELECT d.fichier
                                                        FROM '.$prefixeSPIP.'documents d
                                                        WHERE d.id_document = '.$regsregs[2]);
                            $okAfficheDocumentById = $requeteAfficheDocumentById->execute();
                            $path = $requeteAfficheDocumentById->fetchAll(PDO::FETCH_OBJ);
                            $baliselien='<a target="_blank" href=/'.$dossierJoomla.'/images/'.$path[0]->fichier.'>'.$regs[1].'</a><br>';
                        }
                    }
                } else {
                    if (preg_match_all('/^(#|mailto).*/', $regs[3], $matchesAnchor, PREG_SET_ORDER)) {
                        foreach ($matchesAnchor as $regsAnchor) {
                            $baliselien='<a href='.$regsAnchor[0].'>'.$regs[1].'</a>';
                        }
                            
                    } else if (preg_match_all('/^(http).*/', $regs[3], $matchesAnchor, PREG_SET_ORDER))  {
                        foreach ($matchesAnchor as $regsAnchor) {
                            $baliselien='<a target="_blank" href='.$regsAnchor[0].'>'.$regs[1].'</a>';
                        }
                    } else {
                        $baliselien='<a target="_blank" href=../'.$regs[3].'>'.$regs[1].' A MODIFIER</a>';
                    }
                }
                $letexte = str_replace($regs[0], $baliselien, $letexte);
            }
        }
        return $letexte;
    }

    function formatSPIPtoJoomla($letexte){
        
        
        $debut_intertitre = "\n<h3 class=\"spip\">\n";
	    $fin_intertitre = "</h3>\n";
        
        $letexte = preg_replace(",\r\n?,S", "\n", $letexte);

        // Recuperer les para HTML
        $letexte = preg_replace(",<p[>[:space:]],iS", "\n\n\\0", $letexte);
        $letexte = preg_replace(",</p[>[:space:]],iS", "\\0\n\n", $letexte);
        
        // les listes
	   $letexte=preg_replace("/-/","\n-", $letexte);
		
        
        //
        // Ensemble de remplacements implementant le systeme de mise
        // en forme (paragraphes, raccourcis...)
        //

        $letexte = "\n".trim($letexte);
        
        
        //
        // Raccourcis ancre [ancre<-]
        //
        $regexp = "|\[?([^][]*)<\n-\]|S";
        if (preg_match_all($regexp, $letexte, $matches, PREG_SET_ORDER)){
            foreach ($matches as $regs){
                $letexte = str_replace($regs[0],'<a id="'.$regs[1].'"></a>', $letexte);
            }
        }
            
        
        
        //
	   // Tableaux
	   //

	   // ne pas oublier les tableaux au debut ou a la fin du texte
	   $letexte = preg_replace(",^\n?[|],S", "\n\n|", $letexte);
	   $letexte = preg_replace(",\n\n+[|],S", "\n\n\n\n|", $letexte);
	   $letexte = preg_replace(",[|](\n\n+|\n?$),S", "|\n\n\n\n", $letexte);

	   // traiter chaque tableau
	   if (preg_match_all(',[^|](\n[|].*[|]\n)[^|],UmsS', $letexte,
	   $regs, PREG_SET_ORDER))
	   foreach ($regs as $tab) {
		  $letexte = str_replace($tab[1], traiter_tableau($tab[1]), $letexte);
	   }
        
        // autres raccourcis
        $cherche1 = array(
		/* 1 */ 	"/\n-- */S",
		/* 3 */ 	"/\n_ +/S",
		/* 4 */   "/(^|[^{])[{][{][{]/S",
		/* 5 */   "/[}][}][}]($|[^}])/S",
		/* 6 */ 	"/(( *)\n){2,}(<br\s*\/?".">)?/S",
		/* 7 */ 	"/[{][{]/S",
		/* 8 */ 	"/[}][}]/S",
		/* 9 */ 	"/[{]/S",
		/* 10 */	"/[}]/S",
		/* 11 */	"/(?:<br\s*\/?".">){2,}/S",
		/* 12 */	"/<p>\n*(?:<br\s*\/?".">\n*)*/S",
		/* 13 */	"/<quote>/S",
		/* 14 */	"/<\/quote>/S",
		/* 15 */	"/<\/?intro>/S",
                    "/-[\*]+/",
                    "/-[\#]+/",
                    "/\~\~/"
        );
        $remplace1 = array(
		/* 1 */ 	"\n<br />&mdash;&nbsp;",
		/* 3 */ 	"\n<br />",
		/* 4 */ 	"\$1\n\n$debut_intertitre",
		/* 5 */ 	"$fin_intertitre\n\n\$1",
		/* 6 */ 	"<p>",
		/* 7 */ 	"<strong class=\"spip\">",
		/* 8 */ 	"</strong>",
		/* 9 */ 	"<i class=\"spip\">",
		/* 10 */	"</i>",
		/* 11 */	"<p>",
		/* 12 */	"<p>",
		/* 13 */	"<blockquote class=\"spip\"><p>",
		/* 14 */	"</blockquote><p>",
		/* 15 */	"",
                    "- ",
                    "- ",
                    " "
            
        );
        
        $letexte = preg_replace($cherche1, $remplace1, $letexte);
        $letexte = preg_replace("@^ <br />@S", "", $letexte);
        return $letexte;
    }

    function createPath($bdSPIP,$prefixeSPIP,$id_parent){
        $requeteAfficheTitreParent = $bdSPIP->prepare('SELECT titre
                                                        FROM '.$prefixeSPIP.'rubriques
                                                        WHERE id_rubrique = '.$id_parent);
        $okAfficheTitreParent = $requeteAfficheTitreParent->execute();
        $titreparent = $requeteAfficheTitreParent->fetch(PDO::FETCH_OBJ);
        return $titreparent;
    }

    function afficheIdParent($bdSPIP,$prefixeSPIP,$idparent){
        $requeteAfficheIdParent = $bdSPIP->prepare('SELECT id_parent
                                                    FROM '.$prefixeSPIP.'rubriques
                                                    WHERE id_rubrique = '.$idparent);
        $okAfficheIdParent = $requeteAfficheIdParent->execute();
        $grdparent = $requeteAfficheIdParent->fetch(PDO::FETCH_OBJ);
        return intval($grdparent->id_parent);
    }

    function afficheDocumentsNonLies($bdSPIP,$prefixeSPIP){
        $requeteAfficheDocumentsNonLies = $bdSPIP->prepare('SELECT d.fichier, d.id_document, d.taille
                                                            FROM '.$prefixeSPIP.'documents d
                                                            WHERE d.id_document NOT IN(
                                                                SELECT dl.id_document
                                                                FROM '.$prefixeSPIP.'documents_liens dl
                                                            ) ORDER BY d.fichier');
        $okAfficheDocumentsNonLies = $requeteAfficheDocumentsNonLies->execute();
        $documentsnonlies = $requeteAfficheDocumentsNonLies->fetchAll(PDO::FETCH_OBJ);
        return $documentsnonlies;
    }

?>