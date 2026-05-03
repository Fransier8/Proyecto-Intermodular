<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h1 class="mb-0">Apadrinamientos</h1>
            </div>
            <h4>Búsqueda y filtros</h4>
            <form class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">Ordenar por</label>
                    <select class="form-select" name="order">
                        <option value="date_asc">Fecha ascendente</option>
                        <option value="date_desc">Fecha descendente</option>
                        <option value="amount_asc">Importe ascendente</option>
                        <option value="amount_desc">Importe descendente</option>
                    </select>
                </div>
                <div class="col-12 col-md-7">
                    <label class="form-label">Buscar</label>
                    <input name="search" type="text" class="form-control" placeholder="Buscar por usuario o animal">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn bg-orange-primary w-100">
                        Buscar
                    </button>
                </div>
            </form>
            <section id="sponsorships-container" class="container-fluid mt-4">
                <?php
                require 'views/lists/sponsorships_list.php';
                ?>
            </section>
        </section>
    </section>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const sponsorshipsContainer = document.getElementById("sponsorships-container");

        function fetchSponsorships(page = 1) {
            let formData = new FormData(form);
            let params = new URLSearchParams(formData);
            params.append("ajax", "1");
            params.append("page", page);

            fetch("<?= BASE_URL ?>apadrinamientos?" + params.toString())
                .then(res => res.text())
                .then(html => {
                    sponsorshipsContainer.innerHTML = html;
                });
        }

        sponsorshipsContainer.addEventListener('click', function (e) {

            if (e.target.closest('.page-link')) {
                e.preventDefault();
                const page = e.target.closest('.page-link').dataset.page;
                fetchSponsorships(page);
            }
        });

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            fetchSponsorships(1);
        });

        fetchSponsorships();
    });
</script>