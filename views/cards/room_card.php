<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <div class="bg-orange-primary rounded card p-3">
        <h4 class="card-title"><?= htmlspecialchars($room['code']) ?></h4>
        <img class="card-img-top mb-3" alt="Perrito" loading="lazy"
            src="https://www.purina.es/sites/default/files/styles/ttt_image_510/public/2024-02/sitesdefaultfilesstylessquare_medium_440x440public2022-07Dalmatian1.jpg?itok=B_1aRoJh">
        <p>Nombre: <?= htmlspecialchars($room['name']) ?></p>
        <p>Ubicación: <?= htmlspecialchars($room['location']) ?></p>
        <p>Capacidad: <?= htmlspecialchars($room['capacity']) ?></p>
        <a href="<?= BASE_URL ?>room" class="btn bg-orange-primary border-dark border-1">Más
            información</a>
    </div>
</div>