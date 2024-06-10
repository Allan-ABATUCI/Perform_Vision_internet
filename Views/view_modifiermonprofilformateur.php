<?php require "view_begin.php"; 
?>
<?php require "view_menu.php"; ?>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet" integrity="sha384-T3c6CoIi6uLrA9TneNEoa7RxnatzjcDSCmG1MXxSR1GAsXEV/Dwwykc2MPK8M2HN" crossorigin="anonymous">
  <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js" integrity="sha384-C6RzsynM9kWDrMNeT87bh95OGNyZPhcTNXj1NW7RuBCsyN/o0jlpcV8Qyq46cDfL" crossorigin="anonymous"></script>

<link rel="stylesheet" href="Content/css/profiles.css">

<script>
/*function ajouterCompetence() {
    // Récupérer les valeurs du formulaire
    var skillName = document.getElementById("skillName").value;
    var skillSpecialty = document.getElementById("skillSpecialty").value;
    var skillLevel = document.getElementById("skillLevel").value;

    // Créer un nouvel élément li pour la nouvelle compétence
    var newSkillItem = document.createElement("li");
    newSkillItem.innerHTML = skillName + ' (' + skillSpecialty + ', niveau : ' + skillLevel + ")";

    // Ajouter la nouvelle compétence à la liste des compétences
    document.getElementById("competencesList").appendChild(newSkillItem);

    // Effacer les champs du formulaire après l'ajout
    document.getElementById("skillName").value = "";
    document.getElementById("skillSpecialty").value = "POO";
    document.getElementById("skillLevel").value = "Débutant";
}
*/
function annulerModification() {
    window.location.href = '?controller=profile';
}

</script>

<form action="?controller=profile&action=modifier_info" method="POST" enctype="multipart/form-data">
    <div class="card2 shadow">
        <div class="texte">
            <div class="titre">
                <div class="text-container h1"><h1>Modifier votre profil</h1></div>
            </div>
        </div>
        <div class="texte">
            <div class="cercle">
                <img class="cercle" src="Content/img/<?= $data['photo_de_profil'] ?>" alt="Photo de profil">
            </div>
            <div class="information">
                <div class="sous-titre">
                    <div class="text-container h2">Nom Prénom</div>
                    <div class="text-container p">
                        <p><?= $nom . ' ' . $prenom ?></p>
                    </div>
                </div>
                <div class="sous-titre">Adresse e-mail</div>
                <input class="encadrer" type="email" name="nouvelle_email" value="<?= $mail ?>" required>
                <div class="sous-titre">Nouveau mot de passe</div>
                <input class="encadrer" type="password" name="nouveau_mot_de_passe">
                <div class="sous-titre">LinkedIn</div>
                <input class="encadrer" type="text" name="nouveau_linkedin" value="<?= $formateur['linkedin']; ?>" required>
                <div class="sous-titre">CV</div>
                <div>
                <input class="encadrer" type="file" name="nouveau_cv" value="<?= $formateur['cv']; ?>" required></div>
               
                <button type="button" id="btnannuler" onclick="annulerModification()">Annuler</button>
                <button type="submit" id="btnenregistrer">Enregistrer</button>
            </div>
        </div>
    </div>
</form>

<!-- Formulaire distinct pour ajouter une compétence -->
<form method="post" id="skillsForm" action="?controller=profile&action=modifier_info">
    <div class="card2 shadow">
        <div class="texte">
            <div class="titre">
                <div class="text-container h1"><h1>Compétences :</h1></div>
                </br>
            </div>
        </div>
        <div class="texte">
            
            <?php foreach ($competences as $categorie => $themes) : ?>
                <div>
                      
                   
                        <?= htmlspecialchars($categorie) ?>
                        <?php foreach ($themes as $theme) : ?>
                            (<?= htmlspecialchars($theme['theme']) ?>:<?= htmlspecialchars($theme['niveau']) ?>)
                        <?php endforeach; ?>
                       
                    <div class="buttons-container">
                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#exampleModal" 
                            data-bs-id_categorie="<?= htmlspecialchars($theme['id_categorie']) ?>" 
                            data-bs-id_aep="<?= htmlspecialchars($theme['id_aep']) ?>" 
                            data-bs-nom="<?= htmlspecialchars($categorie) ?>" 
                            data-bs-theme="<?= htmlspecialchars($theme['theme']) ?>" 
                            data-bs-niveau="<?= htmlspecialchars($theme['niveau']) ?>">Modifier</button>
                    </div>
               
                </div>
            <?php endforeach; ?>
                        </br>
           
            <div class="text-container h1"><h1>Ajouter une compétence</h1></div>
            <input type="text" id="skillName" name="skillName" placeholder="Nom de la compétence" required>

            <label for="skillSpecialty">Spécialité:</label>
            <select id="skillSpecialty" name="skillSpecialty" required>
                <option value="POO">POO</option>
                <option value="Bases">Bases</option>
                <option value="Communication C/S">Communication C/S</option>
            </select>

            <label for="skillLevel">Niveau:</label>
            <select id="skillLevel" name="skillLevel" required>
                <option value="Débutant">Débutant</option>
                <option value="Initié">Initié</option>
                <option value="Confirmé">Confirmé</option>
                <option value="Expert">Expert</option>
            </select>
            
            <button type="submit">Ajouter</button>
        </div>
    </div>
