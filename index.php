<!doctype html>
<html>
<?php $titre='Import DB SPIP to DB Joomla!' ; include 'elements/head.inc.php' ; ?>

<body>
    <?php include 'elements/menu.inc.php' ; ?>
    <div class="container">
        <?php include 'elements/titrePage.inc.php' ; ?>

        <div class="row">
            <div class="col-lg-12">
                <form class="form-horizontal" role="form" method="get" action="conversion.php">
                    <div class="col-sm-6">
                        <h3>Base de données SPIP</h3>
                        <div class="form-group">
                            <label for="nom" class="col-sm-4 control-label">Serveur BD SPIP</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="servSPIP" placeholder="Si en local, mettez localhost">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="prenom" class="col-sm-4 control-label">Nom BD SPIP</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomSPIP" placeholder="Nom de la base de données SPIP">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="prenom" class="col-sm-4 control-label">Utilisateur SPIP</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="utilSPIP" placeholder="identifiant de l'utilisateur de la bd SPIP">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="adresse" class="col-sm-4 control-label">Password SPIP</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="passSPIP" placeholder="Password de la base de données SPIP">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nom" class="col-sm-4 control-label">Préfixe tables SPIP</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="prefixeSPIP" placeholder="Rentrez le préfixe des tables (spip_)">
                            </div>
                        </div>
                    </div>
                    <div class="col-sm-6">
                        <h3>Base de données Joomla</h3>
                        <div class="form-group">
                            <label for="nom" class="col-sm-4 control-label">Serveur BD Joomla</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="servJoomla" placeholder="Si en local, mettez localhost">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="prenom" class="col-sm-4 control-label">Nom BD Joomla</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="nomJoomla" placeholder="Nom de la base de données Joomla">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="prenom" class="col-sm-4 control-label">Utilisateur Joomla</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="utilJoomla" placeholder="identifiant de l'utilisateur de la bd Joomla">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="adresse" class="col-sm-4 control-label">Password Joomla</label>
                            <div class="col-sm-8">
                                <input type="password" class="form-control" name="passJoomla" placeholder="Password de la base de données">
                            </div>
                        </div>
                        <div class="form-group">
                            <label for="nom" class="col-sm-4 control-label">Préfixe tables Joomla</label>
                            <div class="col-sm-8">
                                <input type="text" class="form-control" name="prefixeJoomla" placeholder="Rentrez le préfixe des tables (joom_)">
                            </div>
                        </div>
                    </div>


                    <div class="form-group">
                        <div class="col-sm-offset-5 col-sm-10">
                            <button type="submit" class="btn btn-default">Enregistrer</button>
                        </div>
                    </div>

                </form>
            </div>

            <?php include 'elements/footer.inc.php' ; ?>

        </div>
        <!-- Fin du container -->


</body>

</html>