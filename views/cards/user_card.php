<div class="col-12 col-sm-6 col-md-4 col-lg-3">
    <div class="bg-orange-primary rounded card p-3">
        <h4 class="card-title"><?= htmlspecialchars($user['user_name']) ?></h4>
        <p>Nombre: <?= htmlspecialchars($user['name']) ?></p>
        <p>Email: <?= htmlspecialchars($user['email']) ?></p>
        <p>Rol: <?= htmlspecialchars($user['role']) ?></p>
        <a href="<?= BASE_URL ?>user" class="btn bg-orange-primary border-dark border-1">Más
            información</a>
        <form action="<?= BASE_URL ?>change_user_status" method="post"
            onsubmit="return confirm('¿Seguro que quieres cambiar el estado de este usuario?');">
            <input type="hidden" name="id" value="<?= htmlspecialchars($user['id']) ?>">
            <input type="hidden" name="active" value="<?= $user['active'] ? 0 : 1 ?>">
            <button type="submit" class="btn btn-sm <?= $user['active'] ? 'btn-warning' : 'btn-success' ?>">
                <i class="bi <?= $user['active'] ? 'bi-person-x' : 'bi-person-check' ?>"></i>
                <?= $user['active'] ? 'Desactivar' : 'Activar' ?>
            </button>
        </form>
    </div>
</div>