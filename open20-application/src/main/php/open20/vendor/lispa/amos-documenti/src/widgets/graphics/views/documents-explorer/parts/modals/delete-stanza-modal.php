<script id="documents-explorer-delete-stanza-modal" type="text/template">
    <div id="documents-explorer-delete-stanza-modal-content" class="modal modal-document-explorer">
        <div class="row">
            <div class="col-xs-12">
                <h2><?= Yii::t('amosdocumenti', 'Vuoi davvero eliminare questa stanza?'); ?></h2>
                <div id="form-actions" class="bk-btnFormContainer">
                    <button class="btn btn-navigation-primary" id="documents-explorer-delete-stanza-modal-yes">Si</button>
                    <a class="btn btn-secondary undo-edit" id="documents-explorer-delete-stanza-modal-no" rel="modal:close">No</a>
                </div>
                <div class="errors">
                </div>
            </div>
        </div>
    </div>
</script>