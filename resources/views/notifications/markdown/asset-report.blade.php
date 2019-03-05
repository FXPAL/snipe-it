@component('mail::message')

<h1>Disposed Assets Report</h1>

<div><b>Start Date:</b> {{ $start_date }}</div>
<div><b>End Date:</b>   {{ $end_date }}</div>

@component('mail::table')
| Asset | Disposed Date |
| ------------- | ------------- |
@foreach ($assets as $asset)
@php
$disposed_date = \App\Helpers\Helper::getFormattedDateObject($asset->updated_at, 'date');
@endphp
| [{{ $asset->present()->name }}]({{ route('hardware.show', ['assetId' => $asset->id]) }}) | {{ $disposed_date['formatted'] }}
@endforeach
@if (sizeof($assets) == 0)
| **No asset disposed** |
@endif
@endcomponent

Thanks,

{{ $snipeSettings->site_name }}

@endcomponent
