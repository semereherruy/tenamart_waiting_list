@component('mail::message')
# Weekly Waiting List Report

**Total Signups:** {{ $stats['total_signups'] }}

## Signups by Source
@foreach($stats['signups_by_source'] as $source => $count)
- **{{ ucfirst($source) }}:** {{ $count }}
@endforeach

## Daily Trend (Last 7 Days)
@component('mail::table')
| Date       | Count |
|------------|-------|
@foreach($stats['daily_trend_last_7'] as $date => $count)
| {{ $date }} | {{ $count }} |
@endforeach
@endcomponent

Thanks,<br>
TenaMart Team
@endcomponent
