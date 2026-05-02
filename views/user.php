<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>usuarios">Usuarios</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Usuario</li>
                </ol>
            </nav>
            <h1>Usuario</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <h2 class="mb-3 text-break"><?= htmlspecialchars($user['user_name']) ?></h2>
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Nombre:</span>
                                <span class="text-break"><?= htmlspecialchars($user['name']) ?></span></p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Email:</span>
                                <span class="text-break"><?= htmlspecialchars($user['email']) ?></span></p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Identifiacación (DNI/NIE):</span>
                                <span><?= htmlspecialchars($user['identification']) ?></span></p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Rol:</span>
                                <span><?= htmlspecialchars($user['role']) ?></span></p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Teléfono:</span>
                                <span><?= htmlspecialchars($user['phone']) ?></span>
                            </p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Dirección:</span>
                                <span class="text-break"><?= htmlspecialchars($user['address']) ?></span>
                            </p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Activo:</span>
                                <span><?= $user['active'] ? 'Sí' : 'No' ?></span>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <a href="<?= BASE_URL ?>modificar_usuario/<?= $user['id'] ?>"
                            class="btn bg-orange-primary border-dark border-1 flex-fill">Modificar</a>
                    </div>
                </div>
            </article>
        </section>
    </section>
</main>