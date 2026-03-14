<main class="d-flex text-center bg-orange-300 flex-column p-md-5 p-2">
    <h1 class="display-3 fw-bold">Iniciar sesión</h1>
    <form class="d-flex flex-column justify-content-center align-items-center" action="<?= BASE_URL ?>login" method="post">
        <div class="col-12 col-sm-10 col-md-8 col-lg-5 col-xl-3">
            <label class="h4 mt-1">Correo electrónico</label>
            <input name="email" type="email" class="form-control">
            <label class="h4 mt-3">Contraseña</label>
            <input name="password" type="password" class="form-control">
            <input class="mt-3 btn bg-orange-primary border-dark border-1 p-2 rounded" type="submit"
                value="Iniciar sesión">
        </div>
    </form>
</main>