<?php

namespace App\Http\Controllers\Mobile;

use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Http\Resources\Mobile\ProductResource;
use App\Models\Store;
use App\Models\ProductStatus;
class ProductController extends Controller
{
	public function __construct() {
		$this->productStatusIDCease = ProductStatus::where('product_status_name', 'Ngưng bán')->select('id')->first();
	}

	public function search(Request $request) {
		$keywords = $request->keywords;
		$storeId  = $request->storeId;
		$store    = Store::with(['products' => function($query) use ($keywords){
			return $query->show()->likeName($keywords);
		}])->findOrFail($storeId);

		$results = $this->respondSuccess('Search products', $store->products);
		return response($results);
	}

    protected function respondSuccess($message, $data, $type= 'many', $status = 200, $pagination = []) {
		$res = [
			'status'  => 'success',
			'message' => $message . ' successfully.',
		];

		switch ($type) {

			case 'one':
			$res['product']  = new ProductResource($data);
			break;

			case 'many':
			$res['products'] = ProductResource::collection($data);
			if (count($pagination) > 0) {
				$res['pagination'] = $pagination;
			}
			break;
		}

		return $res;
	}

	protected function pagination($data) {
		return $pagination = [
			'total'        => $data->total(),
			'per_page'     => $data->perPage(),
			'from'         => $data->firstItem(),
			'current_page' => $data->currentPage(),
			'to'           => $data->lastItem(),
			'last_page'    => $data->lastPage(),
		];
	}
}
