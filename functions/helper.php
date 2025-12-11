<?php
require_once ROOT."/functions/utilities.php";
class helper extends database
{

    function new_activity($data)
    {
        if (isset($_SESSION['anonymous'])) {
            return null;
        }
        // $data = 'userID', "date_time", "action_name", "link", "action_for", "action_for_ID";
        if (is_array($data) && isset($data['userID'])) {
            $info = [];
            $info['userID'] = $data['userID'];
            unset($data['userID']);
            // var_dump($this->get_visitor_details());
            $visitor_info = [];
            // if(!isset($_SESSION['adminSession'])) {
            $visitor_info = utilities::get_visitor_details();
            // }
            $info = array_merge($info, $visitor_info, $data);
            if (!$this->quick_insert("activities",  $info)) {
                return false;
            }
            return true;
        } else {
            return false;
        }
    }
    function display_tmc($user)
    {
        $name = $user['title'] . ' ' . $user['first_name'] . ' ' . $user['last_name'];
        return '<div class="col-xl-3 col-lg-3 col-md-6 col-sm-">
                    <div class="single-team mb-30">
                        <a href="profile?ref=' . $user['ID'] . '">
                            <div class="team-img">
                                <img src="' . $this->get_image_url("assets/images/profile/" . $user['profile_image']) . '" alt="' . $name . ' - ' . $user['position'] . '" width="200" height="400px">
                            </div>
                        </a>

                        <div class="team-caption p-4">
                            <h3><a href="profile?ref=' . $user['ID'] . '">' . $name . '</a></h3>
                            <span>' . $user['position'] . '</span>
                            
                            <!-- <div class="team-social ">
                                <a href="#"><i class="fab fa-instagram"></i></a>
                                <a href="#"><i class="fab fa-facebook-f"></i></a>
                            </div> -->
                        </div>
                    </div>
                </div>';
    }
    function get_user($userID)
    {
        return $this->getall("users", "ID = ?", [$userID]);
    }

    function strprefix($string)
    {
        $string = str_replace(" ", "-", trim(htmlspecialchars_decode($string)));
        // die($string);
        // return $string;
        // Remove special characters and replace spaces with hyphens
        $sanitized = preg_replace('/[^a-zA-Z0-9_ -]/s', '', $string);

        // Add the prefix '-'
        return $sanitized;
    }

    function get_user_name($userID)
    {
        $user = $this->get_user($userID);
        if (!is_array($user)) return "Unknown";
        return $user['title'] . ", " . $user['first_name'] . " " . $user['last_name'];
    }

    function get_position($userID, $check = null)
    {
        $user = $this->get_user($userID);
        if (!is_array($user)) return "Unknown";
        if ($user['position'] != "" && $check == null) return $user['position'];
        $board = $this->getall("board", "user = ?", [$userID]);
        if (is_array($board) && ($check == null || $check == "board")) return $board['position'];
        $departments = $this->getall("departments", "hod = ?", [$userID]);
        if (is_array($departments) && ($check == null || $check == "hod")) return "HOD of " . $departments['name'];
        $alumni = $this->getall("alumni", "user = ?", [$userID]);
        if (is_array($alumni) && ($check == null || $check == "alumni")) return "Alumni set: " . $alumni['year_of_graduation'];
        return "";
    }

    function extracttext($html)
    {
        if (empty($html)) return $html;
        // Use DOMDocument to parse the HTML
        $doc = new DOMDocument();

        // Suppress warnings for invalid HTML
        libxml_use_internal_errors(true);
        $doc->loadHTML($html);
        libxml_clear_errors();

        // Try to find the first <p> tag
        $paragraphs = $doc->getElementsByTagName('p');
        if ($paragraphs->length > 0) {
            // Return the text content of the first <p>
            return trim($paragraphs->item(0)->textContent);
        }

        // If no <p> tags, return plain text by stripping HTML tags
        return trim(strip_tags($html));
    }

    function delete_button($redirectUrl, $message = "Are you sure you want to delete this item?", $class = "btn btn-sm btn-danger bg-danger boder-none", $atb = "")
    {
        return '<a href="javascript:void(0)" 
                 onclick="if(confirm(\'' . htmlspecialchars($message) . '\')) { 
                     window.location.href=\'' . htmlspecialchars($redirectUrl) . '\'; 
                 }" 
                 ' . $atb . '
                 class="' . $class . '">Delete</a>';
    }


    function display_user($user, $from = null, $class = "col-xl-3 col-lg-3 col-md-6 col-sm-")
    {
        if (!is_array($user)) $user = $this->getall("users", "ID = ?", [$user]);
        if (!is_array($user)) return "";
        $position = $this->get_position($user['ID'], $from);
        $name = $this->get_user_name($user['ID']);
        $links = "";
        if ($user['website'] != "") $links .= '<a href="' . $user['website'] . '"><i class="fas fa-globe"></i></a>';
        if ($user['linkedin'] != "") $links .= '<a href="' . $user['linkedin'] . '"><i class="fab fa-linkedin"></i></a>';
        if ($user['instagram'] != "") $links .= '<a href="' . $user['instagram'] . '"><i class="fab fa-instagram"></i></a>';
        if ($user['facebook'] != "") $links .= '<a href="' . $user['facebook'] . '"><i class="fab fa-facebook-f"></i></a>';
        if ($user['twitter'] != "") $links .= '<a href="' . $user['twitter'] . '"><i class="fab fa-twitter"></i></a>';
        if ($user['tiktok'] != "") $links .= '<a href="' . $user['tiktok'] . '"><i class="fab fa-tiktok"></i></a>';

        return '<div class="' . $class . '">
                    <div class="single-team mb-30">
                        <div class="team-img">
                            <img src="' . $this->get_image_url("assets/images/profile/" . $user['profile_image']) . '" alt="' . $name . ' ' . $position . '" width="200" height="350px">
                        </div>
                        <div class="team-caption p-4 pt-5 pb-5">
                            <h3><a href="profile/' . $this->strprefix($name) . '?ref=' . $user['ID'] . '">' . $name . '</a></h3>
                            <span>' . $position . '</span>
                            <div class="team-social">
                               ' . $links . '
                            </div>
                        </div>
                    </div>
                </div>';
    }

    function rand_passwd($length = 8, $chars = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ0123456789')
    {
        return substr(str_shuffle($chars), 0, $length);
    }

    function options_list($table, $key = "ID", $value = "name")
    {

        if (is_string($table)) $table = $this->getall("$table", fetch: "moredetails");
        if ($table->rowCount() > 0) {
            foreach ($table as $row) {
                $data[$row[$key]] = $this->pass_value($value, $row);
            }
        }
        return $data ?? [];
    }

    function pass_value($value, $row)
    {
        if (is_array($value)) {
            $no = 0;
            $count = count($value);
            $data = "";
            foreach ($value as $key => $val) {
                $no++;
                if ($no == $count) {
                    $data .= $row[$val];
                } else {
                    $data .= $row[$val] . " - ";
                }
            }
        } else {
            $data = $row[$value];
        }
        return $data;
    }
}
