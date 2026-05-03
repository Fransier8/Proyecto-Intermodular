<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>animales">Animales</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>animal/<?= $animal['id'] ?>">Animal</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Apadrinar animal</li>
                </ol>
            </nav>
            <h1>Apadrinar animal</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>apadrinar_animal" method="post" class="row g-4"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $animal['id'] ?>">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <div class="col">
                                    <label class="form-label fw-bold">Importe:</label>
                                    <input type="number" name="amount" class="form-control" required min="0.01"
                                        step="0.01" placeholder="Escribe el importe"
                                        value="<?= htmlspecialchars($sponsorship['amount']) ?>">
                                </div>

                                <div class="col-md-12">
                                    <label class="form-label fw-bold">Mensaje:</label>
                                    <textarea name="message" class="form-control" placeholder="Escribe el mensaje"
                                        required
                                        maxlength="5000"><?= htmlspecialchars($sponsorship['message']) ?></textarea>
                                </div>

                                <div class="col-12 col-md-4">
                                    <h4 class="mb-3">
                                        <?= htmlspecialchars($animal['name']) ?>
                                    </h4>
                                    <img id="preview" src="<?=
                                        !empty($animal['photo'])
                                        ? BASE_URL . "uploads/animals/" . htmlspecialchars($animal['photo'])
                                        : BASE_URL . "img/placeholder.webp";
                                    ?>" class="img-fluid rounded w-100" style="aspect-ratio: 4/3; object-fit: cover;">
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Pagar</button>
                                <a href="<?= BASE_URL ?>animal/<?= $animal['id'] ?>"
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

        const amount = form.querySelector('[name="amount"]').value.trim();
        const message = form.querySelector('[name="message]').value.trim();

        let errors = [];

        if (!amount || amount < 0.01) {
            errors.push("El importe debe ser mayor a 0.");
        }

        if (!message) {
            errors.push("El mensaje es obligatorio.");
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