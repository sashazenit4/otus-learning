<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/header.php';
CJSCore::Init('vue.hello-node');
?>
<div id="vue-application-node"></div>
<script>
    let a = new BX.Vue.HelloNode('#vue-application-node');
    a.run()
</script>
<?php
require $_SERVER['DOCUMENT_ROOT'] . '/bitrix/footer.php';
