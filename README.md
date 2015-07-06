#SPIP2Joomla3
>Script PHP de migration SPIP 2 Joomla 3

![logo](http://image.noelshack.com/fichiers/2015/28/1436185811-logoscrat.png)


###Principe

Ce script permet de migrer un site utilisant le CMS SPIP vers le CMS Joomla. 

En suivant la procédure de migration, le script permet de récupérer les articles, les rubriques et les utilisateurs mais aussi les fichiers (pdf,img) qui sont utilisés.


###Procédure de migration

1.Installez un Joomla vierge sans données d’exemple (si vous n’avez pas déjà créé le site Joomla qui remplacera l’ancien)

2.Pour pouvoir afficher les pdf dans l’article, l’extension Content __Pdf Embed__ de _TechJoomla_ doit être installée (sinon les pdf s’afficheront de cette forme _{pdf=… width=… height=…}_)

3.Copiez le contenu du dossier _IMG_ de SPIP vers le dossier _images_ de Joomla

4.Copiez le script PHP à l’endroit où sont stockés les dossiers pour Joomla et SPIP

5.Rentrez vos identifiants de connexion aux deux différentes bases de données

6.Cliquez sur _Exporter_ et laissez faire la magie

7.Sur le site Joomla, allez dans _contenu_ puis _gestion des catégories_ (il y a normalement des erreurs php)

8.Cochez toutes les catégories puis cliquez sur _reconstruire_.


##Fonctionnalités

* Conversion des rubriques SPIP en catégories Joomla en gardant la hiérarchie

* Conversion des articles SPIP en contenu Joomla associées aux bonnes catégories avec le texte converti en HTML et les liens internes fonctionnels.

* Conversion des auteurs SPIP en utilisateur Joomla avec cependant comme __mot de passe secret__

* Suppression des fichiers transférés inutiles qui sont contenus dans images de Joomla
