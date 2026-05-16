<?php if (!empty($_SESSION['password_changed'])): ?>
    <script>
        alert("<?= $_SESSION['password_changed'] ?>");
    </script>
    <?php unset($_SESSION['password_changed']); ?>
<?php endif; ?>
<main class="flex-fill d-flex bg-orange-300 flex-column p-md-5 p-2">
    <h1 class="display-3 fw-bold text-center">Iniciar sesión</h1>
    <form class="d-flex flex-column justify-content-center align-items-center text-center"
        action="<?= BASE_URL ?>iniciar_sesion" method="post">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-3">
            <label class="h4 mt-1">Correo electrónico</label>
            <input name="email" type="email" class="form-control" placeholder="Escribe el email" required
                maxlength="300" value="<?= htmlspecialchars($user['email']) ?>">
            <label class="h4 mt-3">Contraseña</label>
            <div class="input-group">
                <input id="password" name="password" type="password" class="form-control" required maxlength="300"
                    placeholder="Escribe la contraseña">
                <button type="button" class="btn bg-orange-primary btn-outline-secondary"
                    onclick="togglePasswordVisibility()">
                    <i id="eyeIcon" class="bi bi-eye text-dark"></i>
                </button>
            </div>
            <input class="mt-3 btn bg-orange-primary border-dark border-1 p-2 rounded" type="submit"
                value="Iniciar sesión">
        </div>
    </form>
    <a href="<?= BASE_URL ?>restablecer_contraseña" class="mt-3 text-center">¿Has olvidado tu contraseña?</a>
    <div id="errorBox" class="<?= !empty($errors) ? 'alert alert-danger mt-3' : '' ?>">
        <ul id="errorsList" class="fs-5">
            <?php foreach ($errors as $error): ?>
                <li>
                    <?= htmlspecialchars($error) ?>
                </li>
            <?php endforeach; ?>
            <?php if (!empty($_SESSION['inactive_user'])): ?>
                <div class="mt-3">
                    <p class="text-primary">
                        Puedes reactivar tu cuenta aquí:
                    </p>

                    <button data-url="<?= BASE_URL ?>reactivar_cuenta" class="btn btn-primary reactivate-account-btn">
                        Activar cuenta
                    </button>
                </div>
            <?php endif; ?>
        </ul>
    </div>
</main>
<script>
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        e.preventDefault()
        const email = form.querySelector('[name="email"]').value.trim();
        const password = form.querySelector('[name="password"]').value.trim();
        let errors = [];

        if (!email) {
            errors.push("El email es obligatorio.");
        } else {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                errors.push("El email no es válido.");
            }
        }

        if (!password) {
            errors.push("La contraseña es obligatoria.");
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

    function togglePasswordVisibility() {
        const passwordInput = document.getElementById("password");
        const icon = document.getElementById("eyeIcon");

        if (passwordInput.type == "password") {
            passwordInput.type = "text";
            icon.classList.remove("bi-eye");
            icon.classList.add("bi-eye-slash");
        } else {
            passwordInput.type = "password";
            icon.classList.remove("bi-eye-slash");
            icon.classList.add("bi-eye");
        }
    }
</script>