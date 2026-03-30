<main class="container-fluid bg-orange-300">
    <section class="row">
        <?php
        require 'views/aside.php';
        ?>
        <section class="col p-3">
            <nav aria-label="breadcrumb">
                <ol class="breadcrumb">
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>users">Usuarios</a></li>
                    <li class="breadcrumb-item"><a href="<?= BASE_URL ?>user/<?= $user['id'] ?>">Usuario</a></li>
                    <li class="breadcrumb-item active" aria-current="page">Modificar usuario</li>
                </ol>
            </nav>
            <h1>Usuario</h1>
            <article class="row g-4">
                <div class="col-12 col-md-12 fs-5">
                    <form action="<?= BASE_URL ?>edit_user" method="post" class="row g-4">
                        <input type="hidden" name="id" value="<?= $user['id'] ?>">

                        <div class="col-12 col-md-12 fs-5">
                            <h2 class="mb-3"><?= htmlspecialchars($user['user_name']) ?></h2>
                            <div class="row row-cols-1 row-cols-md-2 g-3">
                                <!-- Nombre -->
                                <div class="col">
                                    <label class="form-label fw-bold">Nombre:</label>
                                    <input type="text" name="name" class="form-control" required
                                        value="<?= htmlspecialchars($user['name']) ?>">
                                </div>

                                <!-- Email -->
                                <div class="col">
                                    <label class="form-label fw-bold">Email:</label>
                                    <input type="email" name="email" class="form-control" required
                                        value="<?= htmlspecialchars($user['email']) ?>">
                                </div>

                                <!-- Identificación -->
                                <div class="col">
                                    <label class="form-label fw-bold">Identificación:</label>
                                    <input type="text" name="identification" class="form-control" required
                                        value="<?= htmlspecialchars($user['identification']) ?>">
                                </div>

                                <!-- Rol -->
                                <div class="col">
                                    <label class="form-label fw-bold">Rol:</label>
                                    <select name="role" class="form-select" <?= $user['id'] == $_SESSION['usuario']['id'] ? 'disabled' : '' ?>>
                                        <option value="cliente" <?= $user['role'] == 'cliente' ? 'selected' : '' ?>>Cliente
                                        </option>
                                        <option value="empleado" <?= $user['role'] == 'empleado' ? 'selected' : '' ?>>
                                            Empleado</option>
                                        <option value="administrador" <?= $user['role'] == 'administrador' ? 'selected' : '' ?>>Administrador</option>
                                    </select>
                                </div>

                                <!-- Teléfono -->
                                <div class="col">
                                    <label class="form-label fw-bold">Teléfono:</label>
                                    <input type="tel" name="phone" class="form-control" required
                                        value="<?= htmlspecialchars($user['phone']) ?>">
                                </div>

                                <!-- Dirección -->
                                <div class="col">
                                    <label class="form-label fw-bold">Dirección:</label>
                                    <input type="text" name="address" class="form-control" required
                                        value="<?= htmlspecialchars($user['address']) ?>">
                                </div>

                                <!-- Activo -->
                                <div class="col">
                                    <div class="form-check mt-3">
                                        <input type="checkbox" class="form-check-input" name="active" value="1"
                                            <?= $user['active'] ? 'checked' : '' ?>
                                            onclick="return confirm('¿Seguro que quieres cambiar el estado de este usuario?')">
                                        <label class="form-check-label">Activo</label>
                                    </div>
                                </div>
                            </div>

                            <!-- Botones -->
                            <div class="d-flex flex-column flex-sm-row gap-2 mt-4">
                                <button type="submit"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Guardar</button>
                                <a href="<?= BASE_URL ?>user/<?= $user['id'] ?>"
                                    class="btn bg-orange-primary border-dark border-1 flex-fill">Cancelar</a>
                            </div>
                        </div>
                    </form>
                </div>
            </article>
        </section>
    </section>
</main>