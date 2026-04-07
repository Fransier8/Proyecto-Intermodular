<article id="animals-list" class="row g-3">
    <?php foreach ($animals as $animal):
        require 'views/cards/animal_card.php';
    endforeach; ?>
</article>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link text-black" href="#" aria-label="Anterior" data-page="<?= $page - 1 ?>">
                &laquo;
            </a>
        </li>

        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link text-black" href="#" data-page="<?= $i ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $totalPages ? 'disabled' : '' ?>">
            <a class="page-link text-black" href="#" aria-label="Siguiente" data-page="<?= $page + 1 ?>">
                &raquo;
            </a>
        </li>
    </ul>
</nav>