<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <?php if (!empty($_SESSION['breadcrumb']['prefill_animal_id'])): ?>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>animales">Animales</a>
                        </li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>animal/<?= $_SESSION['breadcrumb']['prefill_animal_id'] ?>">Animal</a>
                        </li>
                    <?php elseif (!empty($_SESSION['breadcrumb']['prefill_room_id'])): ?>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>salas">Salas</a>
                        </li>
                        <li class="breadcrumb-item"><a href="<?= BASE_URL ?>sala/<?= $_SESSION['breadcrumb']['prefill_room_id'] ?>">Sala</a></li>
                    <?php else: ?>
                        <li class="breadcrumb-item">
                            <a href="<?= BASE_URL ?>mis_reservas">Mis reservas</a>
                        </li>
                    <?php endif; ?>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>solicitar_reserva">Solicitar reserva</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Seleccionar fecha reserva solicitada</li>
                </ol>
            </nav>
            <h1>Seleccionar fecha de la reserva solicitada</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>seleccionar_fecha_reserva_solicitada" method="post" class="row g-4">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 g-3 mb-4">
                                <div class="col-md-6">
                                    <p><span class="fw-bold">Animal:</span>
                                        <span class="text-break"><?= htmlspecialchars($animal['name']) ?></span>
                                    </p>
                                    <p><span class="fw-bold">Sala:</span>
                                        <span class="text-break"><?= htmlspecialchars($room['code']) ?></span>
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
                                        <option value="5">5 minutos</option>
                                        <option value="10">10 minutos</option>
                                        <option value="15">15 minutos</option>
                                        <option value="20">20 minutos</option>
                                        <option value="25">25 minutos</option>
                                        <option value="30">30 minutos</option>
                                        <option value="35">35 minutos</option>
                                        <option value="40">40 minutos</option>
                                        <option value="45">45 minutos</option>
                                        <option value="50">50 minutos</option>
                                        <option value="55">55 minutos</option>
                                        <option value="60" selected>60 minutos</option>
                                    </select>
                                </div>
                                <input type="hidden" name="user-id" id="user-id"
                                    value="<?= htmlspecialchars($reservation['user_id'] ?? '') ?>">
                                <input type="hidden" name="animal-id" id="animal-id"
                                    value="<?= htmlspecialchars($reservation['animal_id'] ?? '') ?>">
                                <input type="hidden" name="room-id" id="room-id"
                                    value="<?= htmlspecialchars($reservation['room_id'] ?? '') ?>">
                                <input type="hidden" name="reservation-date" id="reservation-date">
                                <input type="hidden" name="reservation-start" id="reservation-start">
                                <input type="hidden" name="reservation-end" id="reservation-end">
                            </div>

                            <div id="calendar"></div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Solicitar
                                    reserva</button>
                                <a href="<?= BASE_URL ?>solicitar_reserva"
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
        const existingReservations = <?= json_encode($reservations) ?>;
        let currentDate = new Date();
        const calendarContainer = document.getElementById("calendar");
        const userId = document.getElementById("user-id").value;
        const animalId = document.getElementById("animal-id").value;
        const roomId = document.getElementById("room-id").value;
        const intervalSelect = document.getElementById("interval-select");
        let globalData = [];
        let selectedDate = null;
        let selectedButton = null;
        let selectedReservation = null;
        let openDay = null;

        loadCalendar();

        function loadCalendar() {

            fetch(
                "<?= BASE_URL ?>disponibilidad_calendario" +
                "?ajax=1" +
                "&user_id=" + userId +
                "&animal_id=" + animalId +
                "&room_id=" + roomId +
                "&interval=" + intervalSelect.value +
                "&month=" + (currentDate.getMonth() + 1) +
                "&year=" + currentDate.getFullYear()
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
                                selectedReservation.start === slot.start &&
                                selectedReservation.end === slot.end;

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

            const sameDayReservations =
                existingReservations.filter(r =>
                    r.date == date && parseInt(r.user_id) == parseInt(userId) &&
                    r.status != "cancelada" && r.status != "denegada"
                );

            if (sameDayReservations.length >= 1) {
                errors.push(
                    "No puedes realizar más de una reserva el mismo día."
                );
            }

            function getWeekNumber(dateString) {

                const d = new Date(dateString);

                d.setHours(0, 0, 0, 0);

                d.setDate(d.getDate() + 4 - (d.getDay() || 7));

                const yearStart = new Date(d.getFullYear(), 0, 1);

                return Math.ceil((((d - yearStart) / 86400000) + 1) / 7);
            }

            const selectedWeek = getWeekNumber(date);

            const selectedYear = new Date(date).getFullYear();

            const animalReservationsWeek =
                existingReservations.filter(r => {

                    if (r.status == "cancelada" || r.status == "denegada") {
                        return false;
                    }

                    const reservationDate = new Date(r.date);

                    return (
                        parseInt(r.animal_id) === parseInt(animalId) && parseInt(r.user_id) == parseInt(userId) &&
                        getWeekNumber(r.date) === selectedWeek &&
                        reservationDate.getFullYear() === selectedYear
                    );
                });

            if (animalReservationsWeek.length >= 2) {
                errors.push(
                    "No puedes reservar el mismo animal más de 2 veces por semana."
                );
            }


            const roomReservationsWeek =
                existingReservations.filter(r => {

                    if (r.status === "cancelada" || r.status == "denegada") {
                        return false;
                    }

                    const reservationDate = new Date(r.date);

                    return (
                        parseInt(r.room_id) === parseInt(roomId) && parseInt(r.user_id) == parseInt(userId) &&
                        getWeekNumber(r.date) === selectedWeek &&
                        reservationDate.getFullYear() === selectedYear
                    );
                });

            if (roomReservationsWeek.length >= 2) {
                errors.push(
                    "No puedes reservar la misma sala más de 2 veces por semana."
                );
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