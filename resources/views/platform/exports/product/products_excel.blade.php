@if( isset( $from ) && $from == 'pdf')
<style>
    table{ border-spacing: 0;width:100%; }
    table th,td {
        border:1px solid;
    }
</style>
@endif
<table>
    <thead>
        <tr>
            <th>Sku</th>
            <th>Product Name</th>
            <th>Hsn No</th>
            <th>Category</th>
            <th>Sub Category</th>
            <th>Brand</th> 
            <th>Base Price</th>
            <th>Tax %</th> 
            <th>MOP Price</th>
            <th>MRP Price</th>
            <th>Status</th>
            <th>Quantity</th>
            <th>Stock Status</th>
            <th>Label</th>
            <th>Featured</th>
            <th>Description</th>
            <th>Weight</th>
            <th>Width</th> 
            <th>Hight</th>
            <th>Length</th>
            <th>No Of Items</th>
            <th>Material Ingredients</th>
            <th>Features</th>
            <th>Benefits</th>
            {{-- <th>Added At</th>
            <th>Added By</th> --}}
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            <tr>
                <td>{{ $item->sku }}</td>
                <td>{{ $item->product_name }}</td>
                <td>{{ $item->hsn_code ?? '' }}</td>
                <td>{{ $item->productCategory->parent->name ?? $item->productCategory->name ?? '' }}</td>
                <td>{{ (isset($item->productCategory->parent->name) && !empty($item->productCategory->parent->name) ) ? $item->productCategory->name : '' }}</td>
                <td>{{ $item->productBrand->brand_name ?? '' }}</td>
                <td>{{ $item->price }}</td>
                <td>{{ $item->tax->pecentage ?? '' }}</td>
                <td>{{ $item->mrp }}</td>
                <td>{{ $item->strike_price }}</td>
                <td>{{ $item->status }}</td>
                <td>{{ $item->quantity }}</td>
                <td>{{ $item->stock_status }}</td>                
                <td>{{ $item->productLabel->category_name ?? '' }}</td>
                <td>{{ ( isset( $item->is_featured ) && $item->is_featured == 1 ) ? 'Yes' : 'No' }}</td>
                <td>{{ $item->description ?? '' }}</td>
                <td>{{ $item->productMeasurement->weight ?? '' }}</td>
                <td>{{ $item->productMeasurement->width ?? '' }}</td>
                <td>{{ $item->productMeasurement->hight ?? '' }}</td>
                <td>{{ $item->productMeasurement->length ?? '' }}</td>
                <td>{{ $item->no_of_items ?? '' }}</td>
                <td>{{ $item->material_ingredients ?? '' }}</td>
                <td>{{ $item->features ?? '' }}</td>
                <td>{{ $item->benefits ?? '' }}</td>
                {{-- <td>{{ $item->userInfo->name ?? '' }}</td>
                <td>{{ $item->created_at }}</td> --}}
            </tr>
            @endforeach
        @endif
    </tbody>
</table>