<?php
function convertDatetimeToDate($datetime){
    $date = new DateTime($datetime);
  
    return $date->format('d/m/Y'); 
}

function searchCustomerByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Customer WHERE customer_code=? AND status='0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchSupplierByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Supplier WHERE supplier_code=? AND status='0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchPlantCodeById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['plant_code'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchPlantNameById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchPlantNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE plant_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchProjectByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Site WHERE site_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchTransporterNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Transporter WHERE transporter_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchProductNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Product WHERE product_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchProductIdByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Product WHERE product_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['id'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchProductBasicUomByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT Unit.* FROM Product JOIN Unit ON Product.basic_uom = Unit.id WHERE product_code=? AND Product.status = '0' AND Unit.status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['unit'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchUnitById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Unit WHERE id=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['unit'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchUnitIdByCode($value, $db) {
    $id = '';

    if(isset($value)){
        $value = strtoupper($value);

        if ($select_stmt = $db->prepare("SELECT * FROM Unit WHERE UPPER(unit)=?")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['id'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchRawNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Raw_Mat WHERE raw_mat_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchRawMatIdByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Raw_Mat WHERE raw_mat_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['id'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchRawMatCodeById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Raw_Mat WHERE id=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['raw_mat_code'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchRawMatNameById($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Raw_Mat WHERE id=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchAgentNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Agents WHERE agent_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchDestinationNameByCode($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Destination WHERE destination_code=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['name'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchDestinationCodeByName($value, $db) {
    $id = '';

    if(isset($value)){
        if ($select_stmt = $db->prepare("SELECT * FROM Destination WHERE name=? AND status = '0'")) {
            $select_stmt->bind_param('s', $value);
            $select_stmt->execute();
            $result = $select_stmt->get_result();
            if ($row = $result->fetch_assoc()) {
                $id = $row['destination_code'];
            }
            $select_stmt->close();
        }
    }

    return $id;
}

function searchFilePathById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM files WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['filepath'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchNamebyId($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM Users WHERE username=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['name'];
        }
        $select_stmt->close();
    }

    return $id;
}

function excelSerialToDate($serial) {
    // Excel date starts from 1900-01-01, subtract 1 for correct calculation
    $baseDate = strtotime('1899-12-30');
    return date('Y-m-d', strtotime("+$serial days", $baseDate));
}

####################################### Audit Log Lookup #######################################
function searchActionNameById($value, $db) {
    $id = null;

    if ($select_stmt = $db->prepare("SELECT * FROM Log_Action WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $id = $row['description'];
        }
        $select_stmt->close();
    }

    return $id;
}

function searchCustomerAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Customer WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchDestinationAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Destination WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchProductAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Product WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchRawMatAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Raw_Mat WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchSupplierAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Supplier WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchVehicleAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Vehicle WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchAgentAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Agents WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchTransporterAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Transporter WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchUnitAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Unit WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchUserAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Users WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchPlantAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Plant WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchSiteAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Site WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchWeightAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Customer WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchSoAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Customer WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

function searchPoAuditById($value, $db) {
    $data = array();

    if ($select_stmt = $db->prepare("SELECT * FROM Customer WHERE id=?")) {
        $select_stmt->bind_param('s', $value);
        $select_stmt->execute();
        $result = $select_stmt->get_result();
        if ($row = $result->fetch_assoc()) {
            $data = $row;
        }
        $select_stmt->close();
    }

    return $data;
}

?>