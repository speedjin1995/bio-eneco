<?php
require_once 'db_connect.php';

session_start();

if(!isset($_SESSION['id'])){
	echo '<script type="text/javascript">location.href = "../login.php";</script>'; 
} else{
	$username = $_SESSION["username"];
}
// Check if the user is already logged in, if yes then redirect him to index page
$id = $_SESSION['id'];
// Processing form data when form is submitted
if (isset($_POST['productCode'])) {

    if (empty($_POST["id"])) {
        $productId = null;
    } else {
        $productId = trim($_POST["id"]);
    }

    if (empty($_POST["productCode"])) {
        $productCode = null;
    } else {
        $productCode = trim($_POST["productCode"]);
    }

    if (empty($_POST["productName"])) {
        $productName = null;
    } else {
        $productName = trim($_POST["productName"]);
    }

    if (empty($_POST["productPrice"])) {
        $productPrice = '0.00';
    } else {
        $productPrice = trim($_POST["productPrice"]);
    }

    if (empty($_POST["description"])) {
        $description = null;
    } else {
        $description = trim($_POST["description"]);
    }

    if (empty($_POST["varianceType"])) {
        $varianceType = null;
    } else {
        $varianceType = trim($_POST["varianceType"]);
    }

    if (empty($_POST["high"])) {
        $high = null;
    } else {
        $high = trim($_POST["high"]);
    }

    if (empty($_POST["low"])) {
        $low = null;
    } else {
        $low = trim($_POST["low"]);
    }

    if (empty($_POST["basicUom"])) {
        $basicUom = null;
    } else {
        $basicUom = trim($_POST["basicUom"]);
    } 

    if(! empty($productId))
    {
        $action = "2";
        if ($update_stmt = $db->prepare("UPDATE Product SET product_code=?, name=?, price=?, description=?, variance=?, high=?, low=?, basic_uom=?, created_by=?, modified_by=? WHERE id=?")) 
        {
            $update_stmt->bind_param('sssssssssss', $productCode, $productName, $productPrice, $description, $varianceType, $high, $low, $basicUom, $username, $username, $productId);

            // Execute the prepared query.
            if (! $update_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $update_stmt->error
                    )
                );
            }
            else{
                # Product_RawMat 
                if (isset($_POST['no'])){
                    $no = $_POST['no'];
                    $productRawMatCode = $_POST['productRawMatCode'];
                    $rawMats =  $_POST['rawMats'];
                    $rawMatBasicUom = $_POST['rawMatBasicUom'];
                    $rawMatBasicUomUnitId = $_POST['rawMatBasicUomUnitId'];
                    $rawMatWeight = $_POST['rawMatWeight'];
                    $plant = $_POST['plant'];
                    // $plantCode = $_POST['plantCode'];
                    // $plantName = $_POST['plantName'];
                    $batchDrum = $_POST['batchDrum'];
                    $deleteStatus = 1;
                    if(isset($no) && $no != null && count($no) > 0){
                        # Delete all existing product rawmat records tied to the product id then reinsert
                        if ($delete_stmt = $db->prepare("UPDATE Product_RawMat SET status=? WHERE product_id=?")){
                            $delete_stmt->bind_param('ss', $deleteStatus, $productId);
    
                            // Execute the prepared query.
                            if (! $delete_stmt->execute()) {
                                echo json_encode(
                                    array(
                                        "status"=> "failed", 
                                        "message"=> $delete_stmt->error
                                    )
                                );
                            }
                            else{
                                foreach ($no as $key => $rawMatNo) {
                                    if ($product_stmt = $db->prepare("INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")){
                                        $product_stmt->bind_param('ssssssss', $productId, $rawMats[$key], $productRawMatCode[$key], $rawMatBasicUom[$key], $rawMatBasicUomUnitId[$key], $rawMatWeight[$key], $plant[$key], $batchDrum[$key]);
                                        $product_stmt->execute();
                                    }
                                }

                                /*for ($i=1; $i <= count($no); $i++) {
                                    if(isset($no) && $no != null && count($no) > 0){
                                        for ($i=1; $i <= count($no); $i++) { 
                                            if ($product_stmt = $db->prepare("INSERT INTO Product_RawMat (product_id, raw_mat_code, raw_mat_weight) VALUES (?, ?, ?)")){
                                                $product_stmt->bind_param('sss', $productId, $rawMats[$i], $rawMatWeight[$i]);
                                                $product_stmt->execute();
                                            }
                                        }
                    
                                        $product_stmt->close();
                                    }
                    
                                    // if(isset($productRawMatId[$i]) && $productRawMatId[$i] > 0){
                                    //     if ($product_stmt = $db->prepare("UPDATE Product_RawMat SET product_id=?, raw_mat_code=?, raw_mat_weight=? WHERE id=?")){
                                    //         $product_stmt->bind_param('ssss', $productId, $rawMats[$i], $rawMatWeight[$i], $productRawMatId[$i]);
                                    //         $product_stmt->execute();
                                    //     }
                                    // }else{
                                    //     if ($product_stmt = $db->prepare("INSERT INTO Product_RawMat (product_id, raw_mat_code, raw_mat_weight) VALUES (?, ?, ?)")){
                                    //         $product_stmt->bind_param('sss', $productId, $rawMats[$i], $rawMatWeight[$i]);
                                    //         $product_stmt->execute();
                                    //     }
                                    // }
                                }*/
                            }
                        } 
                    }
                }

                # Product_UOM
                if (isset($_POST['uomNo'])){
                    $uomNo = $_POST['uomNo'];
                    $uomId = $_POST['uomId'];
                    $uom =  $_POST['uom'];
                    $rate = $_POST['rate'];
                    $deleteStatus = 1;
                    if(isset($uomNo) && $uomNo != null && count($uomNo) > 0){
                        # Delete all existing product uom records tied to the product id then reinsert
                        if ($delete_stmt = $db->prepare("UPDATE Product_UOM SET status=? WHERE product_id=?")){
                            $delete_stmt->bind_param('ss', $deleteStatus, $productId);
    
                            // Execute the prepared query.
                            if (! $delete_stmt->execute()) {
                                echo json_encode(
                                    array(
                                        "status"=> "failed", 
                                        "message"=> $delete_stmt->error
                                    )
                                );
                            }
                            else{
                                foreach ($uomNo as $key => $no) {
                                    if ($product_stmt = $db->prepare("INSERT INTO Product_UOM (product_id, unit_id, rate) VALUES (?, ?, ?)")){
                                        $product_stmt->bind_param('sss', $productId, $uom[$key], $rate[$key]);
                                        $product_stmt->execute();
                                    }
                                }
                                $product_stmt->close();
                            }
                        } 
                    }
                }

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Updated Successfully!!" 
                    )
                );
            }

            $update_stmt->close();
            $db->close();
        }
    }
    else
    {
        $action = "1";
        if ($insert_stmt = $db->prepare("INSERT INTO Product (product_code, name, price, description, variance, high, low, basic_uom, created_by, modified_by) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?)")) {
            $insert_stmt->bind_param('ssssssssss', $productCode, $productName,  $productPrice, $description, $varianceType, $high, $low, $basicUom, $username, $username);

            // Execute the prepared query.
            if (! $insert_stmt->execute()) {
                echo json_encode(
                    array(
                        "status"=> "failed", 
                        "message"=> $insert_stmt->error
                    )
                );
            }
            else{
                $productId = $insert_stmt->insert_id;

                # Product_RawMat 
                if(isset($_POST['no'])){
                    $no = $_POST['no'];
                    $productRawMatCode = $_POST['productRawMatCode'];
                    $rawMats =  $_POST['rawMats'];
                    $rawMatBasicUom = $_POST['rawMatBasicUom'];
                    $rawMatBasicUomUnitId = $_POST['rawMatBasicUomUnitId'];
                    $rawMatWeight = $_POST['rawMatWeight'];
                    $plant = $_POST['plant'];
                    $plantCode = $_POST['plantCode'];
                    $plantName = $_POST['plantName'];
                    $batchDrum = $_POST['batchDrum'];

                    if(isset($no) && $no != null && count($no) > 0){
                        foreach ($no as $key => $rawMatNo) {
                            if ($product_stmt = $db->prepare("INSERT INTO Product_RawMat (product_id, raw_mat_id, raw_mat_code, raw_mat_basic_uom, basic_uom_unit_id, raw_mat_weight, plant_id, batch_drum) VALUES (?, ?, ?, ?, ?, ?, ?, ?)")){
                                $product_stmt->bind_param('ssssssss', $productId, $rawMats[$key], $productRawMatCode[$key], $rawMatBasicUom[$key], $rawMatBasicUomUnitId[$key], $rawMatWeight[$key], $plant[$key], $batchDrum[$key]);
                                $product_stmt->execute();
                            }
                        }


                        /*for ($i=1; $i <= count($no); $i++) { 
                            if ($product_stmt = $db->prepare("INSERT INTO Product_RawMat (product_id, raw_mat_code, raw_mat_weight) VALUES (?, ?, ?)")){
                                $product_stmt->bind_param('sss', $productId, $rawMats[$i], $rawMatWeight[$i]);
                                $product_stmt->execute();
                            }
                        }*/
    
                        $product_stmt->close();
                    }
                }

                echo json_encode(
                    array(
                        "status"=> "success", 
                        "message"=> "Added Successfully!!" 
                    )
                );
            }

            $insert_stmt->close();
            $db->close();
        }
    }
    
}
else
{
    echo json_encode(
        array(
            "status"=> "failed", 
            "message"=> "Please fill in all the fields"
        )
    );
}
?>