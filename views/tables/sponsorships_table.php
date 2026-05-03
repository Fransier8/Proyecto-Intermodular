<div class="table-responsive p-0">
    <table class="table table-striped table-hover align-middle">
        <thead class="bg-orange-primary border-dark">
            <tr>
                <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                    <th>Usuario</th>
                <?php endif; ?>
                <th>Animal</th>
                <th>Mensaje</th>
                <th>Importe</th>
                <th>Fecha</th>
            </tr>
        </thead>
        <tbody>
            <?php foreach ($sponsorships as $sponsorship): ?>
                <tr>
                    <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                        <td>
                            <?= htmlspecialchars($sponsorship['user_user_name']) ?>
                        </td>
                    <?php endif; ?>
                    <td>
                        <?= htmlspecialchars($sponsorship['animal_name']) ?>
                    </td>
                    <td><?= htmlspecialchars($sponsorship['message']) ?></td>
                    <td><?= htmlspecialchars($sponsorship['amount']) ?></td>
                    <td><?= htmlspecialchars($sponsorship['date']) ?></td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
</div>