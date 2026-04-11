<main class="container-fluid bg-orange-300">
    <section class="row">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>species">Especies</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>especie/<?= $species['id'] ?>">Especie</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Modificar especie</li>
                </ol>
            </nav>
            <h1>Especie</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>modificar_especie" method="post" class="row g-4">
                        <input type="hidden" name="id" value="<?= $species['id'] ?>">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 g-3">
                                <div class="col">
                                    <label class="form-label fw-bold">Nombre:</label>
                                    <input type="text" name="name" class="form-control" required
                                        value="<?= htmlspecialchars($species['name']) ?>">
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Guardar</button>
                                <a href="<?= BASE_URL ?>especie/<?= $species['id'] ?>"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Cancelar</a>
                            </div>
                        </div>
                    </form>
                    <div class="alert alert-danger mt-3">
                        <ul>
                            <?php foreach ($errores as $error): ?>
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
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        e.preventDefault()

        const email = form.email.value.trim();
        const password = form.password.value.trim();
        let errors = [];

        if (!email) {
            errors.push("El email es obligatorio.");
        } else {
            const emailPattern = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
            if (!emailPattern.test(email)) {
                errors.push("El email no es válido.");
            }
        }


        if (errors.length > 0) {
            alert(errors.join("\n"));
            return;
        }

        form.submit();
    });

</script>