<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>salas">Salas</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>sala/<?= $room['id'] ?>">Sala</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Modificar sala</li>
                </ol>
            </nav>
            <h1>Modificar sala</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>modificar_sala" method="post" class="row g-4" enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $room['id'] ?>">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <div class="col">
                                    <label class="form-label fw-bold">Código:</label>
                                    <input type="text" name="code" class="form-control" required maxlength="20"
                                        value="<?= htmlspecialchars($room['code']) ?>">
                                </div>

                                <div class="col">
                                    <div class="form-check mt-3">
                                        <input type="checkbox" class="form-check-input" name="active" value="1"
                                            <?= $room['active'] ? 'checked' : '' ?>
                                            onclick="return confirm('¿Seguro que quieres cambiar el estado de esta sala?')">
                                        <label class="form-check-label">Activa</label>
                                    </div>
                                </div>
                                <div class="col">
                                    <label class="form-label fw-bold">Nombre:</label>
                                    <input type="text" name="name" class="form-control" required maxlength="300"
                                        value="<?= htmlspecialchars($room['name']) ?>">
                                </div>

                                <div class="col">
                                    <label class="form-label fw-bold">Capacidad:</label>
                                    <input type="number" name="capacity" class="form-control" required maxlength="11"
                                        min="1" value="<?= htmlspecialchars($room['capacity']) ?>">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Descripción (opcional):</label>
                                    <textarea name="description" class="form-control"
                                        maxlength="2000"><?= htmlspecialchars($room['description']) ?></textarea>
                                </div>

                                <div class="col">
                                    <label class="form-label fw-bold">Ubicación (opcional):</label>
                                    <input type="text" name="location" class="form-control" maxlength="500"
                                        value="<?= htmlspecialchars($room['location']) ?>">
                                </div>


                                <div class="col">
                                    <label class="fw-bold">Fotos:</label>
                                    <input type="file" name="photos[]" multiple class="form-control">
                                </div>

                                <div class="col">
                                    <label class="fw-bold">Horarios:</label>

                                    <div class="row g-2 mb-1 fw-bold text-muted">
                                        <div class="col-md-3">Día</div>
                                        <div class="col-md-3">Hora inicio</div>
                                        <div class="col-md-3">Hora fin</div>
                                        <div class="col-md-3">Acciones</div>
                                    </div>

                                    <div id="schedulesContainer"></div>

                                    <button type="button" class="btn btn-sm btn-success mt-2" onclick="addSchedule()">
                                        + Añadir horario
                                    </button>
                                </div>

                                <div class="col mt-3">
                                    <div class="d-flex flex-wrap gap-2">
                                        <?php foreach ($photos as $photo): ?>
                                            <div>
                                                <img src="<?= BASE_URL ?>uploads/rooms/<?= $photo['photo'] ?>" width="120">

                                                <input type="checkbox" name="delete_photos[]" value="<?= $photo['id'] ?>">
                                                <small>Eliminar</small>
                                            </div>
                                        <?php endforeach; ?>
                                    </div>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Guardar</button>
                                <a href="<?= BASE_URL ?>sala/<?= $room['id'] ?>"
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
    let scheduleIndex = document.querySelectorAll('#schedulesContainer .row').length;

    <?php if (!empty($schedules)): ?>
        <?php foreach ($schedules as $s): ?>
            addSchedule("<?= $s['day_of_week'] ?>", "<?= date('H:i', strtotime($s['start_time'])) ?>", "<?= date('H:i', strtotime($s['end_time'])) ?>");
        <?php endforeach; ?>
    <?php endif; ?>

    function addSchedule(day_of_week = '', start_time = '', end_time = '') {
        const container = document.getElementById('schedulesContainer');

        const div = document.createElement('div');
        div.classList.add('row', 'g-2', 'mb-2');

        div.innerHTML = `
        <div class="col-md-3">
            <select name="schedules[${scheduleIndex}][day_of_week]" class="form-select" required>
                <option value="lunes" ${day_of_week === 'lunes' ? 'selected' : ''}>Lunes</option>
                <option value="martes" ${day_of_week === 'martes' ? 'selected' : ''}>Martes</option>
                <option value="miércoles" ${day_of_week === 'miércoles' ? 'selected' : ''}>Miércoles</option>
                <option value="jueves" ${day_of_week === 'jueves' ? 'selected' : ''}>Jueves</option>
                <option value="viernes" ${day_of_week === 'viernes' ? 'selected' : ''}>Viernes</option>
                <option value="sábado" ${day_of_week === 'sábado' ? 'selected' : ''}>Sábado</option>
                <option value="domingo" ${day_of_week === 'domingo' ? 'selected' : ''}>Domingo</option>
            </select>
        </div>
        <div class="col-md-3">
            <input type="time" name="schedules[${scheduleIndex}][start_time]" class="form-control" value="${start_time}" required>
        </div>
        <div class="col-md-3">
            <input type="time" name="schedules[${scheduleIndex}][end_time]" class="form-control" value="${end_time}" required>
        </div>
        <div class="col-md-3">
            <button type="button" class="btn btn-danger w-100" onclick="this.closest('.row').remove()">
                Eliminar
            </button>
        </div>
    `;

        container.appendChild(div);
        scheduleIndex++;
    }

    function timeToMinutes(t) {
        const [h, m] = t.split(':').map(Number);
        return h * 60 + m;
    }

    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        e.preventDefault()

        const code = form.querySelector('[name="code"]').value.trim();
        const name = form.querySelector('[name="name"]').value.trim();
        const capacity = form.querySelector('[name="capacity"]').value.trim();
        const schedules = document.querySelectorAll('#schedulesContainer .row');

        let errors = [];

        if (!code) {
            errors.push("El código es obligatorio.");
        }

        if (!name) {
            errors.push("El nombre es obligatorio.");
        }

        if (!capacity) {
            errors.push("La capacidad es obligatoria.");
        }

        if (schedules.length == 0) {
            errors.push("Debes añadir al menos un horario.");
        }

        schedules.forEach(row => {
            const start = row.querySelector('[name*="[start_time]"]').value;
            const end = row.querySelector('[name*="[end_time]"]').value;


            if (timeToMinutes(start) >= timeToMinutes(end)) {
                if (!errors.includes("La hora de inicio debe ser menor que la de fin.")) {
                    errors.push("La hora de inicio debe ser menor que la de fin.");
                }
            }
        });

        const schedulesForDay = {};

        schedules.forEach(row => {
            const day = row.querySelector('[name*="[day_of_week]"]').value;
            const start = row.querySelector('[name*="[start_time]"]').value;
            const end = row.querySelector('[name*="[end_time]"]').value;

            if (!schedulesForDay[day]) {
                schedulesForDay[day] = [];
            }

            schedulesForDay[day].push({ start, end });
        });

        for (const day in schedulesForDay) {
            const testedSchedules = schedulesForDay[day];

            for (let i = 0; i < testedSchedules.length; i++) {
                for (let j = i + 1; j < testedSchedules.length; j++) {
                    const a = testedSchedules[i];
                    const b = testedSchedules[j];

                    const aStart = timeToMinutes(a.start);
                    const aEnd = timeToMinutes(a.end);
                    const bStart = timeToMinutes(b.start);
                    const bEnd = timeToMinutes(b.end);

                    if (aStart < bEnd && bStart < aEnd) {
                        const msg = `Hay horarios solapados el ${day}`;
                        if (!errors.includes(msg)) {
                            errors.push(msg);
                        }
                    }
                }
            }
        }

        const photosInput = form.querySelector('[name="photos[]"]');

        const allowedTypes = ["image/png", "image/jpeg", "image/webp"];

        for (let file of photosInput.files) {
            const ext = file.name.split('.').pop().toLowerCase();

            const validExt = ["png", "jpg", "jpeg", "webp"].includes(ext);

            if (!allowedTypes.includes(file.type) && !validExt) {
                errors.push("Solo PNG, JPG, JPEG o WEBP.");
                break;
            }
        }

        if (photosInput.files.length > 5) {
            errors.push("Máximo 5 imágenes.");
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