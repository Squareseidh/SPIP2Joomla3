<?php

/***************************************************************************\
 *  SPIP, Systeme de publication pour l'internet                           *
 *                                                                         *
 *  Copyright (c) 2001-2014                                                *
 *  Arnaud Martin, Antoine Pitrou, Philippe Riviere, Emmanuel Saint-James  *
 *                                                                         *
 *  Ce programme est un logiciel libre distribue sous licence GNU/GPL.     *
 *  Pour plus de details voir le fichier COPYING.txt ou l'aide en ligne.   *
\***************************************************************************/

if (!defined('_ECRIRE_INC_VERSION')) return;

include_spip('inc/texte_mini');
include_spip('inc/lien');

/*************************************************************************************************************************
 * Fonctions inutilisees en dehors de inc/texte
 *
 */

// Raccourcis dependant du sens de la langue
function definir_raccourcis_alineas(){
	return array('','');
}


//
// Tableaux
//

define('_RACCOURCI_TH_SPAN', '\s*(:?{{[^{}]+}}\s*)?|<');

// http://doc.spip.org/@traiter_tableau
function traiter_tableau($bloc) {

	// Decouper le tableau en lignes
	preg_match_all(',([|].*)[|]\n,UmsS', $bloc, $regs, PREG_PATTERN_ORDER);
	$lignes = array();
	$debut_table = $summary = '';
	$l = 0;
	$numeric = true;

	// Traiter chaque ligne
	$reg_line1 = ',^(\|(' . '\s*(:?{{[^{}]+}}\s*)?|<' . '))+$,sS';
	$reg_line_all = ',^'  . '\s*(:?{{[^{}]+}}\s*)?|<' . '$,sS';
	foreach ($regs[1] as $ligne) {
		$l ++;

		// Gestion de la premiere ligne :
		if ($l == 1) {
		// - <caption> et summary dans la premiere ligne :
		//   || caption | summary || (|summary est optionnel)
			if (preg_match(',^\|\|([^|]*)(\|(.*))?$,sS', rtrim($ligne,'|'), $cap)) {
				/*$l = 0;
				if ($caption = trim($cap[1]))
					$debut_table .= "<caption>".$caption."</caption>\n";
				$summary = ' summary="'.entites_html(trim($cap[3])).'"';*/
			}
		// - <thead> sous la forme |{{titre}}|{{titre}}|
		//   Attention thead oblige a avoir tbody
			else if (preg_match($reg_line1,	$ligne, $thead)) {
			  	preg_match_all('/\|([^|]*)/S', $ligne, $cols);
				$ligne='';$cols= $cols[1];
				$colspan=1;
				for($c=count($cols)-1; $c>=0; $c--) {
					$attr='';
					if($cols[$c]=='<') {
					  $colspan++;
					} else {
					  if($colspan>1) {
						$attr= " colspan='$colspan'";
						$colspan=1;
					  }
					  $ligne= "<th scope='col'$attr>$cols[$c]</th>$ligne";
					}
				}

				$debut_table .= "<thead><tr class='row_first'>".
					$ligne."</tr></thead>\n";
				$l = 0;
			}
		}

		// Sinon ligne normale
		if ($l) {
			// Gerer les listes a puce dans les cellules
			if (strpos($ligne,"\n-*")!==false OR strpos($ligne,"\n-#")!==false)
				$ligne = traiter_listes($ligne);

			// Pas de paragraphes dans les cellules
			$ligne = preg_replace("/\n{2,}/", "<br />\n", $ligne);

			// tout mettre dans un tableau 2d
			preg_match_all('/\|([^|]*)/S', $ligne, $cols);
			$lignes[]= $cols[1];
		}
	}

	// maintenant qu'on a toutes les cellules
	// on prepare une liste de rowspan par defaut, a partir
	// du nombre de colonnes dans la premiere ligne.
	// Reperer egalement les colonnes numeriques pour les cadrer a droite
	$rowspans = $numeric = array();
    
    /*MODIFICATION BY Squareseidh*/
    if(!empty($lignes[0])){
	   $n = count($lignes[0]);
    }else{
        $n=0;
    }
    /**/
	$k = count($lignes);
	for($i=0;$i<$n;$i++) {
	  $align = true;
	  for ($j=0;$j<$k;$j++) $rowspans[$j][$i] = 1;
	  for ($j=0;$j<$k;$j++) {
	    $cell = trim($lignes[$j][$i]);
	    if (preg_match($reg_line_all, $cell)) {
		if (!preg_match('/^\d+([.,]?)\d*$/', $cell, $r))
		  { $align = ''; break;}
		else if ($r[1]) $align = $r[1];
	      }
	  }
	  $numeric[$i] = !$align ? '' :
	    (" style='text-align: " .
	     // http://www.w3.org/TR/REC-CSS2/tables.html#column-alignment
	     // specifie text-align: "," pour cadrer le long de la virgule
	     // mais les navigateurs ne l'implementent pas ou mal
	     (/* $align !== true ?"\"$align\"" : */ 'right') .
	     "'");
	}

	// et on parcourt le tableau a l'envers pour ramasser les
	// colspan et rowspan en passant
	$html = '';

	for($l=count($lignes)-1; $l>=0; $l--) {
		$cols= $lignes[$l];
		$colspan=1;
		$ligne='';

		for($c=count($cols)-1; $c>=0; $c--) {
			$cell = trim($cols[$c]);
			if($cell=='<') {
			  $colspan++;

			} elseif($cell=='^') {
			  $rowspans[$l-1][$c]+=$rowspans[$l][$c];

			} else {
			  if($colspan>1) {
				$attr .= " colspan='$colspan'";
				$colspan=1;
			  }
			  /*if(($x=$rowspans[$l][$c])>1) {
				$attr.= " rowspan='$x'";
			  }*/
			  //$ligne= "\n<td".$attr.'>'.$cols[$c].'</td>'.$ligne;
                $ligne= "\n<td".'>'.$cols[$c].'</td>'.$ligne; //MODIF BY SQUARESEIDH
			}
		}

		// ligne complete
		$class = alterner($l+1, 'even', 'odd');
		$html = "<tr class='row_$class'>$ligne</tr>\n$html";
	}
	return "\n\n<table>\n"
		. $debut_table
		. "<tbody>\n"
		. $html
		. "</tbody>\n"
		. "</table>\n\n";
}

