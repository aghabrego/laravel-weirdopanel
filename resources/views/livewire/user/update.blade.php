<div class="card">
    <div class="card-header p-0">
        <h3 class="card-title">{{ __('UpdateTitle', ['name' => __('User') ]) }}</h3>
        <div class="px-2 mt-4">
            <ul class="breadcrumb mt-3 py-3 px-4 rounded">
                <li class="breadcrumb-item"><a href="@route(getRouteName().'.home')" class="text-decoration-none">{{ __('Dashboard') }}</a></li>
                <li class="breadcrumb-item"><a href="@route(getRouteName().'.users.lists')" class="text-decoration-none">{{ __(\Illuminate\Support\Str::plural('User')) }}</a></li>
                <li class="breadcrumb-item active">{{ __('Update') }}</li>
            </ul>
        </div>
    </div>

    <form class="form-horizontal" wire:submit.prevent="update" enctype="multipart/form-data">

        <div class="card-body">

            <!-- Name Input -->
            <div class='form-group'>
                <label for='input-name' class='col-sm-2 control-label '>{{ __('Name') }}</label>
                <input type='text' id='input-name' wire:model.blur='name' class="form-control  @error('name') is-invalid @enderror" placeholder='Please enter the name' autocomplete='on'>
                @error('name') <div class='invalid-feedback'>{{ $message }}</div> @enderror
            </div>
            <!-- Email Input -->
            <div class='form-group'>
                <label for='input-email' class='col-sm-2 control-label '>{{ __('Email') }}</label>
                <input type='email' id='input-email' wire:model.blur='email' class="form-control border border-primary @error('email') is-invalid @enderror" placeholder='Please enter the email' autocomplete='on'>
                @error('email') <div class='invalid-feedback'>{{ $message }}</div> @enderror
            </div>
            <!-- Password Input -->
            <div class='form-group'>
                <label for='inputpassword' class='col-sm-2 control-label '>{{ __('Password') }}</label>
                <input type='password' id='input-password' wire:model.blur='password' class="form-control  @error('password') is-invalid @enderror" placeholder='Please enter the password'>
                @error('password') <div class='invalid-feedback'>{{ $message }}</div> @enderror
            </div>
            <!-- Roles Input -->
            <div class='form-group'>
                <label for='input-roles' class='col-sm-2 control-label '>{{__('Select Roles')}}</label>
                <select multiple class="form-control rounded @error('selectedRoles') is-invalid @enderror" wire:model.live="selectedRoles">
                    <option value="null">{{__('Without Role')}}</option>
                    @foreach($roles as $role)
                        <option value="{{$role->id}}">{{$role->name}}</option>
                    @endforeach
                </select>
                @error('selectedRoles') <div class="invalid-feedback">{{ $message }}</div> @enderror
            </div>
            @if (config('weirdo_panel.with_organization_model'))
                <!-- Organizations Input -->
                <div class='form-group'>
                    <label for='input-roles' class='col-sm-2 control-label '>{{__('Select Organizations')}}</label>
                    <select multiple class="form-control rounded @error('selectedOrganizations') is-invalid @enderror" wire:model.live="selectedOrganizations">
                        <option value="null">{{__('Without Organization')}}</option>
                        @foreach($organizations as $organization)
                            <option value="{{$organization->id}}">{{$organization->name}}</option>
                        @endforeach
                    </select>
                    @error('selectedOrganizations') <div class="invalid-feedback">{{ $message }}</div> @enderror
                </div>
            @endif

        </div>

        <div class="card-footer">
            <button type="submit" class="btn btn-info ml-4">{{ __('Update') }}</button>
            <a href="@route(getRouteName().'.users.lists')" class="btn btn-default float-left">{{ __('Cancel') }}</a>
        </div>
    </form>
</div>
