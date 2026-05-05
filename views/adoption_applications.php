<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h1 class="mb-0">Solicitudes de adopción</h1>
            </div>
            <h4>Búsqueda y filtros</h4>
            <form class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">Ordenar por</label>
                    <select class="form-select" name="order">
                        <option value="application_date_asc">Fecha de solicitud ascendente</option>
                        <option value="application_date_desc" selected>Fecha de solicitud descendente</option>
                        <option value="modification_date_asc">Fecha de modificación ascendente</option>
                        <option value="modification_date_desc">Fecha de modificación descendente</option>
                    </select>
                </div>
                <div class="col-12 col-md-7">
                    <label class="form-label">Buscar</label>
                    <input name="search" type="text" class="form-control" placeholder="<?= $_SESSION['user']['role'] == 'administrador'
                        ? 'Buscar por usuario o animal'
                        : 'Buscar por animal' ?>">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn bg-orange-primary w-100">
                        Buscar
                    </button>
                </div>
            </form>
            <section id="adoption-applications-container" class="container-fluid mt-4">
                <?php
                require 'views/lists/adoption_applications_list.php';
                ?>
            </section>
        </section>
    </section>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const adoptionApplicationsContainer = document.getElementById("adoption-applications-container");

        function fetchAdoptionApplications(page = 1) {
            let formData = new FormData(form);
            let params = new URLSearchParams(formData);
            params.append("ajax", "1");
            params.append("page", page);

            fetch("<?= BASE_URL ?>solicitudes_de_adopcion?" + params.toString())
                .then(res => res.text())
                .then(html => {
                    adoptionApplicationsContainer.innerHTML = html;
                });
        }

        adoptionApplicationsContainer.addEventListener('click', function (e) {

            if (e.target.closest('.page-link')) {
                e.preventDefault();
                const page = e.target.closest('.page-link').dataset.page;
                fetchAdoptionApplications(page);
            }

            if (e.target.closest('.delete-btn')) {
                const btn = e.target.closest('.delete-btn');
                const id = btn.dataset.id;

                if (!confirm('¿Seguro que quieres eliminar esta solicitud de adopción?')) {
                    return;
                }

                fetch("<?= BASE_URL ?>eliminar_solicitud_de_adopcion?ajax=1", {
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

                        fetchAdoptionApplications();
                    });
            }
        });

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            fetchAdoptionApplications(1);
        });

        fetchAdoptionApplications();
    });
</script>