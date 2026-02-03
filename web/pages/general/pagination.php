<?php
if ($pageCount > 1) {
?>

    <nav>
        <ul class="pagination" data-total-pages="<?= $pageCount ?>">
            <li class="page-item previous disabled">
                <a data-page="previous" href="#" class="page-link">Previous</a> 
            </li>
            <li class="page-item page-numbered-item active">
                <a data-page="0" href="#" class="page-link">1</a>
            </li>
<?php 
    for ($i = 1; $i < $pageCount; $i++) {
?>
        <li class="page-item page-numbered-item">
            <a data-page="<?= $i ?>" href="#" class="page-link"><?= $i + 1 ?></a>
        </li>
<?php
    }
?>
            <li class="page-item next">
                <a data-page="next" href="#" class="page-link">Next</a>
            </li>
        </ul>
    </nav>
<?php
}
?>