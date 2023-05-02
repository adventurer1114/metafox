
<div>Platform version: <b>v<?php echo \MetaFox\Platform\MetaFox::getVersion() ?></b></div>
<div>Build Service: <b><?php echo $buildService ?></b></div>
<br/>
<div><b>Enviroment Variables</b>:</div>
<code>
    <?php echo nl2br($env)?>
</code>
