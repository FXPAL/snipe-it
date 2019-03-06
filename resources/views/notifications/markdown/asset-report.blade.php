@component('mail::message')

<h1>Disposed Assets Report</h1>

<div><b>Start Date:</b> {{ $start_date }}</div>
<div><b>End Date:</b>   {{ $end_date }}</div>
<br/>
<h1>Fixed Assets</h1>
@component('mail::table')
| Asset  | Asset Tag | Serial  | Price | Disposed Date |
| :----- | --------: | :-----: | ----: | ------------: |
@foreach ($fixed_assets as $asset)
@php
$disposed_date = \App\Helpers\Helper::getFormattedDateObject($asset->updated_at, 'date');
$price = $asset->purchase_cost;
if ($price) {
    $price = '$' . $price;
}
@endphp
| [{{ $asset->present()->name }}]({{ route('hardware.show', ['assetId' => $asset->id]) }}) | {{ $asset->asset_tag }} | {{ $asset->serial }} | {{ $price }} |  {{ $disposed_date['formatted'] }}
@endforeach
@if (sizeof($fixed_assets) == 0)
| **No asset disposed**
@endif
@endcomponent

<br/>&nbsp;<br/>
<hr size="1"/>

<h1>Non-Fixed Assets</h1>
@component('mail::table')
| Asset  | Asset Tag | Serial  | Price | Disposed Date |
| :----- | --------: | :-----: | ----: | ------------: |
@foreach ($nonfixed_assets as $asset)
@php
$disposed_date = \App\Helpers\Helper::getFormattedDateObject($asset->updated_at, 'date');
$price = $asset->purchase_cost;
if ($price) {
    $price = '$' . $price;
}
@endphp
| [{{ $asset->present()->name }}]({{ route('hardware.show', ['assetId' => $asset->id]) }}) | {{ $asset->asset_tag }} | {{ $asset->serial }} | {{ $price }} |  {{ $disposed_date['formatted'] }}
@endforeach
@if (sizeof($nonfixed_assets) == 0)
| **No asset disposed**
@endif
@endcomponent

Thanks,

{{ $snipeSettings->site_name }}

@endcomponent
