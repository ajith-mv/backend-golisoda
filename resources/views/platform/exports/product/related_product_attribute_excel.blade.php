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
            <th> SKU </th>
            <th> Related SKU </th>
            <th> Frequent SKU</th>
            <th> Meta Title</th>
            <th> Meta Description</th>
            <th> Meta Keyword </th>
 
        </tr>
    </thead>
    <tbody>
        @if( isset( $list ) && !empty($list))
            @foreach ($list as $item)
            @php
                $relatedsku=[];
                $related_data="";
            if(isset($item->productRelated)){
             foreach($item->productRelated as $related_sku){
                $relatedsku[]=$related_sku->Product->sku;
                $related_data=implode(",\n",$relatedsku);
                }
                }
             
 
               $frequentsku=[];
               $frequent_data="";
               if(isset($item->productCrossSale)){
               foreach($item->productCrossSale as $frequent_sku){
                $frequentsku[]=$frequent_sku->Product->sku;
                $frequent_data=implode(",\n",$frequentsku);
               }
            }

             @endphp
            <tr>
                <td>{{ $item->sku}}</td>
                <td>{{$related_data}}</td>
                <td>{{$frequent_data}}</td>
                <td>{{ $item->productMeta->meta_title ?? '' }}</td>
                <td>{{ $item->productMeta->meta_description ?? '' }}</td>
                <td>{{ $item->productMeta->meta_keyword ?? '' }}</td>
            </tr>
            @endforeach
        @endif
    </tbody>
</table>