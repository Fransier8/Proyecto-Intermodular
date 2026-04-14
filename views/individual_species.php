<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>especies">Especies</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Especie</li>
                </ol>
            </nav>
            <h1>Especie</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <h2 class="mb-3"><?= htmlspecialchars($species['name']) ?></h2>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <a href="<?= BASE_URL ?>modificar_especie/<?= $species['id'] ?>"
                            class="btn bg-orange-primary border-dark border-1 flex-fill">Modificar</a>
                    </div>
                </div>
            </article>
        </section>
    </section>
</main>