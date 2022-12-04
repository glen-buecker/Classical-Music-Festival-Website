<div id="adminPanelContent">
    <?php if ( $_SESSION['role'] === 'admin' ): ?>
    <div style="width: 600px; margin: 0 auto;"><?php update_core(); ?></div>
    <?php endif; ?>
</div>
