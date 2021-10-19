<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<?php $this->need('header.php'); ?>

<div class="col-mb-12 col-offset-1 col-10" id="main" role="main">
    <article class="post" itemscope itemtype="http://schema.org/BlogPosting">
        <h1 class="post-title" itemprop="name headline">
            <a itemprop="url"
               href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
        </h1>
        <ul class="post-meta">
            <li itemprop="author" itemscope itemtype="http://schema.org/Person">
                <?php _e('作者: '); ?><a itemprop="name"
                                       href="<?php $this->author->permalink(); ?>"
                                       rel="author"><?php $this->author(); ?></a>
            </li>
            <li><?php _e('时间: '); ?>
                <time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time>
            </li>
            <li><?php _e('分类: '); ?><?php $this->category(','); ?></li>
            <li itemprop="keywords" class="tags"><?php _e('标签: '); ?><?php $this->tags(', ', true, '无'); ?></li>
        </ul>
        <div class="post-content" itemprop="articleBody">
            <?php $this->content(); ?>
        </div>
        <?php $this->related(5,'author')->to($relatedPosts); ?>
        <?php if($relatedPosts->have()): ?>
            <h3><?php _e('相关文章'); ?></h3>
            <ul>
                <?php while ($relatedPosts->next()): ?>
                    <li itemprop="name headline">
                        <a itemprop="url" href="<?php $relatedPosts->permalink(); ?>"
                           title="<?php $relatedPosts->title(); ?>"><?php $relatedPosts->title(); ?></a>
                    </li>
                <?php endwhile; ?>
            </ul>
        <?php endif; ?>
    </article>

    <ul class="post-near">
        <li>上一篇: <?php $this->thePrev('%s', '没有了'); ?></li>
        <li>下一篇: <?php $this->theNext('%s', '没有了'); ?></li>
    </ul>

    <?php $this->need('comments.php'); ?>
</div><!-- end #main-->

<?php $this->need('footer.php'); ?>
