<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>solicitudes_de_adopcion">Solicitudes de
                            adopción</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Modificar solicitud de adopción</li>
                </ol>
            </nav>
            <h1>Modificar solicitud de adopción</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>modificar_solicitud_de_adopcion" method="post" class="row g-4"
                        enctype="multipart/form-data">
                        <input type="hidden" name="id" value="<?= $adoption_application['id'] ?>">
                        <div class="col-12 col-md-12 fs-5">
                            <div class="row row-cols-1 row-cols-md-2 g-3">

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
                                <div class="col-12 col-md-8">
                                    <h4 class="mb-3">
                                        <?= htmlspecialchars($user['user_name']) ?>
                                    </h4>
                                    <p class="mb-1"><span class="fw-bold">Nombre:</span>
                                        <span class="text-break"><?= htmlspecialchars($user['name']) ?></span>
                                    </p>
                                    <p class="mb-1"><span class="fw-bold">Identifiacación (DNI/NIE):</span>
                                        <span>
                                            <?= htmlspecialchars($user['identification']) ?>
                                        </span>
                                    </p>
                                    <p class="mb-1"><span class="fw-bold">Email:</span>
                                        <span class="text-break">
                                            <?= htmlspecialchars($user['email']) ?>
                                        </span>
                                    </p>
                                    <p class="mb-1"><span class="fw-bold">Teléfono:</span>
                                        <span>
                                            <?= htmlspecialchars($user['phone']) ?>
                                        </span>
                                    </p>
                                    <p>
                                        <?= htmlspecialchars($adoption_application['message']) ?>
                                    </p>
                                </div>
                            </div>

                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit" name="action" value="aceptar"
                                    onclick="return confirm('¿Seguro que quieres aceptar la solicitud de adopción?')"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Aceptar</button>
                                <button type="submit" name="action" value="reservar"
                                    onclick="return confirm('¿Seguro que quieres reservar el animal?')"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Reservar</button>
                            </div>
                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit" name="action" value="denegar"
                                    onclick="return confirm('¿Seguro que quieres denegar la solicitud de adopción?')"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Denegar</button>
                                <a href="<?= BASE_URL ?>solicitudes_de_adopcion"
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

        let errors = [];

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
    });
</script>