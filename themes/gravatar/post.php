<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
    <?php $this->need('header.php'); ?>

    <div class="col-mb-12 col-8" id="main" role="main">
        <article class="post" itemscope itemtype="http://schema.org/BlogPosting">
            <h1 class="post-title" itemprop="name headline"><a itemprop="url" href="<?php $this->permalink() ?>"><?php $this->title() ?></a></h1>
            <ul class="post-meta">
                <li itemprop="author" itemscope itemtype="http://schema.org/Person"><?php _e('作者: '); ?><a itemprop="name" href="<?php $this->author->permalink(); ?>" rel="author"><?php $this->author(); ?></a></li>
                <li><?php _e('时间: '); ?><time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time></li>
                <li><?php _e('分类: '); ?><?php $this->category(','); ?></li>
                <?php if($this->user->hasLogin()): ?>
                    <li><a href="<?php $this->options->adminUrl('write-post.php?cid='.$this->cid); ?>"><?php _e('编辑'); ?></a></li>
                <?php endif; ?>
            </ul>
            <div class="post-content" itemprop="articleBody">
                <?php $this->content(); ?>
            </div>
            <p itemprop="keywords" class="tags"><?php _e('标签: '); ?><?php $this->tags(', ', true, '无'); ?></p>
        </article>

        <?php $this->need('comments.php'); ?>

        <ul class="post-near">
            <li>上一篇: <?php $this->thePrev('%s','没有了'); ?></li>
            <li>下一篇: <?php $this->theNext('%s','没有了'); ?></li>
        </ul>
    </div><!-- end #main-->

    <div class="col-mb-12 col-offset-1 col-3 kit-hidden-tb article-catalog" id="secondary" role="complementary">
        <section class="widget">
            <h3 class="widget-title"><?php _e('文章导航'); ?></h3>
            <ul class="widget-list">
                <?php if(isset($this->categories[0])): ?>
                    <?php $this->widget('Widget_Archive@myCustomCategory', 'type=category&pageSize=50', 'mid='.$this->categories[0]['mid'])->to($categoryPosts); $categoryPosts = array_reverse($categoryPosts->stack)?>
                    <?php while($categoryPost = current($categoryPosts)): next($categoryPosts); ?>
                        <li class="<?php if($this->cid === $categoryPost['cid']): ?> current <?php endif; ?>"><a itemprop="url" href="<?php echo $categoryPost['permalink'] ?>"><?php echo $categoryPost['title'] ?></a></li>
                    <?php endwhile; ?>
                <?php endif; ?>
            </ul>
        </section>
    </div>

<?php $this->need('footer.php'); ?>
<!--</div>-->
