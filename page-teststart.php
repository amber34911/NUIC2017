<?php

if(isset($_POST["value"])&&$_POST["value"]=="hi子豪"){

    setcookie("WP_TESTING","330c8ef4621649a0237ead79e3791a4af2d57a83979aa0b0976c483fb2f6a377",0,"/","",false,true);

}

?>

<!DOCTYPE html>

<html lang="en">

<head>

    <meta charset="UTF-8">

    <title>NUIC2017</title>

</head>

<body>

   <?php

if(isset($_POST["value"])&&$_POST["value"]=="hi子豪"){

    echo "start testing";

}

?>

    <form action="../teststart" method="post">

        <input type="text" name ="value">

        <input type="submit" value="send">

    </form>

    

</body>

</html>