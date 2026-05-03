<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>animales">Animales</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Animal</li>
                </ol>
            </nav>
            <h1>Animal</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <h2 class="mb-3 text-break"><?= htmlspecialchars($animal['name']) ?></h2>
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        <?php if (!empty($animal['photo'])): ?>
                            <div class="col-12 col-md-4">
                                <img src="<?= BASE_URL ?>uploads/animals/<?= htmlspecialchars($animal['photo']) ?>"
                                    class="img-fluid rounded w-100" style="aspect-ratio: 4/3; object-fit: cover;">
                            </div>
                        <?php endif; ?>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Descripción:</span>
                                <span class="text-break"><?= htmlspecialchars($animal['description']) ?></span>
                            </p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Especie:</span>
                                <span class="text-break"><?= htmlspecialchars($animal['species']) ?></span>
                            </p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Raza:</span>
                                <span class="text-break"><?= htmlspecialchars($animal['breed']) ?></span>
                            </p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Estado:</span>
                                <span><?= ucfirst(htmlspecialchars($animal['status'])) ?></span>
                            </p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Género:</span>
                                <span><?= ucfirst(htmlspecialchars($animal['gender'])) ?></span>
                            </p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Fecha de nacimiento:</span>
                                <span><?= htmlspecialchars($animal['birth_day']) ?></span>
                            </p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Dueño:</span>
                                <span class="text-break"><?= htmlspecialchars($animal['user']) ?></span>
                            </p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Activo:</span>
                                <span><?= $animal['active'] ? 'Sí' : 'No' ?></span>
                            </p>
                        </div>
                    </div>
                    <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                        <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                            <a href="<?= BASE_URL ?>modificar_animal/<?= $animal['id'] ?>"
                                class="btn bg-orange-primary border-dark border-1 flex-fill">Modificar</a>
                        </div>
                    <?php elseif ($_SESSION['user']['role'] == "usuario"): ?>
                        <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                            <button class="btn bg-orange-primary border-dark flex-fill">
                                Adoptar
                            </button>
                            <button class="btn bg-orange-primary border-dark flex-fill">
                                Visitar
                            </button>
                            <?php if ($animal['status'] === 'sin adoptar'): ?>
                                <a href="<?= BASE_URL ?>apadrinar_animal/<?= $animal['id'] ?>"
                                    class="btn bg-orange-primary border-dark flex-fill">
                                    Apadrinar
                                </a>
                                </a>
                            <?php else: ?>
                                <button class="btn btn-secondary flex-fill" disabled>
                                    No disponible
                                </button>
                            <?php endif; ?>
                        </div>
                    <?php endif ?>
                </div>
            </article>
        </section>
    </section>
</main>