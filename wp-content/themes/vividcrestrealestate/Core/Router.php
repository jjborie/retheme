<?php
namespace Vividcrestrealestate\Core;

class Router 
{        
    public static function definePart()
    {
        global $wp_query;
        
        switch ($wp_query->query_vars['pagename']) {
            case "properties":
                $template_part = (isset($wp_query->query_vars['property_id'])
                    ? "property"
                    : "properties"
                );
                break;
            
            case "rets":
                $template_part = "rets";
                break;
                
            case "compare":
                $template_part = "compare";
                break;
                
            case "map":
                $template_part = "map";
                break;
                
            default: 
                // Part "category" does not have template for display
                if ($wp_query->is_category) {
                    $menu_items = wp_get_nav_menu_items("top-menu");
                    $category_id = $wp_query->queried_object_id;
                    $category_menu_item_id = null;
                    $main_post_id = null;
                    
                    // Find menu link, assigned to category
                    foreach ($menu_items as $menu_item) {
                        if ($menu_item->object == "category" && $menu_item->object_id == $category_id) {
                            $category_menu_item_id = $menu_item->ID;
                            break;
                        }
                    }
                    
                    // Find first post-children of category menu item
                    foreach ($menu_items as $menu_item) {
                        if ($menu_item->object == "post" && $menu_item->menu_item_parent == $category_menu_item_id) {
                            $main_post_id = $menu_item->object_id;
                            break;
                        }
                    }                    
                    
                    // Redirect to main page if category is empty or does not exists
                    if (empty($main_post_id)) {
                        wp_redirect(get_site_url());
                    }
                    
                    // Redirect to the post page in the other case
                    wp_redirect(get_permalink($main_post_id));
                }
                
                $template_part = (is_front_page() ? 
                    "main" 
                    : (!empty($wp_query->query_vars['s']) 
                        ? "search_posts" 
                        : "content")
                );
        }
        
        return $template_part;
    }
        
