<?php
/**
 * 首页
 * @package custom
 */

if (!defined('__TYPECHO_ROOT_DIR__')) exit;
     $this->need('header.php');
 ?>

<div class="col-mb-12 col-8 home" id="main" role="main">
    <?php $this->widget('Widget_Contents_Post_Recent')->to($recentPosts); ?>

	<?php while($recentPosts->next()): ?>
        <?php if($recentPosts->sequence ==1): ?>
            <article class="post" itemscope itemtype="http://schema.org/BlogPosting">
                <h2 class="post-title" itemprop="name headline"><a itemprop="url" href="<?php $recentPosts->permalink() ?>"><?php $recentPosts->title() ?></a></h2>
                <ul class="post-meta">
                    <li itemprop="author" itemscope itemtype="http://schema.org/Person"><?php _e('作者: '); ?><a itemprop="name" href="<?php $recentPosts->author->permalink(); ?>" rel="author"><?php $recentPosts->author(); ?></a></li>
                    <li><?php _e('时间: '); ?><time datetime="<?php $recentPosts->date('c'); ?>" itemprop="datePublished"><?php $recentPosts->date(); ?></time></li>
                    <li><?php _e('分类: '); ?><?php $recentPosts->category(','); ?></li>
<!--                    <li itemprop="interactionCount"><a itemprop="discussionUrl" href="--><?php //$recentPosts->permalink() ?><!--#comments">--><?php //$recentPosts->commentsNum('评论', '1 条评论', '%d 条评论'); ?><!--</a></li>-->
                </ul>
                <div class="post-content" itemprop="articleBody">
                    <?php $recentPosts->excerpt(50,'...'); ?>
                    <a itemprop="url" href="<?php $recentPosts->permalink() ?>"><?php _e('阅读更多'); ?> &raquo;</a>
                </div>
            </article>
        <?php endif; ?>
	<?php endwhile; ?>

    <section class="widget">
        <h2 class="widget-title"><?php _e('最新文章'); ?></h2>
        <ul class="widget-list">
            <?php while($recentPosts->next()): ?>
                <?php if($recentPosts->sequence !=1): ?>
                    <li><time datetime="<?php $recentPosts->date('c'); ?>" itemprop="datePublished"><?php $recentPosts->date(); ?></time> &raquo; <a itemprop="url" href="<?php $recentPosts->permalink() ?>"><?php $recentPosts->title() ?></a></li>
                <?php endif; ?>
            <?php endwhile; ?>
            <?php if($this->options->routingTable['archive']['url']): ?>
                <li><a href="<?php echo $this->options->siteUrl().$this->options->routingTable['archive']['url']; ?>"><?php _e('更多文章...'); ?></a></li>
            <?php endif;?>
        </ul>
    </section>

</div><!-- end #main-->

<?php $this->need('sidebar.php'); ?>
<?php $this->need('footer.php'); ?>
