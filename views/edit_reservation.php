<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>reservas">Reservas</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Modificar reserva</li>
                </ol>
            </nav>
            <h1>Modificar reserva</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>modificar_reserva" method="post" class="row g-4">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 g-3">
                                <div class="col">
                                    <label class="form-label fw-bold">Motivo:</label>
                                    <textarea type="text" name="reason" class="form-control" required maxlength="3000"
                                        placeholder="Escribe el motivo"
                                        ><?= htmlspecialchars($reservation['reason']) ?></textarea>
                                </div>

                                <div class="col">
                                    <label class="form-label fw-bold">Acompañantes:</label>
                                    <input type="number" name="companions" class="form-control" required maxlength="11"
                                        placeholder="Escribe los acompañantes" min="0"
                                        value="<?= htmlspecialchars($reservation['companions']) ?>">
                                </div>

                                <input type="hidden" name="reservation-id" value="<?= htmlspecialchars($reservation['id']) ?>">

                                <input type="hidden" name="user-id" id="user-id"
                                    value="<?= htmlspecialchars($reservation['user_id'] ?? '') ?>">

                                <div class="col-12">

                                    <label class="form-label fw-bold">Usuario:</label>

                                    <div class="row g-2 align-items-end mb-3">

                                        <div class="col-12 col-md-3">
                                            <label class="form-label">Ordenar por</label>
                                            <select class="form-select" id="user-order" name="user-order">
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
                                        <div class="col-12 col-md-9">
                                            <label class="form-label">Buscar</label>
                                            <input id="user-search" name="user-search" type="text" class="form-control"
                                                placeholder="Buscar usuario">
                                        </div>

                                    </div>
                                    <section id="users-container" class="mt-3">
                                        <?php require 'views/lists/users_reservation_list.php'; ?>
                                    </section>
                                </div>

                                <input type="hidden" name="animal-id" id="animal-id"
                                    value="<?= htmlspecialchars($reservation['animal_id'] ?? '') ?>">

                                <div class="col-12">

                                    <label class="form-label fw-bold">Animal:</label>

                                    <div class="row g-2 align-items-end mb-3">

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
                                            <label class="form-label">Buscar</label>
                                            <input type="text" id="animal-search" name="animal-search"
                                                class="form-control" placeholder="Buscar por nombre y raza">
                                        </div>

                                    </div>
                                    <section id="animals-container" class="mt-3">
                                        <?php require 'views/lists/animals_reservation_list.php'; ?>
                                    </section>
                                </div>

                                <input type="hidden" name="room-id" id="room-id"
                                    value="<?= htmlspecialchars($reservation['room_id'] ?? '') ?>">

                                <div class="col-12">

                                    <label class="form-label fw-bold">Sala:</label>

                                    <div class="row g-2 align-items-end mb-3">

                                        <div class="col-12 col-md-3">
                                            <label class="form-label">Ordenar por</label>
                                            <select class="form-select" id="room-order" name="room-order">
                                                <option value="code_asc">Código A–Z</option>
                                                <option value="code_desc">Código Z–A</option>
                                                <option value="name_asc">Nombre A–Z</option>
                                                <option value="name_desc">Nombre Z–A</option>
                                                <option value="capacity_asc">Capacidad ascendente</option>
                                                <option value="capacity_desc">Capacidad descendente</option>
                                            </select>
                                        </div>
                                        <div class="col-12 col-md-9">
                                            <label class="form-label">Buscar</label>
                                            <input id="room-search" name="room-search" type="text" class="form-control"
                                                placeholder="Buscar sala">
                                        </div>

                                    </div>
                                    <section id="rooms-container" class="mt-3">
                                        <?php require 'views/lists/rooms_reservation_list.php'; ?>
                                    </section>
                                </div>

                                <input type="hidden" name="monitor-id" id="monitor-id"
                                    value="<?= htmlspecialchars($reservation['monitor_id'] ?? '') ?>">

                                <div class="col-12">

                                    <label class="form-label fw-bold">Monitor:</label>

                                    <div class="row g-2 align-items-end mb-3">

                                        <div class="col-12 col-md-3">
                                            <label class="form-label">Ordenar por</label>
                                            <select class="form-select" id="monitor-order" name="monitor-order">
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
                                        <div class="col-12 col-md-9">
                                            <label class="form-label">Buscar</label>
                                            <input id="monitor-search" name="monitor-search" type="text"
                                                class="form-control" placeholder="Buscar usuario">
                                        </div>

                                    </div>
                                    <section id="monitors-container" class="mt-3">
                                        <?php require 'views/lists/monitors_reservation_list.php'; ?>
                                    </section>
                                </div>

                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Seleccionar fecha y
                                    hora</button>
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

        const usersContainer = document.getElementById("users-container");

        const userSearchInput = document.getElementById("user-search");
        const userOrderSelect = document.getElementById("user-order");

        let selectedUserId = "<?= $reservation['user_id'] ?? '' ?>";

        function fetchUsers(page = 1) {

            let params = new URLSearchParams();

            params.append("ajax", "1");
            params.append("page", page);
            params.append("search", userSearchInput.value);
            params.append("order", userOrderSelect.value);

            fetch("<?= BASE_URL ?>usuarios_reserva?" + params.toString())
                .then(res => res.text())
                .then(html => {

                    usersContainer.innerHTML = html;

                    if (selectedUserId) {
                        const selectedBtn = usersContainer.querySelector(
                            `.select-user-btn[data-id="${selectedUserId}"]`
                        );

                        if (selectedBtn) {
                            selectedBtn.classList.remove("bg-orange-primary");
                            selectedBtn.classList.add("btn-success");
                            selectedBtn.textContent = "Seleccionado";
                        }
                    }
                });
        }

        userSearchInput.addEventListener("input", () => fetchUsers());

        userOrderSelect.addEventListener("change", () => fetchUsers());

        usersContainer.addEventListener("click", function (e) {

            if (e.target.closest(".page-link")) {

                e.preventDefault();

                const page = e.target.closest(".page-link").dataset.page;

                fetchUsers(page);
            }

            if (e.target.closest(".select-user-btn")) {

                const btn = e.target.closest(".select-user-btn");

                selectedUserId = btn.dataset.id;

                document.getElementById("user-id").value = selectedUserId;

                document.querySelectorAll(".select-user-btn").forEach(button => {
                    button.textContent = "Seleccionar";
                });

                const previousBtn = usersContainer.querySelector(".btn-success");

                if (previousBtn) {
                    previousBtn.classList.remove("btn-success");
                    previousBtn.classList.add("bg-orange-primary");
                    previousBtn.textContent = "Seleccionar";
                }

                selectedUserId = btn.dataset.id;
                document.getElementById("user-id").value = selectedUserId;

                btn.classList.remove("bg-orange-primary");
                btn.classList.add("btn-success");
                btn.textContent = "Seleccionado";

                btn.classList.add("btn-success");
            }
        });

        fetchUsers();

        const animalsContainer = document.getElementById("animals-container");

        const searchInput = document.getElementById("animal-search");
        const speciesSelect = document.getElementById("species_id");
        const genderSelect = document.getElementById("gender");
        const orderSelect = document.getElementById("order");

        let selectedAnimalId = "<?= $reservation['animal_id'] ?? '' ?>";

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

        const roomsContainer = document.getElementById("rooms-container");

        const roomSearchInput = document.getElementById("room-search");
        const roomOrderSelect = document.getElementById("room-order");

        let selectedRoomId = "<?= $reservation['room_id'] ?? '' ?>";

        function fetchRooms(page = 1) {

            let params = new URLSearchParams();

            params.append("ajax", "1");
            params.append("page", page);
            params.append("search", roomSearchInput.value);
            params.append("order", roomOrderSelect.value);

            fetch("<?= BASE_URL ?>salas_reserva?" + params.toString())
                .then(res => res.text())
                .then(html => {

                    roomsContainer.innerHTML = html;

                    if (selectedRoomId) {
                        const selectedBtn = roomsContainer.querySelector(
                            `.select-room-btn[data-id="${selectedRoomId}"]`
                        );

                        if (selectedBtn) {
                            selectedBtn.classList.remove("bg-orange-primary");
                            selectedBtn.classList.add("btn-success");
                            selectedBtn.textContent = "Seleccionado";
                        }
                    }
                });
        }

        roomSearchInput.addEventListener("input", () => fetchRooms());

        roomOrderSelect.addEventListener("change", () => fetchRooms());

        roomsContainer.addEventListener("click", function (e) {

            if (e.target.closest(".page-link")) {

                e.preventDefault();

                const page = e.target.closest(".page-link").dataset.page;

                fetchRooms(page);
            }

            if (e.target.closest(".select-room-btn")) {

                const btn = e.target.closest(".select-room-btn");

                selectedRoomId = btn.dataset.id;

                document.getElementById("room-id").value = selectedRoomId;

                document.querySelectorAll(".select-room-btn").forEach(button => {
                    button.textContent = "Seleccionar";
                });

                const previousBtn = roomsContainer.querySelector(".btn-success");

                if (previousBtn) {
                    previousBtn.classList.remove("btn-success");
                    previousBtn.classList.add("bg-orange-primary");
                    previousBtn.textContent = "Seleccionar";
                }

                selectedRoomId = btn.dataset.id;
                document.getElementById("room-id").value = selectedRoomId;

                btn.classList.remove("bg-orange-primary");
                btn.classList.add("btn-success");
                btn.textContent = "Seleccionado";

                btn.classList.add("btn-success");
            }
        });

        fetchRooms();

        const monitorsContainer = document.getElementById("monitors-container");

        const monitorSearchInput = document.getElementById("monitor-search");
        const monitorOrderSelect = document.getElementById("monitor-order");

        let selectedMonitorId = "<?= $reservation['monitor_id'] ?? '' ?>";

        function fetchMonitors(page = 1) {

            let params = new URLSearchParams();

            params.append("ajax", "1");
            params.append("page", page);
            params.append("search", monitorSearchInput.value);
            params.append("order", monitorOrderSelect.value);

            fetch("<?= BASE_URL ?>monitores_reserva?" + params.toString())
                .then(res => res.text())
                .then(html => {

                    monitorsContainer.innerHTML = html;

                    if (selectedMonitorId) {
                        const selectedBtn = monitorsContainer.querySelector(
                            `.select-monitor-btn[data-id="${selectedMonitorId}"]`
                        );

                        if (selectedBtn) {
                            selectedBtn.classList.remove("bg-orange-primary");
                            selectedBtn.classList.add("btn-success");
                            selectedBtn.textContent = "Seleccionado";
                        }
                    }
                });
        }

        monitorSearchInput.addEventListener("input", () => fetchMonitors());

        monitorOrderSelect.addEventListener("change", () => fetchMonitors());

        monitorsContainer.addEventListener("click", function (e) {

            if (e.target.closest(".page-link")) {

                e.preventDefault();

                const page = e.target.closest(".page-link").dataset.page;

                fetchMonitors(page);
            }

            if (e.target.closest(".select-monitor-btn")) {

                const btn = e.target.closest(".select-monitor-btn");

                selectedMonitorId = btn.dataset.id;

                document.getElementById("monitor-id").value = selectedMonitorId;

                document.querySelectorAll(".select-monitor-btn").forEach(button => {
                    button.textContent = "Seleccionar";
                });

                const previousBtn = monitorsContainer.querySelector(".btn-success");

                if (previousBtn) {
                    previousBtn.classList.remove("btn-success");
                    previousBtn.classList.add("bg-orange-primary");
                    previousBtn.textContent = "Seleccionar";
                }

                selectedMonitorId = btn.dataset.id;
                document.getElementById("monitor-id").value = selectedMonitorId;

                btn.classList.remove("bg-orange-primary");
                btn.classList.add("btn-success");
                btn.textContent = "Seleccionado";

                btn.classList.add("btn-success");
            }
        });

        fetchMonitors();

    });

    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        e.preventDefault()

        const reason = form.querySelector('[name="reason"]').value.trim();
        const companions = form.querySelector('[name="companions"]').value.trim();
        const userId = document.getElementById("user-id").value.trim();
        const animalId = document.getElementById("animal-id").value.trim();
        const roomId = document.getElementById("room-id").value.trim();
        const monitorId = document.getElementById("monitor-id").value.trim();

        let errors = [];

        if (!reason) {
            errors.push("El motivo es obligatorio.");
        }

        if (companions == "" || companions === null || isNaN(companions) || companions < 0) {
            errors.push("Los acompañantes deben ser 0 o más.");
        }

        if (!userId) {
            errors.push("Debes seleccionar un usuario.");
        }

        if (!animalId) {
            errors.push("Debes seleccionar un animal.");
        }

        if (!roomId) {
            errors.push("Debes seleccionar una sala.");
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