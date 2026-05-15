<main class="flex-fill container-fluid bg-orange-300 d-flex flex-column overflow-hidden">
    <section class="row flex-fill">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3 overflow-auto">
            <div class="d-flex align-items-center justify-content-between mb-2">
                <h1 class="mb-0">Informes</h1>
                <a href="<?= BASE_URL ?>crear_informe" class="btn bg-orange-primary rounded-pill btn-lg px-4">
                    Crear informe
                </a>
            </div>
            <section class="container-fluid mt-4">
                <article class="row g-3">
                    <div class="col-12 col-lg-6">
                        <h4>Usuarios con más animales adoptados</h4>
                        <div class="table-responsive p-0">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Animales adoptados</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users_with_most_animals as $user): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($user['user_name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($user['total_animals']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h4>Usuarios con más solicitudes de adopción</h4>
                        <div class="table-responsive p-0">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Solicitudes de adopción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users_with_most_adoption_applications as $user): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($user['user_name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($user['total_adoption_applications']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h4>Usuarios con más apadrinamientos</h4>
                        <div class="table-responsive p-0">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Apadrinamientos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users_with_most_sponsorships as $user): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($user['user_name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($user['total_sponsorships']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="table-responsive p-0">
                            <h4>Usuarios con más dinero gastado</h4>
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Total gastado</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users_by_spending as $user): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($user['user_name']) ?>
                                            </td>
                                            <td>
                                                <?= number_format($user['total_spent'], 2, ',', '.') ?> €
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h4>Usuarios con más reservas</h4>
                        <div class="table-responsive p-0">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Usuario</th>
                                        <th>Reservas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($users_with_most_reservations as $user): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($user['user_name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($user['total_reservations']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h4>Monitores con más reservas</h4>
                        <div class="table-responsive p-0">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Monitor</th>
                                        <th>Reservas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($monitors_with_most_reservations as $monitor): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($monitor['user_name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($monitor['total_reservations']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h4>Animales más visitados</h4>
                        <div class="table-responsive p-0">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Animal</th>
                                        <th>Visitas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($animals_with_most_reservations as $animal): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($animal['name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($animal['total_reservations']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h4>Animales con más solicitudes de adopción</h4>
                        <div class="table-responsive p-0">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Animal</th>
                                        <th>Solicitudes de adopción</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($animals_with_most_adoption_applications as $animal): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($animal['name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($animal['total_adoption_applications']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h4>Animales con más apadrinamientos</h4>
                        <div class="table-responsive p-0">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Animal</th>
                                        <th>Apadrinamientos</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($animals_with_most_sponsorships as $animal): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($animal['name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($animal['total_sponsorships']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <div class="table-responsive p-0">
                            <h4>Animales con más dinero recibido</h4>
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Animal</th>
                                        <th>Total recibido</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($animals_by_earnings as $animal): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($animal['name']) ?>
                                            </td>
                                            <td>
                                                <?= number_format($animal['total_earnings'], 2, ',', '.') ?> €
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h4>Salas más reservadas</h4>
                        <div class="table-responsive p-0">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Sala</th>
                                        <th>Reservas</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($rooms_with_most_reservations as $room): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($room['code']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($room['total_reservations']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="col-12 col-lg-6">
                        <h4>Especies con mayor número de animales</h4>
                        <div class="table-responsive p-0">
                            <table class="table table-striped table-hover align-middle">
                                <thead class="bg-orange-primary border-dark">
                                    <tr>
                                        <th>Especie</th>
                                        <th>Animales</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <?php foreach ($species_with_most_animals as $species): ?>
                                        <tr>
                                            <td>
                                                <?= htmlspecialchars($species['name']) ?>
                                            </td>
                                            <td>
                                                <?= htmlspecialchars($species['total_animals']) ?>
                                            </td>
                                        </tr>
                                    <?php endforeach; ?>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </article>
            </section>
        </section>
    </section>
</main>
<script>

</script>