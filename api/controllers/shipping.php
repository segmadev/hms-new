<?php 
require_once "controllers/products.php";
require_once "controllers/user.php";
class shipping extends user {
    protected $shippingForm = [];
    public function __construct()
    {
        parent::__construct();
        $this->shippingForm = [
            "ID"=>["is_required"=>false],
            "userID"=>["is_required"=>false],
            "address"=>[],
            "city"=>[],
            "zipcode"=>[],
            "country"=>[]
        ];
    }

    function getShipping($id = null) {
        if ($id == null && !isset($_GET['ID'])) {
            die(utilities::apiMessage("No ID passed", 401));
        }
        $id = $id ?? htmlspecialchars($_GET['ID']);
        $shipping = $this->getall("shipping", "ID = ?", [$id]);
        if (!is_array($shipping)) die(utilities::apiMessage("Shipping not found", 400));
        return $shipping;
    }
    function getShippingMethods($id = null) {
        if ($id == null && !isset($_GET['ID'])) {
            // fetch all shipping methods
            $shipping = $this->getall("shipping_methods", "status = ?", [1], fetch: "all");
            if ($shipping == "") die(utilities::apiMessage("No shipping method not found", 400));
            return utilities::apiMessage("Shipping methods fetched", 200,  $shipping->fetchAll(PDO::FETCH_ASSOC));
        }
        $id = $id ?? htmlspecialchars($_GET['ID']);
        $shipping = $this->getall("shipping_methods", "ID = ?", [$id]);
        if (!is_array($shipping)) die(utilities::apiMessage("Shipping not found", 400));
        return utilities::apiMessage("Shipping methods fetched", 200, $shipping);
    }

    function fetchShipping() {
        if(isset($_GET['ID'])){
            try {
                $shipping = $this->getShipping(htmlspecialchars($_GET['ID']));
                if (!is_array($shipping)) {
                   return  utilities::apiMessage("Shipping not found", 404);
                }
                unset($shipping['status'], $shipping['addedby']);
                return utilities::apiMessage("Shipping Found", data: $shipping);
            } catch (\Throwable $th) {
                // return utilities::apiMessage("Something went wrong $th", 500);
            }
        }
        return utilities::apiMessage("No ID passed", 401);
    }

    function saveShipping($id = null) {
        $this->set_user();
        $shipping = $this->formHandler->validateForm($this->shippingForm);
        if(!is_array($shipping)) {
            return utilities::apiMessage("Fill all fileds", 400, errors: $this->formHandler->errors);
        }
        if($id == null && !isset($_GET['ID'])) {
            $shipping['ID'] = utilities::genID(6);
            $shipping['userID'] = $this->userID;
            // make sure the current number of shipping added is not up to 10
            $shippingCount = $this->getall("shipping", "userID = ?", [$this->userID], fetch: "");
            if($shippingCount >= 10) {
                return utilities::apiMessage("You have reached the maximum number of shipping addresses", 400);
            }
            if(!$this->quick_insert("shipping", $shipping)) {
                return utilities::apiMessage("Unable to save shipping", 500);
            }
            return utilities::apiMessage("Shipping saved", 200, data: $shipping);
        } else {
            $id = htmlspecialchars($_GET['ID'] ??  $id);
            unset($shipping['ID']);
            // check if shipping is for this user
            $check = $this->getall("shipping", "ID = ? and userID = ?", [$id, $this->userID]);
            if(!is_array($check)) {
                return utilities::apiMessage("Shipping not found", 404);
            }
            // check if shippimg method is valid and status is active
            // $shippingMethod = $this->getall("shipping_methods", "ID = ? and status = ?", [$shipping['shipping_method'], 1]);
            // if(!is_array($shippingMethod)) {
            //     return utilities::apiMessage("Shipping method not found or nor active at the moment", 404);
            // }
            unset($shipping['userID']);
            if(!$this->update("shipping", $shipping, "ID = '$id'", $shipping)) {
                return utilities::apiMessage("Unable to update shipping", 500);
            }
            return utilities::apiMessage("Shipping updated", 200, data: $shipping);
        }

    }

    function order_product($pID) {
        
    }

    // delete shipping using the ID and userID 
    function deleteShipping($id = null) {
        $this->set_user();
        if($id == null && !isset($_GET['ID'])) {
            return utilities::apiMessage("No ID passed", 401);
        }
        $id = $id ?? htmlspecialchars($_GET['ID']);
        $shipping = $this->getall("shipping", "ID = ? and userID = ?", [$id, $this->userID]);
        if(!is_array($shipping)) {
            return utilities::apiMessage("Shipping not found", 404);
        }
        if(!$this->delete("shipping", "ID = ?", [$id])) {
            return utilities::apiMessage("Unable to delete shipping", 500);
        }
        return utilities::apiMessage("Shipping deleted", 200);
    }

    // fetch all shipping for a user
    function fetchShippingForUser() {
        $this->set_user();
        $shipping = $this->getall("shipping", "userID = ? LIMIT 10", [$this->userID], fetch: "all");
        if($shipping == "") {
            return utilities::apiMessage("Shipping not found", 404);
        }
        return utilities::apiMessage("Shipping fetched", 200, data: $shipping->fetchAll(PDO::FETCH_ASSOC));
    }

    // get a user shipping address by the shipping ID
    function getUserShipping($id = null) {
        $this->set_user();
        if($id == null && !isset($_GET['ID'])) {
            return utilities::apiMessage("No ID passed", 401);
        }
        $id = $id ?? htmlspecialchars($_GET['ID']);
        $shipping = $this->getall("shipping", "ID = ? and userID = ?", [$id, $this->userID]);
        if(!is_array($shipping)) {
            return utilities::apiMessage("Shipping not found", 404);
        }
        return utilities::apiMessage("Shipping fetched", 200, data: $shipping);
    }

}