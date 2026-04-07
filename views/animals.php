<main class="container-fluid bg-orange-300">
    <section class="row">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <h1>Animales</h1>
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

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            fetchAnimals(1);
        });

        fetchAnimals();
    });
</script>