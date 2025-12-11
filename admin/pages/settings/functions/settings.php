<?php
    require_once ROOT."functions/helper.php";
    require_once ROOT."functions/formhandler.php";
class settings extends content
{
    protected $role;
    protected $formHandler;
    public function __construct()
    {
        // Call the parent constructor
        parent::__construct();
        $this->role = new roles;
        $this->formHandler = new formHandler; 
    }
    private $accepted_tables = ["features",  "sliders", "testimonies"];
    function new_settings($data, $what = "settings")
    {
        if ($data  == "") {
            return null;
        }
        foreach ($data as $key => $row) {
            if ($key == "input_data") {
                continue;
            }
            if ($this->getall("$what", "meta_name = ?", [$key], fetch: "") > 0) {
                continue;
            }
            $value = "";
            if (isset($data['input_data'][$key])) {
                $value = $data['input_data'][$key];
            }
            $this->quick_insert("$what", ["meta_name" => $key, "meta_value" => $value]);
        }
    }

  
    function update_settings($data, $what =  "settings")
    {
        if ($data  == "") {
            return null;
        }
        $info = $this->formHandler->validateForm($data);
        if (!is_array($info) || $info == null || $info == false) {
            return $this->message("Check required fileds", "error");
        }
        foreach ($info as $key => $value) {
            if ($value == "--placeholder" || $value == "00000000000") continue;
            $settingData = $this->getall("$what", "meta_name = ?", [$key]);
            if (!is_array($settingData)) {
                continue;
            }
            if ($this->isEncrypted($settingData['meta_value']) == true && $this->isEncrypted($value) == false) {
                $value = $this->enypt_and_save_data($value);
                if (!is_array($value)) return $this->message("Error encrypting data", "error", 'json');
                $value = $value['ID'];
            }
            $update = $this->update("$what", ["meta_value" => $value], "meta_name = '$key'");
            if ($update && $this->isEncrypted($value) && $this->isEncrypted($settingData['meta_value']) && $value != $settingData['meta_value']) {
                $this->enypt_unlink(($settingData['meta_value']));
            }
        }
        $return = [
            "message" => ["Success", "$what Updated", "success"],
        ];
        $actInfo = [
            "userID" => adminID,
            "date_time" => date("Y-m-d H:i:s"),
            "action_name" => "Edit Settings",
            "description" => "Edit $what Settings",
            "action_for" => "settings",
            "action_for_ID" => $what
        ];
        $this->formHandler->new_activity($actInfo);

        return json_encode($return);
    }

    function getdata($data, $what = "settings")
    {
        if ($data == "") {
            return null;
        }
        $info = [];
        foreach ($data as $key => $row) {
            if ($key == "input_data") {
                continue;
            }
            $info[$key] = $this->get_settings($key);
        }
        return $info;
    }

    function new_details($data, $what = "features")
    {
        if (!in_array($what, $this->accepted_tables)) {
            return null;
        }
        if (!$this->role->validate_action(["$what" => "new"], true)) return;
        if (isset($data['ID'])) {
            unset($data['ID']);
        }
        $info = $this->formHandler->validateForm($data, "$what");
        if (!is_array($info)) {
            return null;
        }
        $this->quick_insert("$what", $info, "New detail added.");

        $actInfo = [
            "userID" => adminID,
            "date_time" => date("Y-m-d H:i:s"),
            "action_name" => "New $what",
            "description" => "New $what",
            "action_for" => "$what",
            "action_for_ID" => ""
        ];
        $this->formHandler->new_activity(data: $actInfo);
    }

    function edit_details($data, $what = "features")
    {
        if (!in_array($what, $this->accepted_tables)) {
            return null;
        }
        if (!$this->role->validate_action(["$what" => "edit"], true)) return;
        // echo "Got herwe";
        $info = $this->formHandler->validateForm($data, "$what");
        if (isset($info['image']) && $info['image'] == "") unset($info['image']);
        if (!is_array($info)) {
            return null;
        }
        $id = $info['ID'];
        unset($info['ID']);
        $this->update("$what", $info, "ID = '$id'", "Detail updated.");
        $actInfo = [
            "userID" => adminID,
            "date_time" => date("Y-m-d H:i:s"),
            "action_name" => "Edit $what",
            "description" => "Edit $what",
            "action_for" => "$what",
            "action_for_ID" => $id
        ];
        $this->formHandler->new_activity(data: $actInfo);
    }

    function remove_details($id, $what = "features")
    {
        if (!in_array($what, $this->accepted_tables)) {
            return null;
        }
        if (!$this->role->validate_action(["$what" => "delete"], true)) return;
        $delete = $this->delete("$what", "ID = ?", [$id]);
        $return = [
            "message" => ["Success", "one detail deleted", "success"],
            "function" => ["removediv", "data" => [".detail-" . $id, "success"]]
        ];
        $actInfo = [
            "userID" => adminID,
            "date_time" => date("Y-m-d H:i:s"),
            "action_name" => "Delete $what",
            "description" => "Delete $what",
            "action_for" => "$what",
            "action_for_ID" => $id
        ];
        $this->formHandler->new_activity(data: $actInfo);
        return json_encode($return);
    }
}