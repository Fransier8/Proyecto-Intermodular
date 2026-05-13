<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>reservas">Reservas</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>crear_reserva">Crear reserva</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Seleccionar fecha reserva</li>
                </ol>
            </nav>
            <h1>Seleccionar fecha de la reserva</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>crear_reserva" method="post" class="row g-4">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 g-3">
                                <input type="hidden" name="room-id" id="room-id"
                                    value="<?= htmlspecialchars($reservation['room_id'] ?? '1') ?>">
                            </div>

                            <div id="calendar"></div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit" class="btn bg-orange-primary border-dark border-1 flex-fill">Crear
                                    reserva</button>
                                <a href="<?= BASE_URL ?>crear_reserva"
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

        let currentDate = new Date();
        const calendarContainer = document.getElementById("calendar");
        const roomId = document.getElementById("room-id").value;
        let globalData = [];

        loadCalendar();

        function loadCalendar() {

            fetch(
                "<?= BASE_URL ?>disponibilidad_calendario" +
                "?ajax=1&room_id=" + roomId +
                "&month=" + (currentDate.getMonth() + 1) +
                "&year=" + currentDate.getFullYear()
            )
                .then(res => res.json())
                .then(data => {
                    globalData = data;
                    renderCalendar(data);
                });
        }

        function renderCalendar(data) {

            calendarContainer.innerHTML = "";

            // CONTROLES
            const controls = document.createElement("div");
            controls.className = "d-flex justify-content-between mb-3";

            const prev = document.createElement("button");
            prev.textContent = "← Mes anterior";
            prev.className = "btn btn-outline-primary";

            const next = document.createElement("button");
            next.textContent = "Mes siguiente";
            next.className = "btn btn-outline-primary";

            prev.onclick = () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                loadCalendar();
            };

            next.onclick = () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                loadCalendar();
            };

            controls.appendChild(prev);
            controls.appendChild(next);

            calendarContainer.appendChild(controls);

            // CALENDARIO
            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);

            const firstWeekDay = (firstDay.getDay() + 6) % 7;
            const totalDays = lastDay.getDate();

            const monthNames = [
                "Enero", "Febrero", "Marzo", "Abril",
                "Mayo", "Junio", "Julio", "Agosto",
                "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ];

            const weekDays = ["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];

            const title = document.createElement("h2");
            title.textContent = `${monthNames[month]} ${year}`;
            title.className = "mb-4";

            calendarContainer.appendChild(title);

            const grid = document.createElement("div");
            grid.className = "calendar-grid";

            weekDays.forEach(d => {
                const h = document.createElement("div");
                h.className = "calendar-header";
                h.textContent = d;
                grid.appendChild(h);
            });

            for (let i = 0; i < firstWeekDay; i++) {
                const empty = document.createElement("div");
                empty.className = "calendar-cell empty";
                grid.appendChild(empty);
            }

            for (let day = 1; day <= totalDays; day++) {

                const dateString =
                    `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

                const dayData = data.find(d => d.date === dateString);

                const cell = document.createElement("div");
                cell.className = "calendar-cell";

                const number = document.createElement("div");
                number.textContent = day;
                number.className = "calendar-day-number";

                cell.appendChild(number);

                if (dayData) {
                    dayData.slots.forEach(slot => {

                        const btn = document.createElement("button");
                        btn.type = "button";

                        btn.className =
                            "btn btn-sm w-100 mt-1 " +
                            (slot.status === "free"
                                ? "btn-success"
                                : "btn-danger");

                        btn.textContent = slot.start;
                        btn.disabled = slot.status !== "free";

                        cell.appendChild(btn);
                    });
                }

                grid.appendChild(cell);
            }

            calendarContainer.appendChild(grid);
        }
    });

    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        e.preventDefault()


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