<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>animales">Animales</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>animal/<?= $animal['id'] ?>">Animal</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Modificar animal</li>
                </ol>
            </nav>
            <h1>Modificar animal</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>modificar_animal" method="post" class="row g-4"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $animal['id'] ?>">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <div class="col">
                                    <label class="form-label fw-bold">Nombre:</label>
                                    <input type="text" name="name" class="form-control" required maxlength="300"
                                        placeholder="Escribe el nombre"
                                        value="<?= htmlspecialchars($animal['name']) ?>">
                                </div>

                                <div class="col">
                                    <div class="form-check mt-3">
                                        <input type="checkbox" class="form-check-input" name="active" value="1"
                                            <?= $animal['active'] ? 'checked' : '' ?>
                                            onclick="return confirm('¿Seguro que quieres cambiar el estado de este animal?')">
                                        <label class="form-check-label">Activo</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label fw-bold">Raza:</label>
                                    <input type="text" name="breed" class="form-control" maxlength="300"
                                        placeholder="Escribe la raza" value="<?= htmlspecialchars($animal['breed']) ?>">
                                </div>

                                <div class="col">
                                    <label class="form-label fw-bold">Especie:</label>
                                    <select name="species_id" class="form-select" required>
                                        <?php foreach ($species as $s): ?>
                                            <option value="<?= $s['id'] ?>" <?= $animal['species_id'] == $s['id'] ? 'selected' : '' ?>>
                                                <?= htmlspecialchars($s['name']) ?>
                                            </option>
                                        <?php endforeach; ?>
                                    </select>
                                </div>

                                <div class="col">
                                    <label class="form-label fw-bold">Estado:</label>
                                    <select name="status" class="form-select" required>
                                        <option value="sin adoptar" <?= $animal['status'] == 'sin adoptar' ? 'selected' : '' ?>>Sin adoptar
                                        </option>
                                        <option value="reservado" <?= $animal['status'] == 'reservado' ? 'selected' : '' ?>>
                                            Reservado</option>
                                        <option value="adoptado" <?= $animal['status'] == 'adoptado' ? 'selected' : '' ?>>
                                            Adoptado</option>
                                    </select>
                                </div>

                                <div class="col">
                                    <label class="form-label fw-bold">Género:</label>
                                    <select name="gender" class="form-select" required>
                                        <option value="macho" <?= $animal['gender'] == 'macho' ? 'selected' : '' ?>>Macho
                                        </option>
                                        <option value="hembra" <?= $animal['gender'] == 'hembra' ? 'selected' : '' ?>>
                                            Hembra</option>
                                    </select>
                                </div>

                                <div class="col">
                                    <label class="form-label fw-bold">Fecha de nacimiento:</label>
                                    <input type="date" name="birth_day" class="form-control"
                                        value="<?= htmlspecialchars($animal['birth_day']) ?>">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Descripción (opcional):</label>
                                    <textarea name="description" class="form-control"
                                        placeholder="Escribe la descripción"
                                        maxlength="2000"><?= htmlspecialchars($animal['description']) ?></textarea>
                                </div>

                                <div class="col">
                                    <label class="fw-bold">Foto:</label>
                                    <input type="file" name="photo" class="form-control">
                                </div>

                                <div class="col-12 col-md-4">
                                    <img id="preview" src="<?=
                                        !empty($animal['photo'])
                                        ? BASE_URL . "uploads/animals/" . htmlspecialchars($animal['photo'])
                                        : BASE_URL . "img/placeholder.webp";
                                    ?>" class="img-fluid rounded w-100"
                                        style="aspect-ratio: 4/3; object-fit: cover;">
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Guardar</button>
                                <a href="<?= BASE_URL ?>animal/<?= $animal['id'] ?>"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Cancelar</a>
                            </div>
                        </div>
                    </form>
                    <div id="errorBox" class="<?= !empty($errors) ? 'alert alert-danger mt-3' : '' ?>">
                        <ul id="errorsList">
                            <?php foreach ($errors as $error): ?>
                                <li>
                                    <?= htmlspecialchars($error) ?>
                                </li>
                            <?php endforeach; ?>
                        </ul>
                    </div>
                </div>
            </article>
        </section>
    </section>
</main>
<script>

    const form = document.querySelector("form");

    const photoInput = document.querySelector('[name="photo"]');
    const preview = document.getElementById('preview');

    if (photoInput && preview) {
        photoInput.addEventListener('change', function () {
            const file = this.files[0];
            if (file) {
                preview.src = URL.createObjectURL(file);
            }
        });
    }

    form.addEventListener("submit", function (e) {
        e.preventDefault()

        const name = form.querySelector('[name="name"]').value.trim();
        const speciesId = form.querySelector('[name="species_id"]').value.trim();
        const status = form.querySelector('[name="status"]').value.trim();
        const gender = form.querySelector('[name="gender"]').value.trim();

        let errors = [];

        if (!name) {
            errors.push("El nombre es obligatorio.");
        }

        if (!speciesId) {
            errors.push("Selecciona una especie.");
        }

        if (status != "sin adoptar" && status != "reservado" && status != "adoptado") {
            errors.push("Selecciona un estado.");
        }

        if (gender != "macho" && gender != "hembra") {
            errors.push("Selecciona un género.");
        }

        const allowedTypes = ["image/png", "image/jpeg", "image/webp"];

        const file = photoInput.files[0];

        if (file) {
            const ext = file.name.split('.').pop().toLowerCase();

            const validExt = ["png", "jpg", "jpeg", "webp"].includes(ext);

            if (!allowedTypes.includes(file.type) && !validExt) {
                errors.push("Solo PNG, JPG, JPEG o WEBP.");
            }
        }

        const errorBox = document.getElementById("errorBox");
        const errorList = document.getElementById("errorsList");
        errorBox.style.display = errors.length ? "block" : "none";
        if (errors.length > 0) {
            errorBox.className = "mt-3 alert alert-danger";
            errorList.innerHTML = errors.map(e => `<li>${e}</li>`).join("");
            return;
        } else {
            errorBox.classList.remove("alert", "alert-danger");
            errorList.innerHTML = "";
        }

        form.submit();
    });
</script>