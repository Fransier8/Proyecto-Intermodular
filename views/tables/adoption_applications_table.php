<div class="table-responsive p-0">
    <table class="table table-striped table-hover align-middle">
        <thead class="bg-orange-primary border-dark">
            <tr>
                <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                    <th>Usuario</th>
                <?php endif; ?>
                <th>Animal</th>
                <th>Mensaje</th>
                <th>Estado</th>
                <th>Fecha de solicitud</th>
                <th>Fecha de modificación</th>
                <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($adoption_applications as $adoption_application): ?>
                <tr>
                    <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                        <td>
                            <?= htmlspecialchars($adoption_application['user_user_name']) ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?= htmlspecialchars($adoption_application['animal_name']) ?>
                    </td>
                    <td><?= htmlspecialchars($adoption_application['message']) ?></td>
                    <td><?= htmlspecialchars($adoption_application['status']) ?></td>
                    <td><?= htmlspecialchars($adoption_application['application_date']) ?></td>
                    <td><?= htmlspecialchars($adoption_application['modification_date']) ?></td>
                    <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                        <td class="d-flex gap-2">
                            <?php if ($adoption_application['status'] == "pendiente" || $adoption_application['status'] == "reservado"): ?>
                                <a href="<?= BASE_URL ?>modificar_solicitud_de_adopcion/<?= $adoption_application['id'] ?>"
                                    class="btn btn-sm bg-orange-primary d-flex align-items-center gap-1">
                                    <i class="bi bi-pencil"></i>
                                    <span>Modificar</span>
                                </a>
                            <?php endif; ?>
                            <button class="btn btn-sm delete-btn btn-danger d-flex align-items-center gap-1"
                                data-id="<?= $adoption_application['id'] ?>">
                                <i class="bi bi-trash3"></i>
                                <span>Eliminar</span>
                            </button>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>