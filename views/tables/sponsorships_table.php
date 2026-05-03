<table class="table table-striped table-hover align-middle table-responsive">
    <thead>
        <tr>
            <th>Usuario</th>
            <th>Animal</th>
            <th>Mensaje</th>
            <th>Importe</th>
            <th>Fecha</th>
        </tr>
    </thead>
    <tbody>
        <?php foreach ($sponsorships as $sponsorship): ?>
            <tr>
                <td>
                    <?= htmlspecialchars($sponsorship['user_user_name']) ?>
                </td>
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