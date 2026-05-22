<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>reservas">Reservas</a></li>
                    <li class="breadcrumb-item"><a
                            href="<?= BASE_URL ?>modificar_reserva/<?= $reservation['id'] ?>">Modificar reserva</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Modificar fecha reserva</li>
                </ol>
            </nav>
            <h1>Modificar fecha de la reserva</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>modificar_fecha_reserva" method="post" class="row g-4">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 g-3 mb-4">
                                <div class="col-md-6">
                                    <p><span class="fw-bold">Usuario:</span>
                                        <span class="text-break"><?= htmlspecialchars($user['user_name']) ?></span>
                                    </p>
                                    <p><span class="fw-bold">Animal:</span>
                                        <span class="text-break"><?= htmlspecialchars($animal['name']) ?></span>
                                    </p>
                                    <p><span class="fw-bold">Sala:</span>
                                        <span class="text-break"><?= htmlspecialchars($room['code']) ?></span>
                                    </p>
                                    <p><span class="fw-bold">Monitor:</span>
                                        <span
                                            class="text-break"><?= $monitor ? htmlspecialchars($monitor['user_name']) : 'Sin especificar' ?></span>
                                    </p>
                                </div>
                                <div class="col-md-6">
                                    <p class="fw-bold">Horarios:</p>
                                    <ul>
                                        <?php foreach ($schedules as $s): ?>
                                            <li>
                                                <?= htmlspecialchars($s['day_of_week']) ?>:
                                                <?= htmlspecialchars(date('H:i', strtotime($s['start_time']))) ?> -
                                                <?= htmlspecialchars(date('H:i', strtotime($s['end_time']))) ?>
                                            </li>
                                        <?php endforeach; ?>
                                    </ul>
                                </div>
                                <div class="col-12">
                                    <label for="interval-select" class="form-label fw-bold">
                                        Intervalo
                                    </label>

                                    <select id="interval-select" class="form-select">
                                        <?php $interval = $reservation['interval'] ?? 60; ?>
                                        <option value="5" <?= $interval == 5 ? 'selected' : '' ?>>5 minutos</option>
                                        <option value="10" <?= $interval == 10 ? 'selected' : '' ?>>10 minutos</option>
                                        <option value="15" <?= $interval == 15 ? 'selected' : '' ?>>15 minutos</option>
                                        <option value="20" <?= $interval == 20 ? 'selected' : '' ?>>20 minutos</option>
                                        <option value="25" <?= $interval == 25 ? 'selected' : '' ?>>25 minutos</option>
                                        <option value="30" <?= $interval == 30 ? 'selected' : '' ?>>30 minutos</option>
                                        <option value="35" <?= $interval == 35 ? 'selected' : '' ?>>35 minutos</option>
                                        <option value="40" <?= $interval == 40 ? 'selected' : '' ?>>40 minutos</option>
                                        <option value="45" <?= $interval == 45 ? 'selected' : '' ?>>45 minutos</option>
                                        <option value="50" <?= $interval == 50 ? 'selected' : '' ?>>50 minutos</option>
                                        <option value="55" <?= $interval == 55 ? 'selected' : '' ?>>55 minutos</option>
                                        <option value="60" <?= $interval == 60 ? 'selected' : '' ?>>60 minutos</option>
                                    </select>
                                </div>
                                <input type="hidden" name="reservation-id" id="reservation-id"
                                    value="<?= htmlspecialchars($reservation['id']) ?>">
                                <input type="hidden" name="user-id" id="user-id"
                                    value="<?= htmlspecialchars($reservation['user_id'] ?? '') ?>">
                                <input type="hidden" name="animal-id" id="animal-id"
                                    value="<?= htmlspecialchars($reservation['animal_id'] ?? '') ?>">
                                <input type="hidden" name="room-id" id="room-id"
                                    value="<?= htmlspecialchars($reservation['room_id'] ?? '') ?>">
                                <input type="hidden" name="monitor-id" id="monitor-id"
                                    value="<?= htmlspecialchars($reservation['monitor_id'] ?? '') ?>">
                                <input type="hidden" name="reservation-date" id="reservation-date">
                                <input type="hidden" name="reservation-start" id="reservation-start">
                                <input type="hidden" name="reservation-end" id="reservation-end">
                            </div>

                            <div id="calendar"></div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Modificar
                                    reserva</button>
                                <a href="<?= BASE_URL ?>modificar_reserva/<?= $reservation['id'] ?>"
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
        const initialReservation = {
            date: "<?= $reservation['date'] ?? '' ?>",
            start: "<?= $reservation['start_time'] ?? '' ?>",
            end: "<?= $reservation['end_time'] ?? '' ?>"
        };

        const reservationId = document.getElementById("reservation-id").value;
        const initialInterval = <?= (int) ($reservation['interval'] ?? 60) ?>;
        let currentDate = new Date();
        const calendarContainer = document.getElementById("calendar");
        const userId = document.getElementById("user-id").value;
        const animalId = document.getElementById("animal-id").value;
        const roomId = document.getElementById("room-id").value;
        const monitorId = document.getElementById("monitor-id").value;
        const intervalSelect = document.getElementById("interval-select");
        intervalSelect.value = initialInterval;
        let globalData = [];
        let selectedDate = null;
        let selectedButton = null;
        let selectedReservation = null;
        let openDay = null;

        if (initialReservation.date) {
            selectedReservation = {
                date: initialReservation.date,
                start: initialReservation.start,
                end: initialReservation.end,
                slotKey: initialReservation.start + "-" + initialReservation.end
            };

            selectedDate = initialReservation.date;

            document.getElementById("reservation-date").value = initialReservation.date;
            document.getElementById("reservation-start").value = initialReservation.start;
            document.getElementById("reservation-end").value = initialReservation.end;

            currentDate = new Date(initialReservation.date);
        }

        loadCalendar();

        function normalizeTime(t) {
            return t ? t.toString().trim().slice(0, 5) : '';
        }

        function loadCalendar() {

            fetch(
                "<?= BASE_URL ?>disponibilidad_calendario" +
                "?ajax=1" +
                "&user_id=" + userId +
                "&animal_id=" + animalId +
                "&room_id=" + roomId +
                "&monitor_id=" + monitorId +
                "&interval=" + intervalSelect.value +
                "&month=" + (currentDate.getMonth() + 1) +
                "&year=" + currentDate.getFullYear() +
                "&reservation_id=" + reservationId
            )
                .then(res => res.json())
                .then(data => {
                    globalData = data;
                    renderCalendar(data);
                });
        }

        intervalSelect.addEventListener("change", function () {
            selectedReservation = null;
            selectedDate = null;
            openDay = null;
            document.getElementById("reservation-date").value = "";
            document.getElementById("reservation-start").value = "";
            document.getElementById("reservation-end").value = "";
            document.querySelectorAll(".calendar-cell")
                .forEach(c => c.classList.remove("calendar-selected"));
            loadCalendar();
        });

        function renderCalendar(data) {
            calendarContainer.innerHTML = "";

            // CONTROLS
            const controls = document.createElement("div");
            controls.className = "d-flex justify-content-between mb-3";

            const prev = document.createElement("button");
            prev.type = "button";
            prev.textContent = "Mes anterior";
            prev.className = "btn bg-orange-primary border-dark border-1";

            const next = document.createElement("button");
            next.type = "button";
            next.textContent = "Mes siguiente";
            next.className = "btn bg-orange-primary border-dark border-1";

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

            // CALENDAR
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

                const today = new Date();
                today.setHours(0, 0, 0, 0);

                const currentCellDate = new Date(dateString);
                currentCellDate.setHours(0, 0, 0, 0);

                const isPastOrToday = currentCellDate <= today;

                const dayData = data.find(d => d.date === dateString);

                const hasFreeSlots =
                    dayData &&
                    dayData.slots &&
                    dayData.slots.some(slot => slot.status === "free");

                const cell = document.createElement("div");

                cell.className = "calendar-cell";

                if (hasFreeSlots && !isPastOrToday) {
                    cell.classList.add("calendar-available");
                } else {
                    cell.classList.add("calendar-unavailable");
                }

                const number = document.createElement("div");
                number.textContent = day;
                number.className = "calendar-day-number";

                cell.appendChild(number);

                if (dayData && !isPastOrToday) {

                    cell.style.cursor = "pointer";

                    cell.addEventListener("click", function (e) {
                        if (e.target.closest("button")) return;

                        // close previous open
                        if (openDay && openDay !== cell) {
                            const old = openDay.querySelector(".slots-container");
                            if (old) old.remove();
                        }

                        const existing = cell.querySelector(".slots-container");

                        if (existing) {
                            existing.remove();
                            openDay = null;
                            return;
                        }

                        const container = document.createElement("div");
                        container.className = "slots-container mt-2";

                        dayData.slots.forEach(slot => {

                            const isSelectedSlot =
                                selectedReservation &&
                                selectedReservation.date === dateString &&
                                normalizeTime(selectedReservation.start) === normalizeTime(slot.start) &&
                                normalizeTime(selectedReservation.end) === normalizeTime(slot.end);

                            const btn = document.createElement("button");
                            btn.type = "button";

                            btn.className =
                                "btn btn-sm w-100 mt-1 " +
                                (slot.status === "free" ? "btn-success" : "btn-danger");

                            if (isSelectedSlot) {
                                btn.classList.remove("btn-success");
                                btn.classList.add("btn-warning");
                            }

                            btn.textContent = slot.start;
                            btn.disabled = slot.status !== "free";

                            if (slot.status === "free") {
                                btn.addEventListener("click", (e) => {
                                    e.stopPropagation();

                                    const containerButtons = container.querySelectorAll("button");
                                    containerButtons.forEach(b => {
                                        if (!b.disabled) b.classList.remove("btn-warning");
                                        if (!b.disabled) b.classList.add("btn-success");
                                    });

                                    selectedReservation = {
                                        date: dateString,
                                        start: slot.start,
                                        end: slot.end,
                                        slotKey: slot.start + "-" + slot.end
                                    };

                                    selectedDate = dateString;

                                    document.querySelectorAll(".calendar-cell")
                                        .forEach(c => c.classList.remove("calendar-selected"));

                                    cell.classList.add("calendar-selected");

                                    document.getElementById("reservation-date").value = dateString;
                                    document.getElementById("reservation-start").value = slot.start;
                                    document.getElementById("reservation-end").value = slot.end;

                                    btn.classList.remove("btn-success");
                                    btn.classList.add("btn-warning");
                                });
                            }

                            container.appendChild(btn);
                        });

                        cell.appendChild(container);
                        openDay = cell;
                    });
                }

                if (
                    selectedDate === dateString &&
                    selectedReservation &&
                    !openDay
                ) {
                    cell.click();
                }

                grid.appendChild(cell);
            }

            calendarContainer.appendChild(grid);
            if (selectedDate) {
                document.querySelectorAll(".calendar-cell").forEach(cell => {
                    const dayNumber = cell.querySelector(".calendar-day-number")?.textContent;
                    if (!dayNumber) return;

                    const fullDate =
                        `${year}-${String(month + 1).padStart(2, "0")}-${String(dayNumber).padStart(2, "0")}`;

                    if (fullDate === selectedDate) {
                        cell.classList.add("calendar-selected");
                    }
                });
            }
        }

        const form = document.querySelector("form");

        form.addEventListener("submit", function (e) {
            e.preventDefault()

            let errors = [];

            const date =
                document.getElementById("reservation-date").value;

            const startTime =
                document.getElementById("reservation-start").value;

            const endTime =
                document.getElementById("reservation-end").value;

            const interval =
                parseInt(document.getElementById("interval-select").value);

            const allowedIntervals =
                [5, 10, 15, 20, 25, 30, 35, 40, 45, 50, 55, 60];

            if (!selectedReservation || !date || !startTime || !endTime) {
                errors.push("Debes seleccionar una fecha y hora.");
            }

            if (!allowedIntervals.includes(interval)) {
                errors.push("Intervalo inválido.");
            }

            if (date) {

                const today = new Date();

                today.setHours(0, 0, 0, 0);

                const selectedDate = new Date(date);

                selectedDate.setHours(0, 0, 0, 0);

                if (selectedDate <= today) {
                    errors.push("No puedes seleccionar fechas de hoy o anteriores.");
                }
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
    });
</script>