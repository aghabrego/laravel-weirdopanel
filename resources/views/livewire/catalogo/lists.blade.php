@if (count(\WeirdoPanel\Models\CRUD::active()) > 0)
    <aside class="left-sidebar" data-sidebarbg="skin6">
        <div class="scroll-sidebar" data-sidebarbg="skin6">
            <nav class="sidebar-nav">
                <ul class="sidebarnav pl-2">
                    <li class="nav-small-cap"><span class="hide-menu">{{ __('CRUD Menu') }}</span></li>
                    @foreach(\WeirdoPanel\Models\CRUD::active() as $crud)
                        @if (hasPermission(getRouteName().".{$crud->route}.*", $crud->with_acl))
                            <li class="sidebar-item @isActive(getRouteName().'.'.$crud->route.'.read')">
                                <a href="@route(getRouteName().'.'.$crud->route.'.read')" class="sidebar-link @isActive(getRouteName().'.'.$crud->route.'.read')">
                                    <span class="hide-menu">{{ __(\Illuminate\Support\Str::plural(ucfirst($crud->name))) }}</span>
                                </a>
                            </li>
                        @endif
                    @endforeach
                </ul>
            </nav>
        </div>
    </aside>
@endif
