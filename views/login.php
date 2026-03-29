<main class="d-flex text-center bg-orange-300 flex-column p-md-5 p-2">
    <h1 class="display-3 fw-bold">Iniciar sesión</h1>
    <form class="d-flex flex-column justify-content-center align-items-center" action="<?= BASE_URL ?>login"
        method="post">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-3">
            <label class="h4 mt-1">Correo electrónico</label>
            <input name="email" type="email" class="form-control">
            <label class="h4 mt-3">Contraseña</label>
            <div class="input-group">
                <input id="password" name="password" type="password" class="form-control">
                <button type="button" class="btn btn-outline-secondary bg-white" onclick="togglePasswordVisibility()">
                    <i id="eyeIcon" class="bi bi-eye text-dark"></i>
                </button>
            </div>
            <input class="mt-3 btn bg-orange-primary border-dark border-1 p-2 rounded" type="submit"
                value="Iniciar sesión">
        </div>
    </form>
    <a href="<?= BASE_URL ?>reset_password" class="mt-3">¿Has olvidado tu contraseña?</a>
</main>
<script>
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