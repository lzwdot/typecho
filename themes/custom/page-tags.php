<?php
/**
 * 标签
 * @package custom
 */
?>
<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="col-mb-12" id="main" role="main">
    <?php $this->widget('Widget_Metas_Category_List')->to($categories); ?>
    <?php if ($categories->have()): ?>
        <article class="post" itemscope itemtype="http://schema.org/BlogPosting">
            <h1 class="post-title" itemprop="name headline"><?php _e('分类'); ?></h1>
            <div class="post-content" itemprop="articleBody">
                <ul class="tags">
                    <?php while ($categories->next()): ?>
                        <li>
                            <a style="color: rgb(<?php echo(rand(0, 255)); ?>, <?php echo(rand(0, 255)); ?>, <?php echo(rand(0, 255)); ?>)"
                               href="<?php $categories->permalink(); ?>" rel="category"
                               title="<?php $categories->description(); ?>"><?php $categories->name(); ?></a>（<?php $categories->count(); ?>
                            ）
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </article>
    <?php endif; ?>

    <?php $this->widget('Widget_Metas_Tag_Cloud')->to($tags); ?>
    <?php if ($tags->have()): ?>
        <article class="post" itemscope itemtype="http://schema.org/BlogPosting">
            <h1 class="post-title" itemprop="name headline"><?php _e('标签'); ?></h1>
            <div class="post-content" itemprop="articleBody">
                <ul class="tags">
                    <?php while ($tags->next()): ?>
                        <li>
                            <a style="color: rgb(<?php echo(rand(0, 255)); ?>, <?php echo(rand(0, 255)); ?>, <?php echo(rand(0, 255)); ?>);font-size: calc(<?php $tags->split(5, 10, 20, 30); ?>/5em);"
                               href="<?php $tags->permalink(); ?>" rel="tag"
                               title="<?php $tags->count();
                               _e('项目') ?>"><?php $tags->name(); ?></a>
                        </li>
                    <?php endwhile; ?>
                </ul>
            </div>
        </article>
    <?php endif; ?>

    <?php $this->need('comments.php'); ?>
</div><!-- end #main-->

<?php $this->need('footer.php'); ?>
