<?php
$current_date = new DateTime();
$current_date->setTime(0, 0, 0);
?>
<div class="table-responsive p-0">
    <table class="table table-striped table-hover align-middle">
        <thead class="bg-orange-primary border-dark">
            <tr>
                <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                    <th>Usuario</th>
                <?php endif; ?>
                <th>Animal</th>
                <th>Sala</th>
                <th>Monitor</th>
                <th>Motivo</th>
                <th>Acompañantes</th>
                <th>Hora</th>
                <th>Fecha</th>
                <th>Estado</th>
                <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                    <th>Acciones</th>
                <?php endif; ?>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                        <td>
                            <?= htmlspecialchars($reservation['user_user_name']) ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?= htmlspecialchars($reservation['animal_name']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($reservation['room_code']) ?>
                    </td>
                    <td>
                        <?= htmlspecialchars($reservation['monitor_user_name']) ?>
                    </td>
                    <td><?= htmlspecialchars($reservation['reason']) ?></td>
                    <td><?= htmlspecialchars($reservation['companions']) ?></td>
                    <td><?= date('H:i', strtotime($reservation['start_time'])) ?> - <?= date('H:i', strtotime($reservation['end_time'])) ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($reservation['date'])) ?></td>
                    <td><?= htmlspecialchars($reservation['status']) ?></td>
                    <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                        <td>
                            <div class="d-flex gap-2">
                                <?php if ($reservation['status'] == "pendiente"): ?>
                                    <a href="<?= BASE_URL ?>modificar_reserva/<?= $reservation['id'] ?>"
                                        class="btn btn-sm bg-orange-primary d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil"></i>
                                        <span>Modificar</span>
                                    </a>
                                <?php endif; ?>
                                <?php if (new DateTime($reservation['date']) > $current_date): ?>
                                    <button class="btn btn-sm delete-btn btn-danger d-flex align-items-center gap-1"
                                        data-id="<?= $reservation['id'] ?>">
                                        <i class="bi bi-trash3"></i>
                                        <span>Eliminar</span>
                                    </button>
                                <?php endif; ?>
                            </div>
                        </td>
                    <?php endif; ?>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>