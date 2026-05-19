<header>
    <nav class="navbar navbar-expand-md bg-orange-primary p-2 border-bottom border-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL ?>inicio">
                <img src="<?= BASE_URL ?>img/logo.png" class="logo" alt="">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto text-center text-md-start">
                    <?php if (!empty($_SESSION['user'])): ?>
                        <a class="text-black btn" href="<?= BASE_URL ?>perfil"><?= $_SESSION['user']['user_name'] ?></a>
                        <li class="nav-item d-md-none"><a href="<?= BASE_URL ?>animales"
                                class="text-black btn w-100">Animales</a>
                        </li>
                        <li class="nav-item d-md-none"><a href="<?= BASE_URL ?>salas" class="text-black btn w-100">Salas</a></li>
                        <?php if ($_SESSION['user']['role'] == "monitor"): ?>
                            <li class="nav-item d-md-none"></li><a href="<?= BASE_URL ?>reservas"
                                class="text-black btn w-100">Reservas</a></li>
                        <?php endif; ?>
                        <?php if ($_SESSION['user']['role'] == "administrador"): ?>
                            <li class="nav-item d-md-none"><a href="<?= BASE_URL ?>usuarios"
                                    class="text-black btn w-100">Usuarios</a></li>
                            <li class="nav-item d-md-none"><a href="<?= BASE_URL ?>reservas"
                                    class="text-black btn w-100">Reservas</a></li>
                            <li class="nav-item d-md-none"><a href="<?= BASE_URL ?>especies"
                                    class="text-black btn w-100">Especies</a></li>
                            <li class="nav-item d-md-none"><a href="<?= BASE_URL ?>informes"
                                    class="text-black btn w-100">Informes</a></li>
                        <?php else: ?>
                            <?php if ($_SESSION['user']['role'] == "usuario"): ?>
                                <li class="nav-item d-md-none"><a href="<?= BASE_URL ?>mis_animales"
                                        class="text-black btn w-100">Mis animales</a></li>
                            <?php endif; ?>
                            <li class="nav-item d-md-none"><a href="<?= BASE_URL ?>mis_reservas"
                                    class="text-black btn w-100">Mis reservas</a></li>
                        <?php endif; ?>
                        <?php if ($_SESSION['user']['role'] != "monitor"): ?>
                            <li class="nav-item d-md-none"><a href="<?= BASE_URL ?>solicitudes_de_adopcion" class="text-black btn w-100">Adopciones</a></li>
                            <li class="nav-item d-md-none"><a href="<?= BASE_URL ?>apadrinamientos" class="text-black btn w-100">Apadrinamientos</a></li>
                        <?php endif; ?>
                        <li class="nav-item"><a href="<?= BASE_URL ?>cerrar_sesion" class="text-black btn">Cerrar
                                sesión</a></li>
                    <?php else: ?>
                        <li class="nav-item"><a href="<?= BASE_URL ?>inicio" class="text-black btn">Inicio</a></li>
                        <li class="nav-item"><a href="#footer" class="text-black btn">Contacto</a></li>
                        <li class="nav-item"><a href="<?= BASE_URL ?>iniciar_sesion" class="text-black btn">Iniciar
                                sesión</a></li>
                        <li class="nav-item"><a href="<?= BASE_URL ?>registrarse" class="text-black btn">Registrarse</a>
                        </li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <?php if ($view == "inicio"): ?>
        <div id="banner" class="banner p-5">
            <div class="text-center bg-dark bg-opacity-75 text-white p-5">
                <h1 class="display-2 fw-bold">Protectora de animales de La Hoya</h1>
                <p class="h5">En esta página web puedes adoptar, apadrinar o reservar visitas para ver a los animales
                    que están
                    buscando un hogar</p>
            </div>
        </div>
    <?php endif; ?>
</header>