//
// Traitement des listes (merci a Michael Parienti)
//
// http://doc.spip.org/@traiter_listes
function traiter_listes ($texte) {
	return $texte;
}

// Nettoie un texte, traite les raccourcis autre qu'URL, la typo, etc.
// http://doc.spip.org/@traiter_raccourcis
function traiter_raccourcis($letexte) {

	// Appeler les fonctions de pre_traitement
	$letexte = pipeline('pre_propre', $letexte);

	// APPELER ICI UN PIPELINE traiter_raccourcis ?
	// $letexte = pipeline('traiter_raccourcis', $letexte);

	// Appeler les fonctions de post-traitement
	$letexte = pipeline('post_propre', $letexte);

	return $letexte;
}

/*************************************************************************************************************************
 * Fonctions utilisees en dehors de inc/texte
 */

// afficher joliment les <script>
// http://doc.spip.org/@echappe_js
function echappe_js($t,$class=' class="echappe-js"') {
	if (preg_match_all(',<script.*?($|</script.),isS', $t, $r, PREG_SET_ORDER))
	foreach ($r as $regs)
		$t = str_replace($regs[0],
			"<code$class>".nl2br(spip_htmlspecialchars($regs[0])).'</code>',
			$t);
	return $t;
}



/**
 * Empêcher l'exécution de code PHP et JS
 *
 * Sécurité : empêcher l'exécution de code PHP, en le transformant en joli code
 * dans l'espace privé. Cette fonction est aussi appelée par propre et typo.
 * 
 * De la même manière, la fonction empêche l'exécution de JS mais selon le mode
 * de protection déclaré par la globale filtrer_javascript :
 * - -1 : protection dans l'espace privé et public
 * - 0  : protection dans l'espace public
 * - 1  : aucune protection
 * 
 * Il ne faut pas désactiver globalement la fonction dans l'espace privé car elle protège
 * aussi les balises des squelettes qui ne passent pas forcement par propre ou typo après
 * si elles sont appelées en direct
 * 
 * @param string $arg
 *     Code à protéger
 * @return string
 *     Code protégé
**/
function interdire_scripts($arg) {
	// on memorise le resultat sur les arguments non triviaux
	static $dejavu = array();

	// Attention, si ce n'est pas une chaine, laisser intact
	if (!$arg OR !is_string($arg) OR !strstr($arg, '<')) return $arg; 

	if (isset($dejavu[$GLOBALS['filtrer_javascript']][$arg])) return $dejavu[$GLOBALS['filtrer_javascript']][$arg];

	// echapper les tags asp/php
	$t = str_replace('<'.'%', '&lt;%', $arg);

	// echapper le php
	$t = str_replace('<'.'?', '&lt;?', $t);

	// echapper le < script language=php >
	$t = preg_replace(',<(script\b[^>]+\blanguage\b[^\w>]+php\b),UimsS', '&lt;\1', $t);

	// Pour le js, trois modes : parano (-1), prive (0), ok (1)
	switch($GLOBALS['filtrer_javascript']) {
		case 0:
			if (!_DIR_RESTREINT)
				$t = echappe_js($t);
			break;
		case -1:
			$t = echappe_js($t);
			break;
	}

	// pas de <base href /> svp !
	$t = preg_replace(',<(base\b),iS', '&lt;\1', $t);

	// Reinserer les echappements des modeles
	if (defined('_PROTEGE_JS_MODELES'))
		$t = echappe_retour($t,"javascript"._PROTEGE_JS_MODELES);
	if (defined('_PROTEGE_PHP_MODELES'))
		$t = echappe_retour($t,"php"._PROTEGE_PHP_MODELES);

	return $dejavu[$GLOBALS['filtrer_javascript']][$arg] = $t;
}

