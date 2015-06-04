<?php
    include 'helpers/connexionBD.inc.php';
    include 'actions/methodesSPIP.inc.php';
    include 'actions/methodesJoomla.inc.php';
        
    // Pour forcer l'affichage des erreurs rencontrées par le processeur php
    ini_set('display_errors', 1);  error_reporting(E_ALL);



    /* Récupération des données pour avoir accès à la base de données SPIP */
    $serveurSPIP=$_GET["servSPIP"];
    $nomBaseSPIP=$_GET["nomSPIP"];
    $utilisateurSPIP=$_GET["utilSPIP"];
    $passwordSPIP=$_GET["passSPIP"];
    $prefixeSPIP=$_GET["prefixeSPIP"];

    /*Connexion avec la base de données SPIP*/
    $bdSPIP=connexionBD($serveurSPIP,$nomBaseSPIP,$utilisateurSPIP,$passwordSPIP,$prefixeSPIP);
    if($bdSPIP!=false)     $messageSPIP =  '<div class="alert alert-success"><i class="fa fa-thumbs-o-up"></i>
 Connexion à la BD SPIP réussie ! </div>' ;



    /* Récupération des données pour avoir accès à la base de données Joomla */
    $serveurJoomla=$_GET["servJoomla"];
    $nomBaseJoomla=$_GET["nomJoomla"];
    $utilisateurJoomla=$_GET["utilJoomla"];
    $passwordJoomla=$_GET["passJoomla"];
    $prefixeJoomla=$_GET["prefixeJoomla"];



    /* Connexion avec la base de données Joomla */
    $bdJoomla=connexionBD($serveurJoomla,$nomBaseJoomla,$utilisateurJoomla,$passwordJoomla,$prefixeJoomla);
    if($bdJoomla!=false)     $messageJoomla =  '<div class="alert alert-success"><i class="fa fa-thumbs-o-up"></i>
 Connexion à la BD Joomla réussie ! </div>' ;



    /* RECUPERATION des ID max des tables Joomla */
    $idMaxUserJoomla=afficheIdMaxUserJoomla($bdJoomla,$prefixeJoomla);

    $idMaxCategJoomla=afficheIdMaxCategJoomla($bdJoomla,$prefixeJoomla);

    $idMaxArticleJoomla=afficheIdMaxArtJoomla($bdJoomla,$prefixeJoomla);



    /* AUTEURS SPIP */
    /* Appel de la fonction pour afficher les auteurs SPIP qui ne sont pas dans la corbeille */
    $auteurs=afficheAuteursSPIP($bdSPIP,$prefixeSPIP);

    /* Mise en forme du résultat de la requête d'affichage des auteurs SPIP */
    $listauteurs  = '' ;  // On prépare le texte à injecter dans la page web
    foreach($auteurs as $element)
    {  
        $nouvelid=$idMaxUserJoomla[0]->idmax+$element->id_auteur;
        
        $listauteurs .= '<tr>'.'<td>'.$element->id_auteur.'</td><td>'.$nouvelid."</td><td>".$element->nom."</td><td>".$element->email."</td><td>".$element->login."</td><td>".$element->en_ligne.'</td></tr>' ;
    }



    /* RUBRIQUES SPIP */
    /* Appel de la fonction pour afficher les rubriques SPIP*/
    $rubriques=afficheRubriquesSPIP($bdSPIP,$prefixeSPIP);

    /* Mise en forme du résultat de la requête d'affichage des rubriques SPIP */
    $listrubriques  = '' ;  // On prépare le texte à injecter dans la page web
    foreach($rubriques as $element)
    {  
        $texte=formatSPIPtoJoomla($element->texte);
        $nouvelid=$idMaxCategJoomla[0]->idmax+$element->id_rubrique;
        
        if(empty($element->descriptif)){
            $description=$texte;
        } else {
            $description="<h2>".$element->descriptif."</h2><br>".$texte;    
        }
        
        if($element->id_parent==0){ //Toutes les categories Joomla ont un parent nommé Root d'id 1
            $nouvelidparent=1;
        } else {
            $nouvelidparent=$idMaxCategJoomla[0]->idmax+$element->id_parent;
        }
        
        $listrubriques .= '<tr>'.'<td>'.$element->id_rubrique.'</td><td>'.$nouvelid.'</td><td>'.$element->id_parent."</td><td>".$nouvelidparent."</td><td>".$element->titre."</td><td>".$description."</td><td>".$element->date."</td><td>".$element->maj.'</td></tr>' ;
    }



    /* ARTICLES SPIP */
    /* Appel de la fonction pour afficher les articles SPIP*/
    $articles=afficheArticlesSPIP($bdSPIP,$prefixeSPIP);

    /* Mise en forme du résultat de la requête d'affichage des articles SPIP */
    $listarticles  = '' ;  // On prépare le texte à injecter dans la page web
    foreach($articles as $element)
    {  
        $texte=formatSPIPtoJoomla($element->texte);
        $nouvelid=$idMaxArticleJoomla[0]->idmax+$element->id_article;
        $nouvelidRub=$idMaxCategJoomla[0]->idmax+$element->id_rubrique;
        
        /* recuperation et injection au bon endroit des documents associés à un article */
        if (preg_match_all('/<(doc|img)([0-9]+)\|[a-zA-Z0-9_-]*>/', $texte, $matches, PREG_SET_ORDER)) {
            foreach ($matches as $regs) {
                
                $documentlié=afficheDocumentById($bdSPIP,$prefixeSPIP,$regs[2]);
                
                if(!empty($documentlié)){
                    $path=$documentlié[0]->fichier;
                
                    if($regs[1]=='doc'){
                        $titre=preg_match_all('/[a-z]+\/([a-zA-Z0-9_-]*)/', $path, $matchesTitres, PREG_SET_ORDER);
                        if(!empty($matchesTitres[0])){
                          $lien='<a href='.$path.'>'.$matchesTitres[0][1].'</a>'; 
                        } else {
                          $lien='<a href='.$path.'>Document</a>';  
                        } 
                    } else {
                        $lien='<img src='.$path.'></img>';
                    }
                
                    $texte = preg_replace('/<(doc|img)([0-9]+)\|[a-zA-Z0-9_-]*>/', $lien, $texte);
                }
            }
        }
        
        $listarticles .= '<tr>'.'<td>'.$element->id_article.'</td><td>'.$nouvelid.'</td><td>'.$element->titre."</td><td>".$element->id_rubrique."</td><td>".$nouvelidRub."</td><td>".$texte."</td><td>".$element->date."</td><td>".$element->visites."</td><td>".$element->date_modif.'</td></tr>' ;
    }

    

