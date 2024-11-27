<?php

    // output errors during execution - for testing
    error_reporting(E_ALL);
    ini_set('display_errors', 1);	
    //---------------------------------

    // get the data from the form
    $product_description = $_POST['product_description'];
    $curr = $_POST['curr'];
    $list_price = $_POST['list_price'];
    // if they add tip, otherwise set value to 0
    $tip_percent = isset($_POST['tip_percent']) ? floatval($_POST['tip_percent']) : 0;
    $tax_percent = $_POST['tax_percent'];
    $num_cust = $_POST['num_cust'];
    // if they inputted a gift card, otherwise set value to 0
    $gift_card = isset($_POST['gift_card']) ? floatval($_POST['gift_card']) : 0;
    
    // calculate the tip
    $tip = $list_price *($tip_percent) * .01;
    if($curr === "¥")
       $tip=0; // ensures that yen keeps tip at 0 if currency swaps
   
    // calculate the tax
    $tax_price = $list_price * ($tax_percent) * .01; 

    // sum price + tip + tax
    $tipped_price = $list_price + $tip + $tax_price; 

    // final bill - sum price after gift card
    $bill_price = $tipped_price - $gift_card;
    $leftover_gc = 0;
    if($bill_price < 0){
       $leftover_gc = -$bill_price;   // tell customer leftover $ on gift card
       $bill_price = 0;     // set bill to $0 if gift card pays for bill   
    }
    // to split final bill by customers
    $cust_split = $bill_price / $num_cust;

    // apply formatting to the currency and percent amounts
    $list_price_formatted = $curr.number_format($list_price, 2);
    $tip_percent_formatted = $tip_percent."%";
    $tip_formatted = $curr.number_format($tip, 2);
    $tipped_price_formatted = $curr.number_format($tipped_price, 2);
    $tax_price_formatted = $curr.number_format($tax_price, 2);            
    
    // escape the unformatted input
    $product_description_escaped = htmlspecialchars($product_description);

?>
<!DOCTYPE html>
<html>
<head>
    <title>Product Discount Calculator</title>
    <link rel="stylesheet" type="text/css" href="main.css">
</head>
<body>

    <main>
        <h1 style="text-align:center;">Receipt</h1>

        <label>Name of Order:</label>
        <span><?php echo $product_description_escaped; ?></span><br>

        <label>Price:</label>
        <span><?php echo $list_price_formatted; ?></span><br>
       
        <?php   // remove tip part if in Yen, since no tipping
        if($curr !== "¥")
	        echo "
		<label>Tip Percent:</label>
                <span>$tip_percent_formatted</span><br>

                <label>Tip Amount:</label>
                <span>$tip_formatted</span><br>
		";
        ?>

	<label>Tax:</label>
        <span><?php echo $tax_price_formatted; ?></span><br>

        <label><strong>Price After 
		<?php if($curr !== "¥")
        		echo "Tip + ";?>
		Tax:</strong></label>
        <span><?php echo $tipped_price_formatted; ?></span><br>

        <!--  if they input a gift card and/or split bill > 1  -->

        <?php
            // Display price after gift card if gift card > 0 
            if ($gift_card>0) {
                echo "<label>After $curr$gift_card Gift Card:</label>";
                // apply currency formatting
                echo $curr . "<span>".number_format($bill_price, 2)."</span><br>";
            }
        
	    // Display price after gift card if gift card > 0 
            if ($leftover_gc>0) {
                echo "<label>Remaining Gift Card Balance:</label>";
                // apply currency formatting
                echo $curr . "<span>".number_format($leftover_gc, 2)."</span><br>";
            }

            // Check if more than 1 person is selected to split the bill
	    if ($num_cust > 1 && $leftover_gc == 0) {
                echo "<label><strong>Price Per Person:</strong></label>";
		// apply currency formatting
                echo $curr . "<span>".number_format($cust_split, 2)."</span><br>";
            }
        ?>        

    </main>
</body>
</html>