// Typographie generale
// avec protection prealable des balises HTML et SPIP

// http://doc.spip.org/@typo
function typo($letexte, $echapper=true, $connect=null, $env=array()) {
	// Plus vite !
	if (!$letexte) return $letexte;

	// les appels directs a cette fonction depuis le php de l'espace
	// prive etant historiquement ecrit sans argment $connect
	// on utilise la presence de celui-ci pour distinguer les cas
	// ou il faut passer interdire_script explicitement
	// les appels dans les squelettes (de l'espace prive) fournissant un $connect
	// ne seront pas perturbes
	$interdire_script = false;
	if (is_null($connect)){
		$connect = '';
		$interdire_script = true;
	}

	// Echapper les codes <html> etc
	if ($echapper)
		$letexte = echappe_html($letexte, 'TYPO');

	//
	// Installer les modeles, notamment images et documents ;
	//
	// NOTE : propre() ne passe pas par ici mais directement par corriger_typo
	// cf. inc/lien

	$letexte = traiter_modeles($mem = $letexte, false, $echapper ? 'TYPO' : '', $connect, null, $env);
	if ($letexte != $mem) $echapper = true;
	unset($mem);

	$letexte = corriger_typo($letexte);
	$letexte = echapper_faux_tags($letexte);

	// reintegrer les echappements
	if ($echapper)
		$letexte = echappe_retour($letexte, 'TYPO');

	// Dans les appels directs hors squelette, securiser ici aussi
	if ($interdire_script)
		$letexte = interdire_scripts($letexte);

	return $letexte;
}

