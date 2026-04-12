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
            <h1>Modificar especie</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>modificar_especie" method="post" class="row g-4">
                        <input type="hidden" name="id" value="<?= $species['id'] ?>">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 g-3">
                                <div class="col">
                                    <label class="form-label fw-bold">Nombre:</label>
                                    <input type="text" name="name" class="form-control" required maxlength="300"
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
    const form = document.querySelector("form");

    form.addEventListener("submit", function (e) {
        e.preventDefault()

        const name = form.querySelector('[name="name"]').value.trim();
        let errors = [];

        if (!name) {
            errors.push("El nombre es obligatorio.");
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