<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="col-mb-12 col-8" id="main" role="main">
    <h3 class="archive-title"><?php $this->archiveTitle([
            'category' => _t('分类 %s 下的文章'),
            'search'   => _t('包含关键字 %s 的文章'),
            'tag'      => _t('标签 %s 下的文章'),
            'author'   => _t('%s 发布的文章')
        ], '', '');
        $archiveYear = null; ?></h3>
    <?php if ($this->have()): ?>
        <article class="post" itemscope itemtype="http://schema.org/BlogPosting">
            <?php while ($this->next()): ?>
                <?php if ($archiveYear != $this->date->year):
                    $archiveYear = $this->date->year; ?>
                    <h2 class="post post-title more"><?php _e($archiveYear . '年'); ?></h2>
                <?php endif; ?>
                <ul>
                    <li>
                        <span itemprop="name headline">
                            <a itemprop="url" href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                        </span>
                        <span class="post-meta comment-meta">|
                            <span itemprop="interactionCount">
                                <a href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('注释', '1 条注释', '%d 条注释'); ?></a>
                            </span>（
                            <span itemprop="author" itemscope itemtype="http://schema.org/Person">
                                <a itemprop="name" href="<?php $this->author->permalink(); ?>" rel="author"><?php $this->author(); ?></a>
                            </span>@
                            <span>
                                <time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time>
                            </span>）
                        </span>
                    </li>
                </ul>
            <?php endwhile; ?>
        </article>
    <?php else: ?>
        <article class="post">
            <h2 class="post-title"><?php _e('没有找到内容'); ?></h2>
        </article>
    <?php endif; ?>

    <?php $this->pageNav('&laquo; 前一页', '后一页 &raquo;'); ?>
</div><!-- end #main -->

<?php $this->need('sidebar.php'); ?>
<?php $this->need('footer.php'); ?>
