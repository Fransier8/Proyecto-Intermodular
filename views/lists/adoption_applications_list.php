<article id="adoption-applications-list" class="row g-3">
    <?php
    require 'views/tables/adoption_applications_table.php';
    ?>
</article>
<nav class="mt-4 d-flex justify-content-center">
    <ul class="pagination">
        <li class="page-item <?= $page <= 1 ? 'disabled' : '' ?>">
            <a class="page-link text-black" href="#" aria-label="Anterior" data-page="<?= $page - 1 ?>">
                &laquo;
            </a>
        </li>

        <?php for ($i = 1; $i <= $total_pages; $i++): ?>
            <li class="page-item <?= $i == $page ? 'active' : '' ?>">
                <a class="page-link text-black" href="#" data-page="<?= $i ?>">
                    <?= $i ?>
                </a>
            </li>
        <?php endfor; ?>

        <li class="page-item <?= $page >= $total_pages ? 'disabled' : '' ?>">
            <a class="page-link text-black" href="#" aria-label="Siguiente" data-page="<?= $page + 1 ?>">
                &raquo;
            </a>
        </li>
    </ul>
</nav>