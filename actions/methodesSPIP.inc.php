<?php
    include 'helpers/filtres.php';
    include 'helpers/texte.php';


    function afficheAuteursSPIP($bdSPIP,$prefixeSPIP){
         $requeteAfficheAut = $bdSPIP->prepare('SELECT id_auteur, nom, email, login, pass, en_ligne 
                                                FROM '.$prefixeSPIP.'auteurs 
                                                WHERE statut != "5poubelle"');
        $okAfficheAut = $requeteAfficheAut->execute() ;
        $auteurs = $requeteAfficheAut->fetchAll(PDO::FETCH_OBJ)  ;
        return $auteurs;   
    }

    function afficheRubriquesSPIP($bdSPIP,$prefixeSPIP){
        $requeteAfficheRub = $bdSPIP->prepare('SELECT id_rubrique, id_parent, titre, descriptif, texte, date, maj 
                                               FROM '.$prefixeSPIP.'rubriques');
        $okAfficheRub = $requeteAfficheRub->execute() ;
        $rubriques = $requeteAfficheRub->fetchAll(PDO::FETCH_OBJ)  ;
        return $rubriques;         
    }

    function afficheArticlesSPIP($bdSPIP,$prefixeSPIP){
        $requeteAfficheArt = $bdSPIP->prepare('SELECT id_article, titre, id_rubrique, texte, date, visites, date_modif 
                                               FROM '.$prefixeSPIP.'articles');
        $okAfficheArt = $requeteAfficheArt->execute() ;
        $articles = $requeteAfficheArt->fetchAll(PDO::FETCH_OBJ)  ;
        return $articles;         
    }

    function formatSPIPtoJoomla($letexte){
        $debut_intertitre = "\n<h3 class=\"spip\">\n";
	    $fin_intertitre = "</h3>\n";
        
        $letexte = preg_replace(",\r\n?,S", "\n", $letexte);

        // Recuperer les para HTML
        $letexte = preg_replace(",<p[>[:space:]],iS", "\n\n\\0", $letexte);
        $letexte = preg_replace(",</p[>[:space:]],iS", "\\0\n\n", $letexte);
        
        //
        // Ensemble de remplacements implementant le systeme de mise
        // en forme (paragraphes, raccourcis...)
        //

        $letexte = "\n".trim($letexte);
        
        //
        // Enlaces a urls [xxx->url]
        // Note : complique car c'est ici qu'on applique typo(),
        // et en plus on veut pouvoir les passer en pipeline
        //

        /*
        $inserts = array();

        if (preg_match_all(_RACCOURCI_LIEN, $letexte, $matches, PREG_SET_ORDER)) {
            $i = 0;
            foreach ($matches as $regs) {		
                $inserts[++$i] = traiter_raccourci_lien($regs);
                $letexte = str_replace($regs[0], "@@SPIP_ECHAPPE_LIEN_$i@@", $letexte);
            }
        }*/
        
        
        /*
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
	   }*/
        
        
        // les listes
        /*if (ereg("\n-[*#]", $letexte))
            $letexte = traiter_listes($letexte);*/

        // Puce
        /*if (strpos($letexte, "\n- ") !== false)
            $puce = definir_puce();
        else $puce = '';*/
        
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
		/* 15 */	"/<\/?intro>/S"
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
		/* 15 */	""
        );
        
        $letexte = preg_replace($cherche1, $remplace1, $letexte);
        $letexte = preg_replace("@^ <br />@S", "", $letexte);
        return $letexte;
    }

?>