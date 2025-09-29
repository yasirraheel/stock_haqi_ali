<?php
// config
$link_limit = 8; // maximum number of links (a little bit inaccurate, but will be ok for now)
?>

<?php if($paginator->lastPage() > 1): ?>

<div class="col-xs-12"> 
        <nav class="navigation pagination" role="navigation">
          <div class="nav-links">

    <ul>
        <li class="<?php echo e(($paginator->currentPage() == 1) ? ' disabled' : ''); ?>">
            <a class="prev page-numbers" href="<?php echo e($paginator->url(1)); ?>" title="pagination"><i class="fa fa-caret-left"></i></a>
         </li>
        <?php for($i = 1; $i <= $paginator->lastPage(); $i++): ?>
            <?php
            $half_total_links = floor($link_limit / 2);
            $from = $paginator->currentPage() - $half_total_links;
            $to = $paginator->currentPage() + $half_total_links;
            if ($paginator->currentPage() < $half_total_links) {
               $to += $half_total_links - $paginator->currentPage();
            }
            if ($paginator->lastPage() - $paginator->currentPage() < $half_total_links) {
                $from -= $half_total_links - ($paginator->lastPage() - $paginator->currentPage()) - 1;
            }
            ?>
            <?php if($from < $i && $i < $to): ?>
                <li class="<?php echo e(($paginator->currentPage() == $i) ? ' active' : ''); ?>">
                    <a class="page-numbers <?php echo e(($paginator->currentPage() == $i) ? ' active' : ''); ?>" href="<?php echo e($paginator->url($i)); ?>" title="pagination"><?php echo e($i); ?></a>
                </li>
            <?php endif; ?>
        <?php endfor; ?>
        <li class="<?php echo e(($paginator->currentPage() == $paginator->lastPage()) ? ' disabled' : ''); ?>">
            <a class="next page-numbers" href="<?php echo e($paginator->url($paginator->lastPage())); ?>" title="pagination"><i class="fa fa-caret-right"></i></a>
        </li>
    </ul>

        </div>
    </nav>
</div>    
<?php endif; ?>

           <?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/_particles/pagination.blade.php ENDPATH**/ ?>