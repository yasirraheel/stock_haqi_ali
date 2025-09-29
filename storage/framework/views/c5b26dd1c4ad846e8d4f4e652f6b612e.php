<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">
     
    <?php $__currentLoopData = $movies_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $i => $movies_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>     
    <url>        
        <loc><?php echo e(URL::to('movies/details/'.$movies_data->video_slug.'/'.$movies_data->id)); ?></loc>
        <changefreq>Daily</changefreq>
        <priority>0.8</priority>
     </url>        
    <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?> 
     
</urlset><?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/pages/sitemap_movies.blade.php ENDPATH**/ ?>