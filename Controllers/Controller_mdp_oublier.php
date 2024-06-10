<?php

class Controller_mdp_oublier extends Controller
{
   

    public function action_default()
    {
        $this->action_mdp_oublier();
    }
    public function action_mdp_oublier(){
        $this->render('mdp_oublier', []);
}



public function action_mail_envoie(){
    $user= Model::getModel()->getIdUserByEmail($_POST['email_mdp_oublier']);
    if($user){
        $sujet="Mot de passe oublié Perform Vision";
        $code=genererCodeAleatoire(12);
        $message="Voici le code pour modifier votre mot de passe: " . $code ."";
        
        EmailSender::sendVerificationEmail($_POST['email_mdp_oublier'],$sujet,$message);
        $_SESSION['code_mdp_oublier']=$code;
        if(isset($_POST['email_mdp_oublier']) && $_POST['email_mdp_oublier']!==NULL){
            $_SESSION['email_mdp_oublier']=$_POST['email_mdp_oublier'];
        }
       
        $this->render('modification_mdp', []);
    }
    else{
        
        ?>
        <script>
            alert('adresse email invalide')
        </script>
        <?php
        $this->render('mdp_oublier', []);
    }

    
    
}
public function action_changement_mdp(){
    if(isset($_POST['code_email']) && isset($_POST['nouveau_mdp'])){
        $user= Model::getModel()->getIdUserByEmail($_SESSION['email_mdp_oublier']);
        if($_POST['code_email']==$_SESSION['code_mdp_oublier']){
            Model::getModel()->updatePassword($user,$_POST['nouveau_mdp']);
            ?>
            <script>
                alert('Modification réussi')
            </script>
            <?php
                $this->render('auth', []);
            
        }
        else{
            return "le code n'est pas bon ";
        }
        
    
    }
}


}