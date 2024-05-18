<?php

require_once 'Utils/functions.php';

class Controller_auth extends Controller
{
    public function action_auth()
    {
        $this->render("auth", []);
    }

    public function action_default()
    {
        $this->action_auth();
    }

    public function action_login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $email = sanitizeInput($_POST['email']);
            $password = sanitizeInput($_POST['password']);

            if ($this->validateLoginInput($email, $password)) {
                $user = Model::getModel()->getUserByCredentials($email, $password);

                if ($user) {
                    session_start();
                    session_regenerate_id(true);

                    $_SESSION['user_id'] = $user['id_utilisateur'];
                    $_SESSION['user_token'] = $user['token'];
                    $_SESSION['expire_time'] = time() + (30 * 60); // 30 minutes session expiration

                    header("Location: ?controller=dashboard");
                    exit();
                } else {
                    $this->renderError("Identifiants incorrects.");
                }
            } else {
                $this->renderError("Données saisies invalides.");
            }
        } else {
            $this->renderError("Accès non autorisé.");
        }

        $this->render("auth", []);
    }

    public function action_register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            $nom = sanitizeInput($_POST['nom']);
            $prenom = sanitizeInput($_POST['prenom']);
            $email = sanitizeInput($_POST['email']);
            $password = sanitizeInput($_POST['password']);
            $role = sanitizeInput($_POST['tabs']);

            if ($this->validateRegisterInput($nom, $prenom, $email, $password, $role)) {
                $result = Model::getModel()->creationUtilisateur($nom, $prenom, $password, $email, $role);

                if ($result) {
                    $verificationToken = Model::getModel()->getTokenUtilisateur($email);
                    $verificationLink = '?controller=auth&action=valide_email&token=' . urlencode($verificationToken);

                    EmailSender::sendVerificationEmail($email, 'Vérification de l\'adresse e-mail', 'Cliquez sur le lien suivant pour vérifier votre adresse e-mail: ' . $verificationLink);

                    $this->renderSuccess("Inscription réussie! Un e-mail de vérification a été envoyé.");
                } else {
                    $this->renderError("Erreur lors de l'inscription.");
                }
            } else {
                $this->renderError("Données saisies invalides.");
            }
        } else {
            $this->renderError("Accès non autorisé.");
        }

        $this->render("auth", []);
    }

    public function action_valide_email()
    {
        $token = sanitizeInput($_GET['token']);
        $validationResult = Model::getModel()->validerTokenUtilisateur($token);

        if ($validationResult) {
            $this->renderSuccess("Adresse e-mail vérifiée avec succès !");
        } else {
            $this->renderError("Erreur lors de la vérification de l'adresse e-mail. Le lien peut avoir expiré ou être invalide.");
        }

        $this->render("auth", []);
    }

    private function validateLoginInput($email, $password)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password) && strlen($password) <= 256 && strlen($email) <= 128;
    }


    private function validateRegisterInput($nom, $prenom, $email, $password, $role)
    {
        return !empty($nom) && !empty($prenom) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password) &&
            strlen($nom) <= 64 && strlen($prenom) <= 64 && strlen($password) <= 256 && strlen($email) <= 128 &&
            preg_match('/^[a-zA-Z\-]+$/', $nom) && preg_match('/^[a-zA-Z\-]+$/', $prenom) &&
            in_array($role, ['client', 'formateur']);
    }

    private function renderError($message)
    {
        echo "<div class='error'>$message</div>";
    }

    private function renderSuccess($message)
    {
        echo "<div class='success'>$message</div>";
    }
}
