<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <div
                class="d-flex flex-column flex-lg-row align-items-start align-items-lg-center justify-content-between gap-3 mb-2">
                <h1 class="mb-0">Mis reservas</h1>
                <?php if ($_SESSION['user']['role'] == "usuario"): ?>
                    <div class="d-flex flex-wrap gap-2">
                        <a href="<?= BASE_URL ?>descargar_reservas_usuario"
                            class="btn bg-orange-primary rounded-pill btn-lg px-4">
                            Descargar reservas
                        </a>
                        <a href="<?= BASE_URL ?>solicitar_reserva" class="btn bg-orange-primary rounded-pill btn-lg px-4">
                            Solicitar reserva
                        </a>
                    </div>
                <?php else: ?>
                    <a href="<?= BASE_URL ?>descargar_reservas_monitor"
                        class="btn bg-orange-primary rounded-pill btn-lg px-4">
                        Descargar reservas
                    </a>
                <?php endif; ?>
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
                <div class="col-12 col-md-3">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="status">
                        <option value="">Todos</option>
                        <option value="pendiente">Pendiente</option>
                        <option value="cancelada">Cancelada</option>
                        <option value="denegada">Denegada</option>
                        <option value="aceptada">Aceptada</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Desde</label>
                    <input type="date" name="date_from" class="form-control">
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Hasta</label>
                    <input type="date" name="date_to" class="form-control">
                </div>
                <div class="col-12 col-md-10">
                    <label class="form-label">Buscar</label>
                    <input name="search" type="text" class="form-control" placeholder="<?= $_SESSION['user']['role'] == 'monitor'
                        ? 'Buscar por usuario, animal o sala'
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

            if (e.target.closest('.cancel-btn')) {
                const btn = e.target.closest('.cancel-btn');
                const id = btn.dataset.id;

                if (!confirm('¿Seguro que quieres cancelar esta reserva?')) {
                    return;
                }

                fetch("<?= BASE_URL ?>cancelar_reserva?ajax=1", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: "id=" + id
                })
                    .then(res => res.json())
                    .then(data => {

                        if (!data.success) {
                            alert("Error: " + data.message);
                            return;
                        }

                        fetchReservations();
                    });
            }

            if (e.target.closest('.accept-btn')) {
                const btn = e.target.closest('.accept-btn');
                const id = btn.dataset.id;

                if (!confirm('¿Seguro que quieres aceptar esta reserva?')) {
                    return;
                }

                fetch("<?= BASE_URL ?>aceptar_reserva?ajax=1", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: "id=" + id
                })
                    .then(res => res.json())
                    .then(data => {

                        if (!data.success) {
                            alert("Error: " + data.message);
                            return;
                        }

                        fetchReservations();
                    });
            }

            if (e.target.closest('.assign-monitor-btn')) {

                const btn = e.target.closest('.assign-monitor-btn');
                const id = btn.dataset.id;
                const action = btn.dataset.action;

                const message = action === "take"
                    ? "¿Quieres tomar esta reserva?"
                    : "¿Quieres dejar esta reserva?";

                if (!confirm(message)) return;

                fetch("<?= BASE_URL ?>asignar_monitor_reserva", {
                    method: "POST",
                    headers: {
                        'Content-Type': 'application/x-www-form-urlencoded'
                    },
                    body: "id=" + id + "&action=" + action
                })
                    .then(() => {
                        fetchReservations();
                    });
            }

        });

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            fetchReservations(1);
        });

        fetchReservations();
    });
</script>