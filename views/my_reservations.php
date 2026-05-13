<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h1 class="mb-0">Mis reservas</h1>
            </div>
            <h4>Búsqueda y filtros</h4>
            <form class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">Ordenar por</label>
                    <select class="form-select" name="order">
                        <option value="date_asc">Fecha acendente</option>
                        <option value="date_desc" selected>Fecha descendente</option>
                        <option value="companions_asc">Acompañantes acendente</option>
                        <option value="companions_desc">Acompañantes descendente</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="cancelada">Cancelada</option>
                        <option value="denegada">Denegada</option>
                        <option value="aceptada">Aceptada</option>
                    </select>
                </div>
                <div class="col-12 col-md-5">
                    <label class="form-label">Buscar</label>
                    <input name="search" type="text" class="form-control" placeholder="<?= $_SESSION['user']['role'] == 'administrador'
                        ? 'Buscar por usuario, animal, sala o monitor'
                        : 'Buscar por animal, sala o monitor' ?>">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn bg-orange-primary w-100">
                        Buscar
                    </button>
                </div>
            </form>
            <section id="reservations-container" class="container-fluid mt-4">
                <?php
                require 'views/lists/reservations_list.php';
                ?>
            </section>
        </section>
    </section>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const reservationsContainer = document.getElementById("reservations-container");

        function fetchReservations(page = 1) {
            let formData = new FormData(form);
            let params = new URLSearchParams(formData);
            params.append("ajax", "1");
            params.append("page", page);

            fetch("<?= BASE_URL ?>mis_reservas?" + params.toString())
                .then(res => res.text())
                .then(html => {
                    reservationsContainer.innerHTML = html;
                });
        }

        reservationsContainer.addEventListener('click', function (e) {

            if (e.target.closest('.page-link')) {
                e.preventDefault();
                const page = e.target.closest('.page-link').dataset.page;
                fetchReservations(page);
            }
        });

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            fetchReservations(1);
        });

        fetchReservations();
    });
</script>