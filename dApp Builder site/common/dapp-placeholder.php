<?php
    if ($network == 'main') {
        $network_name = 'Main Ethereum Network';
    } elseif ($network == 'rinkeby') {
        $network_name = 'Rinkeby Test Network';
    }
?>
<div id="loading-placeholder" class="text-center">
    <h1>Loading...</h1>
    <p>Waiting for the Smart Contract's response.</p>
    <p>This dApp works in <span style="color: <?php echo $interface['eth_addresses_color']; ?>;"><?php echo $network_name; ?></span>.</p>
</div>
<style>
    #loading-placeholder{
        position: fixed;
        left: 0;
        top: 0;
        width: 100%;
        height: 100%;
        background: <?php echo $interface['background_color']; ?>;
        padding-top: 20%;
    }
</style>
<script type="text/javascript">
    function managePlaceHolders(){
        $('#loading-placeholder').fadeOut();
    }
</script>