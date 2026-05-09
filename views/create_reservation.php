<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>reservas">Reservas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Crear reserva</li>
                </ol>
            </nav>
            <h1>Crear reserva</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>crear_reserva" method="post" class="row g-4">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 g-3">
                                <div class="col">
                                    <label class="form-label fw-bold">Motivo:</label>
                                    <input type="text" name="reason" class="form-control" required maxlength="3000"
                                        placeholder="Escribe el motivo"
                                        value="<?= htmlspecialchars($reservation['reason']) ?>">
                                </div>

                                <input type="hidden" name="animal-id" id="animal-id">

                                <div class="col-12">

                                    <label class="form-label fw-bold">Animal:</label>

                                    <div class="row g-2 align-items-end mb-3">

                                        <div class="col-12 col-md-3">
                                            <label class="form-label">Especie</label>
                                            <select class="form-select" id="species_id" name="species_id">
                                                <option value="">Todas</option>

                                                <?php foreach ($species as $s): ?>
                                                    <option value="<?= $s['id'] ?>">
                                                        <?= htmlspecialchars($s['name']) ?>
                                                    </option>
                                                <?php endforeach; ?>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-3">
                                            <label class="form-label">Género</label>
                                            <select class="form-select" id="gender" name="gender">
                                                <option value="">Todos</option>
                                                <option value="macho">Macho</option>
                                                <option value="hembra">Hembra</option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-3">
                                            <label class="form-label">Ordenar por</label>
                                            <select class="form-select" id="order" name="order">
                                                <option value="name_asc">Nombre A–Z</option>
                                                <option value="name_desc">Nombre Z–A</option>
                                                <option value="breed_asc">Raza A–Z</option>
                                                <option value="breed_desc">Raza Z–A</option>
                                                <option value="birth_day_asc">Edad ascendente</option>
                                                <option value="birth_day_desc">Edad descendente</option>
                                            </select>
                                        </div>

                                        <div class="col-12 col-md-3">
                                            <label class="form-label">Buscar</label>
                                            <input type="text" id="animal-search" class="form-control"
                                                placeholder="Buscar por nombre y raza">
                                        </div>

                                    </div>
                                    <section id="animals-container" class="mt-3">
                                        <?php require 'views/lists/animals_reservation_list.php'; ?>
                                    </section>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Crear</button>
                                <a href="<?= BASE_URL ?>reservas"
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

    document.addEventListener("DOMContentLoaded", function () {

        const animalsContainer = document.getElementById("animals-container");

        const searchInput = document.getElementById("animal-search");
        const speciesSelect = document.getElementById("species_id");
        const genderSelect = document.getElementById("gender");
        const orderSelect = document.getElementById("order");

        let selectedAnimalId = null;

        function fetchAnimals(page = 1) {

            let params = new URLSearchParams();

            params.append("ajax", "1");
            params.append("page", page);
            params.append("search", searchInput.value);
            params.append("species_id", speciesSelect.value);
            params.append("gender", genderSelect.value);
            params.append("order", orderSelect.value);

            fetch("<?= BASE_URL ?>animales_reserva?" + params.toString())
                .then(res => res.text())
                .then(html => {

                    animalsContainer.innerHTML = html;

                    if (selectedAnimalId) {
                        const selectedBtn = animalsContainer.querySelector(
                            `.select-animal-btn[data-id="${selectedAnimalId}"]`
                        );

                        if (selectedBtn) {
                            selectedBtn.classList.remove("bg-orange-primary");
                            selectedBtn.classList.add("btn-success");
                            selectedBtn.textContent = "Seleccionado";
                        }
                    }
                });
        }

        searchInput.addEventListener("input", () => fetchAnimals());

        speciesSelect.addEventListener("change", () => fetchAnimals());

        genderSelect.addEventListener("change", () => fetchAnimals());

        orderSelect.addEventListener("change", () => fetchAnimals());

        animalsContainer.addEventListener("click", function (e) {

            if (e.target.closest(".page-link")) {

                e.preventDefault();

                const page = e.target.closest(".page-link").dataset.page;

                fetchAnimals(page);
            }

            if (e.target.closest(".select-animal-btn")) {

                const btn = e.target.closest(".select-animal-btn");

                selectedAnimalId = btn.dataset.id;

                document.getElementById("animal-id").value = selectedAnimalId;

                document.querySelectorAll(".select-animal-btn").forEach(button => {
                    button.textContent = "Seleccionar";
                });

                const previousBtn = animalsContainer.querySelector(".btn-success");

                if (previousBtn) {
                    previousBtn.classList.remove("btn-success");
                    previousBtn.classList.add("bg-orange-primary");
                    previousBtn.textContent = "Seleccionar";
                }

                selectedAnimalId = btn.dataset.id;
                document.getElementById("animal-id").value = selectedAnimalId;

                btn.classList.remove("bg-orange-primary");
                btn.classList.add("btn-success");
                btn.textContent = "Seleccionado";

                btn.classList.add("btn-success");
            }
        });

        fetchAnimals();
    });

    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        e.preventDefault()

        const reason = form.querySelector('[name="reason"]').value.trim();
        let errors = [];

        if (!reason) {
            errors.push("El motivo es obligatorio.");
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