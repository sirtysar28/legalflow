@php($fieldConfig = $application->application_type === \App\Enums\ApplicationType::PERMIT
    ? config('legalflow.permit_fields')
    : config('legalflow.agreement_fields'))

@foreach ($fieldConfig as $field)
    @php($value = $application->fields->firstWhere('field_name', $field['name'])?->field_value)
    <div class="col-sm-6 mb-2">
        <span class="text-muted small">{{ $field['label'] }}</span>
        <div>
            @if ($field['type'] === 'select' && filled($value))
                {{ $field['options'][$value] ?? $value }}
            @else
                {{ $value ?? '-' }}
            @endif
        </div>
    </div>
@endforeach
