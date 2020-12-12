<?php
/**
 * 分类
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<div class="col-mb-12 col-8" id="main" role="main">
    <article class="post categories" itemscope itemtype="http://schema.org/BlogPosting">
        <section class="widget">
            <h3 class="widget-title"><?php _e('所有分类'); ?></h3>
            <ul class="widget-list row">
                <?php $this->widget('Widget_Metas_Category_List')->to($categories); ?>
                <?php while($categories->next()): ?>
                    <li class="col-6 col-mb-12"><a itemprop="url" href="<?php $categories->permalink() ?>"><?php $categories->name() ?></a>（<?php $categories->count() ?>）<p class="description"><?php $categories->description() ?></p></li>
                <?php endwhile; ?>
            </ul>
        </section>
    </article>

</div><!-- end #main-->

<?php $this->need('sidebar.php'); ?>
<?php $this->need('footer.php'); ?>
