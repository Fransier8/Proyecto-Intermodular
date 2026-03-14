<header>
    <nav class="navbar navbar-expand-md bg-orange-primary p-2 border-bottom border-dark">
        <div class="container-fluid">
            <a class="navbar-brand" href="<?= BASE_URL ?>home">
                <img src="img/logo.png" class="logo" alt="">
            </a>
            <button class="navbar-toggler" type="button" data-bs-toggle="collapse" data-bs-target="#navbarContent"
                aria-controls="navbarContent" aria-expanded="false" aria-label="Toggle navigation">
                <span class="navbar-toggler-icon"></span>
            </button>
            <div class="collapse navbar-collapse" id="navbarContent">
                <ul class="navbar-nav ms-auto text-center text-md-start">
                    <li class="nav-item"><a href="<?= BASE_URL ?>home" class="text-black btn">Inicio</a></li>
                    <li class="nav-item"><a href="#footer" class="text-black btn">Contacto</a></li>
                    <li class="nav-item"><a href="<?= BASE_URL ?>login" class="text-black btn">Iniciar
                            sesión</a></li>
                    <li class="nav-item"><a href="<?= BASE_URL ?>login" class="text-black btn">Registrarse</a></li>
                    <?php if (!empty($_SESSION['user'])): ?>
                        <p><?= $_SESSION['user']['user_name'] ?></p>
                    <?php endif; ?>
                    <?php if ($view == "pets"): ?>
                        <li class="nav-item d-md-none"><a href="pets.html" class="text-black btn">Animales</a></li>
                        <li class="nav-item d-md-none"><a class="text-black btn">Salas</a></li>
                        <li class="nav-item d-md-none"><a class="text-black btn">Mis animales</a></li>
                        <li class="nav-item d-md-none"><a class="text-black btn">Mis reservas</a></li>
                    <?php endif; ?>
                </ul>
            </div>
        </div>
    </nav>
    <?php if ($view == "home"): ?>
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