?>



<!doctype html>
<html>
    <?php 
        $titre= 'Import DB SPIP to DB Joomla!' ;
        include 'elements/head.inc.php' ; 
    ?>
  <body>
    <?php include 'elements/menu.inc.php' ; ?>
    <div class="container">
        <?php include 'elements/titrePage.inc.php' ; ?> 
        <div class="row">
            <div class = "col-lg-12">
                <?php
                    echo $messageSPIP;
                    echo $messageJoomla;     
                ?>
            </div>
            <ul class="nav nav-tabs">
                <li class="active"><a data-toggle="tab" href="#auteurs">Auteurs</a></li>
                <li><a data-toggle="tab" href="#rubriques">Rubriques</a></li>
                <li><a data-toggle="tab" href="#articles">Articles</a></li>
            </ul>
            <div class="col-lg-12 tab-content">
                <div id="auteurs" class="tab-pane fade in active">
                    <h3>Id max des utilisateurs Joomla : <?php echo $idMaxUserJoomla[0]->idmax ?></h3>
                    <h2>Auteurs SPIP</h2>
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>id</th>
                                <th>new id</th>
                                <th>nom</th>
                                <th>email</th>
                                <th>login</th>
                                <th>en_ligne</th>
                            </tr>
                        <?php // On injecte dans la page web le texte créé par le programme situé en début de fichier 
                            echo $listauteurs;
                        ?> 
                        </tbody>
                   </table>
                </div>
                <div id="rubriques" class="tab-pane fade">
                    <h3>Id max des catégories Joomla : <?php echo $idMaxCategJoomla[0]->idmax ?></h3>
                    <h2>Rubriques SPIP</h2>
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>id</th>
                                <th>new id</th>
                                <th>id_parent</th>
                                <th>new id parent</th>
                                <th>titre</th>
                                <th>descriptif</th>
                                <th>date</th>
                                <th>maj</th>
                            </tr>
                        <?php // On injecte dans la page web le texte créé par le programme situé en début de fichier 
                            echo $listrubriques;
                        ?> 
                        </tbody>
                   </table>
                </div>
                <div id="articles" class="tab-pane fade">
                    <h3>Id max des articles Joomla : <?php echo $idMaxArticleJoomla[0]->idmax ?></h3>
                    <h2>Articles SPIP</h2>
                    <table class="table table-bordered table-striped">
                        <tbody>
                            <tr>
                                <th>id_article</th>
                                <th>new id</th>
                                <th>titre</th>
                                <th>id_rubrique</th>
                                <th>new id_rub</th>
                                <th>texte</th>
                                <th>date</th>
                                <th>visites</th>
                                <th>date_modif</th>
                            </tr>
                        <?php // On injecte dans la page web le texte créé par le programme situé en début de fichier 
                            echo $listarticles;
                        ?> 
                        </tbody>
                   </table>
                </div>
            </div>
        </div>     
        <?php include 'elements/footer.inc.php' ; ?>
    </div> <!-- Fin du container -->
  </body>
</html>