// Correcteur typographique
define('_TYPO_PROTEGER', "!':;?~%-");
define('_TYPO_PROTECTEUR', "\x1\x2\x3\x4\x5\x6\x7\x8");

define('_TYPO_BALISE', ",</?[a-z!][^<>]*[".preg_quote(_TYPO_PROTEGER)."][^<>]*>,imsS");

// http://doc.spip.org/@corriger_typo
function corriger_typo($letexte, $lang='') {

	// Plus vite !
	if (!$letexte) return $letexte;

	$letexte = pipeline('pre_typo', $letexte);

	// Caracteres de controle "illegaux"
	$letexte = corriger_caracteres($letexte);

	// Proteger les caracteres typographiques a l'interieur des tags html
	if (preg_match_all(_TYPO_BALISE, $letexte, $regs, PREG_SET_ORDER)) {
		foreach ($regs as $reg) {
			$insert = $reg[0];
			// hack: on transforme les caracteres a proteger en les remplacant
			// par des caracteres "illegaux". (cf corriger_caracteres())
			$insert = strtr($insert, _TYPO_PROTEGER, _TYPO_PROTECTEUR);
			$letexte = str_replace($reg[0], $insert, $letexte);
		}
	}

	// trouver les blocs multi et les traiter a part
	$letexte = extraire_multi($e = $letexte, $lang, true);
	$e = ($e === $letexte);

	// Charger & appliquer les fonctions de typographie
	$typographie = charger_fonction(lang_typo($lang), 'typographie');
	$letexte = $typographie($letexte);

	// Les citations en une autre langue, s'il y a lieu
	if (!$e) $letexte = echappe_retour($letexte, 'multi');

	// Retablir les caracteres proteges
	$letexte = strtr($letexte, _TYPO_PROTECTEUR, _TYPO_PROTEGER);

	// pipeline
	$letexte = pipeline('post_typo', $letexte);

	# un message pour abs_url - on est passe en mode texte
	$GLOBALS['mode_abs_url'] = 'texte';

	return $letexte;
}




//
// Une fonction pour fermer les paragraphes ; on essaie de preserver
// des paragraphes indiques a la main dans le texte
// (par ex: on ne modifie pas un <p align='center'>)
//
// deuxieme argument : forcer les <p> meme pour un seul paragraphe
//
// http://doc.spip.org/@paragrapher
// /!\ appelee dans inc/filtres et public/composer
function paragrapher($letexte, $forcer=true) {
	return $letexte;
}

// Harmonise les retours chariots et mange les paragraphes html
// http://doc.spip.org/@traiter_retours_chariots
// ne sert plus
function traiter_retours_chariots($letexte) {
	$letexte = preg_replace(",\r\n?,S", "\n", $letexte);
	$letexte = preg_replace(",<p[>[:space:]],iS", "\n\n\\0", $letexte);
	$letexte = preg_replace(",</p[>[:space:]],iS", "\\0\n\n", $letexte);
	return $letexte;
}


// Filtre a appliquer aux champs du type #TEXTE*
// http://doc.spip.org/@propre
function propre($t, $connect=null, $env=array()) {
	// les appels directs a cette fonction depuis le php de l'espace
	// prive etant historiquement ecrits sans argment $connect
	// on utilise la presence de celui-ci pour distinguer les cas
	// ou il faut passer interdire_script explicitement
	// les appels dans les squelettes (de l'espace prive) fournissant un $connect
	// ne seront pas perturbes
	$interdire_script = false;
	if (is_null($connect)){
		$connect = '';
		$interdire_script = true;
	}

	if (!$t) return strval($t);

	$t = echappe_html($t);
	$t = expanser_liens($t,$connect, $env);
	$t = traiter_raccourcis($t);
	$t = echappe_retour_modeles($t, $interdire_script);

	return $t;
}

?>
