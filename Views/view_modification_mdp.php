<?php require "view_begin.php"; 
?>

<link rel="stylesheet" href="Content/css/auth.css"/>
<nav>
    <div class="container" id="container">
        
       
            <form action="?controller=mdp_oublier&action=changement_mdp" method="post">
                <h1>Modification mot de passe</h1>
                <input type="text" name="code_email" placeholder="Code reçu par email" required/>
                <input type="password" name="nouveau_mdp" placeholder="Nouveau mot de passe" required/>
                <input type="submit" value="Modifier le mot de passe" class="blanc button" />
            </form>
       
       
    </div>
</nav>

<?php require "view_end.php"; ?>