<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h1 class="mb-0">Animales</h1>
                <a href="<?= BASE_URL ?>crear_animal" class="btn bg-orange-primary rounded-pill btn-lg px-4">
                    Crear animal
                </a>
            </div>
            <h4>Búsqueda y filtros</h4>
            <form class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">Ordenar por</label>
                    <select class="form-select" name="ordenar">
                        <option value="jovenes">Más jóvenes</option>
                        <option value="viejos">Más viejos</option>
                        <option value="nombre_asc">Nombre A–Z</option>
                        <option value="nombre_desc">Nombre Z–A</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Especie</label>
                    <select class="form-select" name="especie">
                        <option value="">Todas</option>
                        <option value="perro">Perro</option>
                        <option value="gato">Gato</option>
                        <option value="conejo">Conejo</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="">Todos</option>
                        <option value="sin_adoptar">Sin adoptar</option>
                        <option value="adoptado">Adoptado</option>
                        <option value="apadrinado">Apadrinado</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Buscar</label>
                    <input name="buscar" type="text" class="form-control">
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

                    animalsContainer.addEventListener('click', function (e) {
                        if (e.target.classList.contains('page-link')) {
                            e.preventDefault();
                            const page = e.target.dataset.page;
                            fetchAnimals(page);
                        }
                    });
                });
        }

        animalsContainer.addEventListener('click', function (e) {

            if (e.target.closest('.page-link')) {
                e.preventDefault();
                const page = e.target.closest('.page-link').dataset.page;
                fetchAnimals(page);
            }

            if (e.target.closest('.change-status-btn')) {
                const btn = e.target.closest('.change-status-btn');
                const id = btn.dataset.id;
                const active = btn.dataset.active;

                if (!confirm('¿Seguro que quieres cambiar el estado de este animal?')) {
                    return;
                }

                fetch("<?= BASE_URL ?>cambiar_estado_animal", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: "id=" + id + "&active=" + active
                })
                    .then(() => {
                        fetchAnimals();
                    });
            }
        });

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            fetchAnimals(1);
        });

        fetchAnimals();
    });
</script>