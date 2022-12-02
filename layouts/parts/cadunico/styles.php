<style>
:root {
    <?php foreach($plugin->config['styles:root'] as $style => $value): ?>
        <?= $style ?>: <?= $value ?>;
    <?php endforeach; ?>
}
<?= $plugin->config['styles'] ?>
</style>