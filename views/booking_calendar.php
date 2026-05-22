<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h1 class="mb-0">Calendario de reservas</h1>
                    <a href="<?= BASE_URL ?>reservas" class="btn bg-orange-primary rounded-pill btn-lg px-4">
                        Reservas
                    </a>
            </div>
            <div id="calendar" class="mt-3"></div>
        </section>
    </section>
</main>
<script>
    document.addEventListener("DOMContentLoaded", function () {

        let currentDate = new Date();

        const calendarContainer =
            document.getElementById("calendar");

        loadCalendar();

        function loadCalendar() {

            fetch(
                "<?= BASE_URL ?>calendario_reservas" +
                "?ajax=1" +
                "&month=" + (currentDate.getMonth() + 1) +
                "&year=" + currentDate.getFullYear()
            )
                .then(res => res.json())
                .then(data => {
                    renderCalendar(data);
                });
        }

        function renderCalendar(data) {

            calendarContainer.innerHTML = "";

            const controls = document.createElement("div");
            controls.className = "d-flex justify-content-between align-items-center mb-4";

            const prev = document.createElement("button");
            prev.type = "button";
            prev.className = "btn bg-orange-primary border-dark border-1";
            prev.textContent = "Mes anterior";

            prev.onclick = () => {
                currentDate.setMonth(currentDate.getMonth() - 1);
                loadCalendar();
            };

            const next = document.createElement("button");
            next.type = "button";
            next.className = "btn bg-orange-primary border-dark border-1";
            next.textContent = "Mes siguiente";

            next.onclick = () => {
                currentDate.setMonth(currentDate.getMonth() + 1);
                loadCalendar();
            };

            controls.appendChild(prev);
            controls.appendChild(next);
            calendarContainer.appendChild(controls);

            const year = currentDate.getFullYear();
            const month = currentDate.getMonth();

            const monthNames = [
                "Enero", "Febrero", "Marzo", "Abril", "Mayo", "Junio",
                "Julio", "Agosto", "Septiembre", "Octubre", "Noviembre", "Diciembre"
            ];

            const title = document.createElement("h2");
            title.className = "mb-4 fw-bold";
            title.textContent = `${monthNames[month]} ${year}`;
            calendarContainer.appendChild(title);

            const grid = document.createElement("div");
            grid.className = "calendar-grid";

            const weekDays = ["Lun", "Mar", "Mié", "Jue", "Vie", "Sáb", "Dom"];

            weekDays.forEach(day => {
                const header = document.createElement("div");
                header.className = "calendar-header";
                header.textContent = day;
                grid.appendChild(header);
            });

            const firstDay = new Date(year, month, 1);
            const lastDay = new Date(year, month + 1, 0);

            const firstWeekDay = (firstDay.getDay() + 6) % 7;
            const totalDays = lastDay.getDate();

            for (let i = 0; i < firstWeekDay; i++) {
                const empty = document.createElement("div");
                empty.className = "calendar-cell calendar-unavailable";
                grid.appendChild(empty);
            }

            for (let day = 1; day <= totalDays; day++) {

                const dateString =
                    `${year}-${String(month + 1).padStart(2, "0")}-${String(day).padStart(2, "0")}`;

                const dayReservations = data.filter(r => r.date === dateString);

                const cell = document.createElement("div");

                cell.className = dayReservations.length
                    ? "calendar-cell calendar-available"
                    : "calendar-cell calendar-unavailable";

                const number = document.createElement("div");
                number.className = "calendar-day-number";
                number.textContent = day;

                cell.appendChild(number);

                dayReservations.forEach(r => {

                    const card = document.createElement("div");

                    card.className = `reservation-card reservation-${r.status}`;

                    card.innerHTML = `
<div class="mb-1">
    <strong>
        ${r.start_time.slice(0, 5)}
        -
        ${r.end_time.slice(0, 5)}
    </strong>
</div>

<div>
    <strong>Usuario:</strong>
    ${r.user_name}
</div>

<div>
    <strong>Animal:</strong>
    ${r.animal_name}
</div>

<div>
    <strong>Sala:</strong>
    ${r.room_code}
</div>

<div>
    <strong>Monitor:</strong>
    ${r.monitor_name ?? 'Sin asignar'}
</div>

<div class="mt-1 mb-3">
    <span class="reservation-status reservation-status-${r.status}">
        ${r.status.charAt(0).toUpperCase() + r.status.slice(1)}
    </span>
</div>
            `;

                    cell.appendChild(card);
                });

                grid.appendChild(cell);
            }

            calendarContainer.appendChild(grid);
        }
    });
</script>