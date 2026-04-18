<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <h1>Registrarse</h1>
    <article class="row g-4">
        <div class="col-12 col-md-12 fs-5">
            <form action="<?= BASE_URL ?>registrarse" method="post" class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        <div class="col">
                            <label class="form-label fw-bold">Nombre de usuario:</label>
                            <input type="text" name="user_name" class="form-control" required maxlength="300"
                                placeholder="Escribe el nombre de usuario"
                                value="<?= htmlspecialchars($user['user_name']) ?>">
                        </div>
                        <div class="col">
                            <label class="form-label fw-bold">Nombre:</label>
                            <input type="text" name="name" class="form-control" required maxlength="300"
                                placeholder="Escribe el nombre" value="<?= htmlspecialchars($user['name']) ?>">
                        </div>

                        <div class="col">
                            <label class="form-label fw-bold">Email:</label>
                            <input type="email" name="email" class="form-control" required maxlength="300"
                                placeholder="Escribe el email" value="<?= htmlspecialchars($user['email']) ?>">
                        </div>

                        <div class="col">
                            <label class="form-label fw-bold">Identificación (DNI/NIE):</label>
                            <input type="text" name="identification" class="form-control" required maxlength="20"
                                placeholder="Escribe la identificación"
                                value="<?= htmlspecialchars($user['identification']) ?>">
                        </div>

                        <div class="col">
                            <label class="form-label fw-bold">Teléfono (opcional):</label>
                            <input type="tel" name="phone" class="form-control" maxlength="20"
                                placeholder="Escribe el teléfono" value="<?= htmlspecialchars($user['phone']) ?>">
                        </div>

                        <div class="col">
                            <label class="form-label fw-bold">Dirección (opcional):</label>
                            <input type="text" name="address" class="form-control" maxlength="500"
                                placeholder="Escribe la dirección" value="<?= htmlspecialchars($user['address']) ?>">
                        </div>

                        <div class="col">
                            <label class="form-label fw-bold">Contraseña:</label>
                            <div class="input-group">
                                <input type="password" name="password" class="form-control" required maxlength="300"
                                    placeholder="Escribe la contraseña">
                                <button type="button" class="btn bg-orange-primary btn-outline-secondary"
                                    onclick="togglePasswordVisibility(this)">
                                    <i class="bi bi-eye text-dark"></i>
                                </button>
                            </div>
                        </div>

                        <div class="col">
                            <label class="form-label fw-bold">Verificar contraseña:</label>
                            <div class="input-group">
                                <input type="password" name="verify_password" class="form-control" required
                                    maxlength="300" placeholder="Escribe la contraseña otra vez">
                                <button type="button" class="btn bg-orange-primary btn-outline-secondary"
                                    onclick="togglePasswordVisibility(this)">
                                    <i class="bi bi-eye text-dark"></i>
                                </button>
                            </div>
                        </div>
                    </div>

                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <button type="submit"
                            class="btn bg-orange-primary border-dark border-1 flex-fill">Registrarse</button>
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
</main>
<script>
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        e.preventDefault()


        const userName = form.querySelector('[name="user_name"]').value.trim();
        const name = form.querySelector('[name="name"]').value.trim();
        const email = form.querySelector('[name="email"]').value.trim();
        const identification = form.querySelector('[name="identification"]').value.trim().toUpperCase();
        const phone = form.querySelector('[name="phone"]').value.trim();
        const password = form.querySelector('[name="password"]').value.trim();
        const verifyPassword = form.querySelector('[name="verify_password"]').value.trim();
        let errors = [];

        if (!userName) {
            errors.push("El nombre de usuario es obligatorio.");
        }

        if (!name) {
            errors.push("El nombre es obligatorio.");
        }

        if (!email) {
            errors.push("El email es obligatorio.");
        } else {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                errors.push("El email no es válido.");
            }
        }

        if (!identification) {
            errors.push("La identificación es obligatoria.");
        } else {
            const dniPattern = /^\d{8}[A-Za-z]$/;
            const niePattern = /^[XYZ]\d{7}[A-Za-z]$/;

            if (!dniPattern.test(identification) && !niePattern.test(identification)) {
                errors.push("DNI o NIE inválido.");
            } else {
                if (dniPattern.test(identification)) {
                    const letras = "TRWAGMYFPDXBNJZSQVHLCKE";
                    const numero = parseInt(identification.substring(0, 8));
                    const letra = identification.substring(8).toUpperCase();

                    if (letra != letras[numero % 23]) {
                        errors.push("La letra del DNI no es correcta.");
                    }
                }
            }
        }

        const phonePattern = /^[0-9]{9}$/;

        if (phone && !phonePattern.test(phone)) {
            errors.push("El teléfono debe tener 9 dígitos.");
        }

        if (password.length < 4) {
            errors.push("La contraseña debe tener al menos 4 caracteres.");
        }
        if (password != verifyPassword) {
            errors.push("Las contraseñas deben coincidir.");
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

    function togglePasswordVisibility(button) {
        const input = button.parentElement.querySelector("input");
        const icon = button.querySelector("i");

        if (input.type === "password") {
            input.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            input.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
</script>