    public static function loadData($part)
    {    
        // Define coordinates by ip
        $coordinates = Libs\Address::recognizeCoordinates();
              
        // Load structures
        $Properties = new Structures\Properties();
        $FeaturedProperties = new Structures\FeaturedProperties();
        
        // Handle search form
        $search = (isset($_GET['search_property']) ? (object)Forms::sanitize($_GET['search_property']) : new \stdClass);
        
        // Set default data
        $data = (object)[
            'search' => $search
        ];
        
        // Hidden adding city for main page
        if (empty($search->address) && $part=="main") {
            $search->address = "Toronto"; //$coordinates->city;
        }             
        
        // Make criterion
        $criterion = self::makeCriterion($search);  
        
        // Fetch wp_query
        global $wp_query;
        
        
        // Extract necessary data
        switch ($part) {            
            case "main":            
                $data->properties = $Properties->get($criterion);
                $data->recent_properties = $Properties->get([
                    'orderby' => "publish_date",
                    'order' => "DESC",
                    'limit' => 4
                ]);
                $data->featured_properties = $FeaturedProperties->getDetailed(['limit'=>3]);
                break;
                
            case "map":
                // $criterion['limit'] = 10000;
                $data->properties = $Properties->get($criterion);
                break;
                
            case "properties":
                $pagination = new Structures\Pagination($Properties->count($criterion));
                $criterion['order'] = $pagination->order;
                $criterion['orderby'] = $pagination->orderby;
                $criterion['limit'] = $pagination->per_page;
                $criterion['limitstart'] = $pagination->current*$pagination->per_page-$pagination->per_page;
                
                $data->properties = $Properties->get($criterion);
                $data->pagination = $pagination;
                break;
            
            case "compare":
                $comparsions = [];
                $properties = [];
                
               
                if (!empty($_COOKIE['comparsions'])) {
                    $decoded = json_decode(stripcslashes($_COOKIE['comparsions']));                    
                    
                    if (!json_last_error()) {
                       $comparsions = $decoded;
                    }  
                }                
                
                foreach ($comparsions as $comparsion) {
                    $properties[] = $Properties->getDetailed((int)$comparsion->id);
                }
                
                // TODO: move to admin panel
                $data->compare_fields = [
                    'publish_date' => "Publish Date",
                    'bedrooms' => "Bedrooms",
                    'bathrooms' => "Bathrooms",
                    'size' => "Square Feet",
                    'Extras' => "Extras",
                    'Gar_type' => "Garage Type",
                    'Gar_spaces' => "Garage Spaces",
                    'Bsmt1_out' => "Basement",
                ];
                
                $data->properties = $properties;
                break;
                
            case "property":
                // Fetch property
                $property_id = $wp_query->query_vars['property_id'];
                $property = $Properties->getDetailed($property_id);
                
                // Fetch simlar properties
                $range = ($property->deal_type == "buy" ? 50000 : 300);
                $min_price = $property->price - $range;
                $max_price = $property->price + $range;
                $similar_properties = $Properties->get([
                    'confines' => [
                        "`city`='{$property->city}'",
                        "`price`>={$min_price}",
                        "`price`<={$max_price}"
                    ],
                    'limit' => 4
                ]);
                
                // Assign data
                $data->property = $property;
                $data->similar_properties = $similar_properties;
                break;
            
            case "search_posts":                
                $search_query = new \WP_Query([
                    's' => $wp_query->query_vars['s'],
                    'posts_per_page' => 999,
                ]);	
                
                $data->posts = $search_query->posts;
                $data->search_query = $wp_query->query_vars['s'];
                break;
            
            case "content":
                $data->post = get_post($wp_query->queried_object_id);
                break;
                
            case "rets":
                // Fixing start
                $start = new \Datetime();
                
                
                // Do the action
                $credentials = Administration\Connection::getStoredOptions();
                $Rets = new Libs\Rets($credentials->url, $credentials->login, $credentials->password);
                $Rets->login();
                $processed_property = $Rets->processProperties(1);
                $data->property = $processed_property; 
                // $data->rets_data = $Rets->synchronizeProperties();
                
                
                // Fixing end and interval
                $end = new \Datetime();
                $interval = $start->diff($end);
                
                
                // Attach info about execution time
                $data->execition_time = $interval->format("%s");
                
                break;
        }
        
        return $data;     
    }
    
    public static function makeCriterion($search)
    {
        $confines = [];
        
        

        if (!empty($search->address)) {
            $recognized = Libs\Address::recognize($search->address);
            
            if(!empty($recognized->postal_code)) {
                $confines[] = "`postal_code`='{$recognized->postal_code}'";
            } elseif (!empty($recognized->neighborhood)) {
                $confines[] = "`neighborhood`='{$recognized->neighborhood}'";
            } elseif (!empty($recognized->sublocality)) {
                $confines[] = "`sublocality`='{$recognized->sublocality}'";
            } elseif(!empty($recognized->city)) {
                $confines[] = "`city`='{$recognized->city}'";
            } 
        }
        
        if (!empty($search->bathrooms)) {
            $confines[] = "`bathrooms`>='{$search->bathrooms}'";
        }
        
        if (!empty($search->bedrooms)) {
            $confines[] = "`bedrooms`>='{$search->bedrooms}'";
        }
        
        if (!empty($search->min_price)) {
            $confines[] = "`price`>='{$search->min_price}'";
        }
        
        if (!empty($search->max_price)) {
            $confines[] = "`price`<='{$search->max_price}'";
        }
                
        if (!empty($search->type)) {
            $confines[] = "`type`='{$search->type}'";
        }
        
        if (!empty($search->deal_type)) {
            $confines[] = "`deal_type`='{$search->deal_type}'";
        }
        
        
        // Fix for exclude imprecise addresses
        $confines[] = "`postal_code`!=''";
        
        
        
        $criterion['confines'] = $confines;
        $criterion['limit'] = 1000;

        
        
        return $criterion;
    }
}
