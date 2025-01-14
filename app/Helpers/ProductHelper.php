<?php


if (! function_exists('getImageUrls')) {
    function getImageUrls(array $mainImages): array
    {
        $imageUrls = [];

        foreach ($mainImages as $image) {
            if (isset($image['urls']) && is_array($image['urls'])) {
                // Lấy URL đầu tiên từ danh sách URLs nếu tồn tại
                $imageUrls[] = $image['urls'][0];
            }
        }

        return $imageUrls;
    }
}


/**
 * Lấy giá lớn nhất từ danh sách SKUs.
 *
 * @param array $skus
 * @return float|null
 */
if (! function_exists('getMaxPrice')) {
    function getMaxPrice(array $skus): ?float
    {
        $prices = array_map(function ($sku) {
            return isset($sku['price']['sale_price']) ? (float) $sku['price']['sale_price'] : null;
        }, $skus);

        // Loại bỏ các giá trị null trước khi tính max
        $prices = array_filter($prices, fn($price) => $price !== null);

        return !empty($prices) ? max($prices) : null;
    }
}
/**
 * Lấy giá nhỏ nhất từ danh sách SKUs.
 *
 * @param array $skus
 * @return float|null
 */
if (! function_exists('getMinPrice')) {
    function getMinPrice(array $skus): ?float
    {
        $prices = array_map(function ($sku) {
            return isset($sku['price']['sale_price']) ? (float) $sku['price']['sale_price'] : null;
        }, $skus);

        // Loại bỏ các giá trị null trước khi tính min
        $prices = array_filter($prices, fn($price) => $price !== null);

        return !empty($prices) ? min($prices) : null;
    }
}
/**
 * Tính tổng lượng hàng tồn kho từ danh sách SKUs.
 *
 * @param array $skus
 * @return int
 */
if (! function_exists('getTotalInventory')) {
    function getTotalInventory(array $skus): int
    {
        $totalQuantity = 0;

        foreach ($skus as $sku) {
            if (isset($sku['inventory']) && is_array($sku['inventory'])) {
                foreach ($sku['inventory'] as $inventoryItem) {
                    if (isset($inventoryItem['quantity'])) {
                        $totalQuantity += (int) $inventoryItem['quantity'];
                    }
                }
            }
        }

        return $totalQuantity;
    }

    if (! function_exists('itemCount')) {
        function itemCount(array $data)
        {
            $collection = collect($data);
            $groupedProducts = $collection
    ->groupBy('product_id')
    ->map(function ($items, $productId) {
        $firstItem = $items->first();

        // Nhóm SKU và hợp nhất thông tin
        $groupedSkus = $items->groupBy('sku_id')->map(function ($skuItems, $skuId) {
            $firstSku = $skuItems->first();

            return [
                'sku_id' => $skuId,
                'sku_name' => $firstSku['sku_name'],
                'sale_price' => $firstSku['sale_price'],
                'sku_image' => $firstSku['sku_image'],
                'sku_count' => $skuItems->count() // Đếm số lần lặp lại
            ];
        })->values();

        return [
            'product_id' => $productId,
            'product_name' => $firstItem['product_name'],
            'sku_count' => $groupedSkus->count(),
            'skus' => $groupedSkus
        ];
    })
    ->values()
    ->map(function ($item, $index) {
        return array_merge(['index' => $index + 1], $item); // Thêm số thứ tự cho product
    });

                return $groupedProducts;
        }
    }
}
