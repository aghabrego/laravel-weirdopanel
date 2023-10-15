@if (count(\WeirdoPanel\Models\CRUD::active()) > 0)
    <li class="list-divider"></li>
    <li class="nav-small-cap"><span class="hide-menu">{{ __('CRUD Menu') }}</span></li>
    @foreach(\WeirdoPanel\Models\CRUD::active() as $crud)
        <x-weirdopanel::crud-menu-item :crud="$crud" />
    @endforeach
@endif