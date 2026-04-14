<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <h1>Salas</h1>
            <h4>Búsqueda y filtros</h4>
            <form class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">Ordenar por</label>
                    <select class="form-select" name="order">
                        <option value="code_asc">Código A–Z</option>
                        <option value="code_desc">Código Z–A</option>
                        <option value="name_asc">Nombre A–Z</option>
                        <option value="name_desc">Nombre Z–A</option>
                        <option value="capacity_asc">Capacidad ascendente</option>
                        <option value="capacity_desc">Capacidad descendente</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Activo</label>
                    <select class="form-select" name="active">
                        <option value="">Todos</option>
                        <option value="1">Sí</option>
                        <option value="0">No</option>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label">Buscar</label>
                    <input name="search" type="text" class="form-control">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn bg-orange-primary w-100">
                        Buscar
                    </button>
                </div>
            </form>
            <section id="rooms-container" class="container-fluid mt-4">
                <?php
                require 'views/lists/rooms_list.php';
                ?>
            </section>
        </section>
    </section>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const roomsContainer = document.getElementById("rooms-container");

        function fetchRooms(page = 1) {
            let formData = new FormData(form);
            let params = new URLSearchParams(formData);
            params.append("ajax", "1");
            params.append("page", page);

            fetch("<?= BASE_URL ?>salas?" + params.toString())
                .then(res => res.text())
                .then(html => {
                    roomsContainer.innerHTML = html;

                    roomsContainer.addEventListener('click', function (e) {
                        if (e.target.classList.contains('page-link')) {
                            e.preventDefault();
                            const page = e.target.dataset.page;
                            fetchRooms(page);
                        }
                    });
                });
        }

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            fetchRooms(1);
        });

        fetchRooms();
    });
</script>