@props([
    'id',
    'name',
    'label',
    'ajaxUrl',
    'placeholder' => 'Please Select',
    'required' => false,
    'disabled' => false,
    'selectedValue' => null,
    'selectedText' => null,
    'createUrl' => null,
    'createUrlTemplate' => null,
    'createTitle' => null,
    'createSize' => 'md',
    'dependsOn' => null,
    'dependsParam' => null,
    'errorKey' => null,
])

@php
    $errorKey = $errorKey ?? $name;
    $showAdd = filled($createUrl) || filled($createUrlTemplate);
    $addDisabled = filled($createUrlTemplate) && blank($createUrl);
    $hasSelectedOption = filled($selectedValue) && filled($selectedText);
@endphp

<label for="{{ $id }}" class="form-label">
    {{ $label }}
    @if ($required)
        <span class="text-danger">*</span>
    @endif
</label>
<div class="select2-add-wrapper {{ $showAdd ? 'has-add' : '' }}">
    <select
        id="{{ $id }}"
        name="{{ $name }}"
        class="form-select init_select_dynamic @error($errorKey) is-invalid @enderror"
        data-placeholder="{{ $placeholder }}"
        data-url="{{ $ajaxUrl }}"
        @if ($dependsOn) data-depends-on="{{ $dependsOn }}" @endif
        @if ($dependsParam) data-depends-param="{{ $dependsParam }}" @endif
        @disabled($disabled)
        @required($required)
        {{ $attributes }}
    >
        <option value=""></option>
        @if ($hasSelectedOption)
            <option value="{{ $selectedValue }}" selected>{{ $selectedText }}</option>
        @endif
    </select>
    @if ($showAdd)
        <button
            type="button"
            class="btn btn-warning btn-add"
            data-ajax-popup="true"
            data-select-target="#{{ $id }}"
            data-title="{{ $createTitle ?? 'Create '.$label }}"
            data-size="{{ $createSize }}"
            @if ($createUrlTemplate) data-create-url-template="{{ $createUrlTemplate }}" @endif
            data-url="{{ $createUrl }}"
            title="{{ $addDisabled ? 'Select a customer first' : ($createTitle ?? 'Create '.$label) }}"
            aria-label="{{ $addDisabled ? 'Select a customer first' : ($createTitle ?? 'Create '.$label) }}"
            @disabled($addDisabled)
        >
            <i class="bi bi-plus-lg" aria-hidden="true"></i>
        </button>
    @endif
</div>
@error($errorKey)
    <div class="invalid-feedback d-block">{{ $message }}</div>
@enderror
