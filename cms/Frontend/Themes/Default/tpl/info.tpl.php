
<div class="floater">
    <div class="flex">
        <div id="nav-side">
            <a id="news-button" href="/info">News</a>
            <?= $this->getData('nav'); ?>
        </div>
        <div id="content">
            <?php $dispatch = $this->getData('dispatch') ?? [];
            foreach ($dispatch as $view) {
                if ($view instanceof \phpOMS\Contract\RenderableInterface) {
                    echo $view->render();
                }
            }
            ?>
        </div>
    </div>
</div>
