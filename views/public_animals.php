<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <h1>Animales</h1>
            <h4>Búsqueda y filtros</h4>
            <form class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">Ordenar por</label>
                    <select class="form-select" name="order">
                        <option value="name_asc">Nombre A–Z</option>
                        <option value="name_desc">Nombre Z–A</option>
                        <option value="breed_asc">Raza A–Z</option>
                        <option value="breed_desc">Raza Z–A</option>
                        <option value="birth_day_asc">Edad ascendente</option>
                        <option value="birth_day_desc">Edad descendente</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Especie</label>
                    <select class="form-select" name="species_id">
                        <option value="">Todas</option>
                        <?php foreach ($species as $s): ?>
                            <option value="<?= $s['id'] ?>">
                                <?= htmlspecialchars($s['name']) ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="sin adoptar">Sin adoptar</option>
                        <option value="reservado">Reservado</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Género</label>
                    <select class="form-select" name="gender">
                        <option value="">Todos</option>
                        <option value="macho">Macho</option>
                        <option value="hembra">Hembra</option>
                    </select>
                </div>
                <div class="col-12 col-md-10">
                    <label class="form-label">Buscar</label>
                    <input name="search" type="text" class="form-control" placeholder="Buscar por nombre y raza">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn bg-orange-primary w-100">
                        Buscar
                    </button>
                </div>
            </form>
            <section id="animals-container" class="container-fluid mt-4">
                <?php
                require 'views/lists/animals_list.php';
                ?>
            </section>
        </section>
    </section>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const animalsContainer = document.getElementById("animals-container");

        function fetchAnimals(page = 1) {
            let formData = new FormData(form);
            let params = new URLSearchParams(formData);
            params.append("ajax", "1");
            params.append("page", page);

            fetch("<?= BASE_URL ?>animales?" + params.toString())
                .then(res => res.text())
                .then(html => {
                    animalsContainer.innerHTML = html;
                });
        }

        animalsContainer.addEventListener('click', function (e) {
            if (e.target.closest('.page-link')) {
                e.preventDefault();
                const page = e.target.closest('.page-link').dataset.page;
                fetchAnimals(page);
            }
        });

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            fetchAnimals(1);
        });

        fetchAnimals();
    });
</script>