<?php

class Controller_profile extends Controller
{
    

    public function action_default()
    {
        $this->action_profile();
    }

    public function action_profile()
    {

        $user = checkUserAccess();

        if (!$user) {
            echo "Accès non autorisé.";
            $this->render('auth', []);
        }

        $role = getUserRole($user);

        $model = Model::getModel();

        $data = [
            'mail' => $user['mail'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'photo_de_profil' => $user['photo_de_profil'],
            'role' => $role
        ];

        if ($role === 'Client') {
            $data['societe'] = $model->getClientById($user['id_utilisateur']);
            $this->render('monprofilclient', $data);
        } elseif ($role === 'Formateur') {
            $data['formateur'] = $model->getFormateurById($user['id_utilisateur']);
            $data['competences'] = $model->getCompetencesFormateurById($user['id_utilisateur']);
            $this->render('monprofilformateur', $data);
        } else {
            echo "Accès non autorisé.";
            $this->render('auth', []);
        }
    }

    public function action_modifier()
    {

        $user = checkUserAccess();

        if (!$user) {
            echo "Accès non autorisé.";
            $this->render('auth', []);
        }

        $role = getUserRole($user);

        $model = Model::getModel();

        $data = [
            'mail' => $user['mail'],
            'nom' => $user['nom'],
            'prenom' => $user['prenom'],
            'photo_de_profil' => $user['photo_de_profil'],
            'role' => $role
        ];

        if ($role === 'Client') {
            $data['societe'] = $model->getClientById($user['id_utilisateur']);
            $this->render('modifiermonprofilClient', $data);
        } elseif ($role === 'Formateur') {
            $data['formateur'] = $model->getFormateurById($user['id_utilisateur']);
            $data['competences'] = $model->getCompetencesFormateurById($user['id_utilisateur']);
            $this->render('modifiermonprofilformateur', $data);
        } else {
            echo "Accès non autorisé.";
            $this->render('auth', []);
        }
    }

    public function action_modifier_info(){

        if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
           // header('Location: ?controller=profile');
            exit();
        }

        $user = checkUserAccess();

        if (!$user) {
            echo "Accès non autorisé.";
            $this->render('auth', []);
        }

        $role = getUserRole($user);

        $model = Model::getModel();

        if (isset($_POST['nouvelle_email']) && !empty($_POST['nouvelle_email']) && $_POST['nouvelle_email'] !== $user['mail'] && filter_var($_POST['nouvelle_email'], FILTER_VALIDATE_EMAIL)) {
            $nouvelle_email = $_POST['nouvelle_email'];
            $model->updateEmail($user['id_utilisateur'], $nouvelle_email);
        }

        if (isset($_POST['nouveau_mot_de_passe']) && !empty($_POST['nouveau_mot_de_passe'])) {
            $nouveau_mot_de_passe = e(trim($_POST['nouveau_mot_de_passe']));
            if (strlen($nouveau_mot_de_passe) <= 256) {
                $model->updatePassword($user['id_utilisateur'], $nouveau_mot_de_passe);
            }
        }

        if (isset($_POST['nouvelle_societe'])) {
            $nouvelle_societe = e(trim($_POST['nouvelle_societe']));
            if (!empty($nouvelle_societe) && $nouvelle_societe !== $model->getClientById($user['id_utilisateur'])['societe']) {
                $model->updateSociete($user['id_utilisateur'], $nouvelle_societe);
            }
        }

        if (isset($_POST['nouveau_linkedin'])) {
            $nouveau_linkedin = e(trim($_POST['nouveau_linkedin']));
            $ancien_linkedin = $model->getFormateurById($user['id_utilisateur'])['linkedin'];
        
            if (!empty($nouveau_linkedin) && $nouveau_linkedin !== $ancien_linkedin) {
                $model->updateLinkedIn($user['id_utilisateur'], $nouveau_linkedin);
            }
        }
        
        if (isset($_FILES['nouveau_cv'])) {
            $nouveau_cv = e(trim($_FILES['nouveau_cv']['name']));
            $ancien_cv = $model->getFormateurById($user['id_utilisateur'])['cv'];
        
            if (!empty($nouveau_cv) && $nouveau_cv !== $ancien_cv) {
                $model->updateCV($user['id_utilisateur'], $nouveau_cv);
            }

            $targetDir = "cv/";
            $fileName = basename($_FILES["nouveau_cv"]["name"]);
            $targetFile = $targetDir . $fileName;
            $uploadOk = 1;
            $fileType = strtolower(pathinfo($targetFile, PATHINFO_EXTENSION));
        
            // Vérifier si le fichier est un fichier PDF
            if ($fileType != "pdf") {
                echo "Seuls les fichiers PDF sont autorisés.";
                $uploadOk = 0;
            }
        
            // Vérifier si le fichier a été téléversé avec succès
            if ($uploadOk == 0) {
                echo "Erreur lors du téléversement du fichier.";
            } else {
                if (move_uploaded_file($_FILES["nouveau_cv"]["tmp_name"], $targetFile)) {
                    echo "Le fichier " . htmlspecialchars($fileName) . " a été téléversé avec succès.";
                } else {
                    echo "Erreur lors du téléversement du fichier.";
                }
            }
        }
        if(isset($_POST['skillName']) && isset($_POST['skillLevel']) && isset($_POST['skillSpecialty'])){
            $id_user=$user['id_utilisateur'];
            
            
            $model->AjtCompetenceFormateur($id_user,$_POST['skillName'],$_POST['skillSpecialty'],$_POST['skillLevel']);

        
        
        }

        if(isset($_POST['id_aep_modif']) && isset($_POST['id_categorie_modif']) && isset($_POST['nom_competence_modif']) && isset($_POST['theme_modification']) && isset($_POST['niveau_modification'])){
            $model->modif_competence($_POST['id_aep_modif'],$_POST['id_categorie_modif'],$_POST['nom_competence_modif'],$_POST['theme_modification'],$_POST['niveau_modification']);
           
        }

        if(isset($_POST['id_categorie_sup']) && isset($_POST['id_aep_sup'])){
            $model->sup_competence($_POST['id_aep_sup'],$_POST['id_categorie_sup']);
        }

       
        
        header('Location: ?controller=profile');
        exit();

    }

    public function action_ajouter_competence(){

        $user = checkUserAccess();

        $model = Model::getModel();
       
    
    }

}