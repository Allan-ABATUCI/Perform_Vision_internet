<?php require "view_begin.php"; ?>
<link rel="stylesheet" href="Content/css/auth.css"/>
<nav>
    <div class="container" id="container">
        
       
            <form action="?controller=mdp_oublier&action=mail_envoie" method="post">
                <h1>Mot de passe oublié</h1>
                <input type="email" name="email_mdp_oublier" placeholder="Email" required/>
                <input type="submit" value="Recevoir un email" class="blanc button" />
            </form>
       
       
    </div>
</nav>

<?php require "view_end.php"; ?>