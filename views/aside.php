<aside class="bg-orange-primary pt-3 pb-3 ps-0 pe-0 col-auto border-end border-dark d-none d-md-flex flex-column">
    <a href="<?= BASE_URL ?>animales" class="btn fs-4 text-start">Animales</a>
    <a href="<?= BASE_URL ?>salas" class="btn fs-4 text-start">Salas</a>
    <?php if ($_SESSION['user']['role'] == "administrador"): ?>
        <a href="<?= BASE_URL ?>usuarios" class="btn fs-4 text-start">Usuarios</a>
        <a href="<?= BASE_URL ?>reservas" class="btn fs-4 text-start">Reservas</a>
        <a href="<?= BASE_URL ?>especies" class="btn fs-4 text-start">Especies</a>
        <a href="<?= BASE_URL ?>informes" class="btn fs-4 text-start">Informes</a>
    <?php else: ?>
        <?php if ($_SESSION['user']['role'] == "usuario"): ?>
            <a href="<?= BASE_URL ?>mis_animales" class="btn fs-4 text-start">Mis animales</a>
        <?php endif; ?>
        <a href="<?= BASE_URL ?>mis_reservas" class="btn fs-4 text-start">Mis reservas</a>
    <?php endif; ?>
    <?php if ($_SESSION['user']['role'] != "monitor"): ?>
        <a href="<?= BASE_URL ?>solicitudes_de_adopcion" class="btn fs-4 text-start">Adopciones</a>
        <a href="<?= BASE_URL ?>apadrinamientos" class="btn fs-4 text-start">Apadrinamientos</a>
    <?php endif; ?>
</aside>