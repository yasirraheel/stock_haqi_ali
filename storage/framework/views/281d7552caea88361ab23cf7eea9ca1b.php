<?php echo '<?xml version="1.0" encoding="UTF-8"?>'; ?>

<urlset xmlns="http://www.sitemaps.org/schemas/sitemap/0.9">    
    
    <url>        
        <loc><?php echo e(URL::to('/')); ?></loc>
        <changefreq>Daily</changefreq>
        <priority>1.0</priority>
     </url>
 
     <?php $__currentLoopData = $pages_list; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $page_data): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>     
     <url>        
        <loc><?php echo e(URL::to('page/'.$page_data->page_slug)); ?></loc>
        <changefreq>Yearly</changefreq>
        <priority>0.6</priority>
     </url>
     <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>         
         
</urlset><?php /**PATH /home/u273790872/domains/cineworm.org/public_html/stock/resources/views/pages/sitemap_misc.blade.php ENDPATH**/ ?>