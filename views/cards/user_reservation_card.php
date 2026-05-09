<div class="col-12 col-sm-6 col-md-4 col-lg-3 d-flex">
    <div class="bg-orange-primary rounded card p-3 w-100 h-100">
        <h4 class="card-title"><?= htmlspecialchars($user['user_name']) ?></h4>
        <p>Nombre: <?= htmlspecialchars($user['name']) ?></p>
        <p>Email: <?= htmlspecialchars($user['email']) ?></p>
        <p>Identificación: <?= htmlspecialchars($user['identification']) ?></p>
        <div class="mt-auto d-flex flex-column gap-2">
            <button type="button" class="btn border-dark border-1 select-user-btn select-monitor-btn"
                data-id="<?= $user['id'] ?>" data-name="<?= htmlspecialchars($user['user_name']) ?>">
                Seleccionar
            </button>
        </div>
    </div>
</div>