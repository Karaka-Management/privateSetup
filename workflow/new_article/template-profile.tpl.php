<?php

use phpOMS\Uri\UriFactory;

echo $this->getData('nav')->render(); ?>

<div id="iSettings" class="tabview tab-2 url-rewrite">
    <div class="box wf-100 col-xs-12">
        <ul class="tab-links">
            <li><label for="c-tab-1"><?= $this->getHtml('Sales'); ?></label></li>
            <li><label for="c-tab-2"><?= $this->getHtml('Purchase'); ?></label></li>
            <li><label for="c-tab-3"><?= $this->getHtml('Accounting'); ?></label></li>
            <li><label for="c-tab-4"><?= $this->getHtml('ProductManager'); ?></label></li>
            <li><label for="c-tab-5"><?= $this->getHtml('QM'); ?></label></li>
        </ul>
    </div>
    <div class="tab-content">
        <input type="radio" id="c-tab-1" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="iGeneralSettings" action="<?= UriFactory::build('{/api}admin/settings/general'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Settings'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iOname"><?= $this->getHtml('Type'); ?></label>
                                    <select id="iOname" name="settings_1000000009">
                                        <option>Test1
                                        <option>Test2
                                        <option>Test3
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iOname"><?= $this->getHtml('Number'); ?></label>
                                    <input type="text">
                                </div>
                            </div>
                        </form>
                    </section>
                </div>

                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="iGeneralSettings" action="<?= UriFactory::build('{/api}admin/settings/general'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Settings'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iOname"><?= $this->getHtml('Type'); ?></label>
                                    <select id="iOname" name="settings_1000000009">
                                        <option>Test1
                                        <option>Test2
                                        <option>Test3
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iOname"><?= $this->getHtml('Number'); ?></label>
                                    <input type="text">
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-2" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
                <div class="col-xs-12 col-md-6">
                    <section class="portlet">
                        <form id="iGeneralSettings" action="<?= UriFactory::build('{/api}admin/settings/general'); ?>" method="post">
                            <div class="portlet-head"><?= $this->getHtml('Settings'); ?></div>
                            <div class="portlet-body">
                                <div class="form-group">
                                    <label for="iOname"><?= $this->getHtml('Type'); ?></label>
                                    <select id="iOname" name="settings_1000000009">
                                        <option>Test1
                                        <option>Test2
                                        <option>Test3
                                    </select>
                                </div>

                                <div class="form-group">
                                    <label for="iOname"><?= $this->getHtml('Number'); ?></label>
                                    <input type="text">
                                </div>
                            </div>
                        </form>
                    </section>
                </div>
            </div>
        </div>

        <input type="radio" id="c-tab-3" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
            </div>
        </div>

        <input type="radio" id="c-tab-4" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
            </div>
        </div>

        <input type="radio" id="c-tab-5" name="tabular-2"<?= $this->request->uri->fragment === 'c-tab-1' ? ' checked' : ''; ?>>
        <div class="tab">
            <div class="row">
            </div>
        </div>
    </div>
</div>