<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/header.php');
CJSCore::init(['greeting.hello', 'greeting.reloader']);
?>
    <script>
        // let rel = BX.Greeting.Reloader({name:'test'})
        let rel = new BX.Greeting.Reloader({name:'test'})
        rel.setName('TEST!2')
        console.log(rel)
        console.log(rel.getName())
    </script>
<?php
require($_SERVER['DOCUMENT_ROOT'].'/bitrix/footer.php');
