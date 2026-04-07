<main class="container-fluid bg-orange-300">
    <section class="row">
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
            <h1>Sala</h1>
            <article class="row g-4">
                <div class="col-12 col-md-4">
                    <img src="https://www.purina.es/sites/default/files/styles/ttt_image_510/public/2024-02/sitesdefaultfilesstylessquare_medium_440x440public2022-07Dalmatian1.jpg?itok=B_1aRoJh"
                        class="img-fluid rounded" alt="Perrito">
                </div>
                <div class="col-12 col-md-8 fs-5">
                    <h2 class="mb-3">Perrito</h2>
                    <div class="mt-3">
                        <h5 class="fw-bold">Descripción:</h5>
                        <p class="mb-3">
                            Perrito muy cariñoso y sociable, le encanta jugar con otros animales
                            y pasear al aire libre.
                        </p>
                    </div>
                    <div class="row row-cols-1 row-cols-md-2 g-3">
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Estado:</span> <span>Sin adoptar</span></p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Género:</span> <span>Macho</span></p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Especie:</span> <span>Perro</span></p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Raza:</span> <span>Dálmata</span></p>
                        </div>
                        <div class="col">
                            <p class="mb-1"><span class="fw-bold">Fecha de nacimiento:</span>
                                <span>12/03/2022</span>
                            </p>
                        </div>
                    </div>
                    <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                        <button class="btn bg-orange-primary border-dark flex-fill">
                            Adoptar
                        </button>
                        <button class="btn bg-orange-primary border-dark flex-fill">
                            Visitar
                        </button>
                        <button class="btn bg-orange-primary border-dark flex-fill">
                            Apadrinar
                        </button>
                    </div>
                </div>
            </article>
        </section>
    </section>
</main>