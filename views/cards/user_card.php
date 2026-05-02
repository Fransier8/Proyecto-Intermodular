<div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
    <div class="bg-orange-primary rounded card p-3 w-100 h-100">
        <h4 class="card-title"><?= htmlspecialchars($user['user_name']) ?></h4>
        <p>Nombre: <?= htmlspecialchars($user['name']) ?></p>
        <p>Email: <?= htmlspecialchars($user['email']) ?></p>
        <p>Rol: <?= htmlspecialchars($user['role']) ?></p>
        <div class="mt-auto d-flex flex-column gap-2">
            <a href="<?= BASE_URL ?>usuario/<?= $user['id'] ?>" class="btn bg-orange-primary border-dark border-1">Más
                información</a>
            <button class="btn change-status-btn <?= $user['active'] ? 'btn-warning' : 'btn-success' ?>"
                data-id="<?= $user['id'] ?>" data-active="<?= $user['active'] ? 0 : 1 ?>"
                <?= $user['id'] == $_SESSION['user']['id'] ? 'disabled' : '' ?>>
                <i class="bi <?= $user['active'] ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                <?= $user['active'] ? 'Desactivar' : 'Activar' ?>
            </button>
        </div>
    </div>
</div>