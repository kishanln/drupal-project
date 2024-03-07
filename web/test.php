
<!DOCTYPE html>
<html>
<body>
<form method="POST">
<p>
    <input type="text" name="address" placeholder="Enter address">
</p>

<input type="submit" name="submit_address">
</form>


</body>
</html>
<?php


    if (isset($_POST["submit_address"]))
    {
        $address = $_POST["address"];
        $address = str_replace(" ", "+", $address);
        $json = file_get_contents("https://maps.google.com/maps?q=<?php echo $address; ?>&sensor=false&region=$region");
        ?>
 
        <iframe width="100%" height="500" src="https://maps.google.com/maps?q=<?php echo $address; ?>&output=embed"></iframe>
 
        <?php
    }
?>


