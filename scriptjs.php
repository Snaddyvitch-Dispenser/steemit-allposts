<script
    src="https://code.jquery.com/jquery-3.3.1.min.js"
    integrity="sha256-FgpCb/KJQlLNfOu91ta32o/NMZxltwRo8QtmkMRdAu8="
    crossorigin="anonymous"></script>
<script src="sync.js"></script>
<script>
    console.log(sync("<?php echo (isset($_GET["username"]) ? $_GET["username"] : ""); ?>"));
</script>