<?php
$current_date = new DateTime();
$current_date->setTime(0, 0, 0);
?>
<div class="table-responsive p-0">
    <table class="table table-striped table-hover align-middle">
        <thead class="bg-orange-primary border-dark">
            <tr>
                <?php if ($_SESSION['user']['role'] != "usuario"): ?>
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
                <th>Acciones</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($reservations as $reservation): ?>
                <tr>
                    <?php if ($_SESSION['user']['role'] != "usuario"): ?>
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
                        <?= !empty($reservation['monitor_user_name'])
                            ? htmlspecialchars($reservation['monitor_user_name'])
                            : 'Sin especificar' ?>
                    </td>
                    <td><?= htmlspecialchars($reservation['reason']) ?></td>
                    <td><?= htmlspecialchars($reservation['companions']) ?></td>
                    <td><?= date('H:i', strtotime($reservation['start_time'])) ?> -
                        <?= date('H:i', strtotime($reservation['end_time'])) ?>
                    </td>
                    <td><?= date('d/m/Y', strtotime($reservation['date'])) ?></td>
                    <td><?= ucfirst(htmlspecialchars($reservation['status'])) ?></td>
                    <td>
                        <div class="d-flex gap-2">
                            <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                                <?php if ($reservation['status'] == "pendiente"): ?>
                                    <a href="<?= BASE_URL ?>modificar_reserva/<?= $reservation['id'] ?>"
                                        class="btn btn-sm bg-orange-primary d-flex align-items-center gap-1">
                                        <i class="bi bi-pencil"></i>
                                        <span>Modificar</span>
                                    </a>
                                    <?php if ($reservation['monitor_id']): ?>
                                        <button class="btn btn-sm accept-btn btn-success d-flex align-items-center gap-1"
                                            data-id="<?= $reservation['id'] ?>">
                                            <i class="bi bi-check-circle"></i>
                                            <span>Aceptar</span>
                                        </button>
                                    <?php endif; ?>
                                    <button class="btn btn-sm deny-btn btn-warning d-flex align-items-center gap-1"
                                        data-id="<?= $reservation['id'] ?>">
                                        <i class="bi bi-x-circle"></i>
                                        <span>Denegar</span>
                                    </button>
                                <?php endif; ?>
                                <?php if (new DateTime($reservation['date']) > $current_date): ?>
                                    <button class="btn btn-sm delete-btn btn-danger d-flex align-items-center gap-1"
                                        data-id="<?= $reservation['id'] ?>">
                                        <i class="bi bi-trash3"></i>
                                        <span>Eliminar</span>
                                    </button>
                                <?php endif; ?>
                            <?php elseif ($_SESSION['user']['role'] == "usuario"): ?>
                                <?php if (new DateTime($reservation['date']) > $current_date && ($reservation['status'] == "pendiente" || $reservation['status'] == "aceptada")): ?>
                                    <?php if ($reservation['status'] == "pendiente"): ?>
                                        <a href="<?= BASE_URL ?>modificar_solicitud_reserva/<?= $reservation['id'] ?>"
                                            class="btn btn-sm bg-orange-primary d-flex align-items-center gap-1">
                                            <i class="bi bi-pencil"></i>
                                            <span>Modificar</span>
                                        </a>
                                    <?php endif; ?>
                                    <button class="btn btn-sm cancel-btn btn-danger d-flex align-items-center gap-1"
                                        data-id="<?= $reservation['id'] ?>">
                                        <i class="bi bi-trash3"></i>
                                        <span>Cancelar</span>
                                    </button>
                                <?php endif; ?>
                            <?php else: ?>
                                <?php
                                if ($reservation['status'] == "pendiente" && $_SESSION['user']['id'] == $reservation['monitor_id']): ?>
                                    <button class="btn btn-sm accept-btn btn-success d-flex align-items-center gap-1"
                                        data-id="<?= $reservation['id'] ?>">
                                        <i class="bi bi-check-circle"></i>
                                        <span>Aceptar</span>
                                    </button>
                                <?php endif; ?>
                                <?php
                                $is_mine = !empty($reservation['monitor_id']) && $_SESSION['user']['id'] == $reservation['monitor_id'];
                                if (
                                    new DateTime($reservation['date']) > $current_date && $reservation['status'] == "pendiente" && (!$reservation['monitor_id'] ||
                                        $is_mine)
                                ): ?>
                                    <button class="btn btn-sm assign-monitor-btn <?= $is_mine ? 'btn-warning' : 'btn-success' ?>"
                                        data-id="<?= $reservation['id'] ?>" data-action="<?= $is_mine ? 'leave' : 'take' ?>">
                                        <i class="bi <?= $is_mine ? 'bi-x-circle' : 'bi-check-circle' ?>"></i>
                                        <span><?= $is_mine ? 'Dejar' : 'Tomar' ?></span>
                                    </button>
                                <?php endif; ?>
                            <?php endif; ?>
                        </div>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>