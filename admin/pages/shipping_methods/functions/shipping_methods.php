<?php 
    require_once ROOT."functions/formhandler.php";
    class shipping_methods extends database {
        public $form;
        protected $fromHandler ;
        function __construct() {
            parent::__construct();
            $this->fromHandler = new formHandler;
            $this->form = [
                "ID"=>["input_type"=>"hidden", "is_requred"=>false],
                "title"=>[],
                "description"=>[],
                "price"=>[],
                "status" => [
                    "is_required" => true,
                    "type" => "select",
                    "class" => "form-select",
                    "options" => [
                        "1" => "Active",
                        "0" => "disabled"
                    ]
                ]
                    ];
        }

        function newShipping_method() {
            unset($this->form['ID']);
            $info = $this->fromHandler->validateForm($this->form);
            if(!is_array($info)) return utilities::apiMessage("", errors: $this->fromHandler->errors);
            // check if title not exits in database 
            if($this->getall("shipping_methods", "title = ?", [$info['title']], fetch: "") > 0) {
                return $this->message("This shipping method exits", "error");
            }
             $this->quick_insert("shipping_methods", $info, "Shipping Method added successfully");

        }


        function editShipping_method() {
            $this->form['ID']['is_required'] = true;
            $info = $this->fromHandler->validateForm($this->form);
            if (!is_array($info)) return utilities::apiMessage("", errors: $this->fromHandler->errors);
            $id = $info['ID'];
            // check if title not exists in another record in the database
            if ($this->getall("shipping_methods", "title = ? AND ID != ?", [$info['title'], $id], fetch: "") > 0) {
            return $this->message("This shipping method already exists", "error");
            }
            return $this->update("shipping_methods", $info, "ID = '$id'", "Shipping Method updated successfully");
        }

    }