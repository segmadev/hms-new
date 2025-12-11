<?php 
class FrontContent extends database {
    function getTCandPolicy() {
        $data = [
            "policy"=>$this->get_settings("policy"),
            "tandc"=>$this->get_settings("terms_and_conditions")
        ];
        return utilities::apiMessage("Details fetched", 200, $data);
    }

    function commonDetails() {
        $data = [
            "contact_email"=>$this->get_settings("support_email"),
            "company_address"=>$this->get_settings("company_address"),
            "phone_number"=>$this->get_settings("phone_number"),
            "default_currency"=>$this->get_settings("default_currency"),
            "social_media"=>[
                "facebook_link"=>$this->get_settings("facebook_link"),
                "instagram_link"=>$this->get_settings("instagram_link"),
                "x_link"=>$this->get_settings("x_link"),
                "tiktok_link"=>$this->get_settings("tiktok_link"),
                "telegram"=>$this->get_settings("telegram"),
            ],
            "seo"=>[
                "title"=>$this->get_settings("seo_title"),
                "description"=>$this->get_settings("seo_description"),
                "tags"=>$this->get_settings("seo_tags"),
                
            ],
            "livechatwidget"=>$this->get_settings("live_chat_widget")
        ];
        return utilities::apiMessage("Details fetched", 200, $data);
    }
}

?>