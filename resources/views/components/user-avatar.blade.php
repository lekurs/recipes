@props(['user'])

@php
    $role = $user->role;
    $iconName = $role->icon();
    $colorClass = $role->color();
@endphp

<div class="{{ $role->getAvatarClasses() }}">
    <flux:icon :name="$iconName" class="{{ $role->getIconClasses() }}" />
</div>
