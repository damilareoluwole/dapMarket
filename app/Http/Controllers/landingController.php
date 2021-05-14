<?php

namespace App\Http\Controllers;
use App\Models\mhomepage;
use App\Models\mFavouriteCollection;
use App\Models\mAdvertisement;
use App\Models\mProduct;
use Illuminate\Http\Request;

class landingController extends Controller
{
    public function category(Request $request)
    {
        $category = getCategories();
        $data = [];
        if(isset($_GET['category_id']) && !empty($_GET['category_id'])){
            $parent_id = $_GET['category_id'];
            $category = get_category_children_id1($parent_id);
        }
        if(isset($_GET['category_id']) && $_GET['category_id']== 0){
            $parent_id = $_GET['category_id'];
            $category = get_category_children_id1($parent_id);
        }
        if(isset($_GET['category_id']) && $_GET['category_id']== ''){
            return errorResponse('Unknown Category.',['error' => 'Category id cannot be empty.'],1);
        }
        if($category){   
            foreach ($category as $item){
                $item->image = resolve_pic_path('brand',$item->image);
                $data[] = $item;
            }
        }
        return successResponse('Success',$data);
    }

    public function brand(Request $request)
    {
        $data = [];
        $t = 0;
        $brand = getBrand();
        if(isset($_GET['brand_id']) && !empty($_GET['brand_id'])){
            $t = 1;
            $brand_id = $_GET['brand_id'];
            $brand = getBrand($brand_id);
        }

        if(isset($_GET['brand_id']) && $_GET['brand_id']== ''){
            return errorResponse('Unknown Brand.',['error' => 'Brand id cannot be empty.'],1);
        }

        if(isset($_GET['brand_id']) && $_GET['brand_id']== 0){
            return errorResponse('Unknown Brand.',['error' => 'Brand id cannot be zero(0)'],1);
        }
        if($brand){
            if(!$t){
                foreach ($brand as $item){
                    $item->image = resolve_pic_path('brand',$item->image);
                    $data[] = $item;
                }
                return successResponse('Success',$data);
            }
            $brand->image = resolve_pic_path('brand',$brand->image);
        }
        return successResponse('Success',$brand);
    }

    public function home(Request $request)
    {
        $data = [];
        $homepape = mhomepage::orderByDesc('id')
                                ->first();  
                                   
        $homepape->image = resolve_pic_path('homepage',$homepape->image);       
        return successResponse('Success',$homepape);
    }

    public function homeCollection(Request $request)
    {
        $favCollection = mFavouriteCollection::where('is_enabled',0)
                                        ->orderByDesc('rank')
                                        ->get();
        return successResponse('Success',$favCollection);
    }

    public function Ads(Request $request)
    {
        $data = [];
        $ads = mAdvertisement::where('is_enabled',0)
                            ->orderByDesc('rank')
                            ->get();
        if($ads){                  
            foreach ($ads as $item){
                $item->image = resolve_pic_path('ads',$item->image);
                $data[] = $item;
            }
        }
        return successResponse('Success',$data);
    }

    public function products(Request $request)
    {
        $data = [];
        $t = 0;
        $product = getProduct();
        if(isset($_GET['product_id']) && !empty($_GET['product_id'])){
            $t = 1;
            $product_id = $_GET['product_id'];
            $product = getProduct($product_id);
        }

        if(isset($_GET['product_id']) && $_GET['product_id']== ''){
            return errorResponse('Unknown Product.',['error' => 'product id cannot be empty'],1);
        }
        
        if(isset($_GET['product_id']) && $_GET['product_id']== 0){
            return errorResponse('Unknown Product.',['error' => 'Product id cannot be zero(0)'],1);
        }
        if($product){
            if(!$t){
                foreach ($product as $item){
                    $item->image = resolve_pic_path('product',$item->image);
                    $data[] = $item;
                }
                return successResponse('Success',$data);
            }
            $product->image = resolve_pic_path('product',$product->image);
            $product->category_name = get_category_name($product->category_id);
            $product->shop_name = get_shop_name($product->shop_id);
            $product->brand_name = get_brand_name($product->brand_id);
            $product->condition_name = get_condition_name($product->condition_id);
            $product->size_name = get_size_name($product->size_id);
            $images = get_all_product_images($product->id);
            if($images){
                foreach ($images as $item){
                    $item->image = resolve_pic_path('product',$item->image);
                    $data[] = $item;
                }
            }
            $product->other_product_images = $data;
        }
        return successResponse('Success',$product);
    }

    public function shops(Request $request)
    {
        $data = [];
        $t = 0;
        $shop = getShop();
        if(isset($_GET['shop_id']) && !empty($_GET['shop_id'])){
            $t = 1;
            $shop_id = $_GET['shop_id'];
            $shop = getShop($shop_id);
        }

        if(isset($_GET['shop_id']) && $_GET['shop_id']== ''){
            return errorResponse('Unknown Shop.',['error' => 'Shop id cannot be empty.'],1);
        }

        if(isset($_GET['shop_id']) && $_GET['shop_id']== 0){
            return errorResponse('Unknown Shop.',['error' => 'Shop id cannot be zero(0)'],1);
        }
        if($shop){
            if(!$t){
                return successResponse('Success',$shop);
            }

            $shop->shop_owner = get_user_data($shop->user_id);
        }
        return successResponse('Success',$shop);
    }
}
