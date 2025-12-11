<?php 
require_once ROOT."/functions/formhandler.php";
    class products extends database {
        public $image_base_url = "https://api.dspice.co.uk/assets/images/products/";
        public function __construct()
        {
            parent::__construct();
        }

        function getProducts($category = null, $platform = null, $search = null, $selete = "ID, name, description, image, size, features", $start = 0, $limit = 50)
        {
            $limit = ($limit > 50) ? 50 : $limit;
            $params = [];

            if ($category) {
                $query .= " AND categoryID = ?";
                $params[] = $category;
            }

            if ($platform) {
                $query .= " AND platformID = ?";
                $params[] = $platform;
            }

            if ($search) {
                $query .= " AND name LIKE ? or description LIKE ?";
                $params[] = "%" . $search . "%";
            }

            $query .= "status = ? LIMIT ?, ?";
            $params[] = 1;
            $params[] = (int)$start;
            $params[] = (int)$limit;

            return $this->getall("products", $query, $params, select: $selete, fetch: "all");
        }

        function getProduct($id, $status = 1)
        {
            return $this->getall("products", "ID = ? and status = ?", [$id, $status], iscacheable: false);
        }

     

        function fetchProducts() {
            if(isset($_GET['ID'])){
                try {
                    $product = $this->getProduct(htmlspecialchars($_GET['ID']));
                    if (!is_array($product)) {
                       return  utilities::apiMessage("Product not found", 404);
                    }
                    $product['sizes'] = json_decode($product['sizes'], true);
                    $product['images'] = json_decode($product['images'], true);
                    $product['features'] = json_decode($product['features'], true);
                    unset($product['status'], $product['addedby']);
                    $product['image_base_url'] = $this->image_base_url;
                    return utilities::apiMessage("Product Found", 200, data: $product);
                } catch (\Throwable $th) {
                    return utilities::apiMessage("Something went wrong $th", 500);
                }
            }
            $category = isset($_GET['category']) ? htmlspecialchars($_GET['category']) : null;
            $platform = isset($_GET['platform']) ? htmlspecialchars($_GET['platform']) : null;
            $search = isset($_GET['s']) ? htmlspecialchars($_GET['s']) : null;
            $start = isset($_GET['start']) ? htmlspecialchars($_GET['start']) : 1;
            $limit = isset($_GET['limit']) ? htmlspecialchars($_GET['limit']) : 50;
            $products = $this->getProducts($category, $platform, $search, start: $start, limit: $limit);
            if ($products != "") $products = $products->fetchAll(PDO::FETCH_ASSOC);
        }

        function reduceSize($productID, $size) {
            $product = $this->getProduct($productID);
            if(!is_array($product)) return false;
            $sizes = json_decode($product['sizes'], true);
            $sizes = json_encode($this->reduceQuantityBySize($sizes, $size), true);
           if($this->update("products", ["sizes"=>$sizes], "ID = '$productID'")) return true;
           return false;
        }

        function reduceQuantityBySize(array $data, string $targetSize): array {
            foreach ($data as &$item) {
                if (strcasecmp($item['size'], $targetSize) === 0) {
                    if ($item['quantity'] > 0) {
                        $item['quantity'] -= 1;
                    }
                    break;
                }
            }
            unset($item); // Break reference

            return $data;
        }

    }