</form>
<!-- Modal -->
<div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
    <div class="modal-dialog">
      <div class="modal-content">

        <div class="modal-header">
          <h1 class="modal-title fs-5" id="exampleModalLabel">Ajouter un événement</h1>
          <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
        </div>
        <div class="modal-body">
                 <form id="formModificationCompetence" action="?controller=profile&action=modifier_info" method="POST">
                    <input type="hidden" name="id_aep_modif" id="id_aep_modification">
                    <input type="hidden" name="id_categorie_modif" id="id_categorie_modification">
                    <div class="mb-3">
                        <label for="theme_modification" class="form-label">Nom de la compétence</label>
                        <input type="text" class="form-control" id="nom_competence_modif" name="nom_competence_modif" required>
                    </div>
                    <div class="mb-3">
                        <label for="theme_modification">Spécialité:</label>
                            <select class="form-select" id="theme_modification" name="theme_modification" required>
                                <option value="POO">POO</option>
                                <option value="Bases">Bases</option>
                                <option value="Communication C/S">Communication C/S</option>
                            </select>
                        
                    </div>
                    <div class="mb-3">
                        <label for="niveau_modification" class="form-label">Niveau</label>
                        <select class="form-select" id="niveau_modification" name="niveau_modification" required>
                            <option value="Débutant">Débutant</option>
                            <option value="Initié">Initié</option>
                            <option value="Avancé">Avancé</option>
                            <option value="Expert">Expert</option>
                        </select>
                    </div>
                    <button type="submit" class="btn btn-primary">Modifier</button>
                </form>
                        </br>
                <form id="formSupCompetence" action="?controller=profile&action=modifier_info" method="POST">
                    <input type="hidden" name="id_aep_sup" id="id_aep_sup">
                    <input type="hidden" name="id_categorie_sup" id="id_categorie_sup">
                    <button type="submit" class="btn btn-danger">Supprimer</button>


       
        </div>
       
       
      </div>
    </div>
</div>

<script>

var exampleModal = document.getElementById('exampleModal');
exampleModal.addEventListener('show.bs.modal', function (event) {
    var button = event.relatedTarget;
    var idAep = button.getAttribute('data-bs-id_aep');
    var idCate = button.getAttribute('data-bs-id_categorie');
    var nom = button.getAttribute('data-bs-nom');
    var theme = button.getAttribute('data-bs-theme');
    var niveau = button.getAttribute('data-bs-niveau');

    // Update the modal's content
    var modalTitle = exampleModal.querySelector('.modal-title');
    var inputIdCate = exampleModal.querySelector('#id_categorie_modification');
    var inputIdAep = exampleModal.querySelector('#id_aep_modification');
    var inputNomCompetence = exampleModal.querySelector('#nom_competence_modif');
    var selectTheme = exampleModal.querySelector('#theme_modification');
    var selectNiveau = exampleModal.querySelector('#niveau_modification');
    var inputIdCateSup = exampleModal.querySelector('#id_categorie_sup');
    var inputIdAepSup = exampleModal.querySelector('#id_aep_sup');

    modalTitle.textContent = 'Modifier la compétence: ' + nom;
    inputIdCate.value=idCate;
    inputIdAep.value = idAep;
    inputNomCompetence.value = nom;
    selectTheme.value = theme;
    selectNiveau.value = niveau;
    inputIdCateSup.value = idCate;
    inputIdAepSup.value = idAep;
});


</script>




<script src="Content/script/modifiermonprofilformateur.js"></script>

<?php require "view_end.php"; ?>
