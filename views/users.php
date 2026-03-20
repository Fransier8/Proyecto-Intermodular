<main class="container-fluid bg-orange-300">
    <section class="row">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <h1>Usuarios</h1>
            <h4>Búsqueda y filtros</h4>
            <form class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">Ordenar por</label>
                    <select class="form-select" name="order">
                        <option value="user_name_asc">Nombre de usuario A–Z</option>
                        <option value="user_name_desc">Nombre de usuario Z–A</option>
                        <option value="name_asc">Nombre A–Z</option>
                        <option value="name_desc">Nombre Z–A</option>
                        <option value="email_asc">Email A–Z</option>
                        <option value="email_desc">Email Z–A</option>
                        <option value="identification_asc">Identificación ascendente</option>
                        <option value="identification_desc">Identificación descendente</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Rol</label>
                    <select class="form-select" name="role">
                        <option value="">Todos</option>
                        <option value="administrador">Administrador</option>
                        <option value="monitor">Monitor</option>
                        <option value="usuario">Usuario</option>
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
                <div class="col-12 col-md-3">
                    <label class="form-label">Buscar</label>
                    <input name="search" type="text" class="form-control">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn bg-orange-primary w-100">
                        Buscar
                    </button>
                </div>
            </form>
            <section id="users-container" class="container-fluid mt-4">
                <?php
                require 'views/lists/users_list.php';
                ?>
            </section>
        </section>
    </section>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function () {
        const form = document.querySelector("form");
        const usersContainer = document.getElementById("users-container");

        function fetchUsers(page = 1) {
            let formData = new FormData(form);
            let params = new URLSearchParams(formData);
            params.append("ajax", "1");
            params.append("page", page);

            fetch("<?= BASE_URL ?>users?" + params.toString())
                .then(res => res.text())
                .then(html => {
                    usersContainer.innerHTML = html;

                    usersContainer.addEventListener('click', function (e) {
                        if (e.target.classList.contains('page-link')) {
                            e.preventDefault();
                            const page = e.target.dataset.page;
                            fetchUsers(page);
                        }
                    });
                });
        }

        form.addEventListener("submit", function (e) {
            e.preventDefault();
            fetchUsers(1);
        });

        fetchUsers();
    });
</script>