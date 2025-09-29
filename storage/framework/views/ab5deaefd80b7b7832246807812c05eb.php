<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<sitemapindex xmlns:xsi="http://www.w3.org/2001/XMLSchema-instance" xsi:schemaLocation="http://www.sitemaps.org/schemas/sitemap/0.9 http://www.sitemaps.org/schemas/sitemap/0.9/siteindex.xsd" xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
    <sitemap>
        <loc><?php echo e(URL::to('/sitemap-misc.xml')); ?></loc>
    </sitemap>

    <?php if(getcong('menu_movies')): ?>
    <sitemap>
        <loc><?php echo e(URL::to('/sitemap-movies.xml')); ?></loc>
    </sitemap>
    <?php endif; ?>
    
    <?php if(getcong('menu_shows')): ?>
    <sitemap>
        <loc><?php echo e(URL::to('/sitemap-show.xml')); ?></loc>
    </sitemap>
    <?php endif; ?>

    <?php if(getcong('menu_sports')): ?>
    <sitemap>
        <loc><?php echo e(URL::to('/sitemap-sports.xml')); ?></loc>
    </sitemap>
    <?php endif; ?>

    <?php if(getcong('menu_livetv')): ?>
    <sitemap>
        <loc><?php echo e(URL::to('/sitemap-livetv.xml')); ?></loc>
    </sitemap>
    <?php endif; ?>
    
</sitemapindex>

<?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/pages/sitemap.blade.php ENDPATH**/ ?>