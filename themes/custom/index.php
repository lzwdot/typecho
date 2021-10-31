<?php
/**
 * Custom theme for Typecho
 *
 * @package Custom Replica Theme
 * @author Custom Team
 * @version 1.0
 * @link http://typecho.org
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
$this->need('header.php');
?>

<div class="col-mb-12 col-8" id="main" role="main">
    <?php while ($this->next()): ?>
        <?php if ($this->sequence == 1): ?>
            <article class="post" itemscope itemtype="http://schema.org/BlogPosting">
                <h2 class="post-title" itemprop="name headline">
                    <a itemprop="url"
                       href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                </h2>
                <ul class="post-meta">
                    <li itemprop="author" itemscope itemtype="http://schema.org/Person"><?php _e('作者: '); ?><a
                            itemprop="name" href="<?php $this->author->permalink(); ?>"
                            rel="author"><?php $this->author(); ?></a></li>
                    <li><?php _e('时间: '); ?>
                        <time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?></time>
                    </li>
                    <li><?php _e('分类: '); ?><?php $this->category(','); ?></li>
                    <li itemprop="interactionCount">
                        <a itemprop="discussionUrl"
                           href="<?php $this->permalink() ?>#comments"><?php $this->commentsNum('评论', '1 条评论', '%d 条评论'); ?></a>
                    </li>
                </ul>
                <div class="post-content" itemprop="articleBody">
                    <?php $this->excerpt(100, '...'); ?>
                    <a itemprop="url" href="<?php $this->permalink() ?>"><?php _e('阅读剩余部分'); ?> &raquo;</a>
                </div>
            </article>
            <h3><?php _e('最新文章'); ?></h3>
        <?php else: ?>
            <ul itemscope itemtype="http://schema.org/BlogPosting">
                <li>
                    <time datetime="<?php $this->date('c'); ?>" itemprop="datePublished"><?php $this->date(); ?> &raquo;</time>
                    <a itemprop="url" href="<?php $this->permalink() ?>"><?php $this->title() ?></a>
                </li>
            </ul>
            <?php if ($this->sequence == $this->parameter->pageSize): ?>
                <ul>
                    <li>
                        <a itemprop="url" href="<?php $this->options->siteUrl() ?>/page/1"><?php _e('更多文章...'); ?></a>
                    </li>
                </ul>
            <?php endif; ?>
        <?php endif; ?>
    <?php endwhile; ?>
</div><!-- end #main-->

<?php $this->need('sidebar.php'); ?>
<?php $this->need('footer.php'); ?>
