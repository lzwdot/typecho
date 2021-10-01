<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>
<!DOCTYPE HTML>
<html>
<head>
    <meta charset="<?php $this->options->charset(); ?>">
    <meta name="renderer" content="webkit">
    <meta name="viewport" content="width=device-width, initial-scale=1, maximum-scale=1">
    <meta name="msapplication-TileColor" content="#da532c">
    <meta name="theme-color" content="#ffffff">

    <!-- 使用url函数转换相关路径 -->
    <link rel="apple-touch-icon" sizes="180x180" href="<?php $this->options->themeUrl('img/apple-touch-icon.png'); ?>">
    <link rel="icon" type="image/png" sizes="32x32" href="<?php $this->options->themeUrl('img/favicon-32x32.png'); ?>">
    <link rel="icon" type="image/png" sizes="16x16" href="<?php $this->options->themeUrl('img/favicon-16x16.png'); ?>">
    <link rel="manifest" href="<?php $this->options->themeUrl('img/site.webmanifest'); ?>">
    <link rel="mask-icon" href="<?php $this->options->themeUrl('img/safari-pinned-tab.svg'); ?>" color="#5bbad5">
    <title><?php $this->archiveTitle([
            'category' => _t('分类 %s 下的文章'),
            'search'   => _t('包含关键字 %s 的文章'),
            'tag'      => _t('标签 %s 下的文章'),
            'author'   => _t('%s 发布的文章')
        ], '', ' - '); ?><?php $this->options->title(); ?></title>

    <!-- 使用url函数转换相关路径 -->
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/normalize.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/grid.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('style.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/highlight.css'); ?>">
    <link rel="stylesheet" href="<?php $this->options->themeUrl('css/custom.css'); ?>">

    <!-- 通过自有函数输出HTML头部信息 -->
    <?php $this->header(); ?>
</head>
<body>

<header id="header" class="clearfix">
    <div class="container">
        <div class="row">
            <div class="site-name col-mb-12 col-tb-9">
                <?php if ($this->options->logoUrl): ?>
                    <a id="logo" href="<?php $this->options->siteUrl(); ?>">
                        <img src="<?php $this->options->logoUrl() ?>" alt="<?php $this->options->title() ?>"/>
                    </a>
                <?php else: ?>
                    <a id="logo" href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title() ?></a>
                    <p class="description"><?php $this->options->description() ?></p>
                <?php endif; ?>
            </div>
            <div class="site-search col-tb-3 kit-hidden-mb">
                <form id="search" method="post" action="<?php $this->options->siteUrl(); ?>" role="search">
                    <label for="s" class="sr-only"><?php _e('搜索关键字'); ?></label>
                    <input type="text" id="s" name="s" class="text" placeholder="<?php _e('输入关键字搜索'); ?>"/>
                    <button type="submit" class="submit"><?php _e('搜索'); ?></button>
                </form>
            </div>
            <div class="col-mb-12 col-tb-8">
                <nav id="nav-menu" class="clearfix" role="navigation">
                    <a<?php if ($this->is('index')): ?> class="current"<?php endif; ?>
                        href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a>
                    <?php \Widget\Contents\Page\Rows::alloc()->to($pages); ?>
                    <?php while ($pages->next()): ?>
                        <a<?php if ($this->is('page', $pages->slug)): ?> class="current"<?php endif; ?>
                            href="<?php $pages->permalink(); ?>"
                            title="<?php $pages->title(); ?>"><?php $pages->title(); ?></a>
                    <?php endwhile; ?>
                </nav>
            </div>
            <div class="breadcrumb col-tb-4 kit-hidden-mb">
                <a href="<?php $this->options->siteUrl(); ?>"><?php _e('首页'); ?></a> &raquo;</li>
                <?php if ($this->is('index')): ?><!-- 页面为首页时 -->
                    <?php _e('最新文章'); ?>
                <?php elseif ($this->is('post')): ?><!-- 页面为文章单页时 -->
                    <?php $this->category(); ?> &raquo; <?php $this->title() ?>
                <?php else: ?><!-- 页面为其他页时 -->
                    <?php $this->archiveTitle(' &raquo; ','',''); ?>
                <?php endif; ?>
            </div>
        </div><!-- end .row -->
    </div>
</header><!-- end #header -->
<div id="body">
    <div class="container">
        <div class="row">