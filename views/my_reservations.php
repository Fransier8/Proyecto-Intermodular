<main class="container-fluid bg-orange-300">
    <section class="row">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <h1>Mis reservas</h1>
            <h4>Búsqueda y filtros</h4>
            <form class="row g-2 align-items-end">
                <div class="col-12 col-md-3">
                    <label class="form-label">Ordenar por</label>
                    <select class="form-select" name="ordenar">
                        <option value="jovenes">Más jóvenes</option>
                        <option value="viejos">Más viejos</option>
                        <option value="nombre_asc">Nombre A–Z</option>
                        <option value="nombre_desc">Nombre Z–A</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Especie</label>
                    <select class="form-select" name="especie">
                        <option value="">Todas</option>
                        <option value="perro">Perro</option>
                        <option value="gato">Gato</option>
                        <option value="conejo">Conejo</option>
                    </select>
                </div>
                <div class="col-12 col-md-2">
                    <label class="form-label">Estado</label>
                    <select class="form-select" name="estado">
                        <option value="">Todos</option>
                        <option value="sin_adoptar">Sin adoptar</option>
                        <option value="adoptado">Adoptado</option>
                        <option value="apadrinado">Apadrinado</option>
                    </select>
                </div>
                <div class="col-12 col-md-3">
                    <label class="form-label">Buscar</label>
                    <input name="buscar" type="text" class="form-control">
                </div>
                <div class="col-12 col-md-2">
                    <button type="submit" class="btn bg-orange-primary w-100">
                        Buscar
                    </button>
                </div>
            </form>
            <section class="container-fluid mt-4">
                <article class="row g-3">
                    <div class="col-12 col-sm-6 col-md-4 col-lg-3">
                        <div class="bg-orange-primary rounded card p-3">
                            <h4 class="card-title">Perrito</h4>
                            <img class="card-img-top mb-3" alt="Perrito" loading="lazy"
                                src="https://www.purina.es/sites/default/files/styles/ttt_image_510/public/2024-02/sitesdefaultfilesstylessquare_medium_440x440public2022-07Dalmatian1.jpg?itok=B_1aRoJh">
                            <p>Especie: Perro</p>
                            <p>Raza: Dálmata</p>
                            <p>Estado: Sin adoptar</p>
                            <a href="<?= BASE_URL ?>reserva" class="btn bg-orange-primary border-dark border-1">Más
                                información</a>
                        </div>
                    </div>
                </article>
                <nav class="mt-4 d-flex justify-content-center">
                    <ul class="pagination">
                        <li class="page-item disabled">
                            <a class="page-link text-black" href="#" aria-label="Anterior">
                                &laquo;
                            </a>
                        </li>
                        <li class="page-item active">
                            <a class="page-link bg-orange-primary border-dark text-black" href="#">1</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link text-black" href="#">2</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link text-black" href="#">3</a>
                        </li>
                        <li class="page-item">
                            <a class="page-link text-black" href="#" aria-label="Siguiente">
                                &raquo;
                            </a>
                        </li>
                    </ul>
                </nav>
            </section>
        </section>
    </section>
</main>