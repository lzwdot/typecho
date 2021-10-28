<?php if (!defined('__TYPECHO_ROOT_DIR__')) exit; ?>

        </div><!-- end .row -->
    </div>
</div><!-- end #body -->

<footer id="footer" role="contentinfo">
    <div class="container">
        <div class="row">
            <div class="col-mb-12 col-tb-10 text-left">
                &copy; <?php echo date('Y'); ?> <a href="<?php $this->options->siteUrl(); ?>"><?php $this->options->title(); ?></a>.
                <?php
                if($this->options->icpNum):
                    _e('<a href="http://beian.miit.gov.cn" rel="nofollow" target="_blank"> '.$this->options->icpNum.'</a>');
                else:
                    _e('由 <a href="http://www.typecho.org">Typecho</a> 强力驱动');
                endif;?>.
            </div>
            <div class="col-mb-12 col-tb-2 text-right">
                <a href="#header"><?php _e('向上↑') ?></a>
            </div>
        </div>
    </div>

</footer><!-- end #footer -->

<?php $this->footer(); ?>

<!-- 使用url函数转换相关路径 -->
<script src="<?php $this->options->themeUrl('js/highlight.pack.js'); ?>"></script>
<script>
    document.addEventListener('DOMContentLoaded', (event) => {
        document.querySelectorAll('pre code').forEach((block) => {
            hljs.highlightBlock(block);
        });
    });
</script>
</body>
</html>
