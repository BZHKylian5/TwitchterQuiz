    <?php 
        require_once "../config.php";

        $stmt = $conn -> prepare("SELECT * FROM categorie");
        $stmt -> execute();
        $categ = $stmt -> fetchAll(PDO::FETCH_ASSOC);

        if($_SERVER['REQUEST_METHOD'] === 'POST'){
            if($_POST['action'] == "creerCateg"){
                $nomCateg = $_POST['nomCateg'];
                $descriptionCateg = $_POST['descriptionCateg'];

                $stmt = $conn -> prepare("INSERT INTO categorie(nomCateg, description) VALUE ('$nomCateg', '$descriptionCateg')");
                $stmt->execute();
            }
        }
    ?>

    <!DOCTYPE html>
    <html lang="en">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Création de Question</title>
        <link rel="stylesheet" href="/asset/css/style.css">
    </head>
    <body>
    <?php require_once "../componant/header.php"; ?>
        <main>
            <section id="categ">
                <!-- Bouton affichant le formulaire de création de categorie quand cliquer !-->
                <div class="btnCreate" id="btnCreateCateg" onclick="ajoutCateg()">Ajouter une catégorie</div>
            
                <form action="index.php" id="formCreateCateg" method="post" class="hidden">
                    <div>
                        <label for="nomCateg" >Nom de la catégorie :</label>
                        <input type='text' name="nomCateg" id="nomCateg" required >
                    </div>
                    <div>
                        <label for="descriptionCateg" >Description de la catégorie :</label>
                        <textarea name="descriptionCateg" id="descriptionCateg" required></textarea>
                    </div>
                    <div>
                        <label id="btnAjoutPhoto" for="ajoutPhoto" class="classAjouterPhotos">Ajouter des Photos</label>
                        <input
                            type="file"
                            id="ajoutPhoto"
                            class="hidden"
                            name="images[]"
                            accept="image/PNG, image/JPG, image/JPEG, image/WEBP, image/GIF"
                            multiple
                            onchange="handleFiles(this)" />
                        <div id="afficheImagesAvis"></div>

                    </div>
            
                    <input type='hidden' name="action" value="creerCateg">
            
                    <input type="submit" placeholder="Ajouter">
                </form>
            </section>

        </main>

        <script>
            const btnCreateCateg = document.getElementById("btnCreateCateg");
            const formCreateCateg = document.getElementById("formCreateCateg");

            const uniqueId = generateUniqueId();
            const maxImages = 3; // Nombre maximum d'images autorisé
            let nbImageTotaleInAvis = 0; // Compteur global

            function ajoutCateg(){
                formCreateCateg.classList.toggle("hidden")
            }

            function handleFiles(inputElement) {
                const files = inputElement.files;
                const formData = new FormData();

                // Vérifie si l'ajout dépasse la limite maximale
                if (nbImageTotaleInAvis + files.length > maxImages) {
                    alert(`Vous ne pouvez ajouter plus ajouter d'images.`);
                    inputElement.value = ""; // Réinitialise le champ file
                    return;
                }

                // Ajoute chaque fichier au FormData pour l'upload
                for (let i = 0; i < files.length; i++) {
                    formData.append("images[]", files[i]);
                }

                // Ajoute l'ID unique pour le dossier temporaire
                formData.append("unique_id", uniqueId);

                // Envoie les fichiers au serveur via une requête AJAX
                fetch("../uploadImageAvisTemp/upload_temp_files.php", {
                        method: "POST",
                        body: formData,
                    })
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            nbImageTotaleInAvis += files.length; // Met à jour le compteur
                            displayUploadedFiles(uniqueId); // Met à jour l'affichage des images
                        } else {
                            alert("Erreur lors de l'upload : " + data.message);
                            inputElement.value = ""; // Réinitialise le champ en cas d'échec
                        }
                    })
                    .catch((error) => {
                        console.error("Erreur lors de la requête :", error);
                        alert("Une erreur est survenue pendant l'upload.");
                        inputElement.value = ""; // Réinitialise le champ en cas d'erreur
                    });
            }

            function displayUploadedFiles(uniqueId) {
                const afficheImages = document.getElementById("afficheImagesAvis");
                afficheImages.innerHTML = ""; // Réinitialise l'affichage

                fetch(`../uploadImageAvisTemp/list_temp_files.php?unique_id=${uniqueId}`)
                    .then((response) => response.json())
                    .then((data) => {
                        if (data.success) {
                            data.files.forEach((fileUrl) => {
                                const div = document.createElement("div");
                                div.classList.add("image-container");
                                div.style.position = "relative";

                                const img = document.createElement("img");
                                img.src = fileUrl;
                                img.alt = "Image uploaded";
                                img.style.width = "100px";
                                img.style.margin = "10px";

                                const deleteIcon = document.createElement("img");
                                deleteIcon.src = "asset/img/icone/croix.png";
                                deleteIcon.alt = "Supprimer";
                                deleteIcon.style.width = "20px";
                                deleteIcon.style.height = "20px";
                                deleteIcon.style.position = "absolute";
                                deleteIcon.style.top = "5px";
                                deleteIcon.style.right = "5px";
                                deleteIcon.style.cursor = "pointer";

                                deleteIcon.addEventListener("click", () => {
                                    deleteFile(fileUrl, uniqueId, div);
                                });

                                div.appendChild(img);
                                div.appendChild(deleteIcon);
                                afficheImages.appendChild(div);
                            });
                        } else {
                            alert("Erreur lors de la récupération des fichiers : " + data.message);
                        }
                    })
                    .catch((error) => {
                        console.error("Erreur lors de la récupération :", error);
                        alert("Une erreur est survenue pendant la récupération des fichiers.");
                    });
            }

            function deleteFile(fileUrl, uniqueId, imageContainer) {
                const formData = new FormData();
                formData.append("fileUrl", fileUrl); // L'URL du fichier à supprimer
                formData.append("unique_id", uniqueId); // L'ID unique pour le dossier temporaire

                fetch("../uploadImageAvisTemp/delete_temp_files.php", {
                        method: "POST",
                        body: formData
                    })
                    .then(response => response.json())
                    .then(data => {
                        if (data.success) {
                            nbImageTotaleInAvis--; // Décrémente le compteur
                            imageContainer.remove(); // Supprime l'image du DOM
                        } else {
                            alert("Erreur lors de la suppression de l'image : " + data.message);
                        }
                    })
                    .catch(error => {
                        console.error("Erreur lors de la suppression :", error);
                        alert("Une erreur est survenue pendant la suppression.");
                    });
            }

            function generateUniqueId() {
                return "temp_" + Math.random().toString(36).substr(2, 9);
            }
        </script>
        
    </body>
    </html>