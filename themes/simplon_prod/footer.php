      <!-- footer -->
      <footer>
        <?php wp_footer(); ?>
        <div class="info">
          <span><?php echo date('Y'); ?>&nbsp;&copy;&nbsp;<a rel="noopener nofollow" target="_blank" href="https://prod.simplon.co">Simplon Prod</a></span>
          <span>Theme by <a rel="noopener nofollow" target="_blank" href="<?php echo wp_get_theme()->get('AuthorURI'); ?>"><?php echo trim(wp_get_theme()->get('Author')); ?></a></span>
          <span><?php printf(__( 'Proudly powered by %s','simplon_prod'),'WordPress') ?></span>
        </div>
      </footer>
      <!-- /footer -->
    </main>
    <!-- /main -->
  </body>
</html>