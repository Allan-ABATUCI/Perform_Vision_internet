<?php

require_once 'Utils/functions.php';

/**
 * Classe Controller_auth gérant les actions d'authentification.
 */
class Controller_auth extends Controller
{
    /**
     * Affiche la page d'authentification.
     */
    public function action_auth()
    {
        $this->render("auth", []);
    }

    /**
     * Action par défaut, redirige vers l'action d'authentification.
     */
    public function action_default()
    {
        $this->action_auth();
    }

    /**
     * Gère la connexion de l'utilisateur.
     * 
     * Si la requête est de type POST, elle tente de valider les identifiants et de créer une session.
     * Affiche des messages d'erreur en cas d'échec.
     */
    public function action_login()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Assainir les entrées email et mot de passe
            $email = sanitizeInput($_POST['email']);
            $password = sanitizeInput($_POST['password']);

            // Valider les entrées de connexion
            if ($this->validateLoginInput($email, $password)) {
                // Récupérer l'utilisateur avec les identifiants fournis
                $user = Model::getModel()->getUserByCredentials($email, $password);

                if ($user) {
                    // Démarrer une session et régénérer l'ID de session
                    session_start();
                    session_regenerate_id(true);

                    // Stocker les informations utilisateur dans la session
                    $_SESSION['user_id'] = $user['id_utilisateur'];
                    $_SESSION['user_token'] = $user['token'];
                    $_SESSION['expire_time'] = time() + (30 * 60); // Expiration de session après 30 minutes

                    // Rediriger vers le tableau de bord
                    header("Location: ?controller=dashboard");
                    exit();
                } else {
                    // Afficher une erreur si les identifiants sont incorrects
                    $this->renderError("Identifiants incorrects.");
                }
            } else {
                // Afficher une erreur si les données saisies sont invalides
                $this->renderError("Données saisies invalides.");
            }
        } else {
            // Afficher une erreur si la méthode d'accès est incorrecte
            $this->renderError("Accès non autorisé.");
        }

        // Affiche la page d'authentification
        $this->render("auth", []);
    }

    /**
     * Gère l'inscription de l'utilisateur.
     * 
     * Si la requête est de type POST, elle tente de valider les données d'inscription et de créer un nouvel utilisateur.
     * Envoie un email de vérification si la création est réussie.
     * Affiche des messages d'erreur en cas d'échec.
     */
    public function action_register()
    {
        if ($_SERVER['REQUEST_METHOD'] === 'POST') {
            // Assainir les entrées d'inscription
            $nom = sanitizeInput($_POST['nom']);
            $prenom = sanitizeInput($_POST['prenom']);
            $email = sanitizeInput($_POST['email']);
            $password = sanitizeInput($_POST['password']);
            $role = sanitizeInput($_POST['tabs']);

            // Valider les entrées d'inscription
            if ($this->validateRegisterInput($nom, $prenom, $email, $password, $role)) {
                // Créer un nouvel utilisateur
                $result = Model::getModel()->creationUtilisateur($nom, $prenom, $password, $email, $role);

                if ($result) {
                    // Récupérer le token de vérification et envoyer un e-mail de vérification
                    $verificationToken = Model::getModel()->getTokenUtilisateur($email);
                    $verificationLink = '?controller=auth&action=valide_email&token=' . urlencode($verificationToken);
                    EmailSender::sendVerificationEmail($email, 'Vérification de l\'adresse e-mail', 'Cliquez sur le lien suivant pour vérifier votre adresse e-mail: ' . $verificationLink);

                    // Afficher un message de succès
                    $this->renderSuccess("Inscription réussie! Un e-mail de vérification a été envoyé.");
                } else {
                    // Afficher une erreur si la création de l'utilisateur a échoué
                    $this->renderError("Erreur lors de l'inscription.");
                }
            } else {
                // Afficher une erreur si les données saisies sont invalides
                $this->renderError("Données saisies invalides.");
            }
        } else {
            // Afficher une erreur si la méthode d'accès est incorrecte
            $this->renderError("Accès non autorisé.");
        }

        // Affiche la page d'authentification
        $this->render("auth", []);
    }

    /**
     * Valide l'adresse e-mail de l'utilisateur via un token.
     * 
     * Vérifie le token fourni et met à jour l'état de vérification de l'utilisateur.
     * Affiche des messages de succès ou d'erreur selon le résultat de la validation.
     */
    public function action_valide_email()
    {
        // Assainir le token reçu
        $token = sanitizeInput($_GET['token']);
        // Valider le token
        $validationResult = Model::getModel()->validerTokenUtilisateur($token);

        if ($validationResult) {
            // Afficher un message de succès si la validation a réussi
            $this->renderSuccess("Adresse e-mail vérifiée avec succès !");
        } else {
            // Afficher une erreur si la validation a échoué
            $this->renderError("Erreur lors de la vérification de l'adresse e-mail. Le lien peut avoir expiré ou être invalide.");
        }

        // Affiche la page d'authentification
        $this->render("auth", []);
    }

    /**
     * Valide les entrées de connexion.
     * 
     * @param string $email L'email de l'utilisateur.
     * @param string $password Le mot de passe de l'utilisateur.
     * @return bool Retourne true si les entrées sont valides, sinon false.
     */
    private function validateLoginInput($email, $password)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password) && strlen($password) <= 256 && strlen($email) <= 128;
    }

    /**
     * Valide les entrées d'inscription.
     * 
     * @param string $nom Le nom de l'utilisateur.
     * @param string $prenom Le prénom de l'utilisateur.
     * @param string $email L'email de l'utilisateur.
     * @param string $password Le mot de passe de l'utilisateur.
     * @param string $role Le rôle de l'utilisateur.
     * @return bool Retourne true si les entrées sont valides, sinon false.
     */
    private function validateRegisterInput($nom, $prenom, $email, $password, $role)
    {
        return !empty($nom) && !empty($prenom) && filter_var($email, FILTER_VALIDATE_EMAIL) && !empty($password) &&
            strlen($nom) <= 64 && strlen($prenom) <= 64 && strlen($password) <= 256 && strlen($email) <= 128 &&
            preg_match('/^[a-zA-Z\-]+$/', $nom) && preg_match('/^[a-zA-Z\-]+$/', $prenom) &&
            in_array($role, ['client', 'formateur']);
    }

    /**
     * Affiche un message d'erreur.
     * 
     * @param string $message Le message d'erreur à afficher.
     */
    private function renderError($message)
    {
        echo "<div class='error'>$message</div>";
    }

    /**
     * Affiche un message de succès.
     * 
     * @param string $message Le message de succès à afficher.
     */
    private function renderSuccess($message)
    {
        echo "<div class='success'>$message</div>";
    }
}
