<main class="flex-fill d-flex bg-orange-300 flex-column p-md-5 p-2">
    <h1 class="display-3 fw-bold text-center">Restablecer contraseña</h1>
    <form class="d-flex flex-column justify-content-center align-items-center text-center"
        action="<?= BASE_URL ?>restablecer_contraseña" method="post">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-3">
            <label class="h4 mt-1">Nombre de usuario</label>
            <input type="text" name="user_name" class="form-control" required maxlength="300"
                placeholder="Escribe el nombre de usuario" value="<?= htmlspecialchars($user['user_name']) ?>">
            <label class="h4 mt-3">Correo electrónico</label>
            <input name="email" type="email" class="form-control" placeholder="Escribe el email" required
                maxlength="300" value="<?= htmlspecialchars($user['email']) ?>">
            <label class="h4 mt-3">Identificación (DNI/NIE)</label>
            <input type="text" name="identification" class="form-control" required maxlength="20"
                placeholder="Escribe la identificación" value="<?= htmlspecialchars($user['identification']) ?>">
            <label class="h4 mt-3">Nueva contraseña</label>
            <div class="input-group">
                <input id="password" name="password" type="password" class="form-control" required maxlength="300"
                    placeholder="Escribe la contraseña">
                <button type="button" class="btn bg-orange-primary btn-outline-secondary"
                    onclick="togglePasswordVisibility(this)">
                    <i id="eyeIcon" class="bi bi-eye text-dark"></i>
                </button>
            </div>
            <label class="h4 mt-3">Verificar contraseña</label>
            <div class="input-group">
                <input id="verify_password" name="verify_password" type="password" class="form-control" required
                    maxlength="300" placeholder="Escribe la contraseña otra vez">
                <button type="button" class="btn bg-orange-primary btn-outline-secondary"
                    onclick="togglePasswordVisibility(this)">
                    <i id="eyeIcon" class="bi bi-eye text-dark"></i>
                </button>
            </div>
            <input class="mt-3 btn bg-orange-primary border-dark border-1 p-2 rounded" type="submit"
                value="Restablecer contraseña">
        </div>
    </form>
    <div id="errorBox" class="<?= !empty($errors) ? 'alert alert-danger mt-3' : '' ?>">
        <ul id="errorsList" class="fs-5">
            <?php foreach ($errors as $error): ?>
                <li>
                    <?= htmlspecialchars($error) ?>
                </li>
            <?php endforeach; ?>
        </ul>
    </div>
</main>
<script>
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        e.preventDefault()
                const userName = form.querySelector('[name="user_name"]').value.trim();
        const email = form.querySelector('[name="email"]').value.trim();
        const identification = form.querySelector('[name="identification"]').value.trim();
        const password = form.querySelector('[name="password"]').value.trim();
        const verifyPassword = form.querySelector('[name="verify_password"]').value.trim();
        let errors = [];

        if (!userName) {
            errors.push("El nombre de usuario es obligatorio.");
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

    document.querySelector(".reactivate-account-btn").addEventListener("click", function () {
        const ok = confirm("¿Seguro que quieres reactivar tu cuenta?");
        if (ok) {
            window.location.href = this.dataset.url;
